<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Home extends CI_Controller {

	function __construct(){
		
		parent::__construct();
		$this->load->model('m_user','user');
		
		if( ! $this->user->check_login() )
		{
			redirect('admin/login');
		}
        else
        {
            if($this->user->data->level == 'user')
                show_404();
        }
	}
	
	public function index()
	{
		$data = $this->settings->data;
		$data['_title'] = ' | Home';

        $home_data = array();

        /*if($this->user->can('read_comment'))
            $home_data['comments'] = array(
                'icon'  => 'commenting-o',
                'name'  => 'نظرات',
                'link'  => site_url('admin/comments'),
                'count' => $this->db->count_all_results('comments') .' &nbsp; ::: &nbsp; '.($this->db->where('submitted',0)->count_all_results('comments'))
            );*/

        if($this->user->can('manage_users'))
            $home_data['users'] = array(
                'icon'  => 'users',
                'name'  => 'کاربران',
                'link'  => site_url('admin/users'),
                'count' => $this->db->count_all_results('users') .' &nbsp; ::: &nbsp; '.($this->db->where('active',0)->count_all_results('users'))
            );

        if($this->user->can('manage_users'))
            $home_data['payamak'] = array(
                'icon'  => 'mobile',
                'name'  => 'پیامک',
                'link'  => site_url('admin/payamak'),
                'count' => 'کل پیامکها : '.$this->db->count_all_results('sended') .' &nbsp; ::: پیامکهای موفق : '.($this->db->where('status',1)->count_all_results('sended'))
            );//Alireza Balvardi

        /*if($this->user->can('read_msg'))
            $home_data['messages'] = array(
                'icon'  => 'envelope-o',
                'name'  => 'پیام ها',
                'link'  => site_url('admin/messages'),
                'count' => $this->db->count_all_results('admin_inbox') .' &nbsp; ::: &nbsp; '.($this->db->where('visited',0)->count_all_results('admin_inbox'))
            );*/

        $data['home_data'] = $home_data;
        
        if($this->user->can('read_site_info'))
        {
            if($this->user->can('site_visits'))
            {
                $where = array(
                    'event'=>'view',
                    'date >=' => date("Y-m-d H:i:s",strtotime("-1 month")),
                    '_is_ !=' => 'robot'
                );
                $data['chart']['date'] = $this->db->select('date')->where($where)->get('logs')->result_array();
            }
        }

		$this->_view('v_home',$data);

        $data_arr = array(
            'اطلاعات کلی وآمار سایت' => array(
                '_read_site_info'     => 'آمار',
                '_site_local_info'    => 'آمار کلی',
                '_site_visits'        => 'آمار بازدید',
                '_site_users_info'    => 'آمار کاربران',
                '_site_tools_info'    => 'آمار ابزارها',
                '_site_missions_info' => 'آمار ماموریت ها',
            ),
        );
	}
	
	public function statistics()
	{
        if(!$this->user->can('site_visits'))
            show_404();

		$data = $this->settings->data;
		$data['_title'] = ' | Statistics';

		
		$date   = $this->input->get('date');
		$from  = $this->input->get('from');
		$to = $this->input->get('to');	
			
		if( ! empty($date) )
		{
			switch($date)
			{
				case 'today': $from = "today";  break;
				case '1-m': $from = "-1 month";  break;
				case '2-m': $from = "-2 month";  break;
				case '3-m': $from = "-3 month";  break;
				case '6-m': $from = "-6 month";  break;
				case '1-y': $from = "-1 year";   break;
				case '2-y': $from = "-2 year";   break;
				case 'all': $from = "-20 year";  break;
			}
			$to = "now";
			
			$data['from'] = strtotime($from);
			$data['to']   = strtotime($to);	
			$from = date("Y-m-d H:i:s",$data['from']);
			$to   = date("Y-m-d H:i:s",$data['to']);
			
			if( $date == 'all') $data['from'] = strtotime("-1 month");
													
		}
		elseif( !empty($from) && ! empty($to) )
		{
			
			$from = explode('-',$from);
			$to = explode('-',$to);
			
			if(count($from)==3 && count($to)==3)
			{
				$from = jalali_to_gregorian($from[0],$from[1],$from[2],'-').' 00:00:00';
				$to = jalali_to_gregorian($to[0],$to[1],$to[2],'-').' 00:00:00';
				$data['from'] = strtotime($from);
				$data['to']   = strtotime($to);					
			}
			else
			{
				$from = date("Y-m-d H:i:s",strtotime("-1 month"));
				$to = date("Y-m-d H:i:s"); 
				$data['from'] = strtotime("-1 month");
				$data['to']   = strtotime("now");					
			}
		}
		else
		{
			$from = date("Y-m-d H:i:s",strtotime("-1 month"));
			$to = date("Y-m-d H:i:s"); 
			$data['from'] = strtotime("-1 month");
			$data['to']   = strtotime("now");								
		}
		
		$where = array(
			'event'=>'view',
			'date >=' => $from,
			'date <=' => $to,
		);
		
		$data['view_all'] = $this->db->select('date')->where($where)->get('logs')->result_array();
		
		$where['_is_'] = 'browser';
		$data['view_browser'] = $this->db->select('date')->where($where)->get('logs')->result_array();
		
		$where['_is_'] = 'mobile';
		$data['view_mobile'] = $this->db->select('date')->where($where)->get('logs')->result_array();
		
		$where['_is_'] = 'robot';
		$data['view_robot'] = $this->db->select('date')->where($where)->get('logs')->result_array();
		
		$this->_view('v_statistics',$data);
	}	
	
	public function _view($view,$data)
	{
		$this->load->view('admin/v_header',$data);
		$this->load->view('admin/v_sidebar',$data);	
		$this->load->view('admin/'.$view,$data);
		$this->load->view('admin/v_footer',$data);		
	}	
}
