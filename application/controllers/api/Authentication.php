<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Authentication extends CI_Controller {

	function register()
	{
		$auth = $this->token->auth('POST',false);
		if($auth) {
			$response = [];

			$params = get_params();
			$nama = $params['nama'];
			$username = $params['username'];
			$password = $params['password'];
			$re_password = $params['re_password'];
			$no_telp = $params['no_telp'];

	       	if($this->form_validation->run() == FALSE) {
	       		$status = 400;
	       		$message = strip_tags($this->form_validation->error_string());
	       	}else{

	       		$username = $this->input->post('username');
	       		$no_telp = $this->input->post('no_telp');
	       		$check_user = $this->Main_Model->view_by_id('user', ['username' => $username, 'no_telp' => $no_telp]);

	       		if(empty($check_user)){
 
		       		$otp = rand(100000,999999);

		       		$data = array(
		       				"nama" => $this->input->post('nama'),
		       				"username" => $username,
		       				"password" => password_hash($this->input->post('password'),PASSWORD_DEFAULT),
		       				"no_telp" => $no_telp,
		       				"otp" => $otp,
		       			);

		       		$process = $this->Main_Model->process_data('user', $data);

		       		if($process) {
		       			$status = 200;
		       			$message = 'Register Berhasil';
		       			$response[] = $data;
		       		}else{
		       			$status = 404;
		       			$message = 'Register Gagal';
		       		}
		       	}else{
		       		$status = 400;
		       		$message = 'Username / No Telp sudah digunakan';
		       	}          
	       	}

	       	print_json($status,$message,$response);
	    }
	}

	function check_otp()
	{
		$auth = $this->token->auth('POST',false);
		if($auth){
			$response = [];
			$username = $this->input->get_request_header('Username');
			$otp = $this->input->post('otp');

			$this->form_validation->set_rules('otp', 'OTP', 'required');

			if ($this->form_validation->run() == FALSE) {
				$status = 400;
		       	$message = strip_tags($this->form_validation->error_string());
		       	// html_escape
			}else{
				$user = $this->Main_Model->view_by_id('user', ['username' => $username, 'otp' => $otp]);
				if(!empty($user)) {
					$this->Main_Model->process_data('user', ['flag' => 1], ['username' => $username]);
					$status = 200;
		       		$message = 'OTP Berhasil';
		       		$response[] = $user;
				}else{
					$status = 404;
		       		$message = 'Data Tidak Ditemukan';
				}
			}

			print_json($status,$message,$response);
		}
	}

	function login()
	{
		$auth = $this->token->auth('POST',false);
		if($auth){
			$params = get_params();
			$username = $params['username'];
			$password = $params['password'];

            $response = [];

            if($username == '' || $password == ''){
            	$status = 400;
            	if($password == ''){
            		$message = "Password harus di isi";	
            	}
            	if($username == ''){
            		$message = "Username harus di isi";
            	}
            }
            else{
            	$username = $this->db->escape_str($username);
            	$password = $this->db->escape_str($password);

				$user = $this->Main_Model->view_by_id('user', ['username' => $username, 'flag' => 1]);
				
				if(!empty($user) && password_verify($password,$user->password)){
					# create token
	                $create_token = $this->token->create_token($user->username);

	                # token
	                $token = isset($create_token['token']) ? $create_token['token'] : '';

	                if($create_token){
	                	$status = 200;
	                	$message = "Login berhasil";
	                	$response = array(
		                	'id' => $user->id,
		                	'nama' => $user->nama,
		                	'username' => $user->username,
		                	'user_level' => $user->user_level,
		                	'no_telp' => $user->no_telp,
		                	'token' => $token,
	                	);
	                }else{
	                    $status = 500;
	                    $message = 'Terjadi kesalahan saat authentikasi';
	                }
				}else{
					$status = 404;
					$message = 'Password salah / Data tidak ditemukan';
				}	
            }
			print_json($status,$message,$response);
		}
	}

	function change_password()
	{
		$auth = $this->token->auth('POST', true);
		if($auth){
			$response = [];
			$user_id = $this->input->get_request_header('User-Id');
			$password = $this->input->post('password');
			$new_password = $this->input->post('new_password');
			$re_password = $this->input->post('re_password');

			$rules_validation = [
	            [
	                'field' => 'password',
	                'label' => 'Password',
	                'rules' => 'trim|required',
	            ],
	            [
	                'field' => 'new_password',
	                'label' => 'New Password',
	                'rules' => 'trim|required|min_length[4]',
	            ],
	            [
	                'field' => 're_password',
	                'label' => 'Re-Password',
	                'rules' => 'trim|required|matches[new_password]',
	            ],
	        ];

	        $this->form_validation->set_rules($rules_validation);
	       	if($this->form_validation->run() == FALSE) {
	       		$status = 400;
	       		$message = strip_tags($this->form_validation->error_string());
	       	}else{
	       		$user = $this->Main_Model->view_by_id('user', ['id' => $user_id, 'flag' => 1]);
	       		if(!empty($user) && password_verify($password,$user->password)){
	       			$data = array(
	       						"password" => password_hash($new_password,PASSWORD_DEFAULT),
	       						"update_at" => date('Y-m-d H:i:s')
	       					);
	       			$this->Main_Model->process_data('user', $data, ['id' => $user_id]);
					$status = 200;
		       		$message = 'Ubah Password Berhasil';
		       		$response[] = $user;
	       		}else{
	       			$status = 404;
					$message = 'Password salah / Data tidak ditemukan';
	       		}
	       	}

			print_json($status,$message,$response);
		}
	}
}
