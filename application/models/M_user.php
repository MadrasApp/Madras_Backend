<?php defined('BASEPATH') OR exit('No direct script access allowed');

if(!session_id()) session_start();

class M_user extends CI_Model {

	public $user_id;
	public $data;
	public $can;
	public $logged;
	
	function __construct(){
		
		parent::__construct();
				
		$this->load->helper(array('cookie','security'));
		
		//Alireza Balvardi Start
		if($this->check_login()){
			$this->load->helper('url');
			$currentURL = current_url();
			if(strpos($currentURL,'admin/login')!==FALSE)
				redirect('admin/home');
		}
		//Alireza Balvardi End
	}

	public function check_login()
	{
		
		$user_session = $this->session->userdata('ci_user');
		
		$user_id = $username = $password = "";
		
		if( !empty($user_session['user_id']) )
		{
			$user_id  = $user_session['user_id'];
			$username = $user_session['username'];
			$password = $user_session['password'];
		}
		else
		{
			$user_id  = get_cookie('ci_user_id',TRUE);
			$username = get_cookie('ci_username',TRUE);
			$password = get_cookie('ci_password',TRUE);
		}
		
		if( is_numeric($user_id) && ($user = $this->selectUserById($user_id)) )
		{
			if( $username == $user->username && $password == $user->password )
			{
				$this->logged = TRUE;
				$this->user_id = $user->id;
				$this->data = $user;
				$this->can = $this->selectLevelById( $this->getUserLevel() );
				return TRUE;
			}
		}
		$this->logged = FALSE;
		return FALSE;
	}
	
	public function login($data)
	{
		$username      = $data['username'];
		$hash_password = do_hash($data['password']);

        /*$this->db->where('event','login_fail');
        $this->db->where('datestr <', strtotime("-2 hours") );
        $this->db->delete('logs');

        if( @$this->settings->data['block_user_login'] == 1 )
        {
            $maxTry    = $this->settings->data['block_user_login_per'];
            $blcokTime = $this->settings->data['block_user_login_time'];
            $ip        = $this->input->ip_address();

            $this->db->where('ip',$ip);
            $this->db->where('event','login_fail');
            $this->db->where('datestr >', strtotime("-{$blcokTime} minutes") );

            if( $this->db->count_all_results('logs') >= $maxTry )
            {
                return FALSE;
            }
        }*/

		$query = $this->db->select('id,username')->where(array('username' => $username, 'password' => $hash_password))->get('users');

        if( $query->num_rows() == 0 )
        {
            $this->load->helper('email');
            if(valid_email($username))
                $query = $this->db->select('id,username')->where(array('email' => $username, 'password' => $hash_password))->get('users');
        }

		if( $query->num_rows() == 1 )
		{
			$user    = $query->row();
			$user_id = $user->id;
			
			$userdata = array(
				'user_id'   => $user_id,
				'username'  => $user->username,
				'password'  => $hash_password,
			);
			
			$this->session->set_userdata('ci_user',$userdata);
			
			if( isset($data['stay']) && $data['stay']=='true')
			{
				$expire = 10*24*3600;
				set_cookie('ci_user_id' , $user_id        , $expire );
				set_cookie('ci_username', $user->username , $expire );
				set_cookie('ci_password', $hash_password  , $expire );
			}
			return  TRUE; 
		}
		else
		{
            //$this->logs->add('login_fail');
			$this->logout();
		}
		return FALSE;
	}
	
	public function logout()
	{
		$this->session->unset_userdata('ci_user');
		delete_cookie('ci_user_id');
		delete_cookie('ci_username');
		delete_cookie('ci_password');
		
		if( isset($this->user_id) && is_numeric($this->user_id) )
		$this->db->where('user_id',$this->user_id)->delete('onlines');
	}
	
	public function checkAccess($access = NULL)
	{
		$key = FALSE;
		$user_level = @$this->data->level;
        $is_active  = @$this->data->active;
		if( $user_level && $is_active )
		{
			if( $user_level == "admin" )
			{
				$key = TRUE;
			}
			else
			{
				$level = $this->db->where('level_id',$user_level)->get('user_level',1);
				if( $level->num_rows() > 0 )
				{
					if( $access )
					{
						if( $this->can($access) )
						return TRUE;
					}
					else
					$key = TRUE;
				}
			}
		}
		if( ! $key )
			die( 'شما به این قسمت دسترسی ندارید');
	}			

