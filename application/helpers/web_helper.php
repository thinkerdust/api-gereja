<?php 
    function response($status = 200, $message = '', $data = [])
    {
        return array(
            'response' => $data,
            'metadata' => array(
                'status' => $status,
                'message' => $message
            )
        );
    }

    function print_json($status = 200, $message = '', $data = [])
    {
        $ci =& get_instance();
        $response = response($status, $message, $data);

        return $ci->token->print_json($response);
    }

    function get_params()
    {
        return json_decode(file_get_contents('php://input'), true);
    }

    function user_level($id)
    {
        $data = array( 1 => 'Admin', 
                        2 => 'Pendeta',
                        3 => 'Pelayan',
                        4 => 'Jemaat');

        return $data[$id];

    }

    function local_date_format($datawaktu) {
        $waktu = explode('-',$datawaktu);
        switch($waktu[1])
        {
            case "01":  $bulan = "Januari";    break;
            case "02":  $bulan = "Februari";   break;
            case "03":  $bulan = "Maret";     break;
            case "04":  $bulan = "April";     break;
            case "05":  $bulan = "Mei";       break;
            case "06":  $bulan = "Juni";      break;
            case "07":  $bulan = "Juli";      break;
            case "08":  $bulan = "Agustus";    break;
            case "09":  $bulan = "September"; break;
            case "10":  $bulan = "Oktober";   break;
            case "11":  $bulan = "November";  break;
            case "12":  $bulan = "Desember";  break;
            default:    $bulan = "Unknown";   break;
        }
        
        return $waktu[2]." ".$bulan." ".$waktu[0];
    }

    function get_nama($nij = '')
    {
        $ci =& get_instance();
        $nama = $ci->Main_Model->get_nama($nij);
        return $nama;
    }

    function get_fcm($nij = '')
    {
        $ci =& get_instance();
        $nama = $ci->Main_Model->get_fcm($nij);
        return $nama;
    }
?>