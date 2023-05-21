<?php
namespace Whatsapp\Models;

use App\Models\Crud_model; //access main app's models

class WhatsapplogModel extends Crud_model {
	protected $table = null;

	public function __construct() {
		$this->table = 'whatsapp_api_debug_log';
		parent::__construct($this->table);
	}

	public function show_all()
	{
		return $this->get_all(true)->getResult();
	}

	public function clear_log()
	{
		$builder = $this->db->table($this->table);
		if ($builder->truncate()) {
			return true;
		}
		return false;
	}

}
