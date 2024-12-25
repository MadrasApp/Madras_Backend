<?php
/**
 * Created by Talkabi.
 * User: nikan
 * Date: 6/23/2016
 * Time: 11:03 AM
 */

defined('BASEPATH') OR exit('No direct script access allowed');

class Payamak extends CI_Controller {

    function __construct(){

        parent::__construct();
        $this->load->model('m_payamak','payamak');

        if( ! $this->user->check_login() )
        {
            redirect('admin/login');
        }
        else
        {
            $this->user->checkAccess('manage_payamak');
        }
    }

    public function index($action='all')
    {
        $data = $this->settings->data;
        $data['_title'] = ' | Payamak';

        $data['_tabs']['all'] = array('name'=>'همه','count'=>$this->db->count_all_results('sended'));

        $this->db->where(array('status'=>0));
        $data['_tabs']['pending'] = array('name'=>'تایید نشده','count'=>$this->db->count_all_results('sended'));

        $this->db->where('status',1);
        $data['_tabs']['status'] = array('name'=>'تایید شده','count'=>$this->db->count_all_results('sended'));

        $fields = array(
            'name'      => array('name'=>'نام'      , 'type'=>'text'),
            'text'      => array('name'=>'نظر'      , 'type'=>'text'),
            'status'	=> array('name'=>'وضعیت'    , 'type'=>'select'),
            'table'     => array('name'=>'مرتبط با' , 'type'=>'select'),
            'row'       => array('name'=>'موضوع'    , 'type'=>'select'),
            'date'      => array('name'=>'تاریخ '   , 'type'=>'date-from-to'),
        );

        $fields['status']['options'][''] = '';
        $fields['status']['options']['1'] = 'ارسال شده';
        $fields['status']['options']['0'] = 'ارسال نشده';

        $fields['table']['options'][''] = '';
        $fields['table']['options']['posts'] = 'نوشته ها';
        $fields['table']['options']['users'] = 'کاربران';
        $fields['table']['options']['instruments'] = 'ابزارها';

        $table = $this->input->get('table');

        if($table && $this->db->table_exists($table) && in_array($table,array('posts')))
        {
            $fields['row']['options'][''] = '';

            switch ($table)
            {
                case 'posts':
                    $this->db->select('id,title as name')->from('posts p');
                    $this->db->select("(SELECT COUNT(*) FROM ci_sended WHERE (`table`='posts' AND `row_id`=p.id)) AS comment_count");
                    $this->db->having('comment_count >',0);
                break;
                //case 'users':$this->db->select('id,displayname as name')->from('users');break;
                //case 'instruments':$this->db->select('id,name')->from('instruments');break;
            }
            $rows = $this->db->get()->result();
            foreach ($rows as $r)
                $fields['row']['options'][$r->id] = $r->name;
        }else unset($fields['row']);

        $data['searchHtml'] = $this->tools->createSearch($fields);
        $searchQuery        = $this->tools->createSearchQuery($fields,'sended');

        $data['query'] = " WHERE 1 $searchQuery ";

        switch($action)
        {
            case'all':
                $data['query'] .= " ";
                break;
            /***************/
            case'pending':
                $data['query'] .= " AND  `status`=0 ";
                break;
            /***************/
            case'status':
                $data['query'] .= " AND  `status`=1 ";
                break;
        }

        if( $this->user->can('delete_comment') )
            $data['options'][] = array('name'=>'حذف','icon'=>'trash-o','click'=>'delete_row(this,\'sended\',[FLD])');

        $data['tableName'] = 'sended';

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

        $this->_view('v_payamak',$data);
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