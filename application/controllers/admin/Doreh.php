<?php
/**
 * Created by Talkabi.
 * User: nikan
 * Date: 6/23/2016
 * Time: 11:03 AM
echo "<pre>";
print_r();
echo "</pre>";
 */

defined('BASEPATH') OR exit('No direct script access allowed');

class Doreh extends CI_Controller {

    function __construct(){

        parent::__construct();
        $this->load->model('m_doreh','doreh');

        if( ! $this->user->check_login() )
        {
            redirect('admin/login');
        }
        else
        {
            $this->user->checkAccess('manage_doreh');
        }
    }
    public function Pre($data,$die = 1){
        echo "<pre>";
        print_r($data);
        echo "</pre>";
        if($die) {
            die();
        }
    }

    public function index($action='all')
    {
        $data = $this->settings->data;
        $data['_title'] = ' | Doreh';

        //$data['_tabs']['all'] = array('name'=>'همه','count'=>$this->db->count_all_results('doreh'));

        //$data['_tabs']['pending'] = array('name'=>'تایید نشده','count'=>$this->db->count_all_results('doreh'));

        //$data['_tabs']['status'] = array('name'=>'تایید شده','count'=>$this->db->count_all_results('doreh'));

        $fields = array(
			'c.tecatid'  => array('name'=>'نام دوره', 'type' => 'select'),
			'c.nezamid'  => array('name'=>'نظام', 'type' => 'select'),
			'c.supplierid'  => array('name'=>'مدیر', 'type' => 'select'),
			'c.placeid'  => array('name'=>'محل برگزاری', 'type' => 'dropdown'),
		);
		$suppliertypes = $this->db->order_by('title','asc')->get('suppliertype')->result();
		$supplierrules = $this->db->get('supplierrules')->result();
		$tecats = $this->db->order_by('name','asc')->get('tecat')->result();
		$nezams = $this->db->order_by('name','asc')->get('nezam')->result();
		$suppliers = $this->db->order_by('title','asc')->get('supplier')->result();
		$tecatid = (int)$this->input->get('tecatid');
		$nezamid = (int)$this->input->get('nezamid');
		$classcount = $this->db->select('dorehid , COUNT(dorehid) C')->group_by('dorehid')->get('dorehclass')->result();

		$fields['c.tecatid']['options']['']  = 'بدون انتخاب';
		$fields['c.nezamid']['options']['']  = 'بدون انتخاب';
		$fields['c.placeid']['options']['']  = 'بدون انتخاب';
		$fields['c.supplierid']['options']['']  = 'بدون انتخاب';
		
		$data["classcount"] = array();
		foreach($classcount as $k=>$v){
			$data['classcount'][$v->dorehid]  = $v->C;
		}
		$data['tecat'] = array();
		foreach($tecats as $k=>$v){
			$fields['c.tecatid']['options'][$v->id]  = $v->name;
			$data['tecat'][$v->id]  = $v->name;
		}

		$data['nezam'] = array();
		foreach($nezams as $k=>$v){
			$fields['c.nezamid']['options'][$v->id]  = $v->name;
			$data['nezam'][$v->id]  = $v->name;
		}

		$data['suppliertype'] = array(0=>"بدون انتخاب");
		foreach($suppliertypes as $k=>$v){
			$data['suppliertype'][$v->id]  = $v->title;
		}

		$data['supplierrules'] = array();
		foreach($supplierrules as $k=>$v){
			$data['supplierrules'][$v->sup_id][]  = $v->type_id;
		}

		$data['supplier'] = array();
		foreach($suppliers as $k=>$v){
			if($v->optype == 1)
				$fields['c.supplierid']['options'][$v->id]  = $v->title;
			else{
				$stype = @$data['supplierrules'][$v->id];
				if(is_array($stype)){
					foreach($stype as $k0=>$v0){
						$fields['c.placeid']['options'][$data['suppliertype'][$v0]][$v->id]  = $v->title;
					}
				}
			}
			$data['supplier'][$v->id]  = $v->title;
		}

        $table = $this->input->get('table');

        $data['searchHtml'] = $this->tools->createSearch($fields);
        $searchQuery        = $this->tools->createSearchQuery($fields,'doreh');

        $data['query'] = " WHERE 1 $searchQuery ";

        if( $this->user->can('delete_comment') )
            $data['options'][] = array('name'=>'حذف','icon'=>'trash-o','click'=>'delete_row(this,\'doreh\',[FLD])');

        $data['tableName'] = 'doreh';

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
            $data['query'] .= " ORDER BY c.createdate DESC";
        }
        $this->_view('v_doreh',$data);
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