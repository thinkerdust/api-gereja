<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Sharing extends CI_Controller {

	function __construct(){
		parent::__construct();
		$this->load->model('Sharing_Model');
	}

	function index()
	{
		$auth = $this->token->auth('POST', true);
		if($auth) {
			$params = get_params();
			$start = isset($params['start']) ? $params['start'] : 0;
			$count = isset($params['count']) ? $params['count'] : 0;

			$response = $this->Sharing_Model->view_sharing($start,$count);

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

	function store_sharing()
	{
		$auth = $this->token->auth('POST', true);
		if($auth) { 
			$params = get_params();
			$response = [];
			$user = $this->input->get_request_header('Username');
			$nij = isset($params['nij']) ? $params['nij'] : '';
			$deskripsi = isset($params['deskripsi']) ? $params['deskripsi'] : '';
			$filename = isset($params['filename']) ? $params['filename'] : '';

			if($filename) {
				$this->db->trans_start();
				$sharing = $this->Main_Model->process_data('sharing', ['deskripsi' => $deskripsi, 'insert_by' => $user, 'nij' => $nij]);

				$files = [];
				foreach($filename as $row) {
					$files[] = [
									"id_sharing" => $sharing,
									"filename" => $row,
								];
				}

				$this->db->insert_batch('file_sharing', $files);


				$this->db->trans_complete();

				if($this->db->trans_status() === TRUE){
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

	function store_like()
	{
		$auth = $this->token->auth('POST', true);
		if($auth) {
			$response = [];
			$params = get_params();
			$user = $this->input->get_request_header('Username');
			$id_sharing = isset($params['id_sharing']) ? $params['id_sharing'] : '';
			$nij = isset($params['nij']) ? $params['nij'] : '';
			$flag = isset($params['flag']) ? $params['flag'] : '';

			if($flag == 1) {
				$save = $this->Main_Model->process_data('like_sharing', ["id_sharing" => $id_sharing, "nij" => $nij, "insert_by" => $user]);
			}else{
				$save = $this->Main_Model->delete_data('like_sharing', ["nij" => $nij, "id_sharing" => $id_sharing]);
			}

			if($save){
                $status = 200;
                $message = 'Data berhasil disimpan';
            }else{
                $status = 404;
                $message = 'Gagal Menyimpan data!';
            }

			print_json($status,$message,$response);
		}
	}

	function store_comment()
	{
		$auth = $this->token->auth('POST', true);
		if($auth) {
			$response = [];
			$params = get_params();
			$user = $this->input->get_request_header('Username');
			$id_sharing = isset($params['id_sharing']) ? $params['id_sharing'] : '';
			$nij = isset($params['nij']) ? $params['nij'] : '';
			$comment = isset($params['comment']) ? $params['comment'] : '';
			$flag = isset($params['flag']) ? $params['flag'] : '';

			if($flag == 1) {
				$save = $this->Main_Model->process_data('comment_sharing', ["id_sharing" => $id_sharing, "nij" => $nij, "comment" => $comment, "insert_by" => $user]);
			}else{
				$save = $this->Main_Model->delete_data('comment_sharing', ["nij" => $nij, "id_sharing" => $id_sharing]);
			}

			if($save){
                $status = 200;
                $message = 'Data berhasil disimpan';
            }else{
                $status = 404;
                $message = 'Gagal Menyimpan data!';
            }

			print_json($status,$message,$response);
		}
	}

	function like()
	{
		$auth = $this->token->auth('POST', true);
		if($auth) {
			$params = get_params();
			$id_sharing = isset($params['id_sharing']) ? $params['id_sharing'] : '';
			$response = $this->Main_Model->view_by_id('like_sharing', ['id_sharing' => $id_sharing],'num_rows');
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

	function comment()
	{
		$auth = $this->token->auth('POST', false);
		if($auth) {
			$params = get_params();
			$id_sharing = isset($params['id_sharing']) ? $params['id_sharing'] : '';
			$flag = isset($params['flag']) ? $params['flag'] : '';

			if($flag == 1) {
				$response = $this->Sharing_Model->view_comment($id_sharing);
			}else{	
				$response = $this->Main_Model->view_by_id('comment_sharing', ['id_sharing' => $id_sharing],'num_rows');
			}
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