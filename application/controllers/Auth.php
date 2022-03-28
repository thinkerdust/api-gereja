<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Auth extends CI_Controller {

	public function index()
	{
		$this->load->view('login');
	}

	public function login()
	{
		$username  = $this->input->post('username');
		$password  = $this->input->post('password');

		$get_user = $this->Main_Model->view_by_id('user', ['username' => $username, 'user_level' => 1]);
		if(!empty($get_user)) {

			$user_name = $get_user->username;
			$user_pass = $get_user->password;
			
			if ((password_verify($password, $user_pass)) && ($username == $user_name)) {
				$session_id = session_id();
	        	$session = array('session' => $session_id, 'username' => $username, 'login' => TRUE);
				$this->session->set_userdata($session);
				redirect(base_url('master/jemaat'));
	        } else {
	            $this->session->set_flashdata('message', 'Username atau Password anda salah.');
				redirect(base_url('auth'));
	        }
	    }else{
	    	$this->session->set_flashdata('message', 'Anda Tidak Memiliki Akses.');
			redirect(base_url('auth'));
	    }
	}

	public function logout()
    {
        $this->session->sess_destroy();
        redirect(base_url('auth'));
    }

}