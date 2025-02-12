<?php
/**
 * Created by Talkabi.
 * User: nikan
 * Date: 6/23/2016
 * Time: 11:03 AM
 */

defined('BASEPATH') OR exit('No direct script access allowed');

class Writer extends CI_Controller {

    function __construct(){

        parent::__construct();
        $this->load->model('m_writer','writer');

        if( ! $this->user->check_login() )
        {
            redirect('admin/login');
        }
        else
        {
            $this->user->checkAccess('manage_writer');
        }
    }

    public function index($action='all')
    {
        $data = $this->settings->data;
        $data['_title'] = ' | Writer';

        $data['_tabs']['all'] = array('name'=>'همه','count'=>$this->db->count_all_results('writer'));

        $fields = array();

        $table = $this->input->get('table');

        $data['searchHtml'] = $this->tools->createSearch($fields);
        $searchQuery        = $this->tools->createSearchQuery($fields,'writer');

        $data['query'] = " WHERE 1 $searchQuery ";

        if( $this->user->can('delete_comment') )
            $data['options'][] = array('name'=>'حذف','icon'=>'trash-o','click'=>'delete_row(this,\'writer\',[FLD])');

        $data['tableName'] = 'writer';

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

        //$data['script'][] = 'ckeditor/ckeditor.js';
		
		
		//==============================//
		$this->db->select("COUNT(r.id) AS total_rates");
        $this->db->select("ROUND((AVG(r.rating)),1) AS app_rating",FALSE);
		$this->db->where('r.table','APP');
		$this->db->where('r.rating !=',0);
		$this->db->where('r.row_id',0);
		$data['rating'] = $this->db->get('rates r',1)->row();
		//=============================//

        $this->_view('v_writer',$data);
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