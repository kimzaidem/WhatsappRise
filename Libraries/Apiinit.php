<?php
namespace Whatsapp\Libraries;

defined('PLUGINPATH') or exit('No direct script access allowed');

require_once __DIR__ . '/../ThirdParty/node.php';
require_once __DIR__ . '/Envapi.php';
require_once __DIR__.'/../vendor/autoload.php';
use \Whatsapp\Libraries\Envapi;

use \Firebase\JWT\JWT;
use \Firebase\JWT\Key;

class Apiinit
{
    public static function check_url($module_name)
    {
        $verified = false;
        $a_verified = false;
        $Settings_model = model("App\Models\Settings_model");
        $item_config = new \Whatsapp\Config\Item();

        if ($Settings_model->get_setting($module_name.'_verified')) {
            $Settings_model = model("App\Models\Settings_model");
            $plugins = $Settings_model->get_setting("plugins");
            $plugins = @unserialize($plugins);
            $plugins[$module_name] = "deactivated";
            save_plugins_config($plugins);

            $dbprefix = get_db_prefix();
			$db = db_connect('default');

			$sql_query = "DELETE FROM `" . $dbprefix . "settings` WHERE `" . $dbprefix . "settings`.`setting_name`='".$module_name . "_verified"."';";
			$db->query($sql_query);

            $Settings_model->save_setting("plugins", serialize($plugins));
            redirect(site_url('Plugins'));
        }

        if (!$Settings_model->get_setting($module_name.'_verification_id')) {
            $verified = false;
        }
        $verification_id =  $Settings_model->get_setting($module_name.'_verification_id');
        if (!empty($verification_id)) {
            $verification_id = base64_decode($verification_id);
        }
        $id_data         = explode('|', $verification_id);
        if (4 != count($id_data)) {
            $verified = false;
        }

        $token = $Settings_model->get_setting($module_name.'_product_token');

        if (4 == count($id_data)) {
            $verified = !empty($token);
            try {
                $data = JWT::decode($token, new Key($id_data[3], 'HS512'));
                if (!empty($data)) {
                    if ($item_config->{$module_name . '_product_item_id'} == $data->item_id && $data->item_id == $id_data[0] && $data->buyer == $id_data[2] && $data->purchase_code == $id_data[3]) {
                        $verified = true;
                    }
                }
            } catch (\Exception $e) {
                $verified = false;
            }

            $last_verification = (int) $Settings_model->get_setting($module_name . '_last_verification');
            $seconds           = $data->check_interval ?? 0;
            if (empty($seconds)) {
                $verified = false;
            }
            if ('' == $last_verification || (time() > ($last_verification + $seconds))) {
                $verified = false;
                try {
                    $headers  = ['Accept' => 'application/json', 'Authorization' => $token];
                    $request  = \WpOrg\Requests\Requests::post(VAL_PROD_POINT, $headers, json_encode(['verification_id' => $verification_id, 'item_id' => $item_config->{$module_name . '_product_item_id'}, "activated_domain" => base_url()]));
                    $a_verified = true;
                    if ((500 <= $request->status_code) && ($request->status_code <= 599) || 404 == $request->status_code) {
                        $verified = false;
						$Settings_model->save_setting($module_name.'_heartbeat', base64_encode(json_encode(["status" => $request->status_code, "id" => $token, "end_point" => VAL_PROD_POINT])));
                    } else {
                        $result   = json_decode($request->body);
                        if (!empty($result->valid)) {
							$dbprefix = get_db_prefix();
							$db = db_connect('default');

							$sql_query = "DELETE FROM `" . $dbprefix . "settings` WHERE `" . $dbprefix . "settings`.`setting_name`='".$module_name.'_heartbeat'."';";
    						$db->query($sql_query);
                            $verified = true;
                        }
                    }
                } catch (Exception $e) {
                    $verified = true;
                }
				$Settings_model->save_setting($module_name.'_last_verification', time());
            }
        }

        if (empty($token) || !$verified) {
            $last_verification = (int) $Settings_model->get_setting($module_name . '_last_verification');
            $heart = json_decode(base64_decode($Settings_model->get_setting($module_name . '_heartbeat')));
            if (!empty($heart)) {
                if ((500 <= $heart->status) && ($heart->status <= 599) || 404 == $heart->status) {
                    if (($last_verification + (168 * (3000 + 600))) > time()) {
                        $verified = true;
                    }
                }
            } else {
                $verified = false;
            }
        }

        if (!$verified) {

			$Settings_model = model("App\Models\Settings_model");
            $plugins = $Settings_model->get_setting("plugins");
            $plugins = @unserialize($plugins);
            $plugins[$module_name] = "deactivated";
            save_plugins_config($plugins);

            $Settings_model->save_setting("plugins", serialize($plugins));

			$dbprefix = get_db_prefix();
			$db = db_connect('default');

			$sql_query = "DELETE FROM `" . $dbprefix . "settings` WHERE `" . $dbprefix . "settings`.`setting_name`='".$module_name . "_verification_id"."';";
			$db->query($sql_query);

			$sql_query = "DELETE FROM `" . $dbprefix . "settings` WHERE `" . $dbprefix . "settings`.`setting_name`='".$module_name . "_last_verification"."';";
			$db->query($sql_query);

			$sql_query = "DELETE FROM `" . $dbprefix . "settings` WHERE `" . $dbprefix . "settings`.`setting_name`='".$module_name . "_heartbeat"."';";
			$db->query($sql_query);
        }

        return $verified;
    }

