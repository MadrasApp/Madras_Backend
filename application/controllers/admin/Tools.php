<?php
/**
 * Created by Talkabi.
 * User: nikan
 * Date: 6/23/2016
 * Time: 11:03 AM
 */

defined('BASEPATH') OR exit('No direct script access allowed');

class Tools extends CI_Controller {

    function __construct(){

        parent::__construct();
        $this->load->model('m_user','user');

        if( ! $this->user->check_login() )
        {
            redirect('admin/login');
        }
        else
        {
            $this->user->checkAccess('read_tools');
        }
    }

    public function index($action='all')
    {
        $data = $this->settings->data;
        $data['_title'] = ' | Tools';

        $data['_tabs']['all'] = array('name'=>'همه','count'=>$this->db->count_all_results('instruments'));

        $this->db->where(array('submitted'=>0));
        $data['_tabs']['pending'] = array('name'=>'تایید نشده','count'=>$this->db->count_all_results('instruments'));

        $this->db->where('submitted',1);
        $data['_tabs']['submitted'] = array('name'=>'تایید شده','count'=>$this->db->count_all_results('instruments'));

        $fields = array(
            'name'      => array('name'=>'نام'      , 'type'=>'text'),
            'fore_sale' => array('name'=>'نوع'      , 'type'=>'select'),
            'submitted' => array('name'=>'وضعیت'    , 'type'=>'select'),
            'date'      => array('name'=>'تاریخ'    , 'type'=>'date-from-to'),
        );

        $fields['submitted']['options'][''] = '';
        $fields['submitted']['options']['1'] = 'تایید شده';
        $fields['submitted']['options']['0'] = 'تایید نشده';

        $fields['fore_sale']['options'][''] = '';
        $fields['fore_sale']['options']['0'] = 'اجاره ای';
        $fields['fore_sale']['options']['1'] = 'فروشی';


        $data['searchHtml'] = $this->tools->createSearch($fields);
        $searchQuery        = $this->tools->createSearchQuery($fields,'instruments');

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

        if( $this->user->can('edit_tools') )
            $data['options'][] = array('name'=>'ویرایش','icon'=>'pencil','href'=>site_url('tools/edit').'/[ID]');

        if( $this->user->can('delete_tools') )
            $data['options'][] = array('name'=>'حذف','icon'=>'trash-o','click'=>'delete_row(this,\'instruments\',[FLD])');

        $data['tableName'] = 'instruments';

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

        $this->_view('v_instruments',$data);
    }

    public function _view($view,$data)
    {
        $this->load->view('admin/v_header',$data);
        $this->load->view('admin/v_sidebar');
        $this->load->view('admin/'.$view);
        $this->load->view('admin/v_footer');
    }
}
