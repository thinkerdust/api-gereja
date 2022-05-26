<?php

class Setting_Model extends CI_Model {

	function view_profil($user_id)
	{
		$condition = '';
		if(!empty($user_id)) {
			$condition = " AND p.user_id = $user_id";
		}

        $path_file = base_url().'assets/upload/images/';

		$query = $this->db->query("
					SELECT p.*, concat('$path_file', p.photo) as file_path, u.nij
					from profil p
					join user u on p.user_id = u.id
					where 1=1
					$condition
				")->row();

		return $query;
	}

}