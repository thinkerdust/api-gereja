<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Attendance extends CI_Controller {

	function __construct(){
		parent::__construct();
		$this->load->library('upload');
	}

	function qr_code($lokasi = '')
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
 
        $image_name = $lokasi.'.png'; //buat name dari qr code sesuai dengan nim

        $qr_url = 'https://nexacard.id/main/qrcode_redirect/'.$lokasi;
        $params['data'] = $qr_url; //data yang akan di jadikan QR CODE
        $params['level'] = 'H'; //H=High
        $params['size'] = 10;
        $params['savename'] = FCPATH.$config['imagedir'].$image_name; //simpan image QR CODE ke folder assets/images/
        $this->ciqrcode->generate($params);
    }

    function generate_qrcode()
    {
    	$auth = $this->token->auth('POST', false);
		if($auth) { 
			$params = get_params();
			$response = [];
			$lokasi = isset($params['lokasi']) ? $params['lokasi'] : '';
			$longitude = isset($params['longitude']) ? $params['longitude'] : '';
			$lagtitude = isset($params['lagtitude']) ? $params['lagtitude'] : '';

			if($lokasi && $longitude && $lagtitude) {
				$response = array(
								"lokasi" => $lokasi,
								"longitude" => $longitude,
								"lagtitude" => $lagtitude
							);

				// generate qr code
				$this->qr_code();
				$status = 200;
	            $message = 'Data berhasil disimpan';

			}else{
				$status = 400;
				$message = 'Data Tidak Boleh Kosong';
			}
			print_json($status,$message,$response);
		}
    }
}