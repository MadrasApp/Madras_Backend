<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Logout extends CI_Controller {
	
	function __construct(){
		
		parent::__construct();
		$this->load->model('m_user','user');
	}
	
	public function index()
	{
		$this->user->logout();
		redirect('admin/login');
	}
}
