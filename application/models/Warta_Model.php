<?php

class Warta_Model extends CI_Model {

	function view_warta()
	{
        $result = [];

        $warta = $this->db->query("
        			SELECT w.*, tm.gitar, tm.bass, tm.keyboard, tm.drum, tm.group 
        			FROM warta w
        			left join tim_musik tm on w.id = tm.id_warta
        			where w.flag = 1
        			order by w.insert_at desc
        			limit 1
        		")->row();

        if(!empty($warta)) {
        	$data_warta = [];
        	$id_warta = $warta->id;
    		$arr_warta = array(
    					'id_warta' => $id_warta,
    					'worship_leader' => get_nama($warta->leader),
    					'singer1' => get_nama($warta->singer1),
    					'singer2' => get_nama($warta->singer2),
    					'koordinator' => get_nama($warta->koordinator),
    					'kolektan' => $warta->kolektan,
    					'usher' => $warta->usher,
    					'petugas_lcd' => $warta->petugas_lcd,
    					'termo_gun' => $warta->termo_gun,
    					'tim_musik' => !empty($warta->group) ? $warta->group : '-',
    				);

        	$get_doa = $this->db->query("
        					SELECT mw.id, mw.judul, mw.deskripsi
    						from warta_detail wd
    						join ms_warta mw on wd.id_ms_warta = mw.id
    						where wd.id_warta = $id_warta
        					")->result();

    		$arr_doa = [];
    		if(!empty($get_doa)){
    			foreach($get_doa as $doa) {
    				$pokok_doa = array(
    						'id_doa' => $doa->id,
    						'judul' => $doa->judul,
    						'isi' => $doa->deskripsi
    					);
    				$arr_doa[] = $pokok_doa;
    			}
    		}
    		$arr_warta['pokok_doa'] = $arr_doa;

    		$data_warta['data_warta'] = $arr_warta;
    		$result = $data_warta;
        }

        return $result;
	}

	function send_notif_warta($id = 0)
	{
		$jemaat = $this->db->where(['flag' => 1, 'fcm_id !=' => ''])->get('user')->result();
		if (!empty($jemaat)) {
			foreach($jemaat as $row) {
				$fcm = $row->fcm_id;
				$warta = 
				

				$this->customcurl->fcm('warta',$fcm,$title,$body,$image,$data);
			}
		}
	}

}