<?php
/**
echo "<pre>";
print_r();
echo "</pre>";
die;
 * Created by Talkabi.
 * User: nikan
 * Date: 6/23/2016
 * Time: 11:03 AM
 */

defined('BASEPATH') OR exit('No direct script access allowed');

class DorehClass extends CI_Controller {

    function __construct(){

        parent::__construct();
        $this->load->model('m_dorehclass','dorehclass');

        if( ! $this->user->check_login() )
        {
            redirect('admin/login');
        }
        else
        {
            $this->user->checkAccess('manage_dorehclass');
        }
    }

    public function index($action='all')
    {
        $data = $this->settings->data;
        $data['_title'] = ' | کلاس های دوره ها';

        //$data['_tabs']['all'] = array('name'=>'همه','count'=>$this->db->count_all_results('dorehclass'));

        //$data['_tabs']['pending'] = array('name'=>'تایید نشده','count'=>$this->db->count_all_results('dorehclass'));

        //$data['_tabs']['status'] = array('name'=>'تایید شده','count'=>$this->db->count_all_results('dorehclass'));

        $fields = array(
			'c.dorehid'  => array('name'=>'نام دوره', 'type' => 'select'),
			'c.classid'  => array('name'=>'نام کلاس', 'type' => 'select'),
			'c.placeid'  => array('name'=>'محل برگزاری', 'type' => 'select'),
			'c.ostadid'  => array('name'=>'استاد', 'type' => 'select'),
		);
		$classrooms = $this->db->order_by('title','asc')->get('classroom')->result();
		$dorehs = $this->db->order_by('t.name','asc')->join('ci_doreh d','t.id=d.tecatid','inner',FALSE)->get('tecat t')->result();
		$ostads = $this->db->where('optype = 1')->order_by('title','asc')->get('supplier')->result();
		$dorehid = (int)$this->input->get('dorehid');
		$suppliertype = $this->db->where("t.datatype = 'place'")->get('suppliertype t')->result();
		$favorites = $this->db->select('t.section_id,COUNT(t.section_id) C')->where("t.section = 'dorehclass'")->group_by('t.section_id')->order_by('C','asc')->get('favorites t')->result();
		$data['favorite'] = array();
		foreach($favorites as $k=>$v){
			$data['favorite'][$v->section_id] = $v->C;
		}
		$place = array(0);
		foreach($suppliertype as $k=>$v){
			$place[] = $v->id;
		}
		
		$places = $this->db->select('s.id,s.title,t.title ttitle')
					->where("t.datatype = 'place'")
					->where("r.type_id IN(".implode(',',$place).")")
					->join('ci_supplierrules r','s.id=r.sup_id','inner',FALSE)
					->join('ci_suppliertype t','t.id=r.type_id','inner',FALSE)
					->order_by('s.title','asc')
					->get('supplier s')->result();

		$fields['c.classid']['options'][''] = 'بدون انتخاب';
		$fields['c.dorehid']['options'][''] = 'بدون انتخاب';
		$fields['c.placeid']['options'][''] = 'بدون انتخاب';
		$fields['c.ostadid']['options'][''] = 'بدون انتخاب';
		
		$data['doreh'] = array();
		foreach($dorehs as $k=>$v){
			$fields['c.dorehid']['options'][$v->id] = $v->name.' ['.$v->tahsili_year.'-'.($v->tahsili_year+1).']';
			$data['doreh'][$v->id] = $v->name.' ['.$v->tahsili_year.'-'.($v->tahsili_year+1).']';
		}

        $data['classroom'] = array();
		foreach($classrooms as $k=>$v){
			$fields['c.classid']['options'][$v->id] = $v->title;
			$data['classmecat'][$v->id] = $v->mecatid;
			$data['classroom'][$v->id] = $v->title;
		}

		$data['place'] = array();
		foreach($places as $k=>$v){
			$fields['c.placeid']['options'][$v->id] = "$v->ttitle | $v->title";
			$data['place'][$v->id] = "$v->ttitle | $v->title";
		}

		$data['ostad'] = array();
		foreach($ostads as $k=>$v){
			$fields['c.ostadid']['options'][$v->id] = $v->title;
			$data['ostad'][$v->id] = $v->title;
		}

        $table = $this->input->get('table');

        $data['searchHtml'] = $this->tools->createSearch($fields);
        $searchQuery        = $this->tools->createSearchQuery($fields,'dorehclass');

        $data['query'] = " WHERE 1 $searchQuery ";

        if( $this->user->can('delete_comment') )
            $data['options'][] = array('name'=>'حذف','icon'=>'trash-o','click'=>'delete_row(this,\'dorehclass\',[FLD])');

        $data['tableName'] = 'dorehclass';

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
        $this->_view('v_dorehclass',$data);
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