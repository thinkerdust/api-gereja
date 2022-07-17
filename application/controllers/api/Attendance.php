<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Attendance extends CI_Controller {

	function __construct(){
		parent::__construct();
		$this->load->model('Attendance_Model');
		$this->load->library('upload');
	}

	function qr_code($filename = '', $id = 0)
    {
        $this->load->library('ciqrcode');
        $config['cacheable']    = true; //boolean, the default is true
        $config['cachedir']     = './assets/'; //string, the default is application/cache/
        $config['errorlog']     = './assets/'; //string, the default is application/logs/
        $config['imagedir']     = './assets/qrcode/'; //direktori penyimpanan qr code
        $config['quality']      = true; //boolean, the default is true
        $config['size']         = '1024'; //interger, the default is 1024
        $config['black']        = array(224,255,255); // array, default is array(255,255,255)
        $config['white']        = array(70,130,180); // array, default is array(0,0,0)
        $this->ciqrcode->initialize($config);
 
        $image_name = $filename.'.png'; //buat name dari qr code sesuai dengan nim

        $qr_url = $id;
        $params['data'] = $qr_url; //data yang akan di jadikan QR CODE
        $params['level'] = 'H'; //H=High
        $params['size'] = 10;
        $params['savename'] = FCPATH.$config['imagedir'].$image_name; //simpan image QR CODE ke folder assets/images/
        $this->ciqrcode->generate($params);
        return $image_name;
    }

    function generate_qrcode()
    {
    	$auth = $this->token->auth('POST', false);
		if($auth) { 
			$params = get_params();
			$response = [];
			$lokasi = isset($params['lokasi']) ? $params['lokasi'] : '';
			$longitude = isset($params['longitude']) ? $params['longitude'] : '';
			$latitude = isset($params['latitude']) ? $params['latitude'] : '';

			if($lokasi && $longitude && $latitude) {
				$data = array(
								"lokasi" => $lokasi,
								"longitude" => $longitude,
								"latitude" => $latitude
							);

				$insert = $this->Main_Model->process_data('lokasi', $data);
				$filename = strtolower(preg_replace('/\s+/', '', $lokasi));

				// generate qr code
				$qrcode = $this->qr_code($filename, $insert);

				if($insert){
					$this->Main_Model->process_data('lokasi', ['file_qr' => $qrcode], ['id' => $insert]);
					$response = $this->Attendance_Model->view_lokasi($insert);
					$status = 200;
	            	$message = 'Data berhasil disimpan';
				}else{
					$status = 404;
	            	$message = 'Data gagal disimpan';
				}
			}else{
				$status = 400;
				$message = 'Data Tidak Boleh Kosong';
			}
			print_json($status,$message,$response);
		}
    }

    function scan_qr()
    {
    	$auth = $this->token->auth('POST', true);
		if($auth) { 
			$response = [];
			$params = get_params();
			$username = $this->input->get_request_header('Username');
			$user_id = $this->input->get_request_header('User-Id');
			$id = isset($params['id']) ? $params['id'] : '';
			$longitude = isset($params['longitude']) ? $params['longitude'] : '';
			$latitude = isset($params['latitude']) ? $params['latitude'] : '';
			$date = date('Y-m-d');

			if($id && $longitude && $latitude){
				$cek_scan = $this->Main_Model->view_by_id('log_attendance', ['date(check_in)' => $date, 'user_id' => $user_id], 'num_rows');
				if($cek_scan < 1) {
					$lokasi = $this->Main_Model->view_by_id('lokasi', ['id' => $id]);

					if($lokasi) {
						$radius = 0.1;
						// cek distance
						$distance = $this->distance($lokasi->latitude, $lokasi->longitude, $latitude, $longitude, 'K');

						if($distance <= $radius) {
							$this->Main_Model->process_data('log_attendance', ['user_id' => $user_id, 'username' => $username]);
							$status = 200;
		            		$message = 'Berhasil Check In '.date('Y-m-d H:i:s');
						}else{
							$status = 400;
		            		$message = 'Gagal Check In. Anda Diluar Radius';
						}					
			        }else{
			        	$status = 404;
		           		$message = 'Data Tidak Ditemukan';
			        }
				}else{
					$status = 400;
		           	$message = 'Anda sudah check in';
				}
				
			}else{
				$status = 400;
				$message = 'Data Tidak Boleh Kosong';
			}
			print_json($status,$message,$response);
		}
    }

    function distance($lat1, $lon1, $lat2, $lon2, $unit) 
    {

	  	$theta = $lon1 - $lon2;
	  	$dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +  cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
	  	$dist = acos($dist);
	  	$dist = rad2deg($dist);
	  	$miles = $dist * 60 * 1.1515;
	  	$unit = strtoupper($unit);

		if ($unit == "K") {
		    return round($miles * 1.609344);
		} else if ($unit == "N") {
		    return round($miles * 0.8684);
		} else {
		    return round($miles);
		}
	}

	function scan_log()
	{
		$auth = $this->token->auth('POST', true);
		if($auth) { 
			$params = get_params();
			$date = isset($params['date']) ? $params['date'] : '';
			$user_id = $this->input->get_request_header('User-Id');

			$response = $this->db->where("user_id = '$user_id' AND DATE(check_in) = '$date'")->get('log_attendance')->result();

			if($response) {
				$status = 200;
	           	$message = 'Data Ditemukan';
			}else{
				$status = 404;
	           	$message = 'Data Tidak Ditemukan';
			}

			print_json($status,$message,$response);
		}
	}

	function history_attendance()
	{
		$auth = $this->token->auth('POST', true);
		if($auth) { 
			$params = get_params();
			$date_start = isset($params['date_start']) ? $params['date_start'] : '';
			$date_end = isset($params['date_end']) ? $params['date_end'] : '';
			$nama = isset($params['nama']) ? $params['nama'] : '';

			$response = $this->Attendance_Model->view_history_attendance($date_start, $date_end, $nama);

			if($response) {
				$status = 200;
	           	$message = 'Data Ditemukan';
			}else{
				$status = 404;
	           	$message = 'Data Tidak Ditemukan';
			}

			print_json($status,$message,$response);
		}
	}

	function list_lokasi()
	{
		$auth = $this->token->auth('GET', false);
		if($auth) {
			$params = get_params();
			$start = isset($params['start']) ? $params['start'] : 0;
			$count = isset($params['count']) ? $params['count'] : 0;

			$response = $this->Attendance_Model->list_lokasi($start,$count);

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