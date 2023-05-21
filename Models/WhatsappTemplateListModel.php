<?php
namespace Whatsapp\Models;

use App\Models\Crud_model; //access main app's models

class WhatsappTemplateListModel extends Crud_model {
	protected $table = null;

	public function __construct() {
		$this->table = 'whatsapp_templates';
		parent::__construct($this->table);
	}

	public function show_all()
	{
		return $this->get_all(true)->getResult();
	}

	public function get_template_data($id)
    {
    	$builder = $this->db->table($this->table);
        return $builder->getWhere(['id' => $id])->getRow();
    }

    public function save_template_map_info($map_info)
    {
    	$builder = $this->db->table('whatsapp_templates_mapping');
		return $builder->insert($map_info);  
    }
}
