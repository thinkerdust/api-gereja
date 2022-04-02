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
}