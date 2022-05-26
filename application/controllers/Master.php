<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Master extends CI_Controller {

	function __construct()
	{
		parent::__construct();
		$this->load->library('upload');
		$this->load->helper('download');
		$this->Main_Model->cek_login();
	}

	public function jemaat()
	{
		$sidebar['sidebar'] = 'jemaat';
		$foot['js'] = 'master/jemaat';

		$data['alert'] = $this->session->flashdata('alert');

		$this->load->view('template/header');
		$this->load->view('template/sidebar', $sidebar);
		$this->load->view('master/jemaat', $data);
		$this->load->view('template/footer', $foot);
	}

	public function ajax_data_jemaat()
	{
		if($this->input->is_ajax_request()){
			$arr = [];
            $no = 1;
            $data = $this->Main_Model->view_by_id('jemaat', ['flag' => 1], 'result');
            if(!empty($data)){
            	foreach($data as $row => $val){
            		$action = '<a href="'.base_url().'master/form_jemaat/'.$val->nij.'" class="btn btn-outline-primary btn-sm" title="edit"><i class="fas fa-edit"></i></a>
                        <a href="'.base_url().'master/delete_jemaat/'.$val->nij.'" class="btn btn-outline-primary btn-sm" title="hapus"><i class="fas fa-trash"></i></a>';

                        if(empty($val->qr_code)){
                        	$action .= ' <a href="'.base_url().'master/generate_qrcode/'.$val->nij.'" class="btn btn-outline-primary btn-sm" title="generate qr"><i class="fas fa-qrcode"></i></a>';
                        }else{
                        	$action .= ' <a href="'.base_url().'master/download_qr/'.$val->qr_code.'" class="btn btn-outline-primary btn-sm" title="show qr"><i class="fas fa-qrcode"></i></a>';
                        }

            		$arr[$row] = array(
                        $no++,
                        $val->nij,
                        $val->nama,
                        $val->no_telp,
                        $val->email,
                        $action
                    );
            	}
            }
            $respon = array('data' => $arr);
            echo json_encode($respon);
		}else{
			show_404();
		}
	}

	public function form_jemaat($nij = '')
	{
		$sidebar['sidebar'] = 'jemaat';
		$foot['js'] = '';

		$data['data'] = $this->Main_Model->view_by_id('jemaat', ['nij'=>$nij]);
		$data['alert'] = $this->session->flashdata('alert');

		$this->load->view('template/header');
		$this->load->view('template/sidebar', $sidebar);
		$this->load->view('master/form_jemaat', $data);
		$this->load->view('template/footer', $foot);
	}

	public function store_jemaat()
	{
		$cond = [];
		$nij = $this->input->post('nij');
		$nama = $this->input->post('nama');
		$no_telp = $this->input->post('no_telp');
		$email = $this->input->post('email');
		$user_level = $this->input->post('user_level');
		$jual = $this->input->post('jual');

		$data = array(
			'nama' => $nama,
			'no_telp' => $no_telp,
			'email' => $email,
			'user_level' => $user_level,
		);

		$cond['no_telp'] = $no_telp;
		$cond['flag'] = 1;
		if(!empty($nij)) {
			$cond['nij != '] = $nij;
		} 

		$cek = $this->Main_Model->view_by_id('jemaat', $cond);

		if(empty($cek)) {
			if(!empty($nij))
		    {
		    	$data['update_by'] = $this->session->userdata('username');
	            $data['update_at'] = date('Y-m-d H-i-s');
		    	$save = $this->Main_Model->process_data('jemaat', $data, ['nij' => $nij]);
		    }else{
		    	$data['nij'] = $this->Main_Model->get_nij();
		    	$data['insert_by'] = $this->session->userdata('username');
	            $data['insert_at'] = date('Y-m-d H-i-s');
		    	$save = $this->db->insert('jemaat', $data);
		    }

		    if($save){
		    	$alert = '<div class="alert alert-success alert-dismissible fade show" role="alert">
				              <strong>Sukses!</strong> Data berhasil tersimpan.
				              <button type="button" class="close" data-dismiss="alert" aria-label="Close">
				                <span aria-hidden="true">&times;</span>
				              </button>
				            </div>';
	  			$this->session->set_flashdata('alert', $alert);
		    	redirect(base_url('master/jemaat'), 'refresh');
		    }else{
		    	$alert = '<div class="alert alert-danger alert-dismissible fade show" role="alert">
				              <strong>Error!</strong> Data gagal tersimpan.
				              <button type="button" class="close" data-dismiss="alert" aria-label="Close">
				                <span aria-hidden="true">&times;</span>
				              </button>
				            </div>';
	  			$this->session->set_flashdata('alert', $alert);
	  			redirect(base_url('master/form_jemaat/'.$nij));
		    }
		}else{
			$alert = '<div class="alert alert-danger alert-dismissible fade show" role="alert">
				              <strong>Error!</strong> No Telp Sudah Dipakai.
				              <button type="button" class="close" data-dismiss="alert" aria-label="Close">
				                <span aria-hidden="true">&times;</span>
				              </button>
				            </div>';
	  			$this->session->set_flashdata('alert', $alert);
	  			redirect(base_url('master/form_jemaat/'.$nij));
		}

	}

	public function delete_jemaat($nij)
	{
		$data = array(
					"flag" => 0,
					"update_by" => $this->session->userdata('username'),
            		"update_at" => date('Y-m-d')
				);
		$hapus = $this->Main_Model->process_data("jemaat", $data, ['nij' => $nij]);

		$alert = '<div class="alert alert-success alert-dismissible fade show" role="alert">
		              <strong>Sukses!</strong> Data berhasil dihapus.
		              <button type="button" class="close" data-dismiss="alert" aria-label="Close">
		                <span aria-hidden="true">&times;</span>
		              </button>
		            </div>';
		$this->session->set_flashdata('alert', $alert);
    	redirect(base_url('master/jemaat'), 'refresh');
	}

	public function download_qr($file){				
		force_download('./assets/qrcode/'.$file,NULL);
	}

	function qr_code($filename = '', $data)
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

        $params['data'] = $data; //data yang akan di jadikan QR CODE
        $params['level'] = 'H'; //H=High
        $params['size'] = 10;
        $params['savename'] = FCPATH.$config['imagedir'].$image_name; //simpan image QR CODE ke folder assets/images/
        $this->ciqrcode->generate($params);
        return $image_name;
    }

    function generate_qrcode($nij)
    {
    	$lokasi = $this->db->query("SELECT * from lokasi order by id desc")->row();
    	$data = array(
    				"nij" => $nij,
    				"id_lokasi" => $lokasi->id
    			);

		// generate qr code
		$qrcode = $this->qr_code($nij, $data);
		$this->Main_Model->process_data('jemaat', ['qr_code' => $qrcode], ['nij' => $nij]);
		$alert = '<div class="alert alert-success alert-dismissible fade show" role="alert">
		              <strong>Sukses!</strong> Generate QR Code Berhasil!
		              <button type="button" class="close" data-dismiss="alert" aria-label="Close">
		                <span aria-hidden="true">&times;</span>
		              </button>
		            </div>';
		$this->session->set_flashdata('alert', $alert);
    	redirect(base_url('master/jemaat'), 'refresh');
    }

	public function renungan()
	{
		$sidebar['sidebar'] = 'renungan';
		$foot['js'] = 'master/renungan';

		$data['alert'] = $this->session->flashdata('alert');

		$this->load->view('template/header');
		$this->load->view('template/sidebar', $sidebar);
		$this->load->view('master/renungan', $data);
		$this->load->view('template/footer', $foot);
	}

	public function ajax_data_renungan()
	{
		if($this->input->is_ajax_request()){
			$arr = [];
            $no = 1;
            $data = $this->Main_Model->view_by_id('renungan', ['flag' => 1], 'result');
            if(!empty($data)){
            	foreach($data as $row => $val){
            		$action = '<a href="'.base_url().'master/form_renungan/'.$val->id.'" class="btn btn-warning">Edit</a>
                        <a href="'.base_url().'master/delete_renungan/'.$val->id.'" class="btn btn-danger">Hapus</a>';

                    $body = $val->body;
                    if(strlen($body) > 100){
                    	$body = substr($val->body, 0, 100).' ... ';
                    }

            		$arr[$row] = array(
                        $no++,
                        '<img src="'.base_url().'assets/upload/images/'.$val->image.'" class="img-fluid" style="width:100px;height:100px">',
                        $val->title,
                        $body,
                        $action
                    );
            	}
            }
            $respon = array('data' => $arr);
            echo json_encode($respon);
		}else{
			show_404();
		}
	}

	public function form_renungan($id = '')
	{
		$sidebar['sidebar'] = 'renungan';
		$foot['js'] = '';

		$data['data'] = $this->Main_Model->view_by_id('renungan', ['id'=>$id]);
		$data['alert'] = $this->session->flashdata('alert');

		$this->load->view('template/header');
		$this->load->view('template/sidebar', $sidebar);
		$this->load->view('master/form_renungan', $data);
		$this->load->view('template/footer', $foot);
	}

	public function store_renungan()
	{
		$id = $this->input->post('id');
		$title = $this->input->post('title');
		$body = $this->input->post('body');

		$data = array(
			'title' => $title,
			'body' => $body,
		);

		// upload gambar
		$image = $_FILES["gambar"]["name"];
		$filename = '';

		if(!empty($image)) {
			$str_file = str_replace(' ', '', $image);
			$filename = date('YmdHis').$str_file;

		    $config['upload_path']          = './assets/upload/images/';
		    $config['allowed_types']        = '*';
		    $config['file_name']            = $filename;
		    $config['max_size']             = 0; 

		    $this->upload->initialize($config);

		    if ($this->upload->do_upload('gambar')) {
		        $filename = $this->upload->data("file_name");
		    }

		    $data['image'] = $filename;
		}

		if(!empty($id))
	    {
	    	$data['update_by'] = $this->session->userdata('username');
            $data['update_at'] = date('Y-m-d H-i-s');
	    	$save = $this->Main_Model->process_data('renungan', $data, ['id' => $id]);
	    }else{
	    	$data['insert_by'] = $this->session->userdata('username');
            $data['insert_at'] = date('Y-m-d H-i-s');
	    	$save = $this->Main_Model->process_data('renungan', $data);
	    	$this->Notifikasi_Model->send_notif($save, 'renungan');
	    }

	    if($save){
	    	$alert = '<div class="alert alert-success alert-dismissible fade show" role="alert">
			              <strong>Sukses!</strong> Data berhasil tersimpan.
			              <button type="button" class="close" data-dismiss="alert" aria-label="Close">
			                <span aria-hidden="true">&times;</span>
			              </button>
			            </div>';
  			$this->session->set_flashdata('alert', $alert);
	    	redirect(base_url('master/renungan'), 'refresh');
	    }else{
	    	$alert = '<div class="alert alert-danger alert-dismissible fade show" role="alert">
			              <strong>Error!</strong> Data gagal tersimpan.
			              <button type="button" class="close" data-dismiss="alert" aria-label="Close">
			                <span aria-hidden="true">&times;</span>
			              </button>
			            </div>';
  			$this->session->set_flashdata('alert', $alert);
  			redirect(base_url('master/form_renungan/'.$id));
	    }
	}

	public function delete_renungan($id)
	{
		$data = array(
					"flag" => 0,
					"update_by" => $this->session->userdata('username'),
            		"update_at" => date('Y-m-d')
				);
		$hapus = $this->Main_Model->process_data("renungan", $data, ['id' => $id]);

		$alert = '<div class="alert alert-success alert-dismissible fade show" role="alert">
		              <strong>Sukses!</strong> Data berhasil dihapus.
		              <button type="button" class="close" data-dismiss="alert" aria-label="Close">
		                <span aria-hidden="true">&times;</span>
		              </button>
		            </div>';
		$this->session->set_flashdata('alert', $alert);
    	redirect(base_url('master/renungan'), 'refresh');
	}

	public function berita()
	{
		$sidebar['sidebar'] = 'berita';
		$foot['js'] = 'master/berita';

		$data['alert'] = $this->session->flashdata('alert');

		$this->load->view('template/header');
		$this->load->view('template/sidebar', $sidebar);
		$this->load->view('master/berita', $data);
		$this->load->view('template/footer', $foot);
	}

	public function ajax_data_berita()
	{
		if($this->input->is_ajax_request()){
			$arr = [];
            $no = 1;
            $data = $this->Main_Model->view_by_id('berita', ['flag' => 1], 'result');
            if(!empty($data)){
            	foreach($data as $row => $val){
            		$action = '<a href="'.base_url().'master/form_berita/'.$val->id.'" class="btn btn-warning">Edit</a>
                        <a href="'.base_url().'master/delete_berita/'.$val->id.'" class="btn btn-danger">Hapus</a>';

                    $body = $val->body;
                    if(strlen($body) > 100){
                    	$body = substr($val->body, 0, 100).' ... ';
                    }

            		$arr[$row] = array(
                        $no++,
                        '<img src="'.base_url().'assets/upload/images/'.$val->image.'" class="img-fluid" style="width:100px;height:100px">',
                        $val->title,
                        $body,
                        $action
                    );
            	}
            }
            $respon = array('data' => $arr);
            echo json_encode($respon);
		}else{
			show_404();
		}
	}

	public function form_berita($id = '')
	{
		$sidebar['sidebar'] = 'berita';
		$foot['js'] = '';

		$data['data'] = $this->Main_Model->view_by_id('berita', ['id'=>$id]);
		$data['alert'] = $this->session->flashdata('alert');

		$this->load->view('template/header');
		$this->load->view('template/sidebar', $sidebar);
		$this->load->view('master/form_berita', $data);
		$this->load->view('template/footer', $foot);
	}

	public function store_berita()
	{
		$id = $this->input->post('id');
		$title = $this->input->post('title');
		$body = $this->input->post('body');

		$data = array(
			'title' => $title,
			'body' => $body,
		);

		// upload gambar
		$image = $_FILES["gambar"]["name"];
		$filename = '';

		if(!empty($image)) {
			$str_file = str_replace(' ', '', $image);
			$filename = date('YmdHis').$str_file;

		    $config['upload_path']          = './assets/upload/images/';
		    $config['allowed_types']        = '*';
		    $config['file_name']            = $filename;
		    $config['max_size']             = 0; 

		    $this->upload->initialize($config);

		    if ($this->upload->do_upload('gambar')) {
		        $filename = $this->upload->data("file_name");
		    }

		    $data['image'] = $filename;
		}

		if(!empty($id))
	    {
	    	$data['update_by'] = $this->session->userdata('username');
            $data['update_at'] = date('Y-m-d H-i-s');
	    	$save = $this->Main_Model->process_data('berita', $data, ['id' => $id]);
	    }else{
	    	$data['insert_by'] = $this->session->userdata('username');
            $data['insert_at'] = date('Y-m-d H-i-s');
	    	$save = $this->Main_Model->process_data('berita', $data);
	    	$this->Notifikasi_Model->send_notif($save, 'berita');
	    }

	    if($save){
	    	$alert = '<div class="alert alert-success alert-dismissible fade show" role="alert">
			              <strong>Sukses!</strong> Data berhasil tersimpan.
			              <button type="button" class="close" data-dismiss="alert" aria-label="Close">
			                <span aria-hidden="true">&times;</span>
			              </button>
			            </div>';
  			$this->session->set_flashdata('alert', $alert);
	    	redirect(base_url('master/berita'), 'refresh');
	    }else{
	    	$alert = '<div class="alert alert-danger alert-dismissible fade show" role="alert">
			              <strong>Error!</strong> Data gagal tersimpan.
			              <button type="button" class="close" data-dismiss="alert" aria-label="Close">
			                <span aria-hidden="true">&times;</span>
			              </button>
			            </div>';
  			$this->session->set_flashdata('alert', $alert);
  			redirect(base_url('master/form_berita/'.$id));
	    }
	}

	public function delete_berita($id)
	{
		$data = array(
					"flag" => 0,
					"update_by" => $this->session->userdata('username'),
            		"update_at" => date('Y-m-d')
				);
		$hapus = $this->Main_Model->process_data("berita", $data, ['id' => $id]);

		$alert = '<div class="alert alert-success alert-dismissible fade show" role="alert">
		              <strong>Sukses!</strong> Data berhasil dihapus.
		              <button type="button" class="close" data-dismiss="alert" aria-label="Close">
		                <span aria-hidden="true">&times;</span>
		              </button>
		            </div>';
		$this->session->set_flashdata('alert', $alert);
    	redirect(base_url('master/berita'), 'refresh');
	}

	public function media()
	{
		$sidebar['sidebar'] = 'media';
		$foot['js'] = 'master/media';

		$data['alert'] = $this->session->flashdata('alert');

		$this->load->view('template/header');
		$this->load->view('template/sidebar', $sidebar);
		$this->load->view('master/media', $data);
		$this->load->view('template/footer', $foot);
	}

	public function ajax_data_media()
	{
		if($this->input->is_ajax_request()){
			$arr = [];
            $no = 1;
            $data = $this->Main_Model->view_by_id('media', ['flag' => 1], 'result');
            if(!empty($data)){
            	foreach($data as $row => $val){
            		$action = '<a href="'.base_url().'master/form_media/'.$val->id.'" class="btn btn-warning">Edit</a>
                        <a href="'.base_url().'master/delete_media/'.$val->id.'" class="btn btn-danger">Hapus</a>';

                    $keterangan = $val->keterangan;
                    if(strlen($keterangan) > 100){
                    	$keterangan = substr($val->keterangan, 0, 100).' ... ';
                    }

            		$arr[$row] = array(
                        $no++,
                        '<img src="'.base_url().'assets/upload/images/'.$val->image.'" class="img-fluid" style="width:100px;height:100px">',
                        $val->judul,
                        $keterangan,
                        '<a target="_blank" href="'.$val->link.'">link</a>',
                        $action
                    );
            	}
            }
            $respon = array('data' => $arr);
            echo json_encode($respon);
		}else{
			show_404();
		}
	}

	public function form_media($id = '')
	{
		$sidebar['sidebar'] = 'media';
		$foot['js'] = '';

		$data['data'] = $this->Main_Model->view_by_id('media', ['id'=>$id]);
		$data['alert'] = $this->session->flashdata('alert');

		$this->load->view('template/header');
		$this->load->view('template/sidebar', $sidebar);
		$this->load->view('master/form_media', $data);
		$this->load->view('template/footer', $foot);
	}

	public function store_media()
	{
		$id = $this->input->post('id');
		$judul = $this->input->post('judul');
		$keterangan = $this->input->post('keterangan');
		$link = $this->input->post('link');

		$data = array(
			'judul' => $judul,
			'keterangan' => $keterangan,
			'link' => $link,
		);

		// upload gambar
		$image = $_FILES["gambar"]["name"];
		$filename = '';

		if(!empty($image)) {
			$str_file = str_replace(' ', '', $image);
			$filename = date('YmdHis').$str_file;

		    $config['upload_path']          = './assets/upload/images/';
		    $config['allowed_types']        = '*';
		    $config['file_name']            = $filename;
		    $config['max_size']             = 0; 

		    $this->upload->initialize($config);

		    if ($this->upload->do_upload('gambar')) {
		        $filename = $this->upload->data("file_name");
		    }

		    $data['image'] = $filename;
		}

		if(!empty($id))
	    {
	    	$data['update_by'] = $this->session->userdata('username');
            $data['update_at'] = date('Y-m-d H-i-s');
	    	$save = $this->Main_Model->process_data('media', $data, ['id' => $id]);
	    }else{
	    	$data['insert_by'] = $this->session->userdata('username');
            $data['insert_at'] = date('Y-m-d H-i-s');
	    	$save = $this->Main_Model->process_data('media', $data);
	    }

	    if($save){
	    	$alert = '<div class="alert alert-success alert-dismissible fade show" role="alert">
			              <strong>Sukses!</strong> Data berhasil tersimpan.
			              <button type="button" class="close" data-dismiss="alert" aria-label="Close">
			                <span aria-hidden="true">&times;</span>
			              </button>
			            </div>';
  			$this->session->set_flashdata('alert', $alert);
	    	redirect(base_url('master/media'), 'refresh');
	    }else{
	    	$alert = '<div class="alert alert-danger alert-dismissible fade show" role="alert">
			              <strong>Error!</strong> Data gagal tersimpan.
			              <button type="button" class="close" data-dismiss="alert" aria-label="Close">
			                <span aria-hidden="true">&times;</span>
			              </button>
			            </div>';
  			$this->session->set_flashdata('alert', $alert);
  			redirect(base_url('master/form_media/'.$id));
	    }
	}

	public function delete_media($id)
	{
		$data = array(
					"flag" => 0,
					"update_by" => $this->session->userdata('username'),
            		"update_at" => date('Y-m-d')
				);
		$hapus = $this->Main_Model->process_data("media", $data, ['id' => $id]);

		$alert = '<div class="alert alert-success alert-dismissible fade show" role="alert">
		              <strong>Sukses!</strong> Data berhasil dihapus.
		              <button type="button" class="close" data-dismiss="alert" aria-label="Close">
		                <span aria-hidden="true">&times;</span>
		              </button>
		            </div>';
		$this->session->set_flashdata('alert', $alert);
    	redirect(base_url('master/media'), 'refresh');
	}

	public function ms_warta()
	{
		$sidebar['sidebar'] = 'ms_warta';
		$foot['js'] = 'master/ms_warta';

		$data['alert'] = $this->session->flashdata('alert');

		$this->load->view('template/header');
		$this->load->view('template/sidebar', $sidebar);
		$this->load->view('master/ms_warta', $data);
		$this->load->view('template/footer', $foot);
	}

	public function ajax_data_ms_warta()
	{
		if($this->input->is_ajax_request()){
			$arr = [];
            $no = 1;
            $data = $this->Main_Model->view_by_id('ms_warta', ['flag' => 1], 'result');
            if(!empty($data)){
            	foreach($data as $row => $val){
            		$action = '<a href="'.base_url().'master/form_ms_warta/'.$val->id.'" class="btn btn-outline-primary btn-sm" title="edit"><i class="fas fa-edit"></i></a>
                        <a href="'.base_url().'master/delete_ms_warta/'.$val->id.'" class="btn btn-outline-primary btn-sm" title="hapus"><i class="fas fa-trash"></i></a>';

                    $deskripsi = $val->deskripsi;
                    if(strlen($deskripsi) > 200){
                    	$deskripsi = substr($val->deskripsi, 0, 200).' ... ';
                    }

            		$arr[$row] = array(
                        $no++,
                        $val->judul,
                        $deskripsi,
                        $action
                    );
            	}
            }
            $respon = array('data' => $arr);
            echo json_encode($respon);
		}else{
			show_404();
		}
	}

	public function form_ms_warta($id = '')
	{
		$sidebar['sidebar'] = 'ms_warta';

		$data['data'] = $this->Main_Model->view_by_id('ms_warta', ['id'=>$id]);
		$data['alert'] = $this->session->flashdata('alert');

		$this->load->view('template/header');
		$this->load->view('template/sidebar', $sidebar);
		$this->load->view('master/form_ms_warta', $data);
		$this->load->view('template/footer');
	}

	public function store_ms_warta()
	{
		$id = $this->input->post('id');
		$judul = $this->input->post('judul');
		$deskripsi = $this->input->post('deskripsi');

		$data = array(
			'judul' => $judul,
			'deskripsi' => $deskripsi,
		);


		if(!empty($id))
	    {
	    	$data['update_by'] = $this->session->userdata('username');
            $data['update_at'] = date('Y-m-d H-i-s');
	    	$save = $this->Main_Model->process_data('ms_warta', $data, ['id' => $id]);
	    }else{
	    	$data['insert_by'] = $this->session->userdata('username');
            $data['insert_at'] = date('Y-m-d H-i-s');
	    	$save = $this->Main_Model->process_data('ms_warta', $data);
	    }

	    if($save){
	    	$alert = '<div class="alert alert-success alert-dismissible fade show" role="alert">
			              <strong>Sukses!</strong> Data berhasil tersimpan.
			              <button type="button" class="close" data-dismiss="alert" aria-label="Close">
			                <span aria-hidden="true">&times;</span>
			              </button>
			            </div>';
  			$this->session->set_flashdata('alert', $alert);
	    	redirect(base_url('master/ms_warta'), 'refresh');
	    }else{
	    	$alert = '<div class="alert alert-danger alert-dismissible fade show" role="alert">
			              <strong>Error!</strong> Data gagal tersimpan.
			              <button type="button" class="close" data-dismiss="alert" aria-label="Close">
			                <span aria-hidden="true">&times;</span>
			              </button>
			            </div>';
  			$this->session->set_flashdata('alert', $alert);
  			redirect(base_url('master/form_ms_warta/'.$id));
	    }
	}

	public function delete_ms_warta($id)
	{
		$data = array(
					"flag" => 0,
					"update_by" => $this->session->userdata('username'),
            		"update_at" => date('Y-m-d')
				);
		$hapus = $this->Main_Model->process_data("ms_warta", $data, ['id' => $id]);

		$alert = '<div class="alert alert-success alert-dismissible fade show" role="alert">
		              <strong>Sukses!</strong> Data berhasil dihapus.
		              <button type="button" class="close" data-dismiss="alert" aria-label="Close">
		                <span aria-hidden="true">&times;</span>
		              </button>
		            </div>';
		$this->session->set_flashdata('alert', $alert);
    	redirect(base_url('master/ms_warta'), 'refresh');
	}
}