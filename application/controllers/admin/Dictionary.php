<?php
/**
 * Created by Talkabi.
 * User: nikan
 * Date: 6/23/2016
 * Time: 11:03 AM
 */

defined('BASEPATH') OR exit('No direct script access allowed');

class Dictionary extends CI_Controller {

    function __construct(){

        parent::__construct();
        $this->load->model('m_dictionary','dictionary');

        if( ! $this->user->check_login() )
        {
            redirect('admin/login');
        }
        else
        {
            $this->user->checkAccess('manage_dictionary');
        }
    }

    public function index($action='all')
    {
        $data = $this->settings->data;
        $data['_title'] = ' | Dictionary';

        $data['_tabs']['all'] = array('name'=>'همه','count'=>$this->db->count_all_results('dictionary'));

        $data['_tabs']['pending'] = array('name'=>'تایید نشده','count'=>$this->db->count_all_results('dictionary'));

        $data['_tabs']['status'] = array('name'=>'تایید شده','count'=>$this->db->count_all_results('dictionary'));

        $fields = array(
			'fromlang'  => array('name'=>'زبان اصلی', 'type' => 'select'),
			'tolang'  => array('name'=>'زبان ترجمه', 'type' => 'select'),
		);
		$fromlang = (int)$this->input->get('fromlang');
		$diclangs = $this->db->order_by('title','asc')->get('diclang')->result();

		$fields['fromlang']['options']['']  = 'بدون انتخاب';
		$fields['tolang']['options']['']  = 'بدون انتخاب';

		foreach($diclangs as $k=>$v){
			$fields['fromlang']['options'][$v->id]  = $v->title;
			$fields['tolang']['options'][$v->id]  = $v->title;
		}

        $table = $this->input->get('table');

        $data['searchHtml'] = $this->tools->createSearch($fields);
        $searchQuery        = $this->tools->createSearchQuery($fields,'dictionary');

        $data['query'] = " WHERE 1 $searchQuery ";

        if( $this->user->can('delete_comment') )
            $data['options'][] = array('name'=>'حذف','icon'=>'trash-o','click'=>'delete_row(this,\'dictionary\',[FLD])');

        $data['tableName'] = 'dictionary';

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
            $data['query'] .= " ORDER BY `regdate` DESC";
        }

        $this->_view('v_dictionary',$data);
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