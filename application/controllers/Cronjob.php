<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Cronjob extends CI_Controller {

	function send_approval_warta()
	{
		$now = date('Y-m-d H:i:s');
		$warta = $this->Main_Model->view_by_id('warta', ['flag' => 1], 'result');
		if(!empty($warta)){
			foreach($warta as $key){
				$id_warta = $key->id;
				$datetime = explode(' ', $key->tanggal);

				$tgl_warta = local_date_format($datetime[0]);
				$notif = $this->Main_Model->view_by_id('notif_approval_warta', ['id_warta' => $id_warta, 'flag' => 1, 'tgl_notif <=' => $now], 'result');
				if(!empty($notif)) {
					foreach($notif as $nf) {
						$token = get_fcm($nf->nij);
						if(!empty($token)){
							$title = 'Jawal Pelayanan';
							$body = 'Anda terjadwal pada pelayanan '.$tgl_warta.' segera konfirmasi kesedian anda';

							$data = array(
										'id_warta' => $id_warta,
										'tgl_warta' => $tgl_warta,
										'posisi' => $nf->posisi,
										'kolom' => $nf->kolom,
										'tabel' => $nf->tabel,
										'nij' => $nf->nij,
										'nama' => get_nama($nf->nij),
									);

							$this->customcurl->fcm('warta',$token,$title,$body,'',$data);

						}
					}
					$this->Main_Model->process_data('notif_approval_warta', ['flag' => 0], ['id_warta' => $id_warta]);
				}
			}
		}
	}

	function change_flag()
	{
		$warta = $this->Main_Model->view_by_id('warta', ['flag'=>1], 'result');

		if(!empty($warta)){
			foreach($warta as $key){
				$tgl = $key->tanggal;
				$now = date('Y-m-d H:i:s');

				if($tgl < $now){
					$this->Main_Model->process_data('warta', ['flag' => 0], ['id' => $key->id]);
				}
			}
		}
	}
}
