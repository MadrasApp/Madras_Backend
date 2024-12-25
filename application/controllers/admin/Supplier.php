<?php
/**
 * Created by Talkabi.
 * User: nikan
 * Date: 6/23/2016
 * Time: 11:03 AM
 */

defined('BASEPATH') OR exit('No direct script access allowed');

class Supplier extends CI_Controller {

    function __construct(){

        parent::__construct();
        $this->load->model('m_supplier','supplier');

        if( ! $this->user->check_login() )
        {
            redirect('admin/login');
        }
        else
        {
            $this->user->checkAccess('manage_supplier');
        }
    }

    public function index($action='all')
    {
        $data = $this->settings->data;
        $data['_title'] = ' | Supplier';

        $data['_tabs']['all'] = array('name'=>'همه','count'=>$this->db->count_all_results('supplier'));

        $data['_tabs']['pending'] = array('name'=>'تایید نشده','count'=>$this->db->count_all_results('supplier'));

        $data['_tabs']['status'] = array('name'=>'تایید شده','count'=>$this->db->count_all_results('supplier'));

        $fields = array(
			'c.optype'  => array('name'=>'حقیقی یا حقوقی', 'type' => 'select'),
			'd.type_id'  => array('name'=>'نوع', 'type' => 'select'),
			'c.smtype'  => array('name'=>'سازمان بالادستی', 'type' => 'select'),
			'c.offer'  => array('name'=>'پیشنهادی', 'type' => 'select'),
		);
		$optype = (int)$this->input->get('optype');
		$suppliertypes = $this->db->order_by('title','asc')->get('suppliertype')->result();
		$suppliers = $this->db->order_by('title','asc')->get('supplier')->result();
		$diclangs = $this->db->order_by('title','asc')->get('diclang')->result();

		$fields['c.optype']['options'] = array(''=>'بدون انتخاب','1'=>'حقیقی','2'=>'حقوقی');
		$fields['d.type_id']['options']['']  = 'بدون انتخاب';
		$fields['c.smtype']['options']['']  = 'بدون انتخاب';
		$fields['c.offer']['options'] = array(''=>'بدون انتخاب','1'=>'پیشنهاد شده','0'=>'عادی');

		foreach($suppliertypes as $k=>$v){
			$fields['d.type_id']['options'][$v->id]  = $v->title;
		}
		foreach($suppliers as $k=>$v){
			$fields['c.smtype']['options'][$v->id]  = $v->title;
		}

		$O = $this->db->get('supplierrules')->result();
		$supplierrules = array();
		foreach($O as $k=>$v){
			$supplierrules[$v->sup_id][] = $fields['d.type_id']['options'][$v->type_id];
		}
		$data['extra']['supplierrules'] = $supplierrules;

        $table = $this->input->get('table');

        $data['searchHtml'] = $this->tools->createSearch($fields);
        $searchQuery        = $this->tools->createSearchQuery($fields,'supplier');

        $data['query'] = " WHERE 1 $searchQuery ";

        if( $this->user->can('delete_comment') )
            $data['options'][] = array('name'=>'حذف','icon'=>'trash-o','click'=>'delete_row(this,\'supplier\',[FLD])');

        $data['tableName'] = 'supplier';

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

        $this->_view('v_supplier',$data);
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