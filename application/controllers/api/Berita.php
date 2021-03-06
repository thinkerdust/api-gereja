<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Berita extends CI_Controller {

	function __construct()
	{
	    parent::__construct();
	    $this->load->model('Berita_Model');
	}

	function berita()
	{
		$auth = $this->token->auth('POST', true);
		if($auth) {
			$params = get_params();
			$id = isset($params['id']) ? $params['id'] : '';
			$start = isset($params['start']) ? $params['start'] : 0;
			$count = isset($params['count']) ? $params['count'] : 0;

			$response = $this->Berita_Model->view_berita($start,$count,$id);

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

	function store_berita()
	{
		$auth = $this->token->auth('POST', true);
		if($auth) {
			$response = [];
			$params = get_params();
			$image = isset($params['image']) ? $params['image'] : '';
			$title = isset($params['title']) ? $params['title'] : '';
			$body = isset($params['body']) ? $params['body'] : '';
			$username = $this->input->get_request_header('Username');

			if($title && $body){
				$data = array(
						'image' => $image,
						'title' => $title,
						'body'	=> $body,
						'insert_at' => date('Y-m-d H:i:s'),
						'insert_by' => $username
					);
				$save = $this->Main_Model->process_data('berita', $data);

				if($save){
					$this->Notifikasi_Model->send_notif($save, 'berita');
	                $status = 200;
	                $message = 'Data berhasil disimpan';
	                $response = $data;
	            }else{
	                $status = 404;
	                $message = 'Gagal Menyimpan data!';
	            }

			}else{
				$status = 400;
				$message = 'Data Tidak Boleh Kosong';
			}

			print_json($status,$message,$response);
		}
	}

}