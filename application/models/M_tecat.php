<?php defined('BASEPATH') OR exit('No direct script access allowed');

class M_tecat extends CI_Model {
	
	public $setting = NULL;
	public $data = NULL;
	public $tecat_list = NULL;
	
	function __construct()
	{
		parent::__construct();
		$this->setting = $this->settings->data;	
	}	
		
		
	public function delete($id)
	{
		$this->db->where('tid',$id)->delete('tecat_data');
		return $this->db->where('id',$id)->delete('tecat');
	}

	public function getTeCatList($owner,$parent=0,$selectable=TRUE,$post_cat=NULL,$pic=FALSE,$sample = NULL , $start = "<ul>", $end = "</ul>")
	{
		$result = "";
		if( $this->tecat_list === NULL )
		{
			if($owner){
				$this->tecat_list = $this->db->where('user_id',$owner)->order_by('position','asc')->get('tecat')->result();
			} else {
				$this->tecat_list = $this->db->order_by('position','asc')->get('tecat')->result();
			}
		}
		
		$tecat = $this->searchTeCatList($parent);
		if( ! empty($tecat) )
		{
			$result .= $start;
			foreach($tecat as $cat)
			{
				$id = $cat['id']; $name = $cat['name'];$checked = "";
		
				if( $cat['pic'] != NULL && file_exists($cat['pic']) )
					$cpic = $cat['pic'];
				else
					$cpic = $this->setting['default_category_pic'];
					
				$cpic150 = thumb($cpic,'150');
				$cpic300 = thumb($cpic,'300');
				$cpic600 = thumb($cpic,'600');
				
				if( $sample )				
				{
					$str_search  = array('[ID]','[NAME]','[DES]','[PIC]','[PIC-150]','[PIC-300]','[PIC-600]',
										 '[PARENT]','[POS]','[ICON]');
										 
					$str_replace = array($cat['id'],$cat['name'],$cat['des'],$cpic,$cpic150,$cpic300,$cpic600,
										 $parent,$cat['pos'],$cat['icon']);
										 
					$_sample = str_replace($str_search,$str_replace,$sample);
					
					$_sample = 
					str_replace(
						'[SUB-MENU]',
						$this->getTeCatList($owner,$cat['id'],0,NULL,FALSE,$sample),
						$_sample
					);
					
					$result .= $_sample;
				}
				else
				{
					if( $post_cat )
					{
						$post_cat_ar = explode(',',$post_cat);
						if( is_array($post_cat_ar) && in_array($id,$post_cat_ar))
						$checked = 'checked';
					}
					$result .= "<li item-id=$id parent=$parent name='$name'>";
					
					$selectable && $result .= "<label>";
					$selectable && $result .= "<input type=radio value=$id name=tecat[] $checked>";
					
					if( $pic )
					{
						$result .= "<span class=tecat-list-img >";
						$result .= "<img src='$cpic150'>";
						$result .= "</span>";
					}
					
					$result .= $name;
					
					$selectable && $result .= "</label>";
					
					$result .= $this->getTeCatList($owner,$cat['id'],$selectable,$post_cat,$pic);
					
					$result .= "</li>";
				}
			}
			$result .= $end;
			return $result;
		}
		return NULL;
	}

	public function getTeCatSelectMenu($owner,$parent = 0)
	{
		$result = "";
		if( $this->tecat_list === NULL )
		{
			if($owner){
				$this->tecat_list = $this->db->where('user_id',$owner)->order_by('position','asc')->get('tecat')->result();
			} else {
				$this->tecat_list = $this->db->order_by('position','asc')->get('tecat')->result();
			}
		}
		
		$tecat = $this->searchTeCatList($parent);
		if( ! empty($tecat) )
		{
			foreach($tecat as $cat)
			{
				$id = $cat['id']; $name = $cat['name']; $pos = $cat['pos'];
				
				$result .= "<option item-id=$id parent=$parent name='$name' pos='$pos' value=$id >$name</option>";
				$result .= $this->getTeCatSelectMenu($owner,$id);
			}
			return $result;
		}
		return NULL;
	}	

	public function searchTeCatList($parent = 0)
	{
		
		if( $this->tecat_list !== NULL )
		{
			$return = array();
			foreach($this->tecat_list as $key => $cat)
			{
				if( $cat->parent == $parent )
				$return[] = array(
					'id'   => $cat->id ,
					'pos'  => $cat->position ,
					'name' => $cat->name ,
					'pic'  => $cat->pic,
					'des'  => $cat->description,
					'icon' => $cat->icon
					);
			}
			if(count($return) > 0) return $return;
		}
			
		return NULL;
	}	
	
	public function addTeCat($data = NULL)
	{
		if( $data )
		{
			unset($data['id']);// = $this->db->last_id('tecat');
			
			if( !isset($data['pic']) ) $data['pic'] = NULL;
			
			if( $this->db->insert('tecat',$data) )
			{
				$insert_id = $this->db->insert_id();
				$return = $this->db->where('id',$insert_id )->get('tecat')->row();
				
				if( ! isset($return->pic) || trim($return->pic) == "")
				$return->pic = $this->setting['default_category_pic'];
								
				$return->pic150 = thumb($return->pic,'150'); 
				$return->pic300 = thumb($return->pic,'300');
				
				return $return;
			}
		}
		return FALSE;
	}
	
	public function updateTeCat($data = NULL)
	{
		if( $data && isset($data['id']))
		{
			$id = $data['id'];
			if( !isset($data['pic']) ) $data['pic'] = NULL;
			if( $this->db->where('id',$id)->update('tecat',$data) )
			{
				
				$return = $this->db->where('id',$id)->get('tecat')->row();
				
				if( ! isset($return->pic) || trim($return->pic) == "")
				$return->pic = $this->setting['default_category_pic'];
								
				$return->pic150 = thumb($return->pic,'150'); 
				$return->pic300 = thumb($return->pic,'300');			
				return $return;
			}
		}
		return FALSE;
	}	
	
	public function deleteTeCat($id = NULL)
	{
		if( $id )
		{
			$sub = $this->db->where('parent',$id)->get('tecat')->result();
			if( $sub )
			{
				foreach( $sub as $row )
				$this->deleteTeCat( $row->id ); 
			}
			if($this->db->where('id',$id)->delete('tecat'))
			return TRUE;
		}
		return FALSE;
	}

	public function addTeCat_Data($tid,$data_type)
	{
		$this->db->where('tid',$tid)->delete('tecat_data');
		foreach($data_type as $k=>$v){
			foreach($v as $k1=>$v1){
				$data = array();
				$data['tid'] = $tid;
				$data['data_type'] = $k;
				$data['data_id'] = $v1;
				$this->db->insert('tecat_data',$data);
			}
		}
	}
}
?>