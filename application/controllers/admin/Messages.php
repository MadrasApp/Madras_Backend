<?php
/**
 * Created by Talkabi.
 * User: nikan
 * Date: 6/23/2016
 * Time: 11:03 AM
 */

defined('BASEPATH') OR exit('No direct script access allowed');

class Messages extends CI_Controller {

    function __construct(){

        parent::__construct();
        $this->load->model('m_user','user');

        if( ! $this->user->check_login() )
        {
            redirect('admin/login');
        }
        else
        {
            $this->user->checkAccess('read_msg');
        }
    }

    public function index($action='all')
    {
        $data = $this->settings->data;
        $data['_title'] = ' | Messages';

        $data['_tabs']['all'] = array('name'=>'همه','count'=>$this->db->count_all_results('admin_inbox'));

        $this->db->where(array('visited'=>0));
        $data['_tabs']['new'] = array('name'=>'جدید','count'=>$this->db->count_all_results('admin_inbox'));

        $this->db->where('visited',1);
        $data['_tabs']['visited'] = array('name'=>'خوانده شده','count'=>$this->db->count_all_results('admin_inbox'));

        $this->db->where('ansver',NULL)->or_where('ansver','');
        $data['_tabs']['not-ansvered'] = array('name'=>'بدون پاسخ','count'=>$this->db->count_all_results('admin_inbox'));

        $this->db->where("ansver IS NOT NULL AND ansver != ''");
        $data['_tabs']['ansvered'] = array('name'=>'پاسخ داده شده','count'=>$this->db->count_all_results('admin_inbox'));

        $fields = array(
            'name'    => array('name'=>'نام'    , 'type'=>'text'),
            'email'   => array('name'=>'ایمیل'  , 'type'=>'text'),
            'subject' => array('name'=>'موضوع'   , 'type'=>'text'),
            'message' => array('name'=>'پیام'   , 'type'=>'text'),
            'date'    => array('name'=>'تاریخ ' , 'type'=>'date-from-to'),
        );

        $data['searchHtml'] = $this->tools->createSearch($fields);
        $searchQuery        = $this->tools->createSearchQuery($fields,'admin_inbox');

        $data['query'] = " WHERE 1 $searchQuery ";

        switch($action)
        {
            case'all':
                $data['query'] .= " ";
                $ops = array('delete','view','reply');
                break;
            /***************/
            case'new':
                $data['query'] .= " AND  `visited`=0 ";
                $ops = array('delete','view','reply');
                break;
            /***************/
            case'visited':
                $data['query'] .= " AND  `visited`=1 ";
                $ops = array('delete','view','reply');
                break;
            /***************/
            case'not-ansvered':
                $data['query'] .= " AND   `ansver` IS NULL OR ansver = '' ";
                $ops = array('delete','view','reply');
                break;
            /***************/
            case'ansvered':
                $data['query'] .= " AND   `ansver` IS NOT NULL AND ansver != '' ";
                $ops = array('delete','view','reply');
                break;
        }

        if( $this->user->can('read_msg') && in_array('view',$ops) )
        {
            $data['options'][] = array('name'=>'مشاهده','icon'=>'eye','click'=>'view_msg(this,[FLD])');
        }
        if( $this->user->can('reply_msg') && in_array('reply',$ops) )
            $data['options'][] = array('name'=>'پاسخ','icon'=>'reply','click'=>'view_msg(this,[FLD])');

        if( $this->user->can('delete_msg') && in_array('delete',$ops) )
            $data['options'][] = array('name'=>'حذف','icon'=>'trash-o','click'=>'delete_row(this,\'admin_inbox\',[FLD])');


        $data['tableName'] = 'admin_inbox';

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
            $data['query'] .= " ORDER BY `visited` ASC, `date` DESC";
        }

        $data['script'][] = 'ckeditor/ckeditor.js';

        $this->_view('v_messages',$data);
    }

    public function _view($view,$data)
    {
        $this->load->view('admin/v_header',$data);
        $this->load->view('admin/v_sidebar');
        $this->load->view('admin/'.$view);
        $this->load->view('admin/v_footer');
    }
}
