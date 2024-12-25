<?php defined('BASEPATH') OR exit('No direct script access allowed');

class M_inbox extends CI_Model {
	
	public $setting;
	
	function __construct()
	{	
		parent::__construct();
		//$this->setting = $this->settings->data;	
	}
	
	public function add($data = NULL)
	{
		if( ! $data OR ! is_array($data) ) return FALSE;
		
		if( $this->db->insert('admin_inbox',$data) )
		return $this->db->insert_id();
	
		return FALSE;
	}
	
	public function delete($id = NULL)
	{
		if( ! empty($id) && $this->db->where('id',$id)->delete('admin_inbox') )
		return TRUE;
		return FALSE;
	}		
	
	public function info()
	{
		return array(
			'all'    => $this->db->count_all_results('admin_inbox'),
			'read'   => $this->db->where('visited',1)->count_all_results('admin_inbox'), 
			'unread' => $this->db->where('visited',0)->count_all_results('admin_inbox'), 
		);
		
	}
	
	public function unreadCount()
	{
		$info = $this->info();
		return $info['unread'];
	}		
		
}//=== end of model




