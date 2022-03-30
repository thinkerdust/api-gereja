<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Media extends CI_Controller {

	function __construct()
	{
	    parent::__construct();
	    $this->load->model('Media_Model');
	}

	function media()
	{
		$auth = $this->token->auth('GET', true);
		if($auth) {
			$params = get_params();
			$id = isset($params['id']) ? $params['id'] : '';
			$start = isset($params['start']) ? $params['start'] : 0;
			$count = isset($params['count']) ? $params['count'] : 0;

			$response = $this->Media_Model->view_media($start,$count,$id);

			if(!empty($response)) {
				$status = 200;
				$message = 'Data Ditemukan';
			}else{
				$status = 404;
				$message = 'Data Tidak Ditemukan';
			}
		    
			print_json($status,$message,$response);
		}
	}
}