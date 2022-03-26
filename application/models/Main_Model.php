<?php

class Main_Model extends CI_Model {
	
	function process_data($table='', $data='', $condition='') 
    {
        if($condition) {
            $this->db->where($condition)->update($table, $data);
            return $this->db->affected_rows();
        } else {
            $this->db->insert($table, $data);
            return $this->db->insert_id();
        }
    }

    function view_by_id($table='',$condition='',$row='row')
    {
        if($row == 'row') {
            if($condition) {
                return $this->db->where($condition)->get($table)->row();
            } else {
                return $this->db->get($table)->row();
            }
        } else if($row == 'result') {
            if($condition) {
                return $this->db->where($condition)->get($table)->result();
            } else {
                return $this->db->get($table)->result();
            }
        } else if($row == 'num_rows') {
            if($condition) {
                return $this->db->where($condition)->get($table)->num_rows();
            } else {
                return $this->db->get($table)->num_rows();
            }
        }
    }

    function send_message($number, $message = 'hello')
    {
        $url = "https://lmao.my.id/api/message";

        $api_key = '466f834b8184a63783201fffdf6723f227d8695aabf624';
        $device_id = 157;

        $phone = $number;
        $phone = preg_replace('/\D/', '', $phone);
        $index = strpos($phone,'0');
        if($index == 0) {
            $phone = substr_replace($phone,'62',0,1);
        }



        $data = [
            "device_id"     => $device_id,
            "api_key"       => $api_key,
            "phone"         => $phone,
            "message"       => $message
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,$url);
        curl_setopt($ch, CURLOPT_POST,1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
        curl_setopt($ch, CURLOPT_VERBOSE,true);
        $result = curl_exec ($ch);
        curl_close($ch);
        return json_decode($result);
    }

}