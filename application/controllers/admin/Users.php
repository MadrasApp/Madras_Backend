<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Users extends CI_Controller {
	
	
	private $logged_in;
	
	function __construct(){
		
		parent::__construct();
		$this->load->model('m_user','user');
		
		if( ! $this->user->check_login() )
		{
			redirect('admin/login');
		}
		else
		{
			$this->user->checkAccess('manage_users');
		}			
	}
	
	public function index()
	{

		$data = $this->settings->data;
		$data['_title'] = ' | Users';


        $data['options'] = array();
        if( $this->user->can('edit_user') )
            $data['options'][] = array('name'=>'ویرایش','icon'=>'pencil','href'=>site_url('profile').'?user=[FLD]');

        if( $this->user->can('delete_user') )
            $data['options'][] = array('name'=>'حذف','icon'=>'trash-o','click'=>'delete_row(this,\'users\',[FLD])');


        $fields = array(

            'username' => array('name'=>'نام کاربری'    , 'type'=>'text'),
            'tel' => array('name'=>'شماره همراه'    , 'type'=>'text'),
            'level'   => array('name'=>'نقش'    , 'type'=>'select'),
            //'type'   => array('name'=>'نوع'    , 'type'=>'select'),
            'email'   => array('name'=>'ایمیل'  , 'type'=>'text'),
            'active'  => array('name'=>'وضعیت'  , 'type'=>'select' ),
            'date'    => array('name'=>'تاریخ ' , 'type'=>'date-from-to'),
        );

        $fields['active']['options'][''] = '';
        $fields['active']['options']['1'] = 'فعال';
        $fields['active']['options']['0'] = 'مسدود';
		
		//$fields['type']['options'][''] = '';
        //$fields['type']['options']['user'] = 'کاربر';
        //$fields['type']['options']['expert'] = 'متخصص';

        $levels = $this->db->where('level_key','level_name')->get('user_level')->result();

        $fields['level']['options'][''] = '';
        if($this->user->data->level == 'admin')
        $fields['level']['options']['admin'] = 'مدیر';
        if($levels)
        {
            foreach($levels as $level)
            $fields['level']['options'][$level->level_id] = $level->level_value;
        }

        $fields['level']['options']['user'] = 'کاربر';
        $fields['level']['options']['teacher'] = 'استاد';


        $data['searchHtml'] = $this->tools->createSearch($fields);
        $searchQuery        = $this->tools->createSearchQuery($fields,'users');


        $tableName = 'users';

        $query  = "SELECT u.*
		,0 AS `userbooks`
		,0 AS `userhighlights`
		,0 AS `usermobiles`
		,0 AS `hasmembership`
		FROM `ci_users` AS u where u.id !=0 $searchQuery ";

        if($this->user->data->level != 'admin')
        $query .= " AND `level` != 'admin' ";

        $query .= " order by  `";

        $order = $this->input->get($tableName.'_order');

        $query .= ($order ? $order:'id')."` ";

        $sort = $this->input->get($tableName.'_sort');

        $query .= ($sort ? $sort:'DESC');

        $data['tableName'] = $tableName;
        $data['query'] = $query;

		$this->_view('users/v_users',$data);
	}
	
	public function levels()
	{
		$data = $this->settings->data;
		$data['_title'] = ' | Levels';
		
		$levels = $this->db->get('user_level')->result();
		
		$data['levels'] = array();
		
		if( $levels )
		{
			foreach( $levels as $key => $row )
			{
				$data['levels'][$row->level_id][$row->level_key] = $row->level_value;
			} 
		}
				
		$this->_view('users/v_user_level',$data);
	}
	
	public function chart()
	{
		$data = $this->settings->data;
		$data['_title'] = ' | Statistics';
		
		$data['chart']['onlines'] = $this->user->getOnlines();
		
		$data['chart']['date'] = $this->db->select('date')->where('NOT ISNULL(`date`) order by `date`')->get('users')->result_array();
		
		$data['chart']['users'] = $this->user->getRegisteredUsersInfo();
				
		$this->_view('users/v_chart',$data);
	}	
	
		
	public function _view($view,$data)
	{
		$this->load->view('admin/v_header',$data);
		$this->load->view('admin/v_sidebar',$data);	
		$this->load->view('admin/'.$view,$data);
		$this->load->view('admin/v_footer',$data);		
	}	
	
	//Alireza Balvardi Start
	public function adduser()
	{
		$data = $this->settings->data;
		$data['_title'] = ' | New User';
		
		$levels = $this->db->get('users')->result();
		$this->load->view('admin/v_header',$data);
		$this->load->view('admin/v_sidebar',$data);	
		$this->load->view('admin/users/v_edit',$data);
		$this->load->view('admin/v_footer',$data);
	}
	//Alireza Balvardi End
}
?>