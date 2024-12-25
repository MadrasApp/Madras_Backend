<?php defined('BASEPATH') OR exit('No direct script access allowed');

class M_dorehclass extends CI_Model {
	
	public $setting = NULL;
	public $data = NULL;
	
	function __construct()
	{
		parent::__construct();
		$this->setting = $this->settings->data;	
	}	
		
		
	public function delete($id)
	{
		$jalasat = $this->db->where('dorehclassid',$id)->get('jalasat')->result();
		$jalasatid = array(0);
		foreach($jalasat as $k=>$v){
			$jalasatid[] = $v->id;
		}
		$this->db->where("jid IN (".implode(",",$jalasatid).")")->delete('jalasat_data');
		$this->db->where('dorehclassid',$id)->delete('jalasat');
		return $this->db->where('id',$id)->delete('dorehclass');
	}

}