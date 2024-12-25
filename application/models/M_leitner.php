<?php defined('BASEPATH') OR exit('No direct script access allowed');

class M_leitner extends CI_Model {
	
	public $setting = NULL;
	public $data = NULL;
	
	function __construct()
	{
		parent::__construct();
		$this->setting = $this->settings->data;	
	}	
		
	public function delete($id)
	{
        $childs =  $this->db->select('id')->where('parent',$id)->get('leitner')->result();

        if(!empty($childs))
        {
            foreach ($childs as $item)
                $this->delete($item->id);
        }
		return $this->db->where('id',$id)->delete('leitner');
	}

	
	/*====================================================
		CLIENT SIDE		
	====================================================*/	
	public function add($data)
	{
		if( ! isset($data['table'],$data['row_id'],$data['parent'],$data['text']) )
		return FALSE;
		
		if( ! isset( $this->user->data->id ) && ! isset($data['name'],$data['email']) )
		return FALSE;
		
		$userid = isset( $this->user->data->id ) ? $this->user->data->id:0;
		$name   = isset( $this->user->data->id ) ? $this->user->data->displayname:$data['name'];
        $email  = isset( $this->user->data->id ) ? $this->user->data->email:$data['email'];

		$data = array(
			'user_id'   => $userid,
			'title'      => $data['title'],
			'regdate'      => date('Y-m-d H:i:s'),
		);
		
		if( $this->db->insert('leitner',$data) )
		return $this->db->insert_id();
		return  NULL;
	}	 	 	 	 	 	 	 	 	 

    public function addLeitnerPart($leitnerId,$data)
    {
		if((int)$data['master'] == 0 && $leitnerId == 0){
			return FALSE;
		}
		if(!intval($data['master'])){
			$data['catid'] = 0;
		}
		$userid = isset( $this->user->data->id ) ? $this->user->data->id:0;
		$partData = array(
            'catid' 		=> $data['catid'] ,
            'title' 	=> trim($data['title']) == '' ? NULL:$data['title'],
            'description' 	=> trim($data['description']) == '' ? NULL:$data['description'],
			'user_id' 	=>$userid
        );

        if(isset($data['id']) && (int)$data['id'])
        {
            $this->db->where('id',(int)$data['id'])->update('leitner',$partData);
			return $data['id'];
        }
        else
        {
            $this->db->insert('leitner',$partData);
			return $this->db->insert_id();
        }
    }
	
	public function get($table,$row_id,$from=0,$limit=20)
	{
		$where = array('table'=>$table,'row_id'=>$row_id);
		return $this->db->where($where)->get('leitner',$from,$limit)->result();	
	}
	
	public function getPrimary($table,$row_id,$from=0,$total=20)
	{
		$where = array('table'=>$table,'row_id'=>$row_id,'parent'=>0);
		return $this->db->where($where)->get('leitner',$from,$total)->result();
		
	}	
	
	public function getChilds($table,$row_id,$id,$from=0,$total=20)
	{
		$where = array('table'=>$table,'row_id'=>$row_id,'parent'=>$id);
		return $this->db->where($where)->get('leitner',$from,$total)->result();
		
	}
	
	public function postLeitner($id,$from=0,$total=20)
	{
		$where = array('table'=>$table,'parent'=>$id);
		return $this->db->where($where)->get('leitner',$from,$total)->result();
		
	}	
	
	public function postLeitnerCount($id)
	{
		$where = array('table'=>'posts','row_id'=>$id);
		return $this->db->where($where)->count_all_results('leitner');	
		
	}
	
	public function selectById($id,$mod='post-leitner')
	{
		if( ! $id ) return NULL;
		
		$id      = intval($id);
		$add     = $mod == 'user-reviews' ? "WHERE c.id=$id LIMIT 1":"WHERE id=$id LIMIT 1";
		$query   = $this->tools->buildQuery($mod,$add);
		$leitner = $this->db->query($query)->result();	
		
		return isset( $leitner[0] )	? $leitner[0]:NULL;
	}	
	
	public function htmlTemplate($cm)
	{
        $USER = isset( $this->user->data->id ) ? $this->user->data->id:0;

		$result =
            /** @lang text */
            '<div class="item leitner" item-id="'.$cm->id.'">
            <header>
                <div class="author">
                    '.($cm->user_username ?'<a href="'.site_url('user/'.$cm->user_username).'">':'').'
                    <img src="'.$this->user->getAvatarSrc(NULL,150,$cm->user_avatar).'" alt="'.html_escape($cm->name).'">
                    '.($cm->user_username ?'</a>':'').'
                </div>
                <div class="cm-date">
                    '.$this->tools->Date($cm->date).'
                </div>
            </header>
            <ul class="list-unstyled cm-options">
                <li class="toggle-rate '.( $cm->is_rated ? 'on':'' ).'" data-toggle=\'{"table":"leitner","row":'.$cm->id.'}\'>
                    <i class="fa fa-star"></i>
                    <span>'.($cm->rate_count ? :'').'</span>
                </li>
                <li onClick="replyLeitner(this)">
                    <i class="fa fa-mail-reply-all"></i>
                </li>
                '.($USER && $cm->user_id == $USER ? '<li onClick="deleteLeitner(this)"><i class="fa fa-trash"></i></li>':'').'
            </ul>
            <div class="body clearfix">
                <div class="content wbr">'.html($cm->text).'</div>
                <div class="reply-con"></div>
            </div>
        </div>';
		return $result;	
	}
}
?>