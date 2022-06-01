<?php

class Sharing_Model extends CI_Model {

	function view_sharing($start=0, $count=0)
	{
		$limit = '';
		if($count > 0){
            $limit = " LIMIT $start,$count";
        }
		$path_file = base_url().'assets/upload/images/';

		$sharing = $this->db->query("SELECT * from sharing s
						where flag = 1
						$limit")->result();

		$data = [];

		if(!empty($sharing)) {
			foreach($sharing as $row){
				$profil = profile($row->nij);
				$foto = '';
				if(!empty($profil)) {
					$foto = $path_file.$profil->photo;
				}

				$data[] = [
							"id" => $row->id,
							"nij" => $row->nij,
							"deskripsi" => $row->deskripsi,
							"insert_at" => $row->insert_at,
							"insert_by" => $row->insert_by,
							"foto_profil" => $foto,
						];

				$files = $this->db->where("id_sharing", $row->id)->get("file_sharing")->result();
				if(!empty($files)) {
					$arr_files = [];
					foreach($files as $key) {
						$arr_files[] = $path_file.$key->filename;
					}

					$data[]["files"] = $arr_files;
				}
			}
		}
		return $data;
	}
}