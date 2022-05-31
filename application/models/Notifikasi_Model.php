<?php

class Notifikasi_Model extends CI_Model {

	function send_notif($id = 0, $flag = '')
	{
		$user = $this->db->where(['flag' => 1, 'fcm_id !=' => ''])->get('user')->result();
		if(!empty($user)){

			foreach($user as $row){
				$token = $row->fcm_id;
				$flag = trim(strtolower($flag));
				$table = $this->db->where('id', $id)->get($flag)->row();
				$title = $table->title;
				$body = $table->body;
				$image = base_url().'assets/upload/images/'.$table->image;
				$data = array(
							'id' => $table->id,
							'title' => $title,
							'body' => $body,
							'images' => $table->image,
							'path_file' => $image,
						);

				$this->customcurl->fcm($flag,$token,$title,$body,$image,$data);
			}
		}
	}

	function get_birthday()
	{
		$path_file = base_url().'assets/upload/images/';
		$start_date = date('Y-m-d');
		$end_date = date('Y-m-t');

		$data = $this->db->query("SELECT p.*, concat('$path_file', p.photo) as file_path
								from profil p
								where p.tgl_lahir between '$start_date' and '$end_date' ")->result();
		return $data;
	}

}