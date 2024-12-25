<?php
/**
 * Created by Talkabi.
 * User: nikan
 * Date: 6/23/2016
 * Time: 11:03 AM
 */

defined('BASEPATH') OR exit('No direct script access allowed');

class Comments extends CI_Controller {

    function __construct(){

        parent::__construct();
        $this->load->model('m_user','user');

        if( ! $this->user->check_login() )
        {
            redirect('admin/login');
        }
        else
        {
            $this->user->checkAccess('read_comment');
        }
    }

    public function index($action='all')
    {
        $data = $this->settings->data;
        $data['_title'] = ' | Comments';

        $data['_tabs']['all'] = array('name'=>'همه','count'=>$this->db->count_all_results('comments'));

        $this->db->where(array('submitted'=>0));
        $data['_tabs']['pending'] = array('name'=>'تایید نشده','count'=>$this->db->count_all_results('comments'));

        $this->db->where('submitted',1);
        $data['_tabs']['submitted'] = array('name'=>'تایید شده','count'=>$this->db->count_all_results('comments'));

        $fields = array(
            'name'      => array('name'=>'نام'      , 'type'=>'text'),
            'text'      => array('name'=>'نظر'      , 'type'=>'text'),
            'submitted' => array('name'=>'وضعیت'    , 'type'=>'select'),
            'table'     => array('name'=>'مرتبط با' , 'type'=>'select'),
            'row'       => array('name'=>'موضوع'    , 'type'=>'select'),
            'date'      => array('name'=>'تاریخ '   , 'type'=>'date-from-to'),
        );

        $fields['submitted']['options'][''] = '';
        $fields['submitted']['options']['1'] = 'تایید شده';
        $fields['submitted']['options']['0'] = 'تایید نشده';

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
                    $this->db->select("(SELECT COUNT(*) FROM ci_comments WHERE (`table`='posts' AND `row_id`=p.id)) AS comment_count");
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
        $searchQuery        = $this->tools->createSearchQuery($fields,'comments');

        $data['query'] = " WHERE 1 $searchQuery ";

        switch($action)
        {
            case'all':
                $data['query'] .= " ";
                break;
            /***************/
            case'pending':
                $data['query'] .= " AND  `submitted`=0 ";
                break;
            /***************/
            case'submitted':
                $data['query'] .= " AND  `submitted`=1 ";
                break;
        }

        if( $this->user->can('read_comment') )
            $data['options'][] = array('name'=>'مشاهده','icon'=>'eye','click'=>'view_comment(this,[FLD])');
        
        //if( $this->user->can('reply_comment') )
            //$data['options'][] = array('name'=>'پاسخ','icon'=>'reply','click'=>'view_comment(this,[FLD])');

        if( $this->user->can('delete_comment') )
            $data['options'][] = array('name'=>'حذف','icon'=>'trash-o','click'=>'delete_row(this,\'comments\',[FLD])');

        $data['tableName'] = 'comments';

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
            $data['query'] .= " ORDER BY `date` DESC";
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

        $this->_view('v_comments',$data);
    }

    public function _view($view,$data)
    {
        $this->load->view('admin/v_header',$data);
        $this->load->view('admin/v_sidebar');
        $this->load->view('admin/'.$view);
        $this->load->view('admin/v_footer');
    }
}
