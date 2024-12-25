<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Login extends CI_Controller {
	
	
	function __construct(){
		parent::__construct();
	}

	public function index()
	{
		if( $this->user->data != NULL ) redirect('user/'.$this->user->data->username);
		
		$data['_title'] =  "ورود";

        //======================================//
        $this->bc->add('ورود کاربران',site_url("login"));
        //======================================//

        $this->tools->view('user/v_login',$data);
        
        $this->logs->addView('login');
	}
}