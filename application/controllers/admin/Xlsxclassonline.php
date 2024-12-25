<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Xlsxclassonline extends CI_Controller {
	
	function __construct(){
		
		parent::__construct();
		
		if( ! $this->user->check_login() )
		{
			redirect('admin/login');
		}
		else
		{
			$this->user->checkAccess('manage_xlsxclassonline');
		}			
	}
	
	public function index()
	{
        $post = $this->input->post();
        $data = $this->settings->data;
		$data['_title'] = ' | ثبت فایل اکسل';
        $data["post"] = $post;
		$this->_view('v_xlsxclassonline',$data);
	}

	public function _view($view,$data)
	{
		$this->load->view('admin/v_header',$data);
		$this->load->view('admin/v_sidebar',$data);	
		$this->load->view('admin/'.$view,$data);
		$this->load->view('admin/v_footer',$data);		
	}	
}
?>