<?php

class Sharing_Model extends CI_Model {

	function view_sharing($start=0, $count=0, $nij="")
	{
		$sess = profil_login();
		$limit = '';
		if($count > 0){
            $limit = " LIMIT $start,$count";
        }
		$path_file = base_url().'assets/upload/images/';

		$condition = '';
		if(!empty($nij)) {
			$condition .= " AND nij = '$nij'";
		}

		$sharing = $this->db->query("SELECT * from sharing s
						where flag = 1
						$condition
						order by id desc
						$limit")->result();

		$data = [];

		if(!empty($sharing)) {
			foreach($sharing as $row){
				$profil = profile($row->nij);
				$foto = '';
				if(!empty($profil)) {
					$foto = $path_file.$profil->photo;
				}


				$files = $this->db->where("id_sharing", $row->id)->get("file_sharing")->result();
				$arr_files = [];
				foreach($files as $key) {
					$arr_files[] = $path_file.$key->filename;
				}

				// count like
				$jml_like = $this->db->where("id_sharing", $row->id)->get("like_sharing")->num_rows();

				// count comment
				$jml_comment = $this->db->where("id_sharing", $row->id)->get("comment_sharing")->num_rows();

				// status like by nij login
				$flag_like = $this->db->where(["id_sharing" => $row->id, "nij" => $sess->nij])->get("like_sharing")->row();

				$data[] = [
							"id" => $row->id,
							"nij" => $row->nij,
							"deskripsi" => $row->deskripsi,
							"jml_comment" => $jml_comment,
							"jml_like" => $jml_like,
							"flag_like" => (empty($flag_like)) ? false:true,
							"insert_at" => $row->insert_at,
							"insert_by" => $row->insert_by,
							"foto_profil" => $foto,
							"files" => $arr_files
						];
			}
		}
		return $data;
	}

	function view_comment($id_sharing = 0)
	{
		$path_file = base_url().'assets/upload/images/';

		$query = $this->db->query("
					SELECT cs.*, concat('$path_file', p.photo) as photo
					from comment_sharing cs
					left join profil p on cs.nij = p.nij
					where cs.id_sharing = $id_sharing
					order by id desc
					")->result();

		return $query;
	}

	function send_notif_sharing($id = 0)
	{
		$user = $this->db->where(['flag' => 1, 'fcm_id !=' => ''])->get('user')->result();
		if(!empty($user)){

			foreach($user as $row){
				$token = $row->fcm_id;
				$table = $this->db->where('id', $id)->get('sharing')->row();
				$profil = profile($table->nij);
				$title = $profil->nama." mengUpload postingan";
				$body = $table->deskripsi;

				$this->customcurl->fcm('sharing',$token,$title,$body);
			}
		}
	}

	function send_notif_comment($id = 0)
	{
		$user = $this->db->where(['flag' => 1, 'fcm_id !=' => ''])->get('user')->result();
		if(!empty($user)){

			foreach($user as $row){
				$token = $row->fcm_id;
				$table = $this->db->where('id', $id)->get('comment_sharing')->row();
				$profil = profile($table->nij);

				$sharing = $this->Main_Model->view_by_id('sharing', ['id' => $id]);
				$profil_post = profile($sharing->nij);

				$title = $profil->nama." Mengomentari postingan ".$profil_post->nama;
				$body = $table->comment;

				$this->customcurl->fcm('comment_sharing',$token,$title,$body);
			}
		}
	}
}