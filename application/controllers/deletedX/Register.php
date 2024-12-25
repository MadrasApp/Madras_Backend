<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Register extends CI_Controller {
	
	
	function __construct(){
		parent::__construct();
	}

	public function index()
	{
		if( $this->user->data != NULL ) redirect('user/'.$this->user->data->username);
		
		$data['_title'] = "ثبت نام";
		
		$this->load->model('m_group','group');

        //======================================//
        $this->bc->add('ثبت نام' ,current_url());
        //======================================//
		
		$data['province'] = $this->group->creatSelect(PROVINCE_ID,NULL,'<select class="form-control" name="state">');
		
		$data['skill']    = $this->group->creatSelect(SKILL_ID,NULL,'<select class="form-control" name="skill">');
		
        $this->tools->view('user/v_register',$data);

        $this->logs->addView('register');
	}
}