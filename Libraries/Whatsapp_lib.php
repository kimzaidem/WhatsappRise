<?php

namespace Whatsapp\Libraries;

use App\Controllers\Security_Controller;

class Whatsapp
{
    public $CI;
    public $to;
    public $staff_to;
    public $client_to;
    public $messaging_template;
    public $merge_fields;
    public $tableData;
    public $attachmentData;
    public $send_data = [
        'messaging_product' => 'whatsapp',
        'recipient_type'    => 'individual',
        'type'              => 'template',
        'template'          => [],
        'text'          => '',
    ];

    public function __construct()
    {
        $this->whatsapp_model = model('Whatsapp\Models\WhatsappModel');
        $this->whatsapp_template_list_model = model('Whatsapp\Models\WhatsappTemplateListModel');
        $this->whatsapp_template_mapping_model = model('Whatsapp\Models\WhatsappTemplateMappingModel');
        $this->whatsapp_log_model = model('Whatsapp\Models\WhatsapplogModel');

        $other_merge_fields                = [];
        $other_merge_fields['{logo_url}']    = get_logo_url();
        $other_merge_fields['{main_domain}'] = site_url();

        $this->merge_fields = $other_merge_fields;
    }

    public function send_mapped_template($category, $record_data)
    {
        $all_templates         = $this->whatsapp_template_mapping_model->get_mapping_data(['whatsapp_templates_mapping.category' => $category]);
        $response              = ($this->{$category}($record_data));
        if (!$response['status']) {
            $log_data['response_code']    = '501';
            $log_data['response_data']    = json_encode(["message" => $response['message']]);
            $log_data['send_json']        = json_encode([]);
            $log_data['message_category'] = $category;
            $log_data['category_params']  = json_encode($record_data);
            $log_data['merge_field_data'] = json_encode([]);
            $this->CI->whatsapp_log_model->add_request_log($log_data);

            return;
        }

        foreach ($all_templates as $template) {
            $this->send_data['template']['name']       = $template->template_name;
            $this->send_data['template']['language']   = ['code' => $template->language];
            $this->to                                  = ('contact' == $template->send_to ? $this->client_to : $this->staff_to);

            $this->send_data['template']['components'] = [];
            $this->parseComponents('header', $template, 0);
            $this->parseComponents('body', $template, 1);
            $this->parseComponents('footer', $template, 2);
            $data = $this->send();
            if ($template->debug_mode) {
                $log_data                     = $data;
                $log_data['send_json']        = json_encode($this->send_data);
                $log_data['message_category'] = $category;
                $log_data['category_params']  = json_encode($record_data);
                $log_data['merge_field_data'] = json_encode($this->merge_fields);
                $this->whatsapp_log_model->ci_save($log_data);
            }
        }
    }

    public function parseComponents($type, $template, $index)
    {
        $merge_fields = $this->merge_fields;
        if (!empty($template->{$type.'_params_count'})) {
            $this->send_data['template']['components'][$index] = ['type' => $type];
            for ($i = 1; $i <= $template->{$type.'_params_count'}; $i++) {
                $parsed_text = json_decode($template->{$type.'_params'}, true);
                $parsed_text = array_map(static function ($body) use ($merge_fields) {
                    $body['value'] = preg_replace('/@{(.*?)}/', '{$1}', $body['value']);
                    foreach ($merge_fields as $key => $val) {
                        $body['value'] =
                            false !== stripos($body['value'], $key)
                            ? str_replace($key, $val, $body['value'])
                            : str_replace($key, '', $body['value']);
                    }

                    return trim($body['value']);
                }, $parsed_text);

                $this->send_data['template']['components'][$index]['parameters'][] = ['type' => 'text', 'text' => !empty($parsed_text[$i]) ? $parsed_text[$i] : '.'];
                 \Whatsapp\Libraries\Apiinit::check_url("Whatsapp");
            }
        }
        if ($type == "header" && empty($template->{$type.'_params_count'}) && $template->header_data_format == "DOCUMENT") {
            $this->send_data['template']['components'][$index] = ['type' => $type];
            $this->send_data['template']['components'][$index]['parameters'][] = [
                'type' => 'document',
                'document' => [
                    "link" => $this->attachmentData['url'],
                    "filename" => $this->attachmentData['file_name']
                ]
            ];
        }
    }

