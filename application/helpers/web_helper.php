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
?>