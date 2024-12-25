<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Gozaresh extends CI_Controller {
	
	function __construct(){
		
		parent::__construct();
		
		if( ! $this->user->check_login() )
		{
			redirect('admin/login');
		}
		else
		{
			$this->user->checkAccess('manage_gozaresh');
		}			
	}
	
	public function index()
	{
		$data = $this->settings->data;
		$user_id = $this->user->user_id;

		$level = $this->user->getUserLevel($user_id);
		$owner = 0;
		$ownerpercent = 100;
		if($level != "admin" && $this->user->can('is_supplier')){
			$owner = $user_id;
			$this->db->select("*");
			$this->db->where("mobile",$this->user->data->tel);
			$row = $this->db->get("supplier")->row();
			if($row){
				$title = "اطلاعات مالی فروش محصولات $row->title";
				$ownerpercent = $row->ownerpercent;
			}
		}

		$data['_title'] = ' | گزارش مالی';
		$data['ownerpercent'] = $ownerpercent;
        /*******************************
		$data['options'] = array();
        if( $this->user->can('edit_user') )
            $data['options'][] = array('name'=>'ویرایش','icon'=>'pencil','href'=>site_url('profile').'?user=[FLD]');

        if( $this->user->can('delete_user') )
            $data['options'][] = array('name'=>'حذف','icon'=>'trash-o','click'=>'delete_row(this,\'users\',[FLD])');
		********************************/
		
			$this->db->select('b.author,u.username,u.displayname');
			$this->db->distinct(TRUE);
			$this->db->order_by('u.username');
			$this->db->join('ci_users u','b.author=u.id','inner',FALSE);
			$data['nasher'] = $this->db->get('posts b')->result();
		/*========================================
				search
		=========================================*/
        $fields = array(
			'author'   => array('name'=>'نام کاربری'   , 'type' => 'select'),
			'parent_category'  => array('name'=>'سطح'        , 'type' => 'select'),
			'category'  => array('name'=>'پایه‌'        , 'type' => 'select'),
            'f.id'       => array('name'=>'شماره فاکتور' , 'type' => 'text'),
            'f.cdate'    => array('name'=>'تاریخ '       , 'type' => 'date-from-to' , 'field_type' => 'int'),
        );
		if($owner){
			unset($fields['author']);
		} else {
			$fields['author']['options']['']  = 'انتخاب کاربر';
			foreach($data['nasher'] as $k=>$v){
				$fields['author']['options'][$v->author]  = "$v->username [ $v->displayname ]";
			}
		}

		$fields['parent_category']['options']['']  = 'انتخاب سطح';
		$cats = $this->LoadSubCategories(0,0);
		foreach($cats as $k=>$v){
			if(!$v->parent)
				$fields['parent_category']['options'][$v->id]  = $v->name;
		}

		$fields['category']['options']['']  = 'انتخاب پایه';
		$parent_category = (int)$this->input->get('parent_category');
		$category = (int)$this->input->get('category');
		if($parent_category){
			$cats = $this->LoadSubCategories($parent_category,1);
			foreach($cats as $k=>$v){
				$fields['category']['options'][$v->id]  = $v->name;
			}
		}
		
        $data['searchHtml'] = $this->tools->createSearch($fields);
		unset($fields['parent_category']);
        $searchQuery        = $this->tools->createSearchQuery($fields,'factor_detail');
		/*========================================
				/ search
		=========================================*/

        $tableName = 'factor_detail';

		if($owner){
			$searchQuery = "AND p.author = $owner $searchQuery";
		}
        $query  = "where d.discount > 0 AND f.paid > 0 $searchQuery ";

		$parent_category = (int)$this->input->get('parent_category');
		if($parent_category){
			$query.=" AND p.category  IN (SELECT id FROM ci_category WHERE parent=$parent_category)";
		}

        $query .= " order by ";
        $order = $this->input->get($tableName.'_order');
        $query .= ($order ? $order:'f.cdate')." ";
        $nasher = $this->input->get('nasher');
        $query .= $nasher ? "b.author = $nasher ":"";
        $sort = $this->input->get($tableName.'_sort');
        $query .= ($sort ? $sort:'DESC');
        $data['tableName'] = $tableName;
        $data['query'] = $query;
		
		$data['showall'] = 25;
		if(isset($_GET) && is_array($_GET)){
			if((isset($_GET["author"]) || isset($_GET["parent_category"]) || isset($_GET["category"]) ) && isset($_GET["date-from"]) && isset($_GET["date-to"]))
				$data['showall'] = 0;
		}
		
		$this->_view('v_gozaresh',$data);
	}
	/****************  List Of Categories  ***************/
	public function LoadSubCategories($id,$level){
        $this->db->select("*");
        $this->db->where("parent",$id);
        $this->db->where("type","book");
        $this->db->order_by("position");
		$O = $this->db->get("category")->result();
		$cats = [];
		foreach($O as $k=>$v){
			$v->name = $level?str_repeat("|__",$level)." ".$v->name:$v->name;
			$cats[$v->id] = $v;
			$subcats = $this->LoadSubCategories($v->id,$level+1);
			if(count($subcats))
				$cats = array_merge($cats,$subcats);
		}
		return $cats;
	}
	
	public function _view($view,$data)
	{
		$this->load->view('admin/v_header',$data);
		$this->load->view('admin/v_sidebar',$data);	
		$this->load->view('admin/'.$view,$data);
		$this->load->view('admin/v_footer',$data);		
	}	
}
?>