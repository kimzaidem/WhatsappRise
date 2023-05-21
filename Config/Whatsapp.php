<?php

namespace Whatsapp\Config;

use CodeIgniter\Config\BaseConfig;
use Whatsapp\Models\Whatsapp_settings_model;

class Whatsapp extends BaseConfig {

    public $app_settings_array = array(
        "whatsapp_file_path" => PLUGIN_URL_PATH . "Whatsapp/files/whatsapp_files/"
    );

    public function __construct() {
        $whatsapp_settings_model = new Whatsapp_settings_model();

        $settings = $whatsapp_settings_model->get_all_settings()->getResult();
        foreach ($settings as $setting) {
            $this->app_settings_array[$setting->setting_name] = $setting->setting_value;
        }
    }

}
