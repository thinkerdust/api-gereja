<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Token
{
	protected $ci;

    function __construct()
    {
        $this->ci =& get_instance();
    }

    // checking method request of client
    function check_method($method = 'GET')
    {
        $request_method = $_SERVER['REQUEST_METHOD'];
        if ($request_method == $method) {
            return true;
        } else {
            return false;
        }
    }

    // check token
    function check_token()
    {
        $username = $this->ci->input->get_request_header('Username', true);
        $token = $this->ci->input->get_request_header('Token', true);

        $query = $this->ci->db->query("
			SELECT *
			FROM user_token a
			WHERE a.`token` = '$token'
			AND a.`username` = '$username' ")->row();

        if ($query) {
            return true;
        } else {
            return false;
        }
    }

    function generate_signature($username = '', $timestamp = '')
    {
        $signature = hash_hmac('sha256', $username.'&'.$timestamp, $username.'die', true);

        return base64_encode($signature);
    }

    // create token
    function create_token($username = '')
    {
        $timestamp = date('YmdHis');
        $encoded_signature = $this->generate_signature($username, $timestamp);

        // users_id
        if ($username == '') {
            $username = $this->ci->input->get_request_header('Username', true);
        }

        $data = array(
            'username' => $username,
            'token' => $encoded_signature,
        );

        # delete token
        $this->ci->db->delete('user_token', ['username' => $username]);
        $simpan = $this->ci->db->insert('user_token', $data);
        if ($simpan > 0) {
            return $data;
        } else {
            return false;
        }
    }

    // auth
    function auth($method = 'GET', $flag = true)
    {
        // check method request dan method yg ditentukan
        $check_method = $this->check_method($method);
        if ($check_method == true) {
            if ($flag == true) {
                // check token 
                $check_token = $this->check_token();
                if ($check_token == true) {
                    return true;
                } else {
                    $response = array(
                        'response' => [],
                        'metadata' => array(
                            'status' => 401,
                            'message' => 'Token anda salah'
                        )
                    );

                    return $this->print_json($response);
                }
            } else {
                return true;
            }
        } else {
            $response = array(
                'response' => [],
                'metadata' => array(
                    'status' => 405,
                    'message' => 'Method not allowed'
                )
            );

            return $this->print_json($response);
        }
    }

    // fungsi untuk mengeluarkan output json
    function print_json($response = '', $statusHeader = 200)
    {
        $ci =& get_instance();
        $ci->output->set_content_type('application/json');
        $ci->output->set_status_header($statusHeader);
        $ci->output->set_output(json_encode($response));
    }
}