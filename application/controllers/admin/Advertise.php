<?php
/**
 * Created by Talkabi.
 * User: nikan
 * Date: 6/23/2016
 * Time: 11:03 AM
 */

defined('BASEPATH') OR exit('No direct script access allowed');

class Advertise extends CI_Controller {

    function __construct(){

        parent::__construct();
        $this->load->model('m_advertise','advertise');

        if( ! $this->user->check_login() )
        {
            redirect('admin/login');
        }
        else
        {
            $this->user->checkAccess('manage_advertise');
        }
    }

    public function index($action='all')
    {
        $data = $this->settings->data;
        $data['_title'] = ' | تبلیغات';

        $data['_tabs']['all'] = array('name'=>'همه','count'=>$this->db->count_all_results('advertise'));

        $data['_tabs']['pending'] = array('name'=>'تایید نشده','count'=>$this->db->count_all_results('advertise'));

        $data['_tabs']['status'] = array('name'=>'تایید شده','count'=>$this->db->count_all_results('advertise'));

        $fields = array(
            'c.section'  => array('name'=>'بخش', 'type' => 'select'),
        );

        $fields['c.section']['options']['']  = 'بدون انتخاب';
        $fields['c.section']['options']["category"] = "دسته بندی";
        $fields['c.section']['options']["classonline"] = "کلاس آنلاین";
        $fields['c.section']['options']["classroom"] = "کلاس عادی";
        $fields['c.section']['options']["tecat"] = "دسته بندی عنوانی";
        $fields['c.section']['options']["supplier"] = "عرضه کنندگان";
        $fields['c.section']['options']["membership"] = "اشتراک";
        $fields['c.section']['options']["link"] = "آدرس وب";

        $data['searchHtml'] = $this->tools->createSearch($fields);
        $searchQuery        = $this->tools->createSearchQuery($fields,'advertise');

        $data['query'] = " WHERE 1 $searchQuery ";

        if( $this->user->can('delete_comment') )
            $data['options'][] = array('name'=>'حذف','icon'=>'trash-o','click'=>'delete_row(this,\'advertise\',[FLD])');

        $data['tableName'] = 'advertise';

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

        $this->_view('v_advertise',$data);
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