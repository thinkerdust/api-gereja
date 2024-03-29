<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Setting extends CI_Controller {

	function __construct(){
		parent::__construct();
		$this->load->model('Setting_Model');
	}

	function profil()
	{
		$auth = $this->token->auth('GET', true);
		if($auth) {
			$user_id = $this->input->get_request_header('User-Id');
			$response = $this->Setting_Model->view_profil($user_id);
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

	function change_profil()
	{
		$auth = $this->token->auth('POST', true);
		if($auth) {
			$params = get_params();
			$user_id = $this->input->get_request_header('User-Id');
			$username = $this->input->get_request_header('Username');
			$id = isset($params['id']) ? $params['id'] : '';
			$nij = isset($params['nij']) ? $params['nij'] : '';
			$photo = isset($params['photo']) ? $params['photo'] : '';
			$nama = isset($params['nama']) ? $params['nama'] : '';
			$email = isset($params['email']) ? $params['email'] : '';
			$no_telp = isset($params['no_telp']) ? $params['no_telp'] : '';
			$alamat = isset($params['alamat']) ? $params['alamat'] : '';
			$gender = isset($params['gender']) ? $params['gender'] : '';
			$tgl_lahir = isset($params['tgl_lahir']) ? $params['tgl_lahir'] : '';
			$response = [];

			$data = array(
					'user_id' => $user_id,
					'nij' => $nij,
					'nama' => $nama,
					'photo' => $photo,
					'email' => $email,
					'no_telp' => $no_telp,
					'alamat' => $alamat,
					'gender' => $gender,
					'tgl_lahir' => $tgl_lahir,
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
                $status = 200;
                $message = 'Data berhasil disimpan';
                $response = $data;
            }else{
                $status = 400;
                $message = 'Gagal Menyimpan data!';
            }

			print_json($status,$message,$response);
		}
	}

	function upload_image()
	{
		$auth = $this->token->auth('POST', true);
		if($auth) { 
			$params = get_params();
			$response = [];
			$file = isset($params['file']) ? $params['file'] : '';

			if($file){
				list($type, $file) = explode(';', $file);
		        list(, $file)      = explode(',', $file);
		     
		        $data = base64_decode($file);
		        
		        $imageName = time().'.png';
		        $upload = file_put_contents('assets/upload/images/'.$imageName, $data);

				if(!empty($upload)){
					$response = $imageName;
					$status = 200;
					$message = 'Upload Image Berhasil';
				}else{
					$status = 404;
					$message = 'Upload Image Gagal';
				}
			}else{
				$status = 400;
                $message = 'File Kosong!';
			}

			print_json($status,$message,$response);
		}
	}

	function send_message()
	{
		$url = "https://lmao.my.id/api/chats/send";

        $api_key = '466f834b8184a63783201fffdf6723f227d8695aabf624';
        $device_id = 179;
        $authorization = "Authorization: Bearer ".$api_key;

        $number = $this->input->post('number');
        $message = $this->input->post('message');

        $phone = $number;
        $phone = preg_replace('/\D/', '', $phone);
        $index = strpos($phone,'0');
        if($index == 0) {
            $phone = substr_replace($phone,'62',0,1);
        }

        $data = [
            "device_id"     => $device_id,
            "phone"         => $phone,
            "message"       => ["text" => $message]
        ];

        $data = json_encode($data, FALSE);

        $ch = curl_init($url);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', $authorization));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		$result = curl_exec($ch);
		curl_close($ch);
		print_r($result);
	}

	function data_jemaat()
	{
		$auth = $this->token->auth('GET', true);
		if($auth) {

			$response = $this->Main_Model->view_by_id('jemaat', ['flag' => 1], 'result');

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

	function master_link()
	{
		$params = get_params();
		$id = isset($params['id']) ? $params['id'] : '';
		$event = isset($params['event']) ? $params['event'] : '';
		$link = isset($params['link']) ? $params['link'] : '';
		$flag = isset($params['flag']) ? $params['flag'] : '';
		$response = [];

		if($flag == 'INSERT') {
			$response = $this->Main_Model->process_data('ms_link', ['event' => $event, "link" => $link]);
			$status = 200;
        	$message = 'Data berhasil disimpan';
		}elseif ($flag == 'DELETE') {
			$respons = $this->Main_Model->delete_data('ms_link', ['id' => $id]);
			$status = 200;
        	$message = 'Data berhasil dihapus';
		}else{
			if(!empty($id)) {
				$response = $this->Main_Model->view_by_id('ms_link', ['id' => $id]);
			}else{
				$response = $this->Main_Model->view_by_id('ms_link', [], 'result');
			}

			if(!empty($response)) {
				$status = 200;
				$message = 'Data Ditemukan';
			}else{
				$status = 404;
				$message = 'Data Tidak Ditemukan';
			}   
		}
		
		print_json($status,$message,$response);
	}
}