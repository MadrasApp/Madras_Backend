<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Payment extends CI_Controller {
	
	function __construct(){
		
		parent::__construct();
		
		if( ! $this->user->check_login() )
		{
			redirect('admin/login');
		}
		else
		{
			$this->user->checkAccess('manage_payment');
		}			
	}
	
	public function index()
	{
		$data = $this->settings->data;

		$user_id = $this->user->user_id;
		$level = $this->user->getUserLevel($user_id);
		$owner = 0;
		$ownerpercent = 100;
		$title = null;
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
		
		$data['_title'] = ' | اطلاعات مالی';
		$data['subtitle'] = $title;
		$data['ownerpercent'] = $ownerpercent;
		// delete unused factor (just created and ignored)
		$this->db->where('status', NULL);
		$this->db->where('cdate <', time()-24*3600);
		$this->db->delete('factors');

		$this->db->where('status', 1);
		$this->db->delete('factors');
		
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

			'u.username'   => array('name'=>'نام کاربری'   , 'type' => 'text'),
			'u.tel'   => array('name'=>'شماره همراه'   , 'type' => 'text'),
			'u.email'   => array('name'=>'ایمیل'   , 'type' => 'text'),
            'f.id'       => array('name'=>'شماره فاکتور' , 'type' => 'text'),
			'f.ref_id'   => array('name'=>'شماره رسید'   , 'type' => 'text'),
            //'f.user_id'  => array('name'=>'کاربر'        , 'type' => 'select'),
            'f.status'   => array('name'=>'وضعیت'        , 'type' => 'select'),
            'f.price'    => array('name'=>'قیمت'         , 'type' => 'text'),
            'f.cdate'    => array('name'=>'تاریخ '       , 'type' => 'date-from-to' , 'field_type' => 'int'),
        );

        $fields['f.status']['options']['']  = '';
        $fields['f.status']['options']['0'] = 'پرداخت موفق';
        $fields['f.status']['options']['1'] = 'پرداخت ناموفق';
        $fields['f.status']['options']['2'] = 'برگشت خورده';


/*
		$this->db->select('u.id, u.tel , u.username');
		$this->db->join('ci_users u','u.id=f.user_id','left',FALSE);
		$this->db->from('factors f');
		$this->db->group_by('f.user_id');
		$this->db->order_by('f.id','desc');
		$users = $this->db->get()->result();
        $fields['f.user_id']['options'][''] = '';
		
		foreach($users as $user)
			$fields['f.user_id']['options'][$user->id] =  '[' . $user->id .'] ' . $user->username .' - ' .$user->tel .'';
*/
        $data['searchHtml'] = $this->tools->createSearch($fields);
        $searchQuery        = $this->tools->createSearchQuery($fields,'factors');
		/*========================================
				/ search
		=========================================*/

        $tableName = 'factors';

        $query  = "where f.id !=0 $searchQuery ";
		
		if($owner)
        $query .= " AND owner = $owner ";

        $query .= " order by ";

        $order = $this->input->get($tableName.'_order');

        $query .= ($order ? $order:'f.id')." ";

        $sort = $this->input->get($tableName.'_sort');

        $query .= ($sort ? $sort:'DESC');

        $data['tableName'] = $tableName;
        $data['query'] = $query;

		$this->_view('v_payment',$data);
	}
	
	public function _view($view,$data)
	{

		$this->load->view('admin/v_header',$data);
		$this->load->view('admin/v_sidebar',$data);	
		$this->load->view('admin/'.$view,$data);
		$this->load->view('admin/v_footer',$data);		
	}	
}