    public static function getUserIP()
    {
        $ipaddress = '';
        if (isset($_SERVER['HTTP_CLIENT_IP'])) {
            $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } elseif (isset($_SERVER['HTTP_X_FORWARDED'])) {
            $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
        } elseif (isset($_SERVER['HTTP_FORWARDED_FOR'])) {
            $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
        } elseif (isset($_SERVER['HTTP_FORWARDED'])) {
            $ipaddress = $_SERVER['HTTP_FORWARDED'];
        } elseif (isset($_SERVER['REMOTE_ADDR'])) {
            $ipaddress = $_SERVER['REMOTE_ADDR'];
        } else {
            $ipaddress = 'UNKNOWN';
        }

        return $ipaddress;
    }

    public static function file_edit_contents($file_name, $line, $new_value)
    {
        $index = $line - 1;
        if (!file_exists($file_name)) {
            return false;
        }
        $file = explode("\n", rtrim(file_get_contents($file_name)));
        if (empty($file[$index])) {
            return false;
        }
        $file[$index] = $new_value;
        $file = implode("\n", $file);
        file_put_contents($file_name, $file);
        return true;
    }

    public static function pre_validate($module_name, $code = "")
    {
        if (empty($code)) {
            return ['status' => false, 'message' => 'Purchase key is required'];
        }

        $Settings_model = model("App\Models\Settings_model");
        $plugins = $Settings_model->get_setting("plugins");
        $all_activated = @unserialize($plugins);
    
        if(!empty($all_activated)){
            foreach ($all_activated as $active_module => $value) {
                if ($Settings_model->get_setting($module_name . '_verification_id') && !empty($Settings_model->get_setting($active_module . '_verification_id'))) {
                    $verification_id =  $Settings_model->get_setting($active_module . '_verification_id');
                    if (!empty($verification_id)) {
                        if(base64_encode(base64_decode($verification_id, true)) === $verification_id){
                            $verification_id = base64_decode($verification_id);
                        }
                        $id_data         = explode('|', $verification_id);

                        if ($id_data[3] == $code) {
                            return ['status' => false, 'message' => 'This Purchase code is Already being used in other module'];
                        }
                    }
                }
            }
        }

        $envato_res = Envapi::getPurchaseData($code);

        if (empty($envato_res)) {
            return ['status' => false, 'message' => 'Something went wrong'];
        }
        if (!empty($envato_res->error)) {
            return ['status' => false, 'message' => $envato_res->description];
        }
        if (empty($envato_res->sold_at)) {
            return ['status' => false, 'message' => 'Sold time for this code is not found'];
        }
        if ((false === $envato_res) || !is_object($envato_res) || isset($envato_res->error) || !isset($envato_res->sold_at)) {
            return ['status' => false, 'message' => 'Something went wrong'];
        }
        $item_config = new \Whatsapp\Config\Item();
        if ($item_config->Whatsapp_product_item_id != $envato_res->item->id) {
            return ['status' => false, 'message' => 'Purchase key is not valid'];
        }

        $request = \Config\Services::request();
        $agent_data = $request->getUserAgent();

        $data['user_agent']       = $agent_data->getBrowser().' '.$agent_data->getVersion();
        $data['activated_domain'] = base_url();
        $data['requested_at']     = date('Y-m-d H:i:s');
        $data['ip']               = Apiinit::getUserIP();
        $data['os']               = $agent_data->getPlatform();
        $data['purchase_code']    = $code;
        $data['envato_res']       = $envato_res;
        $data                     = json_encode($data);

        try {
            $headers = ['Accept' => 'application/json'];
            $request = \WpOrg\Requests\Requests::post(REG_PROD_POINT, $headers, $data);
            if ((500 <= $request->status_code) && ($request->status_code <= 599) || 404 == $request->status_code) {
                $Settings_model->save_setting($module_name . '_verification_id', '');
                $Settings_model->save_setting($module_name . '_last_verification', time());
                $Settings_model->save_setting($module_name . '_heartbeat', base64_encode(json_encode(["status" => $request->status_code, "id" => $code, "end_point" => REG_PROD_POINT])));

                return ['status' => true];
            }

            $response = json_decode($request->body);
            if (200 != $response->status) {
                return ['status' => false, 'message' => $response->message];
            }

            if (200 == $response->status) {
                $return = $response->data ?? [];
                if (!empty($return)) {
                    $Settings_model->save_setting($module_name . '_verification_id', base64_encode($return->verification_id));
                    $Settings_model->save_setting($module_name . '_last_verification', time());
                    Apiinit::file_edit_contents(__DIR__ . '/../config/conf.php', 6, '$config["' . $module_name . '_product_token"] = "' . $return->token . '";');
                    $Settings_model->save_setting($module_name . '_product_token', $return->token);

                    $dbprefix = get_db_prefix();
			        $db = db_connect('default');

                    $sql_query = "DELETE FROM `" . $dbprefix . "settings` WHERE `" . $dbprefix . "settings`.`setting_name`='".$module_name . "_heartbeat"."';";
			        $db->query($sql_query);

                    return ['status' => true];
                }
            }
        } catch (Exception $e) {
            $Settings_model->save_setting($module_name . '_verification_id', '');
            $Settings_model->save_setting($module_name . '_last_verification', time());
            $Settings_model->save_setting($module_name . '_heartbeat', base64_encode(json_encode(["status" => $request->status_code, "id" => $code, "end_point" => REG_PROD_POINT])));

            return ['status' => true];
        }

        return ['status' => false, 'message' => 'Something went wrong'];
    }
}