	public function selectUserById($id,$sel = "*"){
		return $this->db->select($sel)->where('id',$id)->get('users',1)->row();
	}
	public function selectUserByNeme($username,$sel = "*"){
		$user = $this->db->select($sel)->where('username',$username)->get('users',1)->row();
		return $user;	
	}
	public function selectLevelById($id){
		$query = $this->db->where('level_id',$id)->get('user_level')->result();
		
		if( $query && is_array($query) )
		{
			$return = array();

			foreach( $query as $key => $row )
			{
				$return[$row->level_key] = $row->level_value;
			} 
			
			return $return;
		}
		
		return NULL;	
	}
    public function getSrc($user_id = NULL, $size = 150, $src = NULL,$case = 'avatar'){
        if( $case != 'avatar' && $case != 'cover' ) return NULL;

        $SRC = "";

        if (!$user_id && !$src) {

            $SRC = @$this->settings->data['default_user_'.$case];
        }
        elseif (!$src)
        {
            if ($user_id && $user = $this->selectUserById($user_id,$case))
                $SRC = $user->$case;
            elseif (!$user_id)
                $SRC = @$this->data->$case;
        }
        else $SRC = $src;

        if (filter_var($SRC, FILTER_VALIDATE_URL))
        {
            return $SRC;
        }
        else
        {
            $SRC = file_exists($SRC) ? $SRC : @$this->settings->data['default_user_'.$case];
            if ($size !== 'lg') {
				
				if(function_exists('thumb'))
				{
					$SRC = thumb($SRC, $size);	
				}
				else
				{
					$this->load->model('admin/m_media', 'media');
					$SRC = $this->media->thumb($SRC, $size);					
				}

            }
            return base_url() . $SRC;
        }
    }
    public function getAvatarSrc($user_id = NULL, $size = 150, $src = NULL){
        return $this->getSrc($user_id, $size, $src,'avatar');
    }
    public function getCoverSrc($user_id = NULL, $size = 300, $src = NULL){
        return $this->getSrc($user_id, $size, $src,'cover');
    }
	public function getUserLevel($user_id = FALSE){		
		if( $user_id && $user = $this->selectUserById($user_id,'level') )
		{
			$level = $user->level;
		}
		elseif( ! $user_id ) 
		{
			$level = @$this->data->level;
		}
		
		if( !empty( $level ) )
		{
			return  $level;
		}
		return NULL;	
	}
	public function getLevelName($id = NULL , $level = NULL){
		if( $id )
		{
			$where = array('level_key'=>'level_name','level_id'=>$id);
			$level = $this->db->select('level_value')->where($where)->get('user_level')->row(0);
			return @$level->level_value;
		}
        $level = $level?:$this->getUserLevel();
        $text = "";
        if( $level )
		{
			switch ($level){
                case "admin":
                    $text = "مدیر کل";
                    break;
                case "user":
                    $text = "کاربر";
                    break;
                case "teacher":
                    $text = "استاد";
                    break;
                default :
                    $text = $this->getLevelName($level);
            }
        }
        return $text;
    }
	public function is_admin(){
		$level = $this->getUserLevel();
		
		if( $level == "admin" )
		return TRUE;
		
		return FALSE;		
	}
    public function isEditor(){
        $level = $this->getUserLevel();
        //if($level != '' && $level != 'user')
        if($level != '' && $level != 'user')
            return TRUE;

        return FALSE;
    }
	public function can($do = NULL , $user_id = NULL){

		if( $do  )
		{
			$do = "_".$do;
			
			$level = $this->getUserLevel($user_id);

            if(!$user_id)
                $is_active = @$this->data->active;
            else
                $is_active = $this->db->get_field('active','users',$user_id);

            if($is_active != 1)
                return FALSE;

			if( $level == "user" )
			{
				return FALSE;
			}
			elseif( $level == "admin" )
			{
				return TRUE;
			}
			else
			{
				if( $user_id )
				{
					$where = array('level_id'=>$level,'level_key'=>$do);
					$level_data = $this->db->where($where)->get('user_level')->row(0);
					return @$level_data->$do == 1; 
				}
				else
				{
					if( @$this->can[$do] == 1 )
					return TRUE;
				}
			}
		}
		return FALSE;		
	}
	public function addLevel($id,$data){
		$done = TRUE;
		foreach($data as $level_key=>$level_value)	
		{
			$row = array('level_id'=>$id,'level_key'=>$level_key,'level_value'=>$level_value);
			if( ! $this->db->insert('user_level', $row) )
			$done = FALSE;
		}			
		return $done;
	}
	public function updateLevel($id = NULL ,$data = NULL){		
		$done = TRUE;
		
		if( ! $id OR ! $data ) return FALSE;
		
		$fields = array();
		$fetch_fields = $this->db->select('level_key')->where('level_id',$id)
								 ->get('user_level')->result_array();
		if( $fetch_fields )
		foreach( $fetch_fields as $key => $field )
		$fields[] = $field['level_key'];
		
		foreach($data as $level_key => $level_value)	
		{
			if( in_array($level_key,$fields) ) 
			{
				$row = array('level_value' => $level_value);
				$where = array('level_id'=>$id,'level_key'=>$level_key);
				
				if( ! $this->db->where($where)->update('user_level', $row) )
				$done = FALSE;
			}
			else
			{
				$row = array('level_id'=>$id,'level_key'=>$level_key,'level_value'=>$level_value);
				if( ! $this->db->insert('user_level', $row) )
				$done = FALSE;
			}
		}			
		return $done;		
	}	
	public function deleteLevel($id,$replace){				
		$this->db->where('level', $id);
		
		if( $this->db->update('users',array('level' => $replace)))
		if( $this->db->delete('user_level', array('level_id' => $id)) )
		return TRUE;
		
		return FALSE;
	}
	public function isOnline($id = NULL){
		if( ! $id ) $id = @$this->user->user_id;						
		return $this->db->where('user_id',$id)->count_all_results('onlines') > 0;
	}
	public function areOnline($ids = NULL){
		if( is_array($ids) )
		{


			$onlines = $this->db->select('user_id')->where_in($ids)->get('onlines')->result();
            $users = $this->db->select('id,last_seen')->where_in($ids)->get('users')->result();


            $ids = array_flip($ids);

            foreach ($users as $user)
                $ids[$user->id] = $this->tools->Date($user->last_seen,FALSE);

            /*foreach ($ids as $k=>$id)
                $ids[$k] = FALSE;*/

			foreach($onlines as $row)
			{
				//if( array_key_exists($row->user_id,$ids))
				    $ids[$row->user_id] = TRUE;
			}
			
		}
		return $ids;
	}		
	public function getOnlines(){				
		$all_onlines = $this->db->count_all_results('onlines');
		$this->db->where('user_id',0);
		$g_online =  $this->db->count_all_results('onlines');
		
		$this->db->select('user_id,username,displayname');
		$this->db->where('user_id !=',0);
		$this->db->order_by('date','desc');
		$users_online_q = $this->db->get('onlines',0,50);
		$users_online_num = $users_online_q->num_rows();
		$users_online = $users_online_q->result_array();
		
		$return = array(
			'all'  =>  $all_onlines,
			'g_online' => $g_online,
			'users_online_num' => $users_online_num,
			'users_online'=>$users_online
		);
		return $return;
	}
	public function getRegisteredUsersInfo(){						
		$all_users = $this->db->count_all_results('users');
		$this->db->where('register','done');
		$done_users =  $this->db->count_all_results('users');
		$this->db->where('register !=','done');
		$pending_users =  $this->db->count_all_results('users');
		
		$this->db->where('date','done');
		
		$return = array(
			'all'  =>  $all_users,
			'done' => $done_users,
			'pending' => $pending_users,
		);
		return $return;
	}
	public function htmlAvatar($userId=NULL,$src=NULL,$class=""){

		if( ! $src && $userId )
		{
			$user = $this->db->select('avatar')->where('id',$userId)->get('users',1)->row();
			if( isset($user->avatar) )
			$src = $user->avatar;
		}

		$result = "";
		
		if( $userId != NULL && $userId != 0 )
		$result .= "<div class=\"avatar $class\" on-stay=\"userPopup,800\" data-id=\"$userId\">";
		else
		$result .= "<div class=\"avatar $class\">";
		
		$result .= "<img src=\"".$this->getAvatarSrc($userId,150,$src)."\"/>";
		
		$result .= "</div>";
		
		return $result;
	}

