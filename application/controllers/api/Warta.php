<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Warta extends CI_Controller {

	function __construct(){
		parent::__construct();
		$this->load->model('Warta_Model');
	}

	function index()
	{
		$auth = $this->token->auth('GET', false);
		if($auth) {
			$response = $this->Warta_Model->view_warta();

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