<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Setting extends CI_Controller {

	function profil()
	{
		$auth = $this->token->auth('GET', true);
		if($auth) {
			$user_id = $this->input->get_request_header('User-Id');
			$response[] = $this->Main_Model->view_by_id('profil', ['user_id' => $user_id]);
			if(!empty($response)) {
				$status = true;
				$message = 'Data Ditemukan';
			}else{
				$status = false;
				$message = 'Data Tidak Ditemukan';
			}
		    
			print_json($status,$message,$response);
		}
	}

	function change_profil()
	{
		$auth = $this->token->auth('POST', true);
		if($auth) {
			$user_id = $this->input->get_request_header('User-Id');
			$username = $this->input->get_request_header('Username');
			$id = $this->input->post('id');
			$response = [];

			$data = array(
					'user_id' => $user_id,
					'nama' => $this->input->post('nama'),
					'email' => $this->input->post('email'),
					'no_telp' => $this->input->post('no_telp'),
					'alamat' => $this->input->post('alamat'),
					'gender' => $this->input->post('gender')
				);
			if(!empty($id)){
                $data['update_at'] = date('Y-m-d H:i:s');
                $data['update_by'] = $username;
                $save = $this->Main_Model->process_data('profil', $data, array('id' => $id));
            }else{
                $data['insert_at'] = date('Y-m-d H:i:s');
                $data['insert_by'] = $username;
                $save = $this->Main_Model->process_data('profil', $data);
            }

            if($save){
                $status = True;
                $message = 'Data berhasil disimpan';
                $response[] = $data;
            }else{
                $status = false;
                $message = 'Gagal Menyimpan data!';
            }

			print_json($status,$message,$response);
		}
	}
}