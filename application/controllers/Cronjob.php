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
							$title = 'Jadwal Pelayanan';
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

			// log cronjob
			$this->Main_Model->process_data('log_cronjob', ['function' => 'cronjob/send_approval_warta']);
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

			// log cronjob
			$this->Main_Model->process_data('log_cronjob', ['function' => 'cronjob/change_flag']);
		}
	}

	function notif_birthday()
	{
		$date = date('m-d');
		$data = $this->Main_Model->view_by_id('profil', [DATE_FORMAT('tgl_lahir', '%m-%d') => $date], 'result');

		if(!empty($data)) {
			foreach($data as $key) {
				$title = 'Happy Birthday '.$key->nama;
				$body = 'Semoga kamu selalu dalam lindungan Tuhan Yesus. Doa terbaik dari kami, kami panjatkan untukmu. Happy birthday.';
				$user = $this->Main_Model->view_by_id('user', ['flag' => 1, 'fcm_id !=' => ''], 'result');
				foreach ($user as $row) {
					$token = $row->fcm_id;
					$this->customcurl->fcm('notif',$token,$title,$body);
				}
			}

			// log cronjob
			$this->Main_Model->process_data('log_cronjob', ['function' => 'cronjob/notif_birthday']);
		}
	}
}
