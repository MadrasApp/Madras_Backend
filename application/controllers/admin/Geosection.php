<?php
/**
 * Created by Talkabi.
 * User: nikan
 * Date: 6/23/2016
 * Time: 11:03 AM
 */

defined('BASEPATH') OR exit('No direct script access allowed');

class Geosection extends CI_Controller {

    function __construct(){

        parent::__construct();
        $this->load->model('m_geosection','geosection');

        if( ! $this->user->check_login() )
        {
            redirect('admin/login');
        }
        else
        {
            $this->user->checkAccess('is_supplier');
        }
    }

    public function index($id=0)
    {
        $data = $this->settings->data;
		$user_id = $this->user->user_id;

		$level = $this->user->getUserLevel($user_id);
		$owner = 0;
		if($level != "admin" && $this->user->can('is_supplier')){
			$owner = $user_id;
		}

        $data['_title'] = ' | مناطق جغرافیایی';

		$data['owner'] = $owner;

		$data['Cat'] = $this->db->where('id',$id)->get('geosection')->row();
		
		//=============================//

        $this->_view('v_geosection',$data);
    }

    public function _view($view,$data)
    {
        $this->load->view('admin/v_header',$data);
        $this->load->view('admin/v_sidebar');
        $this->load->view('admin/'.$view);
        $this->load->view('admin/v_footer');
    }
}
?>