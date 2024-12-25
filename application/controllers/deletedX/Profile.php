<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Profile extends CI_Controller {
	
	
	function __construct(){
		parent::__construct();
	}

	public function index()
	{
		if( ! isset( $this->user->data->id ) ) redirect('login');
		
		$data['user'] = NULL;
		$data['view_key'] = FALSE;
		
		$userId = $this->input->get('user');

        if( $userId )
        {
            if( $this->user->can('edit_user') )
            {
                if( ! $this->db->where('id',(int)$userId)->count_all_results('users') )
                {
                    $data['alert'] = '<i class="fa fa-warning fa-2x"></i> &nbsp; '.' کاربر مورد نظر پیدا نشد ';
                }
                else
                {
                    $user = $this->db->where('id',(int)$userId)->get('users')->row();
                    $user->meta = $this->user->getMeta((int)$userId);
                    $data['view_key'] = TRUE;
                    $data['mine'] = TRUE;
                }
            }
            else
            {
                $data['alert'] = '<i class="fa fa-warning fa-2x"></i> &nbsp; '.' شما نمی توانید اطلاعات کاربران دیگر را ویرایش کنید ';
            }
        }
        else
        {
            $user = $this->user->data;
            $user->meta = $this->user->getMeta();
            $data['view_key'] = TRUE;
            $data['mine'] = TRUE;

            set_cookie('ci_profile_viewd', 'true' , 10*24*3600);//Alireza Balvardi
            $data['viewd'] = 'true';
        }

        if( $data['view_key'] && $user->type == 'expert')
        {
            $this->load->model('m_group','group');

            $state = $user->meta->state_json;

            if($this->tools->isJson($state))
            {
                $state = json_decode($state);
                $data['province'] = ""; $i = 0;
                foreach ($state as $sid)
                {
                    $id =  $i == 0 ? PROVINCE_ID : $state[$i-1]; $i++;
                    $data['province'] .= $this->group->creatSelect($id,NULL,'<div class="group-holder"><select class="form-control" name="state">','</select></div>',TRUE,$sid);
                }
            }
            else
            {
                $data['province'] = $this->group->creatSelect(PROVINCE_ID,NULL,'<div class="group-holder"><select class="form-control" name="state">','</select></div>');
            }

            $skill = $user->meta->skill_json;

            if($this->tools->isJson($skill))
            {
                $skill = json_decode($skill);

                $data['skill'] = ""; $i = 0;

                foreach ($skill as $sid)
                {
                    $id =  $i == 0 ? SKILL_ID : $skill[$i-1]; $i++;
                    $data['skill'] .= $this->group->creatSelect($id,NULL,'<div class="group-holder"><select class="form-control" name="skill">','</select></div>',TRUE,$sid);
                }
            }
            else
            {
                $data['skill'] = $this->group->creatSelect(PROVINCE_ID,NULL,'<div class="group-holder"><select class="form-control" name="skill">','</select></div>');
            }
        }

        if( $data['view_key'] )
        {
            $data['user'] = $user;
            $data['meta'] = $this->db->where('user_id',$user->id)->get('user_meta')->result();
        }

		$data['_title'] =  "ویرایش پروفایل";

        //======================================//
        $this->bc->add('ویرایش پروفایل' ,current_url());
        //======================================//
		
		if( $data['user'] )
        {
            $data['script'] = array(
                'js/jquery.ui.datepicker.min.js',
                '../js/jQuery-Uploader/jquery.ui.widget.js',
                '../js/jQuery-Uploader/jquery.iframe-transport.js',
                '../js/jQuery-Uploader/jquery.fileupload.min.js',
                '../js/jQuery-Uploader/jquery.fileupload-process.js',
            );
            $data['css'][] = '../style/ui.1.12.1/jquery-ui.css';//Alireza Balvardi
        }
		$this->tools->view(array('user/v_user_head','user/v_user_edit_profile'),$data);
	}
}