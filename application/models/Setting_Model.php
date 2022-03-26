<?php

class Setting_Model extends CI_Model {

	function view_profil($user_id)
	{
		$condition = '';
		if(!empty($user_id)) {
			$condition = " AND user_id = $user_id";
		}

        $path_file = base_url().'assets/upload/images/';

		$query = $this->db->query("
					SELECT *, concat('$path_file', photo) as file_path
					from profil
					where 1=1
					$condition
				")->row();

		return $query;
	}

}