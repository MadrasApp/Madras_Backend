<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Mediaa extends CI_Controller {
	
	private $logged_in;
	
	function __construct(){
		
		parent::__construct();
		$this->load->model('m_user','user');
		
		if(!$this->user->check_login())
		{
			redirect('admin/login');
		}
		
		//$this->load->model('admin/m_media','media_model');			
	}
	
	public function index()
	{
		$data = $this->settings->data;
		$data['_title'] = ' | Media';
		$data['css'] = array('theme.css');
		$this->load->view('admin/v_header',$data);
		$this->load->view('admin/v_sidebar',$data);	
		$this->load->view('admin/media/v_media',$data);
		$this->load->view('admin/v_footer',$data);
	}
	
	public function add()
	{
		$data = $this->settings->data;
		$data['_title'] = ' | Add Media';
		$data['css'] = array('theme.css');
		$this->load->view('admin/v_header',$data);
		$this->load->view('admin/v_sidebar',$data);	
		$this->load->view('admin/media/v_media_add',$data);
		$this->load->view('admin/v_footer',$data);		
	}	
	
}
