<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Discount extends CI_Controller {
	
	function __construct(){
		
		parent::__construct();
		
		if( ! $this->user->check_login() )
		{
			redirect('admin/login');
		}
		else
		{
			$this->user->checkAccess('manage_discount');
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
	public function index()
	{
		$user_id = $this->user->user_id;
		$data = $this->settings->data;
		$level = $this->user->getUserLevel($user_id);
		$title = "";
		$ownerpercent = 100;
		if($level != "admin" && $this->user->can('is_supplier')){
			$this->db->select("*");
			$this->db->where("mobile",$this->user->data->tel);
			$row = $this->db->get("supplier")->row();
			if($row){
				$title = "کدهای تخفیف $row->title";
				$ownerpercent = $row->ownerpercent;
			}
		}
		$data['_title'] = ' | کد تفیف';
		$data['subtitle'] = $title;
		$data['ownerpercent'] = $ownerpercent;

        /*******************************
		$data['options'] = array();
        if( $this->user->can('edit_user') )
            $data['options'][] = array('name'=>'ویرایش','icon'=>'pencil','href'=>site_url('profile').'?user=[FLD]');

        if( $this->user->can('delete_user') )
            $data['options'][] = array('name'=>'حذف','icon'=>'trash-o','click'=>'delete_row(this,\'users\',[FLD])');
		********************************/
		
		/*========================================
				search
		=========================================*/
        $fields = array(
			'd.code'        => array('name'=>'کد'      , 'type' => 'text'),
            'd.percent'     => array('name'=>'درصد'    , 'type' => 'text'),
            'd.category_id' => array('name'=>'سطح'     , 'type' => 'select'),
            'd.used'        => array('name'=>'وضعیت'   , 'type' => 'select'),
            'd.cdate'       => array('name'=>'تاریخ '  , 'type' => 'date-from-to' , 'field_type' => 'int'),
        );

        $fields['d.category_id']['options']['']  = 'انتخاب سطح استفاده شده';
        $fields['d.category_id']['options']['0']  = 'همه جا';
        $fields['d.category_id']['options']['-1']  = 'فقط کتابهای خاص';
        $fields['d.category_id']['options']['-2']  = 'همه کتابها';
		
        $fields['d.used']['options']['']  = '';
        $fields['d.used']['options']['1'] = 'استفاده شده';
        $fields['d.used']['options']['0'] = 'استفاده نشده';

        
		/*========================================
				category
		=========================================*/
		$cats = $this->LoadSubCategories(0,0);

        $catmemberships = [];
        foreach ($cats as $cat){
            if(!$cat->parent){
                $catmemberships[] = $cat;
            }
        }
        $data['catmemberships'] = $catmemberships;

        foreach($cats as $ck => $cat)
		{
			$qu = "
				SELECT SUM(meta_value) AS price
				FROM `ci_post_meta` `m`
				WHERE m.meta_key='price' AND m.post_id IN (
					SELECT `id` FROM `ci_posts` WHERE (`category` IN ( 
						SELECT `id` FROM `ci_category` WHERE `parent`={$cat->id}
					) OR `category`={$cat->id})AND `published`=1
				)";
			$cats[$ck]->price = $this->db->query($qu)->row()->price;
			$fields['d.category_id']['options'][$cat->id] = $cat->name;
		}
        $fields['d.category_id']['options'][-81] = "دسته بندی : اشتراک یک ماهه";
        $fields['d.category_id']['options'][-83] = "دسته بندی : اشتراک سه ماهه";
        $fields['d.category_id']['options'][-86] = "دسته بندی : اشتراک شش ماهه";
        $fields['d.category_id']['options'][-812] = "دسته بندی : اشتراک یک ساله";
        foreach($cats as $ck => $cat){
            if($cat->parent){
                continue;
            }
            $fields['d.category_id']['options']["-81.$cat->id"] = "اشتراک یک ماهه : ".$cat->name;
            $fields['d.category_id']['options']["-83.$cat->id"] = "اشتراک سه ماهه : ".$cat->name;
            $fields['d.category_id']['options']["-86.$cat->id"] = "اشتراک شش ماهه : ".$cat->name;
            $fields['d.category_id']['options']["-812.$cat->id"] = "اشتراک یک ساله : ".$cat->name;
        }


		$data['categories'] = $cats;


		$data['searchHtml'] = $this->tools->createSearch($fields);
        $searchQuery        = $this->tools->createSearchQuery($fields,'discounts');
        $searchQuery = str_replace("`","",$searchQuery);
        $searchQuery = str_replace("d.category_id = '-81.","d.category_id = '-81' AND d.bookid = '",$searchQuery);
        $searchQuery = str_replace("d.category_id = '-83.","d.category_id = '-83' AND d.bookid = '",$searchQuery);
        $searchQuery = str_replace("d.category_id = '-86.","d.category_id = '-86' AND d.bookid = '",$searchQuery);
        $searchQuery = str_replace("d.category_id = '-812.","d.category_id = '-812' AND d.bookid = '",$searchQuery);
		/*========================================
				/# category and search
		=========================================*/
		
        $tableName = 'discounts';

        $query  = "where d.id !=0 $searchQuery ";
		if($level != "admin")
		$query.= " AND  author IN(0,$user_id)";

        $query .= " order by ";

        $order = $this->input->get($tableName.'_order');

        $query .= ($order ? $order:'d.id')." ";

        $sort = $this->input->get($tableName.'_sort');

        $query .= ($sort ? $sort:'DESC');


        $data['tableName'] = $tableName;
        $data['query'] = $query;

		$this->_view('v_discount',$data);
	}
	
	public function _view($view,$data)
	{
		$this->load->view('admin/v_header',$data);
		$this->load->view('admin/v_sidebar',$data);	
		$this->load->view('admin/'.$view,$data);
		$this->load->view('admin/v_footer',$data);		
	}	
}
