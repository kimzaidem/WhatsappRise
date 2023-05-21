<?php
namespace Whatsapp\Controllers;

use App\Controllers\Security_Controller;

class WhatsapplogController extends Security_Controller {

	function __construct() {
	    parent::__construct();
	    $this->whatsapplog_model = model('Whatsapp\Models\WhatsapplogModel');
	}

	public function index()
	{
		return $this->template->rander('Whatsapp\Views\manage_whatsapp_log');
	}

	public function table() {
	    if ($this->request->isAJAX()) {
	        $data   = $this->whatsapplog_model->show_all();
	        $result = [];
	        foreach ($data as $value) {
	            $result[] = $this->_make_row($value);
	        }
	        echo json_encode(["data" => $result]);
	    }
	}

	public function _make_row($data) {
	    $whatsapp_for            = $data->message_category;
	    
	    $color = 'btn-default';
	    if ($data->response_code >= 200 && $data->response_code <= 299) {
	        $color = 'btn-success';
	    }
	    if ($data->response_code >= 300 && $data->response_code <= 399) {
	        $color = 'btn-info';
	    }
	    if ($data->response_code >= 400 && $data->response_code <= 499) {
	        $color = 'btn-warning';
	    }
	    if ($data->response_code >= 500 && $data->response_code <= 599) {
	        $color = 'btn-danger';
	    }
	    $response_code = '<a class="badge '.$color.'">'.$data->response_code.'</a>';

	    $request_url            = $data->recorded_at;
	    $actions                = "<a href='".get_uri('whatsapplog/get_whatsapp_log_details/').$data->id."' class='btn btn-default'><i data-feather='eye' class='icon-16'></i></a>";

	    return [
	        $whatsapp_for,
	        $response_code,
	        $request_url,
	        $actions
	    ];
	}

	public function clear_log()
	{
		$this->whatsapplog_model->clear_log();
		return redirect()->to('whatsapplog'); 
	}

	public function get_whatsapp_log_details($id)
	{
		$data['log_data'] = $this->whatsapplog_model->get_one($id);
		return $this->template->rander('Whatsapp\Views\view_whatsapplog_details',$data);
	}

}

/* End of file WhatsapplogController.php */
/* Location: Whatsapp/Controllers/WhatsapplogController.php */