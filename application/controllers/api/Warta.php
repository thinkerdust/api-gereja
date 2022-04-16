<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Warta extends CI_Controller {

	function __construct(){
		parent::__construct();
		$this->load->model('Warta_Model');
	}

	function index()
	{
		$auth = $this->token->auth('GET', true);
		if($auth) {
			$response = $this->Warta_Model->view_warta();

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

	function view_approval()
	{
		$auth = $this->token->auth('POST', true);
		if($auth) {
			$response = [];
			$params = get_params();
			$id_warta = isset($params['id_warta']) ? $params['id_warta'] : '';
			$nij = isset($params['nij']) ? $params['nij'] : '';
			$notif = $this->Main_Model->view_by_id('notif_approval_warta', ['flag' => 1, 'nij' => $nij, 'id_warta' => $id_warta]);
			$warta = $this->Main_Model->view_by_id('warta', ['flag' => 1, 'id_warta' => $id_warta]);
			$datetime = explode(' ', $warta->tanggal);
			$time = date('H:i', strtotime($datetime[1]));

			if(!empty($notif)) {
				$response = array(
							'id_warta' => $id_warta,
							'tgl_warta' => local_date_format($datetime[0]).' '.$time,
							'posisi' => $notif->posisi,
							'nij' => $notif->nij,
							'nama' => get_nama($notif->nij),
						);

				$status = 200;
				$message = 'Data Ditemukan';
			}else{
				$status = 404;
				$message = 'Data Tidak Ditemukan';
			}


			print_json($status,$message,$response);
		}
	}

	function approval()
	{
		$auth = $this->token->auth('POST', true);
		if($auth) {
			$response = [];
			$params = get_params();
			$id_warta = isset($params['id_warta']) ? $params['id_warta'] : '';
			$nij = isset($params['nij']) ? $params['nij'] : '';
			$nij_kandidat = isset($params['nij_kandidat']) ? $params['nij_kandidat'] : '';
			$approval = isset($params['approval']) ? $params['approval'] : '';
			$alasan = isset($params['alasan']) ? $params['alasan'] : '';

			if($nij && $approval){
				$get_notif = $this->Main_Model->view_by_id('notif_approval_warta', ['flag' => 1, 'nij' => $nij, 'id_warta' => $id_warta]);
				$kolom = $get_notif->kolom;
				$tabel = $get_notif->tabel;
				$warta = $this->Main_Model->view_by_id('warta', ['flag' => 1, 'id_warta' => $id_warta]);
				$datetime = explode(' ', $warta->tanggal);
				$time = date('H:i', strtotime($datetime[1]));

				$cond = '';
				if(trim($tabel) != 'warta') {
					$cond = 'warta';
				}
				// 0:new ; 1: reject ; 2:approve
				if($approval == 2) {
					$data = ['approval_'.trim($kolom) => $approval];
				}else{
					if($nij_kandidat) {
						if($alasan){
							$data = [
										trim($kolom) => $nij_kandidat,
										'approval_'.trim($kolom) => $approval,
										trim($kolom).'_nij_reject' => $nij,
										trim($kolom).'_reason_reject' => $alasan,
									];

						}

						$fcm = get_fcm($nij_kandidat);
						$nama = get_nama($nij);
						$nama_kandidat = get_nama($nij_kandidat);

						$data_notif = array(
								'id_warta' => $id_warta,
								'tgl_warta' => local_date_format($datetime[0]).' '.$time,
								'posisi' => $get_notif->posisi,
								'nij_kandidat' => $nij_kandidat,
								'nama_kandidat' => $nama_kandidat,
								'nama' => $nama,
								'nij' => $nij,
								'flag_approval' => 1
							);

						$this->Warta_Model->notif_reject_warta($fcm, $nama, $data_notif);

						$this->Main_Model->process_data('notif_approval_warta', ['nij' => $nij_kandidat],['id_warta' => $id_warta, 'posisi' => $posisi]);

						$status = 200;
		                $message = 'Data berhasil disimpan';
		                $response = $data_notif;

					}else{
						$status = 400;
						$message = 'Alasan / Kandidat Tidak Boleh Kosong';
					}
				}

				$save = $this->Main_Model->process_data(trim($tabel), $data, 'id_'.$cond);

				if($save){
	                $status = 200;
	                $message = 'Data berhasil disimpan';
	                $response = $data;
	            }else{
	                $status = 404;
	                $message = 'Gagal Menyimpan data!';
	            }
			}else{
				$status = 400;
				$message = 'NIJ / Approval Tidak Boleh Kosong';
			}

			print_json($status,$message,$response);
		}
	}

}