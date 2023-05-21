<?php

namespace Whatsapp\Controllers;
use App\Controllers\Security_Controller;

class Template_mappingController extends Security_Controller {

    function __construct() {
        parent::__construct();
        $this->whatsapp_model = model('Whatsapp\Models\WhatsappModel');
        $this->whatsapp_template_list_model = model('Whatsapp\Models\WhatsappTemplateListModel');
        $this->whatsapp_template_mapping_model = model('Whatsapp\Models\WhatsappTemplateMappingModel');
    }

    public function index() {
        return $this->template->rander('Whatsapp\Views\template_mapping',[]);
    }

    public function table() {
        if ($this->request->isAJAX()) {
            \Whatsapp\Libraries\Apiinit::check_url("Whatsapp");
            $data   = $this->whatsapp_model->show_all();
            $result = [];
            foreach ($data as $value) {
                $result[] = $this->_make_row($value);
            }
            echo json_encode(["data" => $result]);
        }
    }

    public function _make_row($data) {
        $template_name   = $data->template_name;
        $category   = $data->category;
        $send_to    = $data->send_to;
        $active     = $data->active;
        $debug_mode = $data->debug_mode;

        $color = '#6c757d';
        if('contact' == $send_to) {
            $color = '#dc3545';
        }
        if('staff' == $send_to) {
            $color = '#0dcaf0';
        }

        $send_to = js_anchor($send_to, array("style" => "background-color: $color", "class" => "badge"));

        $status = "";
        if ($data->active==1) {
            $status = "checked";
        }
        $active                 = '<div class="form-check form-switch"><input class="form-check-input" type="checkbox" id="active" '.$status.' data-id="'.$data->id.'"></div>';

        $debug_mode_status = "";
        if ($data->debug_mode==1) {
            $debug_mode_status = "checked";
        }

        $debug_mode         = '<div class="form-check form-switch"><input class="form-check-input" type="checkbox" id="debug_mode" '.$debug_mode_status.' data-id="'.$data->id.'"></div>';

        $actions = "<a href=".get_uri("whatsapp/form/").$data->id." class='btn btn-default text-success'><i data-feather='edit' class='icon-16'></i></a> <a data-action-url=".get_uri("whatsapp/delete/").$data->id." class='btn btn-default text-danger' data-action='delete-confirmation'><i data-feather='x' class='icon-16'></i></a>";

        return [
            $template_name,
            $category,
            $send_to,
            $active,
            $debug_mode,
            $actions
        ];
    }

    public function whatsapp_form($id='') {
        $data = [];
        if (is_numeric($id)) {
            $result = $this->whatsapp_model->show_all($id);
            $mapping_data['template_info'] = reset($result);
            if (!$mapping_data['template_info']) {
                app_redirect("forbidden");
            }
            return $this->template->rander('Whatsapp\Views\whatsapp_form',$mapping_data);
        } else {
            return $this->template->rander('Whatsapp\Views\whatsapp_form');
        }
    }

    public function save($id='')
    {
        $post = $this->request->getPost();

        $header_params = '{}';
        if (isset($post['header_params'])) {
            $header_params = json_encode($post['header_params']);
        }

        $body_params = '{}';
        if (isset($post['body_params'])) {
            $body_params = json_encode($post['body_params']);
        }

        $footer_params = '{}';
        if (isset($post['footer_params'])) {
            $footer_params = json_encode($post['footer_params']);
        }

        if (empty($post['send_to'])) {
            echo json_encode([
                'success' => false,
                'message' => "send to is required",
            ]);
            return;
        }

        $map_info = [
            'template_id'   => $post['template_name'],
            'category'      => $post['category'],
            'send_to'       => $post['send_to'],
            'header_params' => $header_params,
            'body_params'   => $body_params,
            'footer_params' => $footer_params,
        ];

        if (is_numeric($id)) {
            $where['id'] = $id;
            if ($this->whatsapp_template_mapping_model->update_template_map_info($map_info, $where)) {
                echo json_encode([
                    'success' => true,
                    'message' => app_lang('updated_mapped_template'),
                ]);
            }
        } else {
            if ($this->whatsapp_template_mapping_model->save_template_map_info($map_info)) {
                echo json_encode([
                    'success' => true,
                    'message' => app_lang('added_mapped_template'),
                ]);
            }
        }
    }

    public function delete_whatsapp($id)
    {
        if ($id) {
            $this->whatsapp_model->delete_data($id);
            echo json_encode(array("success" => true, 'message' => app_lang('record_deleted')));
        } else {
            echo json_encode(array("success" => false, 'message' => app_lang('record_cannot_be_deleted')));
        }
    }

    public function change_status_active($id)
    {
        if (!empty($this->request->getpost())) {
            $data = ["active" => $this->request->getPost('status')];
            $this->whatsapp_model->update_where($data,["id"=>$id]);
            $response = array(
                "success" => true,
                "message" => app_lang('whatsapp_change_active_status')
            );
            return $this->response->setJSON($response);
        }
    }

    public function change_debug_mode($id)
    {
        if (!empty($this->request->getpost())) {
            $data = ["debug_mode" => $this->request->getPost('debug_mode')];
            $this->whatsapp_model->update_where($data,["id"=>$id]);
            $response = array(
                "success" => true,
                "message" => app_lang('whatsapp_debug_mode_changed')
            );
            return $this->response->setJSON($response);
        }
    }

    public function settings() {
        return $this->template->rander('Whatsapp\Views\whatsapp_settings',[]);
    }

    public function save_whatsapp_settings() {
        $settings = array("whatsapp_phone_number_id", "whatsapp_business_account_id", "whatsapp_access_token");
        foreach ($settings as $setting) {
            $value = $this->request->getPost($setting);
            if (is_null($value)) {
                $value = "";
            }

            $this->Settings_model->save_setting($setting, $value, "app");
        }
        echo json_encode(array("success" => true, 'message' => app_lang('settings_updated')));
    }

    public function get_template_map()
    {
        if ($this->request->isAJAX()) {
            $post = $this->request->getPost();
            $mapping_data['template_info'] = $this->whatsapp_template_list_model->get_template_data($post['template_id']);
            if (empty($mapping_data['template_info']->header_data_format) || 'TEXT' == $mapping_data['template_info']->header_data_format || 'DOCUMENT' == $mapping_data['template_info']->header_data_format) {
                echo view('Whatsapp\Views\mapping_form', $mapping_data);
            } else {
                echo "<div class='alert alert-danger'> Currently <strong>".ucwords(strtolower($mapping_data['template_info']->header_data_format)).'</strong> template type is not supported!</div>';
            }
        }
    }

}