    /**********************************
                User Meta
    ********************************/

    public function addMeta($data,$userId=NULL)
    {
        if( ! $userId )
            $userId = $this->user_id;

        foreach( $data as $key=>$value  )
        {
			if( is_array($value) )
			{
				foreach( $value as $v )
				$this->db->insert('user_meta' , array(
					'user_id'    => $userId,
					'meta_name'  => $key,
					'meta_value' => $v
				));
				$value = json_encode($value);
				$key .= '_json';
			}
            $this->db->insert('user_meta' , array(
                'user_id'    => $userId,
                'meta_name'  => $key,
                'meta_value' => $value
            ));
        }
    }

    public function updateMeta($data,$userId=NULL)
    {
        if( ! $userId )
            $userId = $this->user_id;

        foreach( $data as $key=>$value  )
        {
            if( is_array($value) )
            {
                $this->db->where(array(
                    'user_id'    => $userId,
                    'meta_name'  => $key.'_json',
                ))->delete('user_meta');
            }
            $this->db->where(array(
                'user_id'    => $userId,
                'meta_name'  => $key,
            ))->delete('user_meta');

            /*$this->db->where(array(
                'user_id'    => $userId,
                'meta_name'  => $key,
            ))->set('meta_value',$value)->update('user_meta');*/
        }
        $this->addMeta($data,$userId);
    }

