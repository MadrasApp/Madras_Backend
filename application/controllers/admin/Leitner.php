<?php
/**
 * Created by Talkabi.
 * User: nikan
 * Date: 6/23/2016
 * Time: 11:03 AM
 */

defined('BASEPATH') OR exit('No direct script access allowed');

class Leitner extends CI_Controller {

    function __construct(){

        parent::__construct();
        $this->load->model('m_user','user');

        if( ! $this->user->check_login() )
        {
            redirect('admin/login');
        }
        else
        {
            $this->user->checkAccess('read_leitner');
        }
    }

    public function index($action='all')
    {
        $data = $this->settings->data;
        $data['_title'] = ' | Leitner';

        $data['_tabs']['all'] = array('name'=>'همه','count'=>$this->db->count_all_results('leitner'));

        $fields = array(
            'catid'      => array('name'=>'گروه بندی'      , 'type'=>'select'),
            'title'      => array('name'=>'متن'      , 'type'=>'text'),
            'lid'     => array('name'=>'مرتبط با' , 'type'=>'select'),
            'regdate'      => array('name'=>'تاریخ '   , 'type'=>'date-from-to'),
        );

        $fields['catid']['options'][''] = 'انتخاب گروه بندی';
        $fields['catid']['options']['1'] = 'یادداشت';
        $fields['catid']['options']['2'] = 'لغت';
        $fields['catid']['options']['3'] = 'سوال تستی';
        $fields['catid']['options']['4'] = 'سوال تشریحی';

        $data['searchHtml'] = $this->tools->createSearch($fields);
        $searchQuery        = $this->tools->createSearchQuery($fields,'leitner');

        $data['query'] = strlen($searchQuery)?" WHERE 1 $searchQuery ":"";
		$data['query'] = str_replace('AND `','AND c.',$data['query']);
		$data['query'] = str_replace('`','',$data['query']);

        if( $this->user->can('read_leitner') )
            $data['options'][] = array('name'=>'مشاهده','icon'=>'eye','click'=>'view_leitner(this,[FLD])');
        
        //if( $this->user->can('reply_leitner') )
            //$data['options'][] = array('name'=>'پاسخ','icon'=>'reply','click'=>'view_leitner(this,[FLD])');

        if( $this->user->can('delete_leitner') )
            $data['options'][] = array('name'=>'حذف','icon'=>'trash-o','click'=>'delete_row(this,\'leitner\',[FLD])');

        $data['tableName'] = 'leitner';

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

        $this->_view('v_leitner',$data);
    }

    public function _view($view,$data)
    {
        $this->load->view('admin/v_header',$data);
        $this->load->view('admin/v_sidebar');
        $this->load->view('admin/leitner/'.$view);
        $this->load->view('admin/v_footer');
    }
	public function editLeitner($id=NULL){
		$data = $this->settings->data;
		
		$data['_title']  = (int)$id?' | ویرایش لایتنر':' | لایتنر جدید';
		$data['id'] = (int)$id?$id:-1;
		$data['leitner'] = (object)$this->db->where('id',$data['id'])->get('leitner')->row();
		if(!isset($data['leitner']->id))
			$data['leitner']->id = 0;

		$this->_view('v_edit',$data);
	}	
}
?>