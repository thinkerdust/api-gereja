<?php

class Main_Model extends CI_Model {

    function cek_login()
    {
        if(empty($this->session->userdata('login')))
        {
            redirect(base_url('auth'));
        }
    }
	
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

    function delete_data($table = '', $condition = '')
    {
        $this->db->where($condition)->delete($table);
        return $this->db->affected_rows();
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
        $url = "https://lmao.my.id/api/chats/send";

        $api_key = '466f834b8184a63783201fffdf6723f227d8695aabf624';
        $device_id = 179;
        $authorization = "Authorization: Bearer ".$api_key;

        $phone = $number;
        $phone = preg_replace('/\D/', '', $phone);
        $index = strpos($phone,'0');
        if($index == 0) {
            $phone = substr_replace($phone,'62',0,1);
        }

        $data = [
            "device_id"     => $device_id,
            "phone"         => $phone,
            "message"       => ["text" => $message]
        ];

        $data = json_encode($data, FALSE);

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', $authorization));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        $result = curl_exec($ch);
        curl_close($ch);
        
        return json_decode($result);
    }

    function log_message($number = 0, $message = '', $status = 0)
    {
        if(!empty($status)) {
            $status = 'success';
        }else{
            $status = 'failed';
        }
        
        $data = array(
                    "number" => $number,
                    "message" => $message,
                    "status" => $status,
                );

        $this->db->insert("log_message", $data);
    }

    function get_nij()
    {
        $data = $this->db->order_by('insert_at', 'DESC')->get('jemaat')->row();
        if(!empty($data)){
            $set_no = (int) $data->nij + 1;
            if (strlen($set_no) == 1) {
                $counter = "0000" . $set_no;
            } elseif (strlen($set_no) == 2) {
                $counter = "000" . $set_no;
            } elseif (strlen($set_no) == 3) {
                $counter = "00" . $set_no;
            } elseif (strlen($set_no) == 4) {
                $counter = "0" . $set_no;
            } else {
                $counter = $set_no;
            }
        }else{
            $counter = '00001';
        }

        return $counter;
    }

    function get_nama($nij = '')
    {
        $data = $this->view_by_id('jemaat', ['nij' => $nij, 'flag' => 1]);
        return $data->nama; 
    }

    function get_fcm($nij = '')
    {
        $data = $this->view_by_id('user', ['nij' => $nij, 'fcm_id !=' => '', 'flag' => 1]);
        if(!empty($data)) {
            return $data->fcm_id; 
        }else{
            return 0;
        }
    }

    function nij_user($token = '')
    {
        $data = $this->view_by_id('user', ['fcm_id' => $token, 'flag' => 1]);
        if(!empty($data)) {
            return $data->nij; 
        }else{
            return 0;
        }
    }

    function profile($nij = '')
    {
        $data = $this->view_by_id('profil', ['nij' => $nij]);
        if(!empty($data)) {
            return $data; 
        }else{
            return 0;
        }
    }

}