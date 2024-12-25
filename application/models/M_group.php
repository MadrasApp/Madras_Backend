<?php defined('BASEPATH') OR exit('No direct script access allowed');

class M_group extends CI_Model
{
    public $items;
    public $info;
    public $ids;

    function __construct()
    {
        parent::__construct();
        $this->items = array();
        $this->info  = array();
        $this->ids   = array();
    }

    public function addInfo($id=0,$name=FALSE)
    {
        if(!$id) return;

        if(is_array($id))
        {
            foreach ($id as $i)
                $this->addInfo($i);
        }

        if($name === FALSE)
        {
            if(!in_array($id, $this->ids)) $this->ids[] = $id;
        }
        else
        {
            $this->info[$id] = $name;
        }
    }

    public function getInfo()
    {
        if (is_array($this->ids) && !empty($this->ids))
        {
            $results = $this->db->where_in('id',$this->ids)->select('id,name')->get('group')->result();
            foreach ($results as $row)
            {
                $this->info[$row->id] = $row->name;
                unset($this->ids[$row->id]);
            }
        }
        return $this->info;
    }

    public function name($id=0)
    {
        if(!$id) return NULL;

        if(empty($this->info))
        {
            $this->addInfo($id);
            $this->getInfo();
        }

        if(isset($this->info[$id]))
            return $this->info[$id];

        $row = $this->db->select('name')->where('id',$id)->get('group',1)->row();

        if(empty($row)) return NULL;
        return $row->name;
    }

    /*function get($id=NULL)
    {
        $family = $this->db->select('id,name,parent,GetFamilyTree(id) as family')->where('id',$id)->get('group')->row();

        if( $family )
        {
            $result = $this->db->where_in('id',explode(',',$family->family))->order_by('position','asc')->get('group')->result();
            foreach ($result as $key=>$item)
            {
                $this->items[$item->id] = $item;
            }
        }
        $this->setSubGroup();
        return $this->items;
    }*/
	
	function get($id=NULL)
    {
		$this->getChildren($id);
        $this->setSubGroup();
        return $this->items;
    }
	
	function getChildren($id)
	{
		$children = $this->db->where('parent',$id)->order_by('position','asc')->get('group')->result();
		foreach($children as $item)
		{
			$this->items[$item->id] = $item;
			$this->getChildren($item->id);
		}
	}

    function convertItems()
    {
        foreach( $this->items as $id=>$item )
        {
            $this->setSubGroup($id);
        }
    }

    function setSubGroup($id=NULL)
    {
        foreach( $this->items as $item )
        {
            if( $id && $item->parent == $id )
            {
                $this->items[$id]->sub[$item->id] = $item;
                $this->setSubGroup($item->id);
                unset($this->items[$item->id]);
            }
            elseif( $id == NULL )
                $this->setSubGroup($item->id);
        }
    }

    function creatList($html="",$start="<ul>",$end="</ul>",$list=NULL,$selected=NULL)
    {
        if( ! $list )
        $list = $this->items;

        if( empty($list) ) return NULL;

        $res = $start;
        foreach( $list as $item )
        {
            $temp = $html;
            $temp = str_replace('[ID]',$item->id,$temp);
            $temp = str_replace('[PARENT]',$item->parent,$temp);
            $temp = str_replace('[POSITION]',$item->position,$temp);
            $temp = str_replace('[NAME]',$item->name,$temp);
            $temp = str_replace('[SELECTED]',( $selected && $item->id == $selected ) ?'selected':'',$temp);


            $children = "";
            if( isset($item->sub) )
            {
                $children = $this->creatList($html,$start,$end,$item->sub);
            }
            $temp = str_replace('[CHILDREN]',$children,$temp);
            $res .= $temp;
        }
        $res .= $end;

        return $res;
    }

    function deleteGroup($id=NULL)
    {
		$this->items = array();
		
		$this->getChildren($id);
		
		if(!empty($this->items))
		{
			foreach($this->items as $item)
			$this->db->where('id',$item->id)->delete('group');
		}
        return $this->db->where('id',$id)->delete('group');
    }

    function add($data)
    {
    }

    function getRow($id=NULL)
    {
        if( ! $id ) return NULL;
        return $this->db->where('parent',$id)->order_by('position','asc')->get('group')->result();
    }
	
	function creatSelect($id,$html=NULL,$start='<select class="form-control" name="group">',$end="</select>",$please_select = TRUE,$selected=NULL,$all=FALSE)
    {
        if (!$id) return NULL;
        $list = $this->getRow($id);

        if (!$list) return NULL;

        if ($please_select && $selected) $start .= '<option disabled>انتخاب کنید</option>' . "\n";
        elseif ($please_select) $start .= '<option disabled selected>انتخاب کنید</option>' . "\n";

        if ($all)
        {
            if( ! is_string($all) ) $all = 'همه';

            $start .= '<option value="">'.$all.'</option>'."\n";
        }

		
		if( ! $html ) $html = '<option value="[ID]" [SELECTED]>[NAME]</option>'."\n";
		
		return $this->creatList($html,$start,$end,$list,$selected);
	}
	
	function getParents($from,$to=NULL)
	{
		$re[] = $from;
		$g = $this->db->select('id,parent')->where('id',$from)->get('group',1)->row();
		
		if( $to )
		{
			while( $g && $g->id != $to && $g->parent != 0)
			{
				$g = $this->db->select('id,parent')->where('id',$g->parent)->get('group',1)->row();
				if( $g && $g->parent != 0 ) $re[] = $g->id;
			}			
		}
		else
		{
			while( $g && $g->parent != 0 )
			{
				$g = $this->db->select('id,parent')->where('id',$g->parent)->get('group',1)->row();
				if( $g && $g->parent != 0 ) $re[] = $g->id;
			}	
		}
		return array_reverse($re);
	}
	

}