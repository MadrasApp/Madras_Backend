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

class Jalasat extends CI_Controller {

    function __construct(){

        parent::__construct();
        $this->load->model('m_jalasat','jalasat');

        if( ! $this->user->check_login() )
        {
            redirect('admin/login');
        }
        else
        {
            $this->user->checkAccess('manage_jalasat');
        }
    }

    public function index($action='all')
    {
        $data = $this->settings->data;
        $data['_title'] = ' | جلسات کلاسهای دوره ها';

        //$data['_tabs']['all'] = array('name'=>'همه','count'=>$this->db->count_all_results('jalasat'));

        //$data['_tabs']['pending'] = array('name'=>'تایید نشده','count'=>$this->db->count_all_results('jalasat'));

        //$data['_tabs']['status'] = array('name'=>'تایید شده','count'=>$this->db->count_all_results('jalasat'));

        $fields = array(
			'c.dorehid'  => array('name'=>'نام دوره', 'type' => 'select'),
			'd.classid'  => array('name'=>'نام کلاس', 'type' => 'select'),
			'c.dorehclassid'  => array('name'=>'کلاس دوره', 'type' => 'select'),
			'c.placeid'  => array('name'=>'محل برگزاری', 'type' => 'select'),
			'c.ostadid'  => array('name'=>'استاد', 'type' => 'select'),
		);
		$dorehclasss = $this->db->order_by('id','asc')->get('dorehclass')->result();
		$classrooms = $this->db->order_by('title','asc')->get('classroom')->result();
		$dorehs = $this->db->order_by('t.name','asc')->join('ci_doreh d','t.id=d.tecatid','inner',FALSE)->get('tecat t')->result();
		$ostads = $this->db->where('optype = 1')->order_by('title','asc')->get('supplier')->result();
		$dorehid = (int)$this->input->get('dorehid');
		$suppliertype = $this->db->where("t.datatype = 'place'")->get('suppliertype t')->result();
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

		$fields['d.classid']['options'][''] = 'بدون انتخاب';
		$fields['c.dorehid']['options'][''] = 'بدون انتخاب';
		$fields['c.dorehclassid']['options'][''] = 'بدون انتخاب';
		$fields['c.placeid']['options'][''] = 'بدون انتخاب';
		$fields['c.ostadid']['options'][''] = 'بدون انتخاب';

		$data['doreh'] = array();
		foreach($dorehs as $k=>$v){
			$name = $v->name.' ['.$v->tahsili_year.'-'.($v->tahsili_year+1).']';
			$fields['c.dorehid']['options'][$v->id] = $name;
			$data['doreh'][$v->id] = $name;
		}

		$data['classroom'] = array();
		foreach($classrooms as $k=>$v){
			$fields['d.classid']['options'][$v->id] = $v->title;
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

		$data['dorehclass'] = array();
		foreach($dorehclasss as $k=>$v){
			if(isset($data['classroom'][$v->classid])){
				$title = $data['classroom'][$v->classid];
				$title = $title.' / '.$data['doreh'][$v->dorehid];
				$fields['c.dorehclassid']['options'][$v->id] = $title;
				$data['dorehclass'][$v->id] = $title;
			} else {
				$data['dorehclass'][$v->id] = 'کلاس حذف شده است';
			}
		}

        $table = $this->input->get('table');

        $data['searchHtml'] = $this->tools->createSearch($fields);
        $searchQuery        = $this->tools->createSearchQuery($fields,'jalasat');

        $data['query'] = " WHERE 1 $searchQuery ";

        if( $this->user->can('delete_comment') )
            $data['options'][] = array('name'=>'حذف','icon'=>'trash-o','click'=>'delete_row(this,\'jalasat\',[FLD])');

        $data['tableName'] = 'jalasat';

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
        $this->_view('v_jalasat',$data);
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
