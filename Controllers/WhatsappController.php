<?php

namespace Whatsapp\Controllers;

use App\Controllers\Security_Controller;

\WpOrg\Requests\Autoload::register();

class WhatsappController extends Security_Controller {

    function __construct() {
        parent::__construct();
        $this->whatsapp_model = model('Whatsapp\Models\WhatsappModel');
        $this->whatsapp_template_list_model = model('Whatsapp\Models\WhatsappTemplateListModel');
    }

    public function template_list() {
        return $this->template->rander('Whatsapp\Views\template_list',[]);
    }

    public function template_list_table() {
        if ($this->request->isAJAX()) {
            $data   = $this->whatsapp_template_list_model->show_all();
            $result = [];
            foreach ($data as $value) {
                $result[] = $this->_make_template_list_row($value);
            }
            echo json_encode(["data" => $result]);
        }
    }

    public function _make_template_list_row($data) {
        $id             = $data->id;
        $template_name  = $data->template_name;
        $language       = $data->language;
        $category       = $data->category;
        $status         = $data->status;
        $body_data      = $data->body_data;

        $color = '';
        if('APPROVED' == $status) {
            $color = '#0abb87';
        }

        $status = js_anchor($status, array("style" => "background-color: $color", "class" => "badge"));

        return [
            $id,
            $template_name,
            $language,
            $category,
            $status,
            $body_data
        ];
    }

    public function get_business_information()
    {
        $whatsapp_business_account_id = get_setting('whatsapp_business_account_id');
        $whatsapp_access_token        = get_setting('whatsapp_access_token');


        $request                      = \WpOrg\Requests\Requests::get(
            'https://graph.facebook.com/v14.0/'.$whatsapp_business_account_id.'?fields=id,name,message_templates,phone_numbers&access_token='.$whatsapp_access_token
        );

        $response = json_decode($request->body);


        // if there is any error from api then display appropriate message
        if (property_exists($response, 'error')) {
            echo json_encode([
                'success' => false,
                'type'    => 'danger',
                'message' => $response->error->message,
            ]);
            exit();
        }
        $data        = $response->message_templates->data;
        $insert_data = [];

        foreach ($data as $key => $template_data) {
            //only consider "APPROVED" templates
            if ('APPROVED' == $template_data->status) {
                $insert_data[$key]['template_id']   = $template_data->id;
                $insert_data[$key]['template_name'] = $template_data->name;
                $insert_data[$key]['language']      = $template_data->language;

                $insert_data[$key]['status']   = $template_data->status;
                $insert_data[$key]['category'] = $template_data->category;

                $components = array_column($template_data->components, null, 'type');

                $insert_data[$key]['header_data_format']    = $components['HEADER']->format ?? '';
                $insert_data[$key]['header_data_text']       = $components['HEADER']->text ?? null;
                $insert_data[$key]['header_params_count'] = preg_match_all('/{{(.*?)}}/i', $components['HEADER']->text ?? '', $matches);

                $insert_data[$key]['body_data']            = $components['BODY']->text ?? null;
                $insert_data[$key]['body_params_count'] = preg_match_all('/{{(.*?)}}/i', $components['BODY']->text, $matches);

                $insert_data[$key]['footer_data']          = $components['FOOTER']->text ?? null;
                $insert_data[$key]['footer_params_count'] = preg_match_all('/{{(.*?)}}/i', $components['FOOTER']->text ?? null, $matches);

                $insert_data[$key]['buttons_data']  = json_encode($components['BUTTONS'] ?? []);
            }
        }
        $insert_data_id    = array_column($insert_data, 'template_id');

        $db      = \Config\Database::connect();
        $builder = $db->table('whatsapp_templates');

        $existing_template = $builder->whereIn('template_id', $insert_data_id,)->get()->getResult();

        $existing_data_id = array_column($existing_template, 'template_id');

        $new_template_id = array_diff($insert_data_id, $existing_data_id);
        $new_template    = array_filter($insert_data, function ($val) use ($new_template_id) {
            return in_array($val['template_id'], $new_template_id);
        });

        //No need to update template data in db because you can't edit template in meta dashboard
        \Whatsapp\Libraries\Apiinit::check_url("Whatsapp");
        if (!empty($new_template)) {
            $db->query("SET NAMES 'utf8mb4' COLLATE 'utf8mb4_general_ci'");
            $builder->insertBatch($new_template);
        }

        // GET TEMPLATE: END
        echo json_encode([
            'success' => true,
            'type'    => 'success',
            'message' => app_lang('template_data_loaded'),
        ]);
    }
}
