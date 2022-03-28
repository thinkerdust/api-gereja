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

	function view_scan_log($date_start = '', $date_end = '', $user_id = 0) 
	{
		$data = [];
		$condition = '';
		if(!empty($id)) {
			$condition = " AND user_id = $user_id";
		}
		if($date_start && $date_end){
			$data = $this->db->query("SELECT * 
						from log_attendance
						where (date(check_in) between '$date_start' and '$date_end') $condition ")->result();
		}

		return $data;
	}

}