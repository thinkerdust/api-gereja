<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Authentication extends CI_Controller {

	function register()
	{
		$auth = $this->token->auth('POST',false);
		if($auth) {
			$response = [];

			$params = get_params();
			$nama = isset($params['nama']) ? $params['nama'] : '';
			$username = isset($params['username']) ? $params['username'] : '';
			$password = isset($params['password']) ? $params['password'] : '';
			$re_password = isset($params['re_password']) ? $params['re_password'] : '';
			$no_telp = isset($params['no_telp']) ? $params['no_telp'] : '';
			$otp = isset($params['otp']) ? $params['otp'] : '';
			$username = $this->db->escape_str($username);
            $password = $this->db->escape_str($password);

			if($nama && $username && $password && $re_password && $no_telp && $otp) {
				$check_user = $this->Main_Model->view_by_id('user', ['username' => $username, 'no_telp' => $no_telp]);
				if(strlen($password) < 4){
					$status = 400;
	       			$message = "Min karakter password adalah 4!";
				}elseif ($password != $re_password) {
					$status = 400;
	       			$message = "Password tidak sama dengan Re-Password!";
				}elseif (strlen($password) > 3 && $password == $re_password && empty($check_user)) {

		       		$data = array(
		       				"nama" => $nama,
		       				"username" => $username,
		       				"password" => password_hash($password,PASSWORD_DEFAULT),
		       				"no_telp" => $no_telp,
		       				"otp" => $otp,
		       				"flag" => 1
		       			);

		       		$process = $this->Main_Model->process_data('user', $data);

		       		if($process) {
		       			$status = 200;
		       			$message = 'Register Berhasil';
		       			$response = $data;
		       		}else{
		       			$status = 404;
		       			$message = 'Register Gagal';
		       		}
				}else{
					$status = 400;
		       		$message = 'Username / No Telp sudah digunakan';
				}

			}else{
				$status = 400;
	       		$message = "Mohon Lengkapi Data!";
			}

	       	print_json($status,$message,$response);
	    }
	}

	function request_otp()
	{
		$auth = $this->token->auth('POST', true);
		if($auth){
			$response = [];
			$params = get_params();
			$otp = rand(100000,999999);
			$number = isset($params['number']) ? $params['number'] : '';

			if($number){
				$message = "Helasterion-Ministry Pendaftaran akun baru 
Jangan berikan kode dengan siapapun

kode Akun anda: ".$otp."

GBT Kristus Alfa Omega";
				// send otp
				$send = $this->Main_Model->send_message($number, $message);
				$this->Main_Model->log_message($number, $message, $send->success);
				if($send->success) {
					$status = 200;
		       		$message = 'Request OTP Berhasil';
		       		$response = ['otp' => $otp];
				}else{
					$status = 404;
		       		$message = 'Request OTP Gagal';
				}
			}else{
				$status = 400;
		       	$message = "Nomor tidak boleh kosong";
			}

			print_json($status,$message,$response);
		}
	}

	function check_otp()
	{
		$auth = $this->token->auth('POST',false);
		if($auth){
			$response = [];
			$params = get_params();
			$username = $this->input->get_request_header('Username');
			$otp = isset($params['otp']) ? $params['otp'] : '';

			if ($otp) {
				$user = $this->Main_Model->view_by_id('user', ['username' => $username, 'otp' => $otp]);
				if(!empty($user)) {
					$this->Main_Model->process_data('user', ['flag' => 1], ['username' => $username]);
					$status = 200;
		       		$message = 'OTP Berhasil';
		       		$response = $user;
				}else{
					$status = 404;
		       		$message = 'Data Tidak Ditemukan';
				}
				
			}else{
				$status = 400;
		       	$message = "OTP tidak boleh kosong";
			}

			print_json($status,$message,$response);
		}
	}

	function login()
	{
		$auth = $this->token->auth('POST',false);
		if($auth){
			$params = get_params();
			$username = isset($params['username']) ? $params['username'] : '';
			$password = isset($params['password']) ? $params['password'] : '';

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
			$params = get_params();
			$user_id = $this->input->get_request_header('User-Id');
			$password = isset($params['password']) ? $params['password'] : '';
			$new_password = isset($params['new_password']) ? $params['new_password'] : '';
			$re_password = isset($params['re_password']) ? $params['re_password'] : '';

			$password = $this->db->escape_str($password);
			$new_password = $this->db->escape_str($new_password);
			$re_password = $this->db->escape_str($re_password);

			if($password && $new_password && $re_password){
				$user = $this->Main_Model->view_by_id('user', ['id' => $user_id, 'flag' => 1]);

				if(strlen($new_password) < 4) {
					$status = 400;
	       			$message = "Min karakter password adalah 4!";
				}elseif($new_password != $re_password) {
					$status = 400;
	       			$message = "New Password tidak sama dengan Re-Password";
				}elseif(strlen($new_password) > 3 && $new_password == $re_password && !empty($user) && password_verify($password,$user->password)) {
					$data = array(
	       						"password" => password_hash($new_password,PASSWORD_DEFAULT),
	       						"update_at" => date('Y-m-d H:i:s')
	       					);
	       			$this->Main_Model->process_data('user', $data, ['id' => $user_id]);
					$status = 200;
		       		$message = 'Ubah Password Berhasil';
		       		$response = $user;
				}else{
					$status = 404;
					$message = 'Password salah / Data tidak ditemukan';
				}

			}else{
				$status = 400;
	       		$message = "Data Tidak Boleh Kosong!";
			}

			print_json($status,$message,$response);
		}
	}
}
