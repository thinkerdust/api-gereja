<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Master extends CI_Controller {

	function __construct()
	{
		parent::__construct();
		$this->Main_Model->cek_login();
	}

	public function jemaat()
	{
		$sidebar['sidebar'] = 'jemaat';
		$foot['js'] = 'master/jemaat';

		$data['alert'] = $this->session->flashdata('alert');
		$data['data'] = '';

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
            		$action = '<a href="'.base_url().'master/form_jemaat/'.$val->nij.'" class="btn btn-warning">Edit</a>
                        <a href="'.base_url().'master/delete_jemaat/'.$val->nij.'" class="btn btn-danger">Hapus</a>';

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

		if(!empty($nij))
	    {
	    	$data['update_by'] = $this->session->userdata('username');
            $data['update_at'] = date('Y-m-d');
	    	$save = $this->Main_Model->process_data('jemaat', $data, ['nij' => $nij]);
	    }else{
	    	$data['nij'] = $this->Main_Model->get_nij();
	    	$data['insert_by'] = $this->session->userdata('username');
            $data['insert_at'] = date('Y-m-d');
	    	$save = $this->Main_Model->process_data('jemaat', $data);
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
  			redirect(base_url('master/form_jemaat'));
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
}