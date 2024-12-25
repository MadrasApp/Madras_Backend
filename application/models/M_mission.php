<?php defined('BASEPATH') OR exit('No direct script access allowed');

class M_mission extends CI_Model {
	
	public $setting = NULL;
	public $data = NULL;
	
	function __construct()
	{
		parent::__construct();
		$this->setting = $this->settings->data;	
	}	
		
	public function unsubmit($id)
	{
		$data = array('submitted'=>0);
		return $this->db->where('id',$id)->update('missions',$data);
	}
		
	public function submit($id)
	{
		$data = array('submitted'=>1);
		return $this->db->where('id',$id)->update('missions',$data);
	}
			
	public function delete($id)
	{
		return $this->db->where('id',$id)->delete('missions');
	}

	
	/*====================================================
		CLIENT SIDE		
	====================================================*/	
	public function add($data)
	{
		if( ! isset($data['to'],$data['text']) )
		    return FALSE;
		
		if( ! isset( $this->user->data->id ) && ! isset($data['from']) )
		    return FALSE;

        if( ! $data['from'] )
            $data['from'] = $this->user->data->id;

        $data['submitted'] = $this->settings->data['auto_submit_mission'];
        $data['date'] = date('Y-m-d H:i:s');
        $data['done'] = 0;

		if( $this->db->insert('missions',$data) )
		    return $this->db->insert_id();

		return  NULL;
	}
	
	public function selectById($id)
	{
		if( ! $id ) return NULL;
		
		$id      = intval($id);
		$add     =  "WHERE m.id=$id LIMIT 1";
		$query   = $this->tools->buildQuery("user-missions",$add);
		$mission = $this->db->query($query)->result();
		
		return isset( $mission[0] )	? $mission[0]:NULL;
	}	
	
	public function printUserMissions($do='to',$user_id,$from=0,$limit=20)
	{
        $me      = isset($this->user->data->id) ? $this->user->data->id:0;
        $user_id = (int)$user_id;
        $from    = (int)$from;
        $limit   = (int)$limit;
        $do      = $do == 'to' ?'to':'from';
        $undo    = $do == 'to' ?'from':'to';
        $mine    = $me == $user_id;

        $where = "`m`.`submitted`=1 AND `m`.`{$do}`={$user_id}";

        if(!$this->user->can('edit_user') && !$mine)
        {
            $where .= " AND (m.hidden=0 OR (m.hidden=1 AND m.{$undo}={$me}))";
        }

		$add = 
		"WHERE $where 
		ORDER BY `m`.`date` DESC LIMIT $from,$limit";

        $query = $this->tools->buildQuery('user-missions',$add);

        $missions = $this->db->query($query)->result();

        $total = $this->db->where($where,'',FALSE)->count_all_results('missions m');

        $from += $limit;

		if( $missions )
		{
			foreach( $missions as $mission )
                echo $this->htmlTemplate($mission);

            if( $total > $from ) : ?>
                <div class="text-center load-more-missions">
                    <span class="text-muted mb-10" onclick="loadMoremissions(this,<?php echo  "'$do',$user_id,$from,$limit" ?>)">
                        <i class="fa fa-refresh"></i>
                        <span> &nbsp; نمایش بیشتر</span>
                    </span>
                </div>
            <?php endif;
		}	
	}
	
	public function htmlTemplate($mission)
	{
        $me       = isset($this->user->data->id) ? $this->user->data->id:0;
        $isMaster = $me && $mission->from == $me;
        $isProf   = $me && $mission->to == $me;
        $isAdmin  = $this->user->can('edit_user');

        $result =
        '<div class="item mission" data-id="'.$mission->id.'">
            <header>
                <div class="author">
                    <a href="'.site_url('user/'.$mission->from_username).'">
                        <img src="'.$this->user->getAvatarSrc(NULL,150,$mission->from_avatar).'" alt="'.html_escape($mission->from_name).'">
                    </a>
                </div>
                <div class="name">
                    <i>'.$mission->from_name.'</i>
                </div>
                <div class="ms-date">'.$this->tools->Date($mission->date).'</div>
            </header>
            <ul class="list-unstyled ms-options">
                <li class="toggle-done done-status '.($mission->done ? 'done':($isMaster ? 'can-edit set-mission-done':'')).'" title="'.($mission->done?'انجام شده':'').'">
                    <i class="fa fa-check-circle"></i>
                </li>
                '.(($isAdmin OR (!$mission->done && ($isMaster OR $isProf))) ? '<li class="can-edit ms-delete" onClick="deleteMission(this)"><i class="fa fa-trash"></i></li>':'').'
            </ul>
            <div class="body clearfix">
                <div class="content wbr">
                '.html($mission->text).'
                '.(($isMaster OR $isProf OR $isAdmin) ? '<p class="text-info"> اطلاعات تماس : ' . $mission->tel .'</p>':'').'
                </div>
            </div>
        </div>';

		return $result;	
	}
}