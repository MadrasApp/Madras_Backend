<?php
/**
 * Created by Talkabi.
 * User: nikan
 * Date: 6/23/2016
 * Time: 11:03 AM
 */

defined('BASEPATH') OR exit('No direct script access allowed');

class Missions extends CI_Controller {

    function __construct(){

        parent::__construct();
        $this->load->model('m_user','user');

        if( ! $this->user->check_login() )
        {
            redirect('admin/login');
        }
        else
        {
            $this->user->checkAccess('read_missions');
        }
    }

    public function index($action='all')
    {
        $data = $this->settings->data;
        $data['_title'] = ' | Missions';

        $data['_tabs']['all'] = array('name'=>'همه','count'=>$this->db->count_all_results('missions'));

        $this->db->where(array('submitted'=>0));
        $data['_tabs']['pending'] = array('name'=>'تایید نشده','count'=>$this->db->count_all_results('missions'));

        $this->db->where('submitted',1);
        $data['_tabs']['submitted'] = array('name'=>'تایید شده','count'=>$this->db->count_all_results('missions'));

        $fields = array(
            'from'      => array('name'=>'ماموریت دهنده'      , 'type'=>'select'),
            'to'        => array('name'=>'ماموریت گیرنده'     , 'type'=>'select'),
            'done'      => array('name'=>'انجام شده'          , 'type'=>'select'),
            'submitted' => array('name'=>'وضعیت'              , 'type'=>'select'),
            'hidden'    => array('name'=>'مخفی'               , 'type'=>'select'),
            //'tel'       => array('name'=>'تلفن'               , 'type'=>'text'),
            'm.date'      => array('name'=>'تاریخ'              , 'type'=>'date-from-to'),
        );

        $fields['submitted']['options'][''] = '';
        $fields['submitted']['options']['1'] = 'تایید شده';
        $fields['submitted']['options']['0'] = 'تایید نشده';

        $fields['done']['options'][''] = '';
        $fields['done']['options']['1'] = 'انجام شده';
        $fields['done']['options']['0'] = 'انجام نشده';

        $fields['hidden']['options'][''] = '';
        $fields['hidden']['options']['1'] = 'مخفی';
        $fields['hidden']['options']['0'] = 'غیر مخفی';

        $fields['from']['options'][''] = '';
        $fields['to']['options'][''] = '';


        $users = $this->db->select('id,displayname,type')->order_by('displayname')->get('users')->result();

        foreach ($users as $u)
        {
            $fields['from']['options'][$u->id] = $u->displayname;
            if($u->type == 'expert')
                $fields['to']['options'][$u->id] = $u->displayname;
        }

        $data['searchHtml'] = $this->tools->createSearch($fields);
        $searchQuery        = $this->tools->createSearchQuery($fields,'missions');

        $data['query'] =
            /** @lang text */
            "SELECT m.*, from.username as from_username, from.displayname as from_name, to.username as to_username, to.displayname as to_name
              FROM ci_missions m
              LEFT JOIN (`ci_users` `from`) ON `m`.`from`=`from`.`id`
              LEFT JOIN (`ci_users` `to`)   ON `m`.`to`   = `to`.`id`  
            WHERE 1 $searchQuery 
           ";

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

        if( $this->user->can('delete_missions') )
            $data['options'][] = array('name'=>'حذف','icon'=>'trash-o','click'=>'delete_row(this,\'missions\',[FLD])');

        $data['tableName'] = 'missions';

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

        $data['script'][] = 'ckeditor/ckeditor.js';

        $this->_view('v_missions',$data);
    }

    public function _view($view,$data)
    {
        $this->load->view('admin/v_header',$data);
        $this->load->view('admin/v_sidebar');
        $this->load->view('admin/'.$view);
        $this->load->view('admin/v_footer');
    }
}
