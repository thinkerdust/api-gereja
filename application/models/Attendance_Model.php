<?php

class Attendance_Model extends CI_Model {

	function view_lokasi($id = 0)
	{
		$condition = '';
		if(!empty($id)) {
			$condition = " AND id = $id";
		}

        $path_file = base_url().'assets/qrcode/';

		$query = $this->db->query("
					SELECT *, concat('$path_file', file_qr) as file_path
					from lokasi
					where 1=1
					$condition
				")->row();

		return $query;
	}

}