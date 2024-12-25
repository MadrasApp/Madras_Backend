<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Setting extends CI_Controller {
	
	
	function __construct(){
		
		parent::__construct();
		$this->load->model('m_user','user');
		
		if(!$this->user->check_login())
		{
			redirect('admin/login');
		}
        else
            $this->user->checkAccess('change_settings');
	}
	
	public function index()
	{			
		$set = $this->settings->data;
		$set['_title'] = ' | Settings ';
        $set['script'][] = 'ckeditor/ckeditor.js';

		$this->load->view('admin/v_header',$set);
		$this->load->view('admin/v_sidebar',$set);	
		$this->load->view('admin/v_setting',$set);
		$this->load->view('admin/v_footer',$set);
	}
}