    public function getMeta($userId=NULL)
    {
        if( ! $userId )
            $userId = $this->user_id;

        $meta = new stdClass();

        $data = $this->db->where('user_id',$userId)->get('user_meta')->result();

        foreach( $data as $key=>$value )
        {
			
			if( isset($meta->{$value->meta_name}) )
			{
				if( ! is_array($meta->{$value->meta_name}) )
					$meta->{$value->meta_name} = array($meta->{$value->meta_name});
				array_push($meta->{$value->meta_name},$value->meta_value);
			}
			else
				$meta->{$value->meta_name} = $value->meta_value;
        }

        return $meta;
    }
	
	public function meta($meta=NULL,$metaName=NULL)
	{
		if( ! $meta OR ! $metaName ) return NULL;
		
		if( isset( $meta->$metaName ) && $meta->$metaName != '' )
		return $meta->$metaName;
		
		return NULL;
	}
    /***********************
    public function experts()
    {
        $this->db->select('u.id, u.username, u.displayname, u.avatar, m.meta_name, m.meta_value');
        $this->db->from('users u');
        $this->db->join('user_meta m', 'm.user_id=u.id', 'right');
        //$this->db->join('Soundtrack c', 'c.album_id=a.album_id', 'left');
        $this->db->where('u.active',1);
        $this->db->where('u.type','expert');
        $this->db->group_by('id');
        //$this->db->order_by('c.track_title','asc');


        return $this->db->get()->result();
    }*/

    public function deleteAccount($id=0)
    {
        $id = (int)$id;
        if(!$id) return FALSE;

        if($id==1) return FALSE;

        $user = $this->db->where('id',$id)->get('users')->row();

        if(empty($user)) return FALSE;
        
        $this->load->helper('file');

        if( ! $this->db->where('id',$id)->delete('users') )
            return FALSE;

        $username = trim($user->username);
        if($username != '' && $username != '_ac' && $username != '_df')
        {
            delete_files("uploads/{$user->username}",TRUE);
            delete_files("uploads/_ac/{$user->username}",TRUE);
            @unlink("uploads/{$user->username}");
            @unlink("uploads/_ac/{$user->username}");
        }

        $this->db->where('table','users')->where('row_id',$id)->delete('comments');
        $this->db->where('table','users')->where('row_id',$id)->delete('rates');

        return TRUE;
    }

} /* end of model */
?>