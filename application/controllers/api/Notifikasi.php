<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Notifikasi extends CI_Controller {

	function __construct(){
		parent::__construct();
		$this->load->model('Berita_Model');
	}

	function notifikasi()
	{
		$auth = $this->token->auth('POST', true);
		if($auth) { 
			$params = get_params();
			$response = [];
			$token = isset($params['token']) ? $params['token'] : '';
			$flag = isset($params['flag']) ? $params['flag'] : '';
			$id = isset($params['id']) ? $params['id'] : '';

			if($token && $flag && $id) {
				$flag = trim(strtolower($flag));
				$table = $this->Main_Model->view_by_id($flag, ['id' => $id]);

				if(!empty($table)) {
					$title = $table->title;
					$body = $table->body;
					$image = base_url().'assets/upload/images/'.$table->image;
					$data = array(
								'id' => $table->id,
								'title' => $title,
								'body' => $body,
								'images' => $table->image,
								'path_file' => $image,
							);

					$this->customcurl->fcm($flag,$token,$title,$body,$image,$data);

					$response = $data;
					$status = 200;
					$message = 'Data Ditemukan';
				}else{
					$status = 404;
					$message = 'Data Tidak Ditemukan';
				}
			}else{
				$status = 400;
				$message = 'Data Tidak Boleh Kosong';
			}

			print_json($status,$message,$response);
		}
	}

	function store_fcm()
	{
		$auth = $this->token->auth('POST', true);
		if($auth) { 
			$params = get_params();
			$response = [];
			$user_id = $this->input->get_request_header('User-Id');
			$fcm_id = isset($params['fcm_id']) ? $params['fcm_id'] : '';

			if($fcm_id){
				$user = $this->Main_Model->process_data('user', ['fcm_id' => $fcm_id], ['id' => $user_id]);

				if(!empty($user)) {
					$response = $user;
					$status = 200;
					$message = 'Data Berhasil Disimpan';
				}else{
					$status = 404;
					$message = 'Data Gagal Disimpan';
				}

			}else{
				$status = 400;
				$message = 'Data Tidak Boleh Kosong';
			}

			print_json($status,$message,$response);
		}
	}
}