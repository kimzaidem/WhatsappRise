<?php

require_once __DIR__ . '/../Libraries/Apiinit.php';

use Whatsapp\Libraries\Apiinit;

ini_set('max_execution_time', 300); //300 seconds

$product = "Whatsapp";

//check requirements

if (!(isset($item_purchase_code) && $item_purchase_code)) {
    echo json_encode(array("success" => false, "message" => "Please enter a valid purchase code."));
    exit();
}


$return = Apiinit::pre_validate($product, $item_purchase_code);
if (!$return['status']) {
    echo json_encode(array("success" => false, "message" => $return['message']));
    exit();
}

$this_is_required = true;
if (!$this_is_required) {
    echo json_encode(array("success" => false, "message" => "This is required!"));
    exit();
}

//run installation sql
$db = db_connect('default');
$dbprefix = get_db_prefix();

if (!$db->tableExists($dbprefix . 'whatsapp_templates')) {
    $sql_query = "CREATE TABLE IF NOT EXISTS `" . $dbprefix . "whatsapp_templates` (
                   `id` INT NOT NULL AUTO_INCREMENT ,
                    `template_id` BIGINT UNSIGNED NOT NULL ,
                    `template_name` VARCHAR(255) NOT NULL ,
                    `language` VARCHAR(50) NOT NULL ,
                    `status` VARCHAR(50) NOT NULL ,
                    `category` VARCHAR(100) NOT NULL ,
                    `header_data_format` VARCHAR(10) NOT NULL ,
                    `header_data_text` TEXT ,
                    `header_params_count` INT NOT NULL ,
                    `body_data` TEXT NOT NULL ,
                    `body_params_count` INT NOT NULL ,
                    `footer_data` TEXT,
                    `footer_params_count` INT NOT NULL ,
                    `buttons_data` JSON NOT NULL ,
                    PRIMARY KEY (`id`),
                    UNIQUE KEY `template_id` (`template_id`)
                    ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;";
    $db->query($sql_query);
}

if (!$db->tableExists($dbprefix . 'whatsapp_templates_mapping')) {
    $sql_query = "CREATE TABLE IF NOT EXISTS `" . $dbprefix . "whatsapp_templates_mapping` (
                     `id` INT NOT NULL AUTO_INCREMENT ,
                    `template_id` INT(11) NOT NULL,
                    `category` VARCHAR(100) NOT NULL ,
                    `send_to` VARCHAR(50) NOT NULL ,
                    `header_params` JSON NOT NULL ,
                    `body_params` JSON NOT NULL ,
                    `footer_params` JSON NOT NULL ,
                    `active` TINYINT NOT NULL DEFAULT '1',
                    `debug_mode` TINYINT NOT NULL DEFAULT '0',
                    PRIMARY KEY (`id`)
                    ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;";
    $db->query($sql_query);
}

if (!$db->tableExists($dbprefix . 'whatsapp_api_debug_log')) {
    $sql_query = "CREATE TABLE IF NOT EXISTS  `" . $dbprefix . "whatsapp_api_debug_log` (
                     `id` int(11) NOT NULL AUTO_INCREMENT,
                    `api_endpoint` varchar(255) NULL DEFAULT NULL,
                    `phone_number_id` varchar(255) NULL DEFAULT NULL,
                    `access_token` TEXT NULL DEFAULT NULL,
                    `business_account_id` varchar(255) NULL DEFAULT NULL,
                    `response_code` varchar(4) NOT NULL,
                    `response_data` text NOT NULL,
                    `send_json` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`send_json`)),
                    `message_category` varchar(50) NOT NULL,
                    `category_params` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`category_params`)),
                    `merge_field_data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`merge_field_data`)),
                    `recorded_at` datetime NOT NULL DEFAULT current_timestamp(),
                    PRIMARY KEY (`id`)
                    ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;";
    $db->query($sql_query);
}

$settings = new \App\Models\Settings_model();
$settings->save_setting("whatsapp_enabled", 1);
