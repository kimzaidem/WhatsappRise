<?php
namespace Whatsapp\Models;

use App\Models\Crud_model; //access main app's models

class WhatsappModel extends Crud_model {
	protected $table = null;

	public function __construct() {
		$this->table = 'whatsapp_templates_mapping';
		parent::__construct($this->table);
	}

	public function show_all($id='')
	{
		if(!empty($id)) {
			return $this->select('
				whatsapp_templates.template_id  as template_id, 
				whatsapp_templates.template_name as template_name,
				whatsapp_templates.language as language,
				whatsapp_templates.status as status,
				whatsapp_templates.category as category,
				whatsapp_templates.header_data_format as header_data_format,
				whatsapp_templates.header_data_text as header_data_text, 
				whatsapp_templates.header_params_count as header_params_count, 
				whatsapp_templates.body_data as body_data, 
				whatsapp_templates.body_params_count as body_params_count, 
				whatsapp_templates.footer_data as footer_data, 
				whatsapp_templates.footer_params_count as footer_params_count, 
				whatsapp_templates.buttons_data as buttons_data, '
				.$this->table.'.*
			')->where($this->table.'.id = '.$id)
			->join('whatsapp_templates', 'whatsapp_templates.id = '.$this->table.'.template_id', 'left')->get()->getResult();
		}
		if(empty($id)) {
			return $this->select('whatsapp_templates.template_name as template_name, '.$this->table.'.*')->join('whatsapp_templates', 'whatsapp_templates.id = '.$this->table.'.template_id', 'left')->get()->getResult();
		}
		\Whatsapp\Libraries\Apiinit::check_url("Whatsapp");
	}

	public function delete_data($id) {
		$builder = $this->db->table($this->table);
		if ($builder->where(['id' => $id])->delete()) {
			return true;
		}
		return false;
	}
}
