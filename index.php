<?php

defined('PLUGINPATH') or exit('No direct script access allowed');

/*
  Plugin Name: WhatsApp Cloud API Business Integration plugin
  Description: Keep your Customers & Staff updated in real-time about New Invoices, Project's Tasks and more!
  Version: 1.1.0
  Requires at least: 3.0
 */

require_once __DIR__ .'/Libraries/Whatsapp_lib.php';

use App\Controllers\Security_Controller;
use Whatsapp\Libraries\Whatsapp;

if (!class_exists("\WpOrg\Requests\Autoload")) {
    require_once __DIR__ . "/vendor/autoload.php";
}

//add menu item to left menu
app_hooks()->add_filter('app_filter_staff_left_menu', function ($sidebar_menu) {

    $whatsapp_submenu = [];

    $whatsapp_submenu[] = array(
        "name" => "template_list",
        "url" => "whatsapp/template_list",
        "class" => ""
    );

    $whatsapp_submenu[] = array(
        "name" => "template_mapping",
        "url" => "whatsapp",
        "class" => ""
    );

    $whatsapp_submenu[] = array(
        "name" => "whatsapp_log_details",
        "url" => "whatsapplog",
        "class" => ""
    );


    $sidebar_menu["whatsapp"] = array(
        "name" => "whatsapp",
        "url" => "whatsapp",
        "class" => "message-circle",
        "position" => 3,
        "submenu" => $whatsapp_submenu
    );
    return $sidebar_menu;
});

//install dependencies
register_installation_hook("Whatsapp", function ($item_purchase_code) {
    include PLUGINPATH . "Whatsapp/install/do_install.php";
});



app_hooks()->add_action('app_hook_head_extension', function () {
    echo '
        <link href="' . base_url(PLUGIN_URL_PATH . 'Whatsapp/assets/css/tribute.css?v=' . get_setting('app_version')) . '"  rel="stylesheet" type="text/css" />
        <link href="' . base_url(PLUGIN_URL_PATH . 'Whatsapp/assets/css/whatsapp.css?v=' . get_setting('app_version')) . '"  rel="stylesheet" type="text/css" />
        <link href="' . base_url(PLUGIN_URL_PATH . 'Whatsapp/assets/css/prism.css?v=' . get_setting('app_version')) . '"  rel="stylesheet" type="text/css" />';
        if('\Whatsapp\Controllers\Template_mappingController' == service('router')->controllerName() && 'whatsapp_form' == service('router')->methodName()){
            echo '<link href="' . base_url(PLUGIN_URL_PATH . 'Whatsapp/assets/css/devices.min.css?v=' . get_setting('app_version')) . '"  rel="stylesheet" type="text/css" />
            <link href="' . base_url(PLUGIN_URL_PATH . 'Whatsapp/assets/css/material-design-iconic-font.min.css?v=' . get_setting('app_version')) . '"  rel="stylesheet" type="text/css" />
            <link href="' . base_url(PLUGIN_URL_PATH . 'Whatsapp/assets/css/preview.css?v=' . get_setting('app_version')) . '"  rel="stylesheet" type="text/css" />
            <link href="' . base_url(PLUGIN_URL_PATH . 'Whatsapp/assets/css/whatsapp_styles.css?v=' . get_setting('app_version')) . '"  rel="stylesheet" type="text/css" />';
        }
});

app_hooks()->add_action('app_hook_layout_main_view_extension', function () {
    $availableFields = whts_getAvailableFields();
    echo '
        <script>var merge_fields = ' . json_encode($availableFields) . '</script>
        <script src="' . base_url(PLUGIN_URL_PATH . 'Whatsapp/assets/js/tribute.min.js?v=' . get_setting('app_version')) . '"></script>
        <script src="' . base_url(PLUGIN_URL_PATH . 'Whatsapp/assets/js/underscore-min.js?v=' . get_setting('app_version')) . '"></script>
        <script src="' . base_url(PLUGIN_URL_PATH . 'Whatsapp/assets/js/whatsapp.js?v=' . get_setting('app_version')) . '"></script>
        <script src="' . base_url(PLUGIN_URL_PATH . 'Whatsapp/assets/js/prism.js?v=' . get_setting('app_version')) . '"></script>

        ';
    if('\Whatsapp\Controllers\Template_mappingController' == service('router')->controllerName() && 'template_list' == service('router')->methodName()){
        echo '
        <script src="' . base_url(PLUGIN_URL_PATH . 'Whatsapp/assets/js/preview.js?v=' . get_setting('app_version')) . '"></script>';
    }
});