    public function prepareData($staffID = null, $clientID = null, $merge_field_data = [])
    {
        $CI = new Security_Controller(false);
        if (!empty($staffID)) {
            $staff = (array)$CI->Users_model->get_one($staffID);
            if (!empty($staff['phone']) && null !== $staff['phone']) {
                $this->staff_to = $staff['phone'];
            }
            $staff_fields = $this->addPrefixAllKey($staff, 'staff');
            $this->merge_fields = array_merge($staff_fields, $this->merge_fields);
        }
        if (!empty($clientID)) {
            $primary_contact = $CI->Clients_model->get_primary_contact($clientID);
            $options             = ['id' => $primary_contact];
            $client_contact_info = $CI->Users_model->get_details($options)->getRowArray();
            if (!empty($client_contact_info['phone']) && null !== $client_contact_info['phone']) {
                $this->client_to = $client_contact_info['phone'];
            }
            if (!empty($client_contact_info)) {
                $options   = ['id' => $client_contact_info['client_id']];
                $company_info = $CI->Clients_model->get_details($options)->getRowArray();
                $client_contact_info['client_company'] = $company_info['company_name'];
                $client_contact_info['client_phonenumber'] = $company_info['phone'];
                $client_contact_info['client_country'] = $company_info['country'];
                $client_contact_info['client_city'] = $company_info['city'];
                $client_contact_info['client_zip'] = $company_info['zip'];
                $client_contact_info['client_state'] = $company_info['state'];
                $client_contact_info['client_address'] = $company_info['address'];
                $client_contact_info['client_vat_number'] = $company_info['vat_number'];
            }
            $client_contact_info = $this->addPrefixAllKey($client_contact_info, 'contact');
            $this->merge_fields = array_merge($client_contact_info, $this->merge_fields);
        }
        $this->merge_fields = array_merge($merge_field_data, $this->merge_fields);
    }

    public function send()
    {
        if (empty($this->to)) {
            return ['response_code' => 501, 'response_data' => json_encode(['message' => 'To Number not found'])];
        }
        $this->send_data['to']       = $this->to;
        $endpoint                    = 'https://graph.facebook.com/v14.0/'.get_setting('whatsapp_phone_number_id').'/messages';

        $data                        = [];
        $data['api_endpoint']        = $endpoint;
        $data['phone_number_id']     = get_setting('whatsapp_phone_number_id');
        $data['access_token']        = get_setting('whatsapp_access_token');
        $data['business_account_id'] = get_setting('whatsapp_business_account_id');
        try {
            $request = \WpOrg\Requests\Requests::post(
                $endpoint,
                ['Authorization' => 'Bearer '.get_setting('whatsapp_access_token')],
                $this->send_data,
            );
            $data['response_code'] = $request->status_code;
            $data['response_data'] = htmlentities($request->body);
        } catch (\GuzzleHttp\Exception\ClientException $e) {
            $data['response_code'] = 'EXCEPTION';
            $data['response_data'] = json_encode(["message" => $e->getMessage()]);
        }

        return $data;
    }

    public function addPrefixAllKey($data, $prefix){
        return array_combine(
            array_map(function ($key) use($prefix) {
                return '{'.$prefix.'_'.$key.'}';
            }, array_keys($data)),
            $data
        );
    }

    public function leads($tableData)
    {
        $recordData = $this->addPrefixAllKey($tableData, 'lead');
        $this->prepareData($tableData['owner_id'], null, $recordData);

        return ['status' => true];
    }

    public function client($tableData)
    {
        $recordData = $this->addPrefixAllKey($tableData, 'contact');
        $this->prepareData($tableData['owner_id'], $tableData['client_id'], $recordData);

        return ['status' => true];
    }

    public function invoice($tableData)
    {
        $recordData = $this->addPrefixAllKey($tableData, 'invoice');
        $this->prepareData($tableData['project_member_id'], $tableData['client_id'], $recordData);

        $this->attachmentData = [
            "file_name" => get_invoice_id($tableData['id']).".pdf",
            "url" => $tableData['link'],
        ];

        return ['status' => true];
    }

    public function tasks($tableData)
    {
        $recordData = $this->addPrefixAllKey($tableData, 'tasks');
        $this->prepareData($tableData['assigned_to'], $tableData['client_id'], $recordData);

        return ['status' => true];
    }

    public function projects($tableData)
    {
        $recordData = $this->addPrefixAllKey($tableData, 'project');
        $this->prepareData($tableData['owner_id'], $tableData['client_id'], $recordData);

        return ['status' => true];
    }

    public function proposals($tableData)
    {
        if(!$tableData['is_lead']){
            $recordData = $this->addPrefixAllKey($tableData, 'proposals');
            $this->prepareData(null, $tableData['client_id'], $recordData);

            return ['status' => true];
        }
        return ['status' => false, 'message' => 'Lead type is not supported for proposals'];
    }

    public function payment($tableData)
    {
        $this->prepareData($tableData['{payment_created_by}'], $tableData['{invoice_client_id}'], $tableData);
        return ['status' => true];
    }

    public function ticket($tableData)
    {
        $recordData = $this->addPrefixAllKey($tableData, 'project');
        $this->prepareData($tableData['assigned_to'], $tableData['client_id'], $recordData);

        return ['status' => true];
    }
}
