<?php
/**
 * Created by Talkabi.
 * User: nikan
 * Date: 6/23/2016
 * Time: 11:03 AM
 */

defined('BASEPATH') OR exit('No direct script access allowed');

class ClassRoom extends CI_Controller {

    function __construct(){

        parent::__construct();
        $this->load->model('m_classroom','classroom');

        if( ! $this->user->check_login() )
        {
            redirect('admin/login');
        }
        else
        {
            $this->user->checkAccess('manage_classroom');
        }
    }

    public function index($action='all')
    {
        $data = $this->settings->data;
        $data['_title'] = ' | کلاس';
		$user_id = $this->user->user_id;

		$level = $this->user->getUserLevel($user_id);

		$owner = 0;
		if($level != "admin" && $this->user->can('is_supplier')){
			$owner = $user_id;
		}

        $fields = array(
			'c.mecatid'  => array('name'=>'دسته بندی موضوعی', 'type' => 'select'),
		);
		$mecats = $this->db->order_by('name','asc')->get('mecat')->result();
		$mecatid = (int)$this->input->get('mecatid');

		$fields['c.mecatid']['options']['']  = 'بدون انتخاب';
		
		$data['mecat'] = array();
		foreach($mecats as $k=>$v){
			$fields['c.mecatid']['options'][$v->id]  = $v->name;
			$data['mecat'][$v->id]  = $v->name;
		}
        $table = $this->input->get('table');

        $data['searchHtml'] = $this->tools->createSearch($fields);
        $searchQuery        = $this->tools->createSearchQuery($fields,'classroom');

        $data['query'] = " WHERE 1 $searchQuery ";

        if( $this->user->can('delete_comment') )
            $data['options'][] = array('name'=>'حذف','icon'=>'trash-o','click'=>'delete_row(this,\'classroom\',[FLD])');

		if($owner)
			$data['Books'] = $this->db->where('author',$id)->where('type','book')->select('id,title')->get('posts')->result();
		else
			$data['Books'] = $this->db->where('type','book')->select('id,title')->get('posts')->result();

        $data['tableName'] = 'classroom';

        $order = $this->input->get($data['tableName'].'_order');
        $sort = $this->input->get($data['tableName'].'_sort');

        if( $order && $sort )
        {
            $data['query'] .= " ORDER BY `";
            $data['query'] .= $order."` ";
            $data['query'] .= $sort;
        }
        else
        {
            $data['query'] .= " ORDER BY c.createdate DESC";
        }
        $this->_view('v_classroom',$data);
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