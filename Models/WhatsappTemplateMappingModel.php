<?php
namespace Whatsapp\Models;

use App\Models\Crud_model; //access main app's models

class WhatsappTemplateMappingModel extends Crud_model {
	protected $table = null;

	public function __construct() {
		$this->table = 'whatsapp_templates_mapping';
		parent::__construct($this->table);
	}

    public function save_template_map_info($map_info)
    {
    	return $this->ci_save($map_info);
    }

    public function update_template_map_info($map_info, $where)
    {
    	$db      = \Config\Database::connect();
    	$builder = $db->table($this->table);
    	return $builder->update($map_info, array('id' => $where['id']));
    }

    public function get_mapping_data($where)
    {
        \Whatsapp\Libraries\Apiinit::check_url("Whatsapp");
    	$db      = \Config\Database::connect();
    	$builder = $db->table($this->table);
    	$a = $builder->select('whatsapp_templates_mapping.*, wt.template_name, wt.header_data_format, wt.language, wt.header_data_text, wt.body_data, wt.footer_data, wt.buttons_data, wt.header_params_count, wt.body_params_count, wt.footer_params_count, whatsapp_templates_mapping.send_to')->join('whatsapp_templates wt', 'whatsapp_templates_mapping.template_id = wt.id')->getWhere($where)->getResult();
		return $a;
    }
}
