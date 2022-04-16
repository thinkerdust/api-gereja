<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Warta extends CI_Controller {

	function __construct()
	{
		parent::__construct();
		$this->Main_Model->cek_login();
		$this->load->model('Warta_Model');
	}

	public function index()
	{
		$sidebar['sidebar'] = 'warta';
		$foot['js'] = 'master/warta';

		$data['alert'] = $this->session->flashdata('alert');

		$this->load->view('template/header');
		$this->load->view('template/sidebar', $sidebar);
		$this->load->view('master/warta', $data);
		$this->load->view('template/footer', $foot);
	}

	public function ajax_data_warta()
	{
		if($this->input->is_ajax_request()){
			$arr = [];
			$no = 1;
			$data = $this->db->order_by('insert_at', 'desc')->where('flag', 1)->get('warta')->result();
			if(!empty($data)){
				foreach($data as $row => $val){
					$action = '<a href="'.base_url().'warta/form_warta/'.$val->id.'" class="btn btn-warning">Edit</a>
									<a href="'.base_url().'warta/notif_warta/'.$val->id.'" class="btn btn-info">Notif</a>
									<a href="'.base_url().'warta/delete_warta/'.$val->id.'" class="btn btn-danger">Hapus</a>';

					$datetime = explode(' ', $val->tanggal);
					$time = date('H:i', strtotime($datetime[1]));

					$arr[$row] = array(
									$no++,
									local_date_format($datetime[0]).' '.$time,
									$this->Main_Model->get_nama($val->leader),
									'Singer 1 :'.$this->Main_Model->get_nama($val->singer1).'
									<br> Singer 2 :'.$this->Main_Model->get_nama($val->singer2),
									$this->Main_Model->get_nama($val->koordinator),
									$val->usher,
									$val->kolektan,
									$val->petugas_lcd,
									$val->termo_gun,
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

	public function form_warta($id = '')
	{
		$sidebar['sidebar'] = 'warta';

		$data['data'] = $this->Main_Model->view_by_id('warta', ['id'=>$id]);
		$data['jemaat'] = $this->Main_Model->view_by_id('jemaat', ['flag' => 1], 'result');
		$data['ms_warta'] = $this->Main_Model->view_by_id('ms_warta', ['flag' => 1], 'result');
		$data['musik'] = $this->Main_Model->view_by_id('tim_musik', ['id_warta' => $id]);
		$data['alert'] = $this->session->flashdata('alert');
		$warta_detail = $this->Main_Model->view_by_id('warta_detail', ['id_warta' => $id], 'result');

		$arr_wd = [];
		if(!empty($warta_detail)){
			foreach($warta_detail as $row) {
				$arr_wd[] = $row->id_ms_warta;
			}
		}

		$data['warta_detail'] = $arr_wd;

		$this->load->view('template/header');
		$this->load->view('template/sidebar', $sidebar);
		$this->load->view('master/form_warta', $data);
		$this->load->view('template/footer');
	}

	public function store_warta()
	{
		$id = $this->input->post('id');
		$tanggal = $this->input->post('tanggal');
		$tanggal = date('Y-m-d H:i:s', strtotime($tanggal));
		$leader = $this->input->post('leader');
		$singer1 = $this->input->post('singer1');
		$singer2 = $this->input->post('singer2');
		$koordinator = $this->input->post('koordinator');
		$petugas_lcd = $this->input->post('petugas_lcd');
		$kolektan = $this->input->post('kolektan');
		$termo_gun = $this->input->post('termo_gun');
		$usher = $this->input->post('usher');
		$gitar = $this->input->post('gitar');
		$keyboard = $this->input->post('keyboard');
		$bass = $this->input->post('bass');
		$drum = $this->input->post('drum');
		$group = $this->input->post('group');
		$pokok_doa = $this->input->post('pokok_doa');

		$this->db->trans_start();

		$data = array(
			'tanggal' => $tanggal,
			'leader' => $leader,
			'singer1' => $singer1,
			'singer2' => $singer2,
			'koordinator' => $koordinator,
			'petugas_lcd' => $petugas_lcd,
			'usher' => $usher,
			'kolektan' => $kolektan,
			'termo_gun' => $termo_gun,
		);

		if(!empty($id))
		{
			$data['update_by'] = $this->session->userdata('username');
					$data['update_at'] = date('Y-m-d H-i-s');
			$save = $this->Main_Model->process_data('warta', $data, ['id' => $id]);
			$id_warta = $id;
		}else{
			$data['insert_by'] = $this->session->userdata('username');
					$data['insert_at'] = date('Y-m-d H-i-s');
			$save = $this->Main_Model->process_data('warta', $data);
			$id_warta = $save;
		}

		$this->db->delete('tim_musik', ['id_warta' => $id_warta]);
		$this->db->delete('warta_detail', ['id_warta' => $id_warta]);
		$this->db->delete('notif_approval_warta', ['id_warta' => $id_warta]);

		$tim_musik = array(
			'id_warta' => $id_warta,
			'gitar' => $gitar,
			'bass' => $bass,
			'keyboard' => $keyboard,
			'drum' => $drum,
			'group' => $group
		);

		$this->db->insert('tim_musik', $tim_musik);

		$data_pokok_doa = [];
		foreach($pokok_doa as $row){
			$data_pokok_doa[] = array(
								'id_warta' => $id_warta,
								'id_ms_warta' => $row,
							);
		}

		$this->db->insert_batch('warta_detail', $data_pokok_doa);

		$tgl_notif = date('Y-m-d', strtotime($tanggal));
		$tgl_notif = date('Y-m-d H:i:s', strtotime($tgl_notif.' -4 day'.' 19:00:00'));

		$data_notif = [];
		$data_notif[] = array(
								'id_warta' => $id_warta,
								'tgl_notif' => $tgl_notif,
								'nij' => $leader,
								'posisi' => 'Worship Leader',
								'kolom' => 'leader',
								'tabel' => 'warta',
							);

		$data_notif[] = array(
								'id_warta' => $id_warta,
								'tgl_notif' => $tgl_notif,
								'nij' => $singer1,
								'posisi' => 'Singer 1',
								'kolom' => 'singer1',
								'tabel' => 'warta',
							);

		$data_notif[] = array(
								'id_warta' => $id_warta,
								'tgl_notif' => $tgl_notif,
								'nij' => $singer2,
								'posisi' => 'Singer 2',
								'kolom' => 'singer2',
								'tabel' => 'warta',
							);

		$data_notif[] = array(
								'id_warta' => $id_warta,
								'tgl_notif' => $tgl_notif,
								'nij' => $koordinator,
								'posisi' => 'Koordinator',
								'kolom' => 'koordinator',
								'tabel' => 'warta',
							);

		$data_notif[] = array(
								'id_warta' => $id_warta,
								'tgl_notif' => $tgl_notif,
								'nij' => $gitar,
								'posisi' => 'Team Musik - Gitar',
								'kolom' => 'gitar',
								'tabel' => 'tim_musik',
							);

		$data_notif[] = array(
								'id_warta' => $id_warta,
								'tgl_notif' => $tgl_notif,
								'nij' => $bass,
								'posisi' => 'Team Musik - Bass',
								'kolom' => 'bass',
								'tabel' => 'tim_musik',
							);

		$data_notif[] = array(
								'id_warta' => $id_warta,
								'tgl_notif' => $tgl_notif,
								'nij' => $drum,
								'posisi' => 'Team Musik - Drum',
								'kolom' => 'drum',
								'tabel' => 'tim_musik',
							);

		$data_notif[] = array(
								'id_warta' => $id_warta,
								'tgl_notif' => $tgl_notif,
								'nij' => $keyboard,
								'posisi' => 'Team Musik - Keyboard',
								'kolom' => 'keyboard',
								'tabel' => 'tim_musik',
							);

		$this->db->insert_batch('notif_approval_warta', $data_notif);

		$this->db->trans_complete();

		if($this->db->trans_status() === TRUE){
				$alert = '<div class="alert alert-success alert-dismissible fade show" role="alert">
										<strong>Sukses!</strong> Data berhasil tersimpan.
										<button type="button" class="close" data-dismiss="alert" aria-label="Close">
											<span aria-hidden="true">&times;</span>
										</button>
									</div>';
				$this->session->set_flashdata('alert', $alert);
				redirect(base_url('warta'), 'refresh');
		}else{
				$alert = '<div class="alert alert-danger alert-dismissible fade show" role="alert">
										<strong>Error!</strong> Data gagal tersimpan.
										<button type="button" class="close" data-dismiss="alert" aria-label="Close">
											<span aria-hidden="true">&times;</span>
										</button>
									</div>';
				$this->session->set_flashdata('alert', $alert);
				redirect(base_url('warta/form_warta/'.$id));
			}
	}

	public function delete_warta($id)
	{
		$data = array(
					"flag" => 0,
					"update_by" => $this->session->userdata('username'),
					"update_at" => date('Y-m-d')
				);
		$hapus = $this->Main_Model->process_data("warta", $data, ['id' => $id]);

		$alert = '<div class="alert alert-success alert-dismissible fade show" role="alert">
									<strong>Sukses!</strong> Data berhasil dihapus.
									<button type="button" class="close" data-dismiss="alert" aria-label="Close">
										<span aria-hidden="true">&times;</span>
									</button>
								</div>';
		$this->session->set_flashdata('alert', $alert);
		redirect(base_url('warta'), 'refresh');
	}

	public function notif_warta($id)
	{
		$notif = $this->Warta_Model->send_notif_warta($id);
		$alert = '<div class="alert alert-success alert-dismissible fade show" role="alert">
							<strong>Sukses!</strong> Push Notif berhasil.
							<button type="button" class="close" data-dismiss="alert" aria-label="Close">
								<span aria-hidden="true">&times;</span>
							</button>
						</div>';
		$this->session->set_flashdata('alert', $alert);
			redirect(base_url('warta'), 'refresh');
	}
}