<?php defined('BASEPATH') OR exit('No direct script access allowed');

class M_geotype extends CI_Model {
	
	public $setting = NULL;
	public $data = NULL;
	
	function __construct()
	{
		parent::__construct();
		$this->setting = $this->settings->data;	
	}	
		
		
	public function delete($id)
	{
		return $this->db->where('id',$id)->delete('geotype');
	}

}