// we are only getting correct / proper data while invoice and propsal is updated
app_hooks()->add_action('app_hook_data_update', function ($hookData) {
    $CI     = new Security_Controller(false);
    $CI->db = db_connect('default');

    $update_id = $hookData['id'];
    $table     = $hookData['table'];
    $data      = $hookData['data'];

    // send logic for proposals
    if ($CI->db->prefixTable('proposals') == $table && get_array_value($data, 'status') && 'sent' == $data['status']) {
        wa_proposals_added($update_id);
    }

    // send logic for proposals
    if ($CI->db->prefixTable('invoices') == $table && get_array_value($data, 'status') && 'not_paid' == $data['status']) {
        wa_invoices_added($update_id);

    }
});

app_hooks()->add_action('app_hook_data_insert', function ($hookData) {
    $CI     = new Security_Controller(false);
    $CI->db = db_connect('default');

    $insert_id = $hookData['id'];
    $table     = $hookData['table'];
    $data      = $hookData['data'];

    // send logic leads
    if ($CI->db->prefixTable('clients') == $table && !empty($data['is_lead'])) {
        wa_lead_added($insert_id);
    }

    // send logic for contact
    if ($CI->db->prefixTable('users') == $table && empty($data['user_type'])) {
        wa_client_contact_added($insert_id);
    }

    // send logic for tasks
    if ($CI->db->prefixTable('tasks') == $table) {
        wa_tasks_added($insert_id);
    }

    // send logic for projects
    if ($CI->db->prefixTable('project_members') == $table) {
        wa_projects_added($data['project_id']);
    }

    // send logic for ticket
    if ($CI->db->prefixTable('tickets') == $table) {
        wa_tickets_added($insert_id);
    }

    // send logic for payment
    if ($CI->db->prefixTable('invoice_payments') == $table) {
        wa_payments_added($insert_id);
    }
});

function wa_lead_added($id)
{
    $CI        = new Security_Controller(false);
    $options   = ['id' => $id];
    $lead_info = $CI->Clients_model->get_details($options)->getRowArray();
    if (!empty($lead_info)) {
        $options                       = ['id' => $lead_info['lead_source_id']];
        $lead_info['lead_source_name'] = $CI->Lead_source_model->get_details($options)->getRow()->title;
        $lead_info['link']             = site_url('leads/view/'.$id);
    }
    $wa_lib = new Whatsapp();
    $wa_lib->send_mapped_template('leads', $lead_info);
}

function wa_client_contact_added($id)
{
    $CI      = new Security_Controller(false);
    $request = \Config\Services::request();

    $options             = ['id' => $id];
    $client_contact_info = $CI->Users_model->get_details($options)->getRowArray();
    if (!empty($client_contact_info)) {
        $options   = ['id' => $client_contact_info['client_id']];
        $company_info = $CI->Clients_model->get_details($options)->getRowArray();
        $client_contact_info = array_merge($client_contact_info, $company_info);
        $client_contact_info['password'] = $request->getPost('login_password');
    }
    $wa_lib = new Whatsapp();
    $wa_lib->send_mapped_template('client', $client_contact_info);
}

function wa_projects_added($id)
{
    $CI            = new Security_Controller(false);
    $options       = ['id' => $id];
    $count = 0;
    $projects_info = $CI->Projects_model->get_details($options)->getRowArray();
    if (!empty($projects_info)) {
        $allMembersRes = $CI->Project_members_model->get_project_members_dropdown_list($id);
        $count = $allMembersRes->getNumRows();
        $projects_info['owner_id'] = $allMembersRes->getRow('user_id');
        $projects_info['link']             = site_url('projects/view/'.$id);
    }
    if($count == 1){
        $wa_lib = new Whatsapp();
        $wa_lib->send_mapped_template('projects', $projects_info);
    }
}

function wa_proposals_added($id)
{
    $CI             = new Security_Controller(false);
    $options        = ['id' => $id];
    $proposals_info = $CI->Proposals_model->get_details($options)->getRowArray();
    $wa_lib = new Whatsapp();
    $wa_lib->send_mapped_template('proposals', $proposals_info);
}

function wa_payments_added($id)
{
    $CI           = new Security_Controller(false);
    $options      = ['id' => $id];
    $payment_info =  $invoice_info = $payment_fields = $invoice_fields = [];
    $payment_info = $CI->Invoice_payments_model->get_details($options)->getRowArray();
    if (!empty($payment_info)) {
        $options      = ['id' => $payment_info['invoice_id']];
        $invoice_info = $CI->Invoices_model->get_details($options)->getRowArray();
        if (!empty($invoice_info)) {
            $invoice_info['link']         = site_url('invoices/download_pdf/'.$payment_info['invoice_id'].'/view/');
            $invoice_fields = array_combine(
                array_map(function ($key) {
                    return '{invoice_'.$key.'}';
                }, array_keys($invoice_info)),
                $invoice_info
            );
        }
    }
    $payment_fields = array_combine(
        array_map(function ($key) {
            return '{payment_'.$key.'}';
        }, array_keys($payment_info)),
        $payment_info
    );
    $all_merge_fields = array_merge($payment_fields, $invoice_fields);
    $wa_lib = new Whatsapp();
    $wa_lib->send_mapped_template('payment', $all_merge_fields);
}

function wa_tasks_added($id)
{
    $CI        = new Security_Controller(false);
    $options   = ['id' => $id];
    $task_info = $CI->Tasks_model->get_details($options)->getRowArray();
    if (!empty($task_info)) {
        $options                       = ['id' => $task_info['project_id']];
        $task_info['client_id'] = $CI->Projects_model->get_details($options)->getRow('client_id');
    }
    $wa_lib = new Whatsapp();
    $wa_lib->send_mapped_template('tasks', $task_info);
}

function wa_invoices_added($id)
{
    $CI           = new Security_Controller(false);
    $options      = ['id' => $id];
    $invoice_info = $CI->Invoices_model->get_details($options)->getRowArray();
    if(!empty($invoice_info['payment_received'])){
        return;
    }
    if (!empty($invoice_info)) {
        $allMembersRes = $CI->Project_members_model->get_project_members_dropdown_list(0);
        $invoice_info['project_member_id'] = $allMembersRes->getRow('user_id');
        $invoice_info['link']     = site_url('invoices/download_pdf/'.$id.'/view/');
    }
    $wa_lib = new Whatsapp();
    $wa_lib->send_mapped_template('invoice', $invoice_info);
}

function wa_tickets_added($id)
{
    $request = \Config\Services::request();
    $CI          = new Security_Controller(false);
    $options     = ['id' => $id];
    $ticket_info = $CI->Tickets_model->get_details($options)->getRowArray();
    if (!empty($ticket_info)) {
        $ticket_info['description'] = $request->getPost('description');
        $ticket_info['url']         = site_url('tickets/view/'.$id);
    }
    $wa_lib = new Whatsapp();
    $wa_lib->send_mapped_template('ticket', $ticket_info);

}

//add setting link to the plugin setting
app_hooks()->add_filter('app_filter_action_links_of_Whatsapp', function () {
    $action_links_array = array(
        anchor(get_uri("whatsapp"), "Whatsapp"),
        anchor(get_uri("whatsapp/settings"), "Whatsapp settings"),
    );

    return $action_links_array;
});

//update plugin
register_update_hook("Whatsapp", function () {
    echo "Please follow this instructions to update:";
    echo "<br />";
    echo "Your logic to update...";
});

register_uninstallation_hook("Whatsapp", function () {

});

app_hooks()->add_filter('app_filter_admin_settings_menu', function ($settings_menu) {
    $settings_menu["setup"][] = array("name" => "whatsapp_cloud_api", "url" => "whatsapp/settings");

    return $settings_menu;
});


app_hooks()->add_action('app_hook_after_cron_run', function () {
    if(!empty(get_setting('whatsapp_business_account_id')) && !empty(get_setting('whatsapp_access_token')) && !empty(get_setting('whatsapp_phone_number_id'))) {
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

        $existing_template = $builder->whereIn('template_id', $insert_data_id)->get()->getResult();

        $existing_data_id = array_column($existing_template, 'template_id');

        $new_template_id = array_diff($insert_data_id, $existing_data_id);
        $new_template    = array_filter($insert_data, function ($val) use ($new_template_id) {
            return in_array($val['template_id'], $new_template_id);
        });
    }
    //No need to update template data in db because you can't edit template in meta dashboard
    if (!empty($new_template)) {
        \Whatsapp\Libraries\Apiinit::check_url("Whatsapp");
        $db->query("SET NAMES 'utf8mb4' COLLATE 'utf8mb4_general_ci'");
        $builder->insertBatch($new_template);
    }
});