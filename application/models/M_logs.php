<?php defined('BASEPATH') OR exit('No direct script access allowed');

class M_logs extends CI_Model {
	
	function __construct()
	{
		parent::__construct();
		$this->load->helper(array('cookie','security'));
		$this->load->model('m_user','user');
		$this->load->library('user_agent');
	}
	
	public function add($event = "view",$table=NULL,$row_id=NULL)
	{
		$_is_ = "Unidentified User Agent";
		
		if ($this->agent->is_robot())
		$_is_ = "robot";
		
		elseif ($this->agent->is_mobile())
		$_is_ = "mobile";
		
		elseif ($this->agent->is_browser())
		$_is_ = "browser";

		
		$data = array(
			'table'      => $table,
			'row_id'     => $row_id,
			'page_url'   => rawurldecode(current_url()),
			'_is_'       => $_is_,
			'referer'    => rawurldecode($this->agent->referrer()),
			'browser'    => $this->agent->browser(),
			'mobile'     => $this->agent->mobile(),
			'robot'      => $this->agent->robot() ,
			'platform'   => $this->agent->platform(),
			'agent'      => $this->agent->agent_string(),
			'user_id'    => @$this->user->user_id,
			'user_level' => @$this->user->data->level,
			'event'      => $event ,
			'ip'         => $this->input->ip_address() ,
			'date'       => date("Y-m-d H:i:s"),
            'datestr'    => time(),
		);		
		
		$this->db->insert('logs',$data);
	} 	
	
	public function addView($page='home',$id=NULL)
	{
        $ip = $this->input->ip_address();
        $ex = $this->db->where('ip',$ip)->where('event','view')->where('datestr >',strtotime('today'))->count_all_results('logs');

        if(!$ex)
        $this->add("view",$page);
	} 
	
}