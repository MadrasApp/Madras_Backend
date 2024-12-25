<?php
/**
 * Created by Talkabi.
 * User: nikan
 * Date: 6/23/2016
 * Time: 11:03 AM
 */

defined('BASEPATH') OR exit('No direct script access allowed');

class ClassOnline extends CI_Controller {

    function __construct(){

        parent::__construct();
        $this->load->model('m_classonline','classonline');

        if( ! $this->user->check_login() )
        {
            redirect('admin/login');
        }
        else
        {
            $this->user->checkAccess('manage_classonline');
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
			'c.teachername'  => array('name'=>'استاد', 'type' => 'select'),
		);

		$mecats = $this->db->order_by('name','asc')->get('mecat')->result();
		$mecatid = (int)$this->input->get('mecatid');

		$fields['c.mecatid']['options']['']  = 'بدون انتخاب';
		
		$data['mecat'] = array();
		foreach($mecats as $k=>$v){
			$fields['c.mecatid']['options'][$v->id]  = $v->name;
			$data['mecat'][$v->id]  = $v->name;
		}


        $teachernames = $this->db->order_by('displayname','asc')->where('level','teacher')->get('users')->result();
        $teachername = (int)$this->input->get('teachername');

        $fields['c.teachername']['options']['']  = 'بدون انتخاب';
        $data['teachername'] = array();
        foreach($teachernames as $k=>$v){
            $fields['c.teachername']['options'][$v->id]  = $v->displayname;
            $data['teachername'][$v->id]  = $v->displayname;
        }

        $data['ClassAccounts'] = array();
        $ClassAccounts = $this->db->select('classonline_id,COUNT(classonline_id) AS C')->group_by('classonline_id','asc')->get('classaccount')->result();
        foreach ($ClassAccounts as $classAccount){
            $data['ClassAccounts'][$classAccount->classonline_id] = $classAccount->C;
        }

        $data['ClassStudents'] = array();
        $ClassStudents = $this->db->select('classonline_id,COUNT(classonline_id) AS C')->where('user_id > 0')->group_by('classonline_id','asc')->get('classaccount')->result();
        foreach ($ClassStudents as $classAccount){
            $data['ClassStudents'][$classAccount->classonline_id] = $classAccount->C;
        }

        $table = $this->input->get('table');

        $data['searchHtml'] = $this->tools->createSearch($fields);
        $searchQuery        = $this->tools->createSearchQuery($fields,'classonline');

        $data['query'] = " WHERE 1 $searchQuery ";

        if( $this->user->can('delete_comment') )
            $data['options'][] = array('name'=>'حذف','icon'=>'trash-o','click'=>'delete_row(this,\'classonline\',[FLD])');

		if($owner)
			$data['Books'] = $this->db->where('author',$id)->where('type','book')->select('id,title')->get('posts')->result();
		else
			$data['Books'] = $this->db->where('type','book')->select('id,title')->get('posts')->result();

        $data['tableName'] = 'classonline';

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
        $this->_view('v_classonline',$data);
    }

    public function _view($view,$data)
    {
        $this->load->view('admin/v_header',$data);
        $this->load->view('admin/v_sidebar');
        $this->load->view('admin/'.$view);
        $this->load->view('admin/v_footer');
    }
    public function Pre($data,$die = 1){
        echo "<pre>";
        print_r($data);
        echo "</pre>";
        if($die) {
            die();
        }
    }
}