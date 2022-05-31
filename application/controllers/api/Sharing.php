<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Sharing extends CI_Controller {

	function store_sharing()
	{
		$auth = $this->token->auth('POST', false);
		if($auth) { 
			$params = get_params();
			$response = [];
			$user = $this->input->get_request_header('Username');
			$deskripsi = isset($params['deskripsi']) ? $params['deskripsi'] : '';
			$filename = isset($params['filename']) ? $params['filename'] : '';

			if($filename) {
				$this->db->trans_start();
				$sharing = $this->Main_Model->process_data('sharing', ['deskripsi' => $deskripsi, 'insert_by' => $user]);

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

}