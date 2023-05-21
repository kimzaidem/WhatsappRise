<?php

namespace Config;

$routes = Services::routes();

$whatsapp_namespace = ['namespace' => 'Whatsapp\Controllers'];

//for loading datatable
$routes->post('whatsapp/table', 'Template_mappingController::table', $whatsapp_namespace);

//whatsapp
$routes->match(['get','post'], 'whatsapp', 'Template_mappingController::index',$whatsapp_namespace);
$routes->match(['get','post'], 'whatsapp/form', 'Template_mappingController::whatsapp_form',$whatsapp_namespace);
$routes->match(['get','post'], 'whatsapp/form/(:num)', 'Template_mappingController::whatsapp_form/$1',$whatsapp_namespace);
$routes->match(['get','post'], 'whatsapp/save', 'Template_mappingController::save',$whatsapp_namespace);
$routes->match(['get','post'], 'whatsapp/save/(:num)', 'Template_mappingController::save/$1',$whatsapp_namespace);
$routes->match(['get','post'], 'whatsapp/delete/(:num)', 'Template_mappingController::delete_whatsapp/$1',$whatsapp_namespace);
$routes->match(['get','post'], 'whatsapp/change_status_active/(:num)', 'Template_mappingController::change_status_active/$1',$whatsapp_namespace);
$routes->match(['get','post'], 'whatsapp/change_debug_mode/(:num)', 'Template_mappingController::change_debug_mode/$1',$whatsapp_namespace);
$routes->match(['get','post'], 'whatsapp/get_template_map', 'Template_mappingController::get_template_map',$whatsapp_namespace);
$routes->match(['get','post'], 'whatsapp/form/get_template_map', 'Template_mappingController::get_template_map',$whatsapp_namespace);

$routes->get('whatsapp/get_business_information', 'WhatsappController::get_business_information',$whatsapp_namespace);

//whatsapp_settings
$routes->match(['get','post'], 'whatsapp/settings', 'Template_mappingController::settings',$whatsapp_namespace);
$routes->post('whatsapp/save_whatsapp_settings', 'Template_mappingController::save_whatsapp_settings',$whatsapp_namespace);

//whatsapp logs
$routes->match(['get','post'], 'whatsapplog', 'WhatsapplogController::index',$whatsapp_namespace);
$routes->post('whatsapplog/table', 'WhatsapplogController::table', $whatsapp_namespace);
$routes->match(['get','post'], 'whatsapplog/clear_log', 'WhatsapplogController::clear_log',$whatsapp_namespace);
$routes->match(['get','post'], 'whatsapplog/get_whatsapp_log_details/(:num)', 'WhatsapplogController::get_whatsapp_log_details/$1',$whatsapp_namespace);

//whatsapp template list
$routes->get('whatsapp/template_list', 'WhatsappController::template_list', $whatsapp_namespace);
$routes->post('whatsapp/template_list_table', 'WhatsappController::template_list_table', $whatsapp_namespace);
