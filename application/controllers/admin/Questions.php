<?php
/**
 * Created by Talkabi.
 * User: nikan
 * Date: 6/23/2016
 * Time: 11:03 AM
 */

defined('BASEPATH') OR exit('No direct script access allowed');

class Questions extends CI_Controller {

    function __construct(){

        parent::__construct();
        $this->load->model('m_user','user');

        if( ! $this->user->check_login() )
        {
            redirect('admin/login');
        }
        else
        {
            $this->user->checkAccess('read_question');
        }
    }

    public function index($action='all')
    {
        $data = $this->settings->data;
        $data['_title'] = ' | Questions';

        $data['_tabs']['all'] = array('name'=>'همه','count'=>$this->db->count_all_results('questions'));

        $this->db->where(array('published'=>0));
        $data['_tabs']['pending'] = array('name'=>'تایید نشده','count'=>$this->db->count_all_results('questions'));

        $this->db->where('published',1);
        $data['_tabs']['published'] = array('name'=>'تایید شده','count'=>$this->db->count_all_results('questions'));

        $fields = array(
            'catid'      => array('name'=>'گروه بندی'      , 'type'=>'select'),
            'title'      => array('name'=>'متن پشتیبانی'      , 'type'=>'text'),
            'published' => array('name'=>'وضعیت'    , 'type'=>'select'),
            'table'     => array('name'=>'مرتبط با' , 'type'=>'select'),
            'regdate'      => array('name'=>'تاریخ '   , 'type'=>'date-from-to'),
        );

        $fields['catid']['options'][''] = 'انتخاب گروه بندی';
		$rows = $this->db->get('catquest')->result();
		foreach ($rows as $r)
			$fields['catid']['options'][$r->id] = $r->title;

        $fields['published']['options'][''] = '';
        $fields['published']['options']['1'] = 'تایید شده';
        $fields['published']['options']['0'] = 'تایید نشده';

        $table = $this->input->get('table');

        if($table && $this->db->table_exists($table) && in_array($table,array('posts')))
        {
            $fields['row']['options'][''] = '';

            switch ($table)
            {
                case 'posts':
                    $this->db->select('id,title as name')->from('posts p');
                    $this->db->select("(SELECT COUNT(*) FROM ci_questions WHERE (`table`='posts' AND `row_id`=p.id)) AS question_count");
                    $this->db->having('question_count >',0);
                break;
                //case 'users':$this->db->select('id,displayname as name')->from('users');break;
                //case 'instruments':$this->db->select('id,name')->from('instruments');break;
            }
            $rows = $this->db->get()->result();
            foreach ($rows as $r)
                $fields['row']['options'][$r->id] = $r->name;
        }else unset($fields['row']);

        $data['searchHtml'] = $this->tools->createSearch($fields);
        $searchQuery        = $this->tools->createSearchQuery($fields,'questions');

        $data['query'] = " WHERE qid = 0 $searchQuery ";

        switch($action)
        {
            case'all':
                $data['query'] .= " ";
                break;
            /***************/
            case'pending':
                $data['query'] .= " AND  `published`=0 ";
                break;
            /***************/
            case'published':
                $data['query'] .= " AND  `published`=1 ";
                break;
        }

        if( $this->user->can('read_question') )
            $data['options'][] = array('name'=>'مشاهده','icon'=>'eye','click'=>'view_question(this,[FLD])');
        
        //if( $this->user->can('reply_question') )
            //$data['options'][] = array('name'=>'پاسخ','icon'=>'reply','click'=>'view_question(this,[FLD])');

        if( $this->user->can('delete_question') )
            $data['options'][] = array('name'=>'حذف','icon'=>'trash-o','click'=>'delete_row(this,\'questions\',[FLD])');

        $data['tableName'] = 'questions';

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

        $this->_view('v_questions',$data);
    }

    public function _view($view,$data)
    {
        $this->load->view('admin/v_header',$data);
        $this->load->view('admin/v_sidebar');
        $this->load->view('admin/questions/'.$view);
        $this->load->view('admin/v_footer');
    }
	public function editQuestion($id=NULL){
		$data = $this->settings->data;
		
		$data['_title']  = (int)$id?' | ویرایش پشتیبانی':' | پشتیبانی جدید';
		$data['qid'] = (int)$id?$id:-1;
		$data['catquest'] = $this->db->get('catquest')->result();
		$data['question'] = (object)$this->db->where('id',$data['qid'])->get('questions')->row();
		if(!isset($data['question']->id))
			$data['question']->id = 0;
		$data['questiondetail'] = $this->db->where('qid',$data['qid'])->get('questions')->result();

		$this->_view('v_edit',$data);
	}	
}
?>