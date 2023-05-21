<?php

/**
 * get the defined config value by a key
 * @param string $key
 * @return config value
 */
if (!function_exists('get_whatsapp_setting')) {

    function get_whatsapp_setting($key = "") {
        $config = new Whatsapp\Config\Whatsapp();

        $setting_value = get_array_value($config->app_settings_array, $key);
        if ($setting_value !== NULL) {
            return $setting_value;
        } else {
            return "";
        }
    }

}

/**
 * link the css files
 *
 * @param array $array
 * @return print css links
 */
if (!function_exists('whatsapp_load_css')) {

    function whatsapp_load_css(array $array) {
        $version = get_setting("app_version");

        foreach ($array as $uri) {
            echo "<link rel='stylesheet' type='text/css' href='" . base_url(PLUGIN_URL_PATH . "Whatsapp/$uri") . "?v=$version' />";
        }
    }

}

/**
 * link the js files
 *
 * @param array $array
 * @return print css links
 */
if (!function_exists('whatsapp_load_js')) {

    function whatsapp_load_js(array $array) {
        $version = get_setting("app_version");

        foreach ($array as $uri) {
            echo "<script src='" . base_url(PLUGIN_URL_PATH . "Whatsapp/$uri") . "?v=$version'></script>";
        }
    }

}

if (!function_exists('whatsapp_get_source_url')) {

    function whatsapp_get_source_url($whatsapp_file = "") {
        if (!$whatsapp_file) {
            return "";
        }

        try {
            $file = unserialize($whatsapp_file);
            if (is_array($file)) {
                return get_source_url_of_file($file, get_whatsapp_setting("whatsapp_file_path"), "thumbnail", false, false, true);
            }
        } catch (\Exception $ex) {

        }
    }

}

