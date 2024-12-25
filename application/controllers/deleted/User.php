<?php defined('BASEPATH') OR exit('No direct script access allowed');

class User extends CI_Controller{
	
	function __construct(){
		parent::__construct();
	}

	public function index($username=NULL)
	{
		$data['mine'] = FALSE;		
		$data['user'] = NULL;
		$data['view_key'] = TRUE;
		$data['view_msg'] = NULL;

		if( ! $username ) redirect('profile');

        $username = urldecode($username);

		$user = $this->db->where('username',$username)->get('users',1)->row();

        if( ! isset($this->user->data->id) )
            $data['mine'] = FALSE;
        elseif( ($user && $user->username == $this->user->data->username) OR $this->user->can('edit_user') )
            $data['mine'] = TRUE;
		
		if( $user )
		{
            if( ! $user->active )
            {
                if( $data['mine'] )
                {
                    if( $user->id == @$this->user->data->id )
                        $data['alert']  = '<i class="fa fa-warning fa-2x"></i> &nbsp; '.'متاسفانه حساب کاربری شما بنا به دلیل زیر مسدود است : ';
                    else
                        $data['alert']  = '<i class="fa fa-warning fa-2x"></i> &nbsp; '.'حساب کاربری این کاربر بنا به دلیل زیر مسدود است : ';

                    $data['alert'] .= '<hr>';
                    $data['alert'] .= '<i class="fa fa-arrow-circle-left fa-lg"></i> &nbsp; ' . $user->pending_reason;
                }
                else
                {
                    $data['alert'] = 'اطلاعات این کاربر در حال حاضر قابل نمایش نیست';
                    $data['view_key'] = FALSE;
                }
            }

            if( $user->active OR $data['mine'] )
            {
                $data['user'] = $user;
                $data['_title'] = $user->displayname;

                $user->meta = $this->user->getMeta($user->id);

                $this->load->model('m_comment','comment');
                $this->load->model('m_mission','mission');

                if($user->type == 'expert')
                {
                    $user->mission_count = $this->db->where('to',$user->id)->where('submitted',1)->count_all_results('missions');
                    $user->mission_done  = $this->db->where('to',$user->id)->where('submitted',1)->where('done',1)->count_all_results('missions');
                }
            }

            //======================================//
            $this->bc->add('کاربران' ,site_url('proficient'));
            $this->bc->add($user->displayname ,current_url());
            //======================================//
		}
		else
		{
			$data['view_key'] = FALSE;
			$data['view_msg'] = "<h3>هیچ کاربری با نام کاربری <span class=en>{$username}</span> وجود ندارد !</h3><p> لطفا نام کاربری را اصلاح کنید </p>";
			$data['_title'] = "404";
			$this->output->set_status_header('404');
		}		
		$this->tools->view(array('user/v_user_head','user/v_user_view'),$data);

        $this->logs->addView('user');
	}
}