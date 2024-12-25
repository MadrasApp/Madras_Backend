<?php defined('BASEPATH') OR exit('No direct script access allowed');

class M_classroom extends CI_Model {
	
	public $setting = NULL;
	public $data = NULL;
	
	function __construct()
	{
		parent::__construct();
		$this->setting = $this->settings->data;	
	}	
		
		
	public function delete($id)
	{
		return $this->db->where('id',$id)->delete('classroom');
	}

	public function addClassroom_Data($tid,$data_type)
	{
		$this->db->where('cid',$tid)->delete('classroom_data');
		foreach($data_type as $k=>$v){
            switch ($k){
                case 'book':
                    foreach($v as $k1=>$v1){
                        $data = array();
                        $data['cid'] = $tid;
                        $data['data_type'] = $k;
                        $data['data_id'] = $v1;
                        $data['startpage'] = $data_type['startpage'][$v1];
                        $data['endpage'] = $data_type['endpage'][$v1];
                        $this->db->insert('classroom_data',$data);
                    }
                    break;
                case 'hamniaz':
                    foreach($v as $k1=>$v1){
                        $data = array();
                        $data['cid'] = $tid;
                        $data['data_type'] = $k;
                        $data['data_id'] = $v1;
                        $data['startpage'] = 0;
                        $data['endpage'] = 0;
                        $this->db->insert('classroom_data',$data);
                    }
                    break;
            }
        }
    }
}