function whts_getAvailableFields()
{
    return [
        [
            'leads' => [
                ['name' => 'Lead Name', 'key' => '{lead_company_name}'],
                ['name' => 'Lead Website', 'key' => '{lead_website}'],
                ['name' => 'Lead Phone Number', 'key' => '{lead_phone}'],
                ['name' => 'Lead Country', 'key' => '{lead_country}'],
                ['name' => 'Lead Zip', 'key' => '{lead_zip}'],
                ['name' => 'Lead City', 'key' => '{lead_city}'],
                ['name' => 'Lead State', 'key' => '{lead_state}'],
                ['name' => 'Lead Address', 'key' => '{lead_address}'],
                ['name' => 'Lead Owner', 'key' => '{lead_owner_name}'],
                ['name' => 'Lead Status', 'key' => '{lead_lead_status_title}'],
                ['name' => 'Lead Souce', 'key' => '{lead_lead_source_name}'],
                ['name' => 'Lead Link', 'key' => '{lead_link}'],
            ],
        ],
        [
            'client' => [
                ['name' => 'Contact Firstname', 'key' => '{contact_first_name}'],
                ['name' => 'Contact Lastname', 'key' => '{contact_last_name}'],
                ['name' => 'Contact Phone Number', 'key' => '{contact_phone}'],
                ['name' => 'Contact Title', 'key' => '{contact_job_title}'],
                ['name' => 'Contact Email', 'key' => '{contact_email}'],
                ['name' => 'Contact Skype', 'key' => '{contact_skype}'],
                ['name' => 'Client Company', 'key' => '{contact_client_company}'],
                ['name' => 'Client Phone Number', 'key' => '{contact_client_phonenumber}'],
                ['name' => 'Client Country', 'key' => '{contact_client_country}'],
                ['name' => 'Client City', 'key' => '{contact_client_city}'],
                ['name' => 'Client Zip', 'key' => '{contact_client_zip}'],
                ['name' => 'Client State', 'key' => '{contact_client_state}'],
                ['name' => 'Client Address', 'key' => '{contact_client_address}'],
                ['name' => 'Client Vat Number', 'key' => '{contact_client_vat_number}'],
                ['name' => 'Client ID', 'key' => '{contact_client_id}'],
                ['name' => 'Password', 'key' => '{contact_password}'],
            ],
        ],
        [
            'invoice' => [
                ['name' => 'Invoice Link', 'key' => '{invoice_link}'],
                ['name' => 'Invoice Number', 'key' => '{invoice_id}'],
                ['name' => 'Invoice Duedate', 'key' => '{invoice_due_date}'],
                ['name' => 'Invoice Date', 'key' => '{invoice_bill_date}'],
                ['name' => 'Invoice Status', 'key' => '{invoice_status}'],
                ['name' => 'Invoice Total', 'key' => '{invoice_invoice_value}'],
                ['name' => 'Payment Recorded Total', 'key' => '{invoice_payment_received}'],
                ['name' => 'Client name', 'key' => '{invoice_company_name}'],
            ],
        ],
        [
            'payment' => [
                ['name' => 'Invoice Link', 'key' => '{invoice_link}'],
                ['name' => 'Invoice Number', 'key' => '{invoice_id}'],
                ['name' => 'Invoice Duedate', 'key' => '{invoice_due_date}'],
                ['name' => 'Invoice Date', 'key' => '{invoice_bill_date}'],
                ['name' => 'Invoice Status', 'key' => '{invoice_status}'],
                ['name' => 'Invoice Total', 'key' => '{invoice_invoice_value}'],
                ['name' => 'Payment Recorded Total', 'key' => '{invoice_payment_received}'],
                ['name' => 'Client name', 'key' => '{invoice_company_name}'],
                ['name' => 'Payment Amount', 'key' => '{payment_amount}'],
                ['name' => 'Payment Date', 'key' => '{payment_payment_date}'],
                ['name' => 'Payment Note', 'key' => '{payment_note}'],
                ['name' => 'Payment Method', 'key' => '{payment_payment_method_title}'],
            ],
        ],
        [
            'ticket' => [
                ['name' => 'Ticket ID', 'key' => '{ticket_id}'],
                ['name' => 'Ticket Client', 'key' => '{ticket_company_name}'],
                ['name' => 'Ticket Type', 'key' => '{ticket_ticket_type}'],
                ['name' => 'Ticket Label', 'key' => '{ticket_labels_list}'],
                ['name' => 'Ticket Assigned To', 'key' => '{ticket_assigned_to_user}'],
                ['name' => 'Ticket Client', 'key' => '{ticket_company_name}'],
                ['name' => 'Ticket URL', 'key' => '{ticket_url}'],
                ['name' => 'Date Opened', 'key' => '{ticket_created_at}'],
                ['name' => 'Ticket Title', 'key' => '{ticket_title}'],
                ['name' => 'Ticket Message', 'key' => '{ticket_description}'],
                ['name' => 'Ticket Status', 'key' => '{ticket_status}'],
                ['name' => 'Ticket Task', 'key' => '{ticket_task_title}'],
                ['name' => 'Project name', 'key' => '{ticket_project_title}'],
            ],
        ],
        [
            'tasks' => [
                ['name' => 'Task Name', 'key' => '{task_title}'],
                ['name' => 'Task Description', 'key' => '{task_description}'],
                ['name' => 'Task Status', 'key' => '{task_status_title}'],
                ['name' => 'Task Priority', 'key' => '{task_priority_title}'],
                ['name' => 'Task Start Date', 'key' => '{task_start_date}'],
                ['name' => 'Task Due Date', 'key' => '{task_deadline}'],
            ],
        ],
        [
            'projects' => [
                ['name' => 'Project Name', 'key' => '{project_title}'],
                ['name' => 'Project Description', 'key' => '{project_description}'],
                ['name' => 'Project Start Date', 'key' => '{project_start_date}'],
                ['name' => 'Project Deadline', 'key' => '{project_deadline}'],
                ['name' => 'Project Link', 'key' => '{project_link}'],
                ['name' => 'Project Client', 'key' => '{project_company_name}'],
                ['name' => 'Project Price', 'key' => '{project_price}'],
            ],
        ],
        [
            'other' => [
                ['name' => 'Logo URL', 'key' => '{logo_url}'],
                ['name' => 'Main Domain', 'key' => '{main_domain}'],
            ],
        ],
        [
            'proposals' => [
                ['name' => 'Proposal ID', 'key' => '{proposal_id}'],
                ['name' => 'Open Till', 'key' => '{proposal_valid_until}'],
                ['name' => 'Proposal Date', 'key' => '{proposal_proposal_date}'],
                ['name' => 'Note', 'key' => '{proposal_note}'],
                ['name' => 'Proposal Company', 'key' => '{proposal_company_name}'],
                ['name' => 'Proposal Tax 1', 'key' => '{proposal_tax_percentage}'],
                ['name' => 'Proposal Tax 2', 'key' => '{proposal_tax_percentage2}'],
                ['name' => 'Proposal Status', 'key' => '{proposal_status}'],
            ],
        ],
        [
            'staff' => [
                ['name' => 'Staff Firstname', 'key' => '{staff_first_name}'],
                ['name' => 'Staff Lastname', 'key' => '{staff_last_name}'],
                ['name' => 'Staff Email', 'key' => '{staff_email}'],
                ['name' => 'Staff Date Created', 'key' => '{staff_created_at}'],
                ['name' => 'Staff Job Title', 'key' => '{staff_job_title}'],
            ],
        ]
    ];
}