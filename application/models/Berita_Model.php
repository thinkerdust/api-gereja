<?php

class Berita_Model extends CI_Model {

	function view_berita($start=0, $count=0, $id='')
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
					from berita
					where flag = 1
					$condition
					$limit
				")->result();

		return $query;
	}

}