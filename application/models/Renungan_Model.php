<?php

class Renungan_Model extends CI_Model {

	function view_renungan($start=0, $count=0, $id='')
	{
		$condition = '';
		if(!empty($id)) {
			$condition = " AND id = $id";
		}

		$limit = '';
		if($count > 0){
            $limit = " LIMIT $start,$count";
        }

        $path_file = base_url().'assets/upload/images/';

		$query = $this->db->query("
					SELECT *, concat('$path_file', image) as file_path
					from renungan
					where flag = 1
					$condition
					order by id desc
					$limit
				")->result();

		return $query;
	}

}