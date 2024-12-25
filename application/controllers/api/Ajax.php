<?php
/*
		echo "<pre>";
		print_r();
		echo "</pre>";
		die();

*/

defined('BASEPATH') OR exit('No direct script access allowed');


class Ajax extends CI_Controller {

	public $setting;

	function __construct(){
		
		parent::__construct();
		if (!$this->input->is_ajax_request())
		{
		   //exit('No direct script access allowed 1');
		}		
		$this->load->model('m_user','user');			
		$this->setting = $this->settings->data;	
	}
	
	public function index()
	{
		echo 'User Ajax';
	}
	
	public function login()
	{
		$data = $this->input->post('data');
		try
		{
			if( ! isset( $data['username'],$data['password'] ) )
				throw new Exception("اطلاعات ارسالی صحیح نیست !" , 2);
			
			$this->load->library('form_validation');
			$this->form_validation->set_rules('data[username]','نام کاربری','trim|xss_clean|required');
			$this->form_validation->set_rules('data[password]', 'گذرواژه','trim|xss_clean|required');
			
			if( $this->form_validation->run() == FALSE )
				throw new Exception( validation_errors(), 1);

            $time = time() - 1800;
            $this->db->where('event','fail_login');
            $this->db->where('datestr <=',$time);
            $this->db->delete('logs');

            $this->db->where('event','fail_login');
            $this->db->where('datestr >',$time);
            $this->db->where('ip',$this->input->ip_address());

            $failedLogin = $this->db->count_all_results('logs');

            $showCap = $failedLogin >= 1 ? "<script>$('.login-captcha').slideDown().find('input').addClass('need')</script>":"";

            if( $this->setting['cap_protect'] == 1 && $failedLogin >= 2 && ! $this->tools->checkCaptcha($this->input->post('data[captcha]')))
                throw new Exception( 'تصویر امنیتی اشتباه است' . $showCap  , 1);

			if( ! $this->user->login($data))
			{
				$this->user->logout();
                ($failedLogin < 3 && $this->logs->add('fail_login'));
				throw new Exception("نام کاربری یا گذرواژه اشتباه است !" . $showCap , 1);
			}
			
			$msg = "با موفیت وارد شدید ! <script>setTimeout(function(){location.reload()},600);</script>";
			$this->tools->outS(0,$msg);
            $this->tools->destroyCaptcha();
		}
		catch(Exception $e)
		{
			$this->tools->outE($e);
		}
	}
	
	public function logout()
	{
		try
		{
			$this->user->logout();
			$msg = "با موفیت خارج شدید !";
			$this->tools->outS(0,$msg);
		}
		catch(Exception $e)
		{
			$this->tools->outE($e);
		}
	}
	
	public function sendpass()
	{
		try
		{
			$captcha = $this->input->post('data[captcha]');
            if( $this->setting['cap_protect'] == 1 && ! $this->tools->checkCaptcha($captcha))
                throw new Exception( 'تصویر امنیتی اشتباه است' , 1);

			$this->load->library('form_validation');
			
			$this->form_validation->set_rules('data[email]', 'ایمیل', 'trim|xss_clean|required|valid_email');
			
			if( $this->form_validation->run() == FALSE )
				throw new Exception(validation_errors(), 1); 
				
			/****************/
			$email = $this->input->post('data[email]');
			
			$reset_cap = '<script>refresh_captcha()</script>'; 
				
			if( $this->db->where('email',$email)->count_all_results('users') != 1 )
				throw new Exception("کاربری با این ایمیل پیدا نشد".$reset_cap , 2);
					
			$new_pass = rand(10000,99999);
			
			$mail = "گذرواژه جدید شما $new_pass می باشد .
			با نام کاربری و گذرواژه جدید خود وارد شوید .";
			
			$hash_pass = do_hash($new_pass);
			
			if( ! $this->tools->sendEmail($email,"رمز جدید",$mail) )
				throw new Exception("در حال حاضر امکان ارسال ایمیل وجود ندارد" , 1);
			
			if( ! $this->db->where('email',$email)->update('users',array('password'=>$hash_pass)) )
				throw new Exception("خطا در انجام عملیات" , 1);
			
			
			$this->tools->outS(0,"گذرواژه جدید به ایمیل شما ارسال شد " .$reset_cap);
            $this->tools->destroyCaptcha();
		}
		catch(Exception $e)
		{
			$this->tools->outE($e);
		}
	}
	
	public function register()
	{
		$data = $this->input->post('data');
		
		try
		{
			if( empty( $data ) OR ! isset($data['register_type']) )
				throw new Exception("اطلاعات ارسالی صحیح نیست" , 1);

            if( $this->setting['cap_protect'] == 1 && ! $this->tools->checkCaptcha($this->input->post('data[captcha]')))
                throw new Exception( 'تصویر امنیتی اشتباه است' , 1);

            $auto_u = $this->input->post('data[auto_username]') == 'true';
            $isUser = $this->input->post('data[register_type]') == 'user';

			$this->load->library('form_validation');

            if(!$auto_u)
			$this->form_validation->set_rules('data[username]'   , 'نام کاربری' , 'trim|xss_clean|required|alpha_numeric|to_lower|is_unique[users.username]|min_length[4]|max_length[30]');
			$this->form_validation->set_rules('data[email]'      , 'ایمیل'      , 'trim|xss_clean|required|valid_email|to_lower|is_unique[users.email]');
			$this->form_validation->set_rules('data[password]'   , 'گذرواژه'    , 'trim|xss_clean|required|min_length[4]|max_length[30]');
			$this->form_validation->set_rules('data[displayname]', 'نام'        , 'trim|xss_clean|required|min_length[4]|max_length[30]');
			$this->form_validation->set_rules('data[about]'      , 'درباره'     , 'trim|xss_clean|max_length[1000]');
			$this->form_validation->set_rules('data[age]'        , 'سن'         , 'trim|xss_clean|required|numeric');
			$this->form_validation->set_rules('data[tel]'        , 'موبایل'     , 'trim|xss_clean|valid_mobile|exact_length[11]');
			$this->form_validation->set_rules('data[nationality]', 'قومیت'      , 'trim|xss_clean|max_length[30]');

            if(!$isUser)
            {
                $this->form_validation->set_rules('data[state]'      , 'استان-شهر'   , 'trim|xss_clean|required|numeric');
                $this->form_validation->set_rules('data[skill]'      , 'تخصص'         , 'trim|xss_clean|required|numeric');
                $this->form_validation->set_rules('active_times[]'   , 'ساعات فعال'  , 'trim|xss_clean|required');
            }

            if( $this->form_validation->run() == FALSE )
                throw new Exception( validation_errors() , 2);

			$data = $this->input->post('data');
			
			//$data['username'] = strtolower($data['username']);
			//$data['email']    = strtolower($data['email']);
			
			$allowed = array('username','email','password','displayname','age','tel','nationality');
			$data = array_intersect_key($data,array_flip($allowed));

			$password = $data['password'];
			
			$data['type']           = $isUser ?  NULL:'expert';
			$data['date']           = date('Y-m-d H:i:s');
			$data['last_seen']      = $data['date'];
			$data['password']       = do_hash($data['password']);
			$data['active']         = $this->setting['auto_submit_register'];
			$data['pending_reason'] = "منتظر تایید حساب کاربری از طرف مدیریت";

			if( ! $this->db->insert('users',$data) )
				throw new Exception("خطا در انجام عملیات" , 1);			
			
			$userId = $this->db->insert_id();

            if (!$isUser)
            {
                /*********************************************/
                $state = $this->input->post('data[state]');
                $skill = $this->input->post('data[skill]');
                /*********************************************/
                $this->load->model('m_group','group');
                $meta = array(
                    'state' => $this->group->getParents($state,PROVINCE_ID),
                    'skill' => $this->group->getParents($skill,SKILL_ID)
                );

                $active_times = $this->input->post('active_times[]');

                foreach ($active_times as $AT)
                {
                    $AT = trim($AT);
                    if($AT != '')
                    {
                        $meta['active_times'][] = $AT;
                    }
                }
				
				if($data['tel'] != '')
					$meta['call'][] = $data['tel'];
				
            }
            $about = $this->input->post('data[about]');
            if ($about)
                $meta['about'] =  $about;

            if(isset($meta))
			    $this->user->addMeta($meta,$userId);

            if($auto_u)
            {
                $username = $isUser ? 'user':'proficient';
                $username = $username.$userId.rand(100,999);
                $data['username'] = $username;
                $this->db->set('username',$username)->where('id',$userId)->update('users');
            }

			$logindata = array('username'=>$data['username'],'password'=>$password);
			$this->user->login($logindata);
			
			$msg = "<span>ثبت نام با موفقیت انجام شد !</span><"."script> setTimeout(function(){location.replace('".site_url('profile')."');},600);<"."/script>";
			
			$mail  = "ضمن قدردانی از شما همراه گرامی، ثبت نام شما انجام شد. <br>";
			
			if($this->setting['auto_submit_register'] == 1)
			$mail .= "پروفایل شما اکنون قابل دسترس از طریق لینک زیر می باشد : <br>";
				else
			$mail .= "پروفایل شما بعد از بررسی و تایید  تیم پشتیبانی قابل نمایش برای عموم خواهد بود! لینک پروفایل شما :  <br>";
		
			$link = site_url('user') ."/".$data['username'];
			$mail .= "<a href=\"{$link}\">{$data['username']}</a>";
			
			$this->tools->sendEmail($data['email'],"ثبت نام",$mail);
			
			$this->tools->outS(0,$msg);
            $this->tools->destroyCaptcha();
		}
		catch(Exception $e)
		{
			$this->tools->outE($e);
		}
	}
	
	public function updateprofile()
	{
		try
		{
			if( ! isset($this->user->data->id) ) exit('login');
				
			$this->load->library('form_validation');

            $userId = $this->input->post('data[userid]');
            $user = $this->_getUserToEdit($userId);

			if( $user->email != $this->input->post('data[email]') )
			$this->form_validation->set_rules('data[email]'       , 'ایمیل'      ,'trim|xss_clean|valid_email|is_unique[users.email]');
			$this->form_validation->set_rules('data[displayname]' , 'نام'        , 'trim|xss_clean|required|max_length[30]');
            $this->form_validation->set_rules('data[about]'       , 'درباره'     , 'trim|xss_clean|max_length[1000]');
            $this->form_validation->set_rules('data[age]'         , 'سن'         , 'trim|xss_clean|required|numeric');
            $this->form_validation->set_rules('data[tel]'         , 'تلفن'       , 'trim|xss_clean|exact_length[11]|valid_mobile');
            $this->form_validation->set_rules('data[nationality]' , 'قومیت'      , 'trim|xss_clean|max_length[30]');

            if( $user->type == 'expert' )
            {
                $this->form_validation->set_rules('data[state]'       , 'استان-شهر'  , 'trim|xss_clean|required|numeric');
                $this->form_validation->set_rules('data[skill]'       , 'تخصص'       , 'trim|xss_clean|required|numeric');
            }

			if( $this->form_validation->run() == FALSE )
				throw new Exception( validation_errors() , 2);	
			
			$data = $this->input->post('data');

			$data['email'] = strtolower($data['email']);

            if( $user->type == 'expert' )
            {
                /*********************************************/
                $state = $this->input->post('data[state]');
                $skill = $this->input->post('data[skill]');
                /*********************************************/

                $this->load->model('m_group','group');
                $meta = array(
                    'state' => $this->group->getParents($state,PROVINCE_ID),
                    'skill' => $this->group->getParents($skill,SKILL_ID),
                );
                $this->user->updateMeta($meta,$user->id);
            }

            $meta = array('about' => $data['about']);
            $this->user->updateMeta($meta,$user->id);
            
			$allowed = array('email','displayname','age','tel','nationality');
			$data = array_intersect_key($data,array_flip($allowed));

			if( ! $this->db->where('id',$user->id )->update('users',$data) )
				throw new Exception("خطا در انجام عملیات" , 1);

			$msg = "اطلاعات به روز شد !";
			
			$this->tools->outS(0,$msg);
			
		}
		catch(Exception $e)
		{
			$this->tools->outE($e);
		}
	}

    public function addMeta()
    {
        try
        {
            $userId = $this->input->post('data[uid]');
            $user = $this->_getUserToEdit($userId);

            $this->load->library('form_validation');

            $data = $this->input->post('data');

            $this->form_validation->set_rules('data[metakey]'    , 'نام متا'   , 'trim|xss_clean|required|in_list[active_times,call,address,website,history_json,documents_json]');
            $this->form_validation->set_rules('data[metavalue]'  , 'مقدار'     , 'trim|xss_clean');

            if( $data['metakey'] == 'history_json' )
            {
                $this->form_validation->set_rules('data[from]'     , 'تاریخ شروع'    , 'trim|xss_clean|required');
                $this->form_validation->set_rules('data[to]'       , 'تاریخ پایان'   , 'trim|xss_clean');
                $this->form_validation->set_rules('data[company]'  , 'شرکت'          , 'trim|xss_clean');
                $this->form_validation->set_rules('data[rol]'      , 'سمت'           , 'trim|xss_clean');
            }

            if( $data['metakey'] == 'documents_json' )
            {
                $this->form_validation->set_rules('data[title]'     , 'عنوان مدرک'    , 'trim|xss_clean|required|min_length[10]');
                $this->form_validation->set_rules('data[content]'   , 'توضیحات'       , 'trim|xss_clean|min_length[20]|max_length[1000]');
            }

            if( $this->form_validation->run() == FALSE )
                throw new Exception( validation_errors() , 2);

            $data = $this->input->post('data');

            if( $data['metakey'] == 'history_json' )
            {
                $from = explode('-',$data['from']);
                $to = explode('-',$data['to']);

                if( count($from) !== 3 )
                    throw new Exception( 'تاریخ شروع وارد شده معتبر نیست' , 2);

                if( count($to) !== 3 )
                    throw new Exception( 'تاریخ پایان وارد شده معتبر نیست' , 2);

                $from = jalali_to_gregorian($from[0],$from[1],$from[2],'-').' 00:00:00';
                $to   = jalali_to_gregorian($to[0],$to[1],$to[2],'-').' 00:00:00';
                $from = strtotime($from);
                $to   = strtotime($to);

                if( $from >= $to )
                    throw new Exception( 'تاریخ شروع از تاریخ پایان کمتر است !' , 1);

                if( $from >= strtotime("now") )
                    throw new Exception( 'تاریخ شروع نمی تواند از زمان حاضر بزرگتر باشد !' , 1);

                if( $to >= strtotime("now") )
                    throw new Exception( 'تاریخ پایان نمی تواند از زمان حاضر بزرگتر باشد !' , 1);

                $data['metavalue'] = $this->MakeJSON(array(
                    'company' => $data['company'] ,
                    'rol'     => $data['rol'] ,
                    'from'    => $from ,
                    'to'      => $to ,
                ));
            }

            if( $data['metakey'] == 'documents_json' )
            {
                if( $this->input->post('data[content]') == '' && ! isset($_FILES['file']['name'][0]) )
                    throw new Exception( 'وارد کردن یکی از موارد توضیحات یا تصویر مدرک الزامی است' , 1);

                $file = "";
                if( isset($_FILES['file']['name'][0]) )
                {
                    $this->load->model('admin/m_media','media');

                    $file_temp = $_FILES['file']['tmp_name'][0];
                    $file_name = $_FILES['file']['name'][0];

                    if( ! $this->media->isImage($file_temp) )
                        throw new Exception( 'تصویر انتخاب شده معتبر نیست' , 1);

                    $dir_arr = array(
                        'uploads/',
                        $user->username,
                        'documents',
                    );

                    $directory = $this->media->mkDirArray($dir_arr);

                    $file = $directory."/".$file_name;

                    $file = $this->media->optimizedFileName($file);

                    if( ! move_uploaded_file($file_temp,$file) )
                        throw new Exception( 'خطایی در هنگام انتقال تصویر رخ داده است' , 1);

                    $this->media->optimizeImage($file);
                }
                $data['metavalue'] = $this->MakeJSON(array(
                    'title'    => $data['title'] ,
                    'content'  => $data['content'] ,
                    'pic'      => $file ,
                ));
            }

            if( $data['metakey'] == 'website' && ! $this->tools->isValidUrl($data['metavalue']) )
                throw new Exception( 'لینک وارد شده معتبر نیست' , 1);

            if( isset($this->setting['max_user_meta_'.$data['metakey']]) &&
                (int)$this->setting['max_user_meta_'.$data['metakey']] > 0 &&
                $this->db->where('meta_name',$data['metakey'])->where('user_id',$user->id)
                     ->count_all_results('user_meta') >= (int)$this->setting['max_user_meta_'.$data['metakey']])
                throw new Exception( 'تعداد آیتم ها به حداکثر رسیده است' , 2);

            $meta = array($data['metakey'] => $data['metavalue']);

            $this->user->addMeta($meta,$user->id);

            $m = array(
                'key'   => $data['metakey'] ,
                'value' => $data['metavalue'] ,
                'id'    => $this->db->insert_id(),
                'uid'   => $user->id
            );

            if( $data['metakey'] == 'website' )
            {
                $m['site'] = $this->tools->siteName($data['metavalue']);
                $m['url']  = $this->tools->siteUrl($data['metavalue']);
            }

            if( $data['metakey'] == 'history_json' )
            {
                $m['from']    = jdate('F y',$from);
                $m['to']      = jdate('F y',$to);
                $m['period']  = $this->tools->period($from,$to);
                $m['company'] = $data['company'];
                $m['rol']     = $data['rol'];
            }

            if( $data['metakey'] == 'documents_json' )
            {
                $m['subject']  = html($data['title']);
                $m['content']  = html($data['content']);

                $m['pic'] = "";

                if( $file != '' )
                    $m['pic'] = '<div class="col-xs-12 text-center mb-15"><img src="'.base_url().$file.'" class="img-responsive img-thumbnail"></div>';
            }

            $this->tools->outS(0,"اطلاعات ثبت شد !" , array('meta'=>$m));
        }
        catch(Exception $e)
        {
            $this->tools->outE($e);
        }
    }

    public function deleteMeta()
    {
        try
        {
            $userId = $this->input->post('userid');
            $user = $this->_getUserToEdit($userId);

            $this->load->library('form_validation');

            $this->form_validation->set_rules('mid'    , 'شماره متا'   , 'trim|xss_clean|required');

            if( $this->form_validation->run() == FALSE )
                throw new Exception( validation_errors() , 2);

            $mid = $this->input->post('mid');

            if( ! $this->db->where('id',$mid)->where('user_id',$user->id)->count_all_results('user_meta') )
                throw new Exception( 'آیتم مورد نظر پیدا نشد' , 2);

            $meta = $this->db->where('id',$mid)->get('user_meta')->row();

            if( $meta->meta_name == 'documents_json' && $this->tools->isJson($meta->meta_value) )
            {
                $item =  $this->tools->jsonDecode($meta->meta_value);
                if( isset($item['pic']) && trim($item['pic']) && file_exists($item['pic']) )
                   @unlink($item['pic']);
            }

            if( ! $this->db->where('id',$mid)->where('user_id',$user->id)->delete('user_meta') )
                throw new Exception( 'آیتم مورد نظر حذف نشد' , 2);

            $this->tools->outS(0,"حذف شد");
        }
        catch(Exception $e)
        {
            $this->tools->outE($e);
        }
    }

    public function updateMetaInfo()
    {
        try
        {
            if( ! isset($this->user->data->id) ) exit('login');

            $this->load->library('form_validation');
            $userId = $this->input->post('userid');

            if( $userId )
            {
                if( ! $this->user->can('edit_users') )
                    throw new Exception( 'شما نمی توانید کاربران دیگر را ویرایش کنید' , 1);

                if( $userId == 1 && $this->user->data->id != $userId )
                    throw new Exception( 'شما نمی توانید اطلاعات مدیر را ویرایش کنید' , 1);

                $user = $this->db->select('id')->where('id',$userId)->get('users')->row();

                if( ! $user )
                    throw new Exception( 'کاربر مورد نظر پیدا نشد' , 1);
            }
            else
                $user = $this->user->data;

            $this->form_validation->set_rules('data[metakey]'    , 'نام متا'   , 'trim|xss_clean|required');
            $this->form_validation->set_rules('data[metavalue]'  , 'مقدار'     , 'trim|xss_clean');


            if( $this->form_validation->run() == FALSE )
                throw new Exception( validation_errors() , 2);

            $data = $this->input->post('data');

            $data['email'] = strtolower($data['email']);

            /*********************************************/
            $state = $this->input->post('data[state]');
            $skill = $this->input->post('data[skill]');
            /*********************************************/

            $this->load->model('m_group','group');
            $meta = array(
                'state' => $this->group->getParents($state,PROVINCE_ID),
                'skill' => $this->group->getParents($skill,SKILL_ID),
                'about' => $data['about']
            );

            $this->user->updateMeta($meta,$user->id);

            $allowed = array('email','displayname','age');
            $data = array_intersect_key($data,array_flip($allowed));

            if( ! $this->db->where('id',$user->id )->update('users',$data) )
                throw new Exception("خطا در انجام عملیات" , 1);

            $msg = "اطلاعات به روز شد !";

            $this->tools->outS(0,$msg);

        }
        catch(Exception $e)
        {
            $this->tools->outE($e);
        }
    }

    public function updatesocials()
    {
        try
        {
            $userId = $this->input->post('data[userid]');
            $user = $this->_getUserToEdit($userId);

            $this->load->library('form_validation');
            $this->form_validation->set_rules('data[id]', 'ID' ,  'trim|xss_clean');
            $this->form_validation->set_rules('data[case]', 'CASE' ,  'trim|required|xss_clean|in_list[instagram,twitter,facebook,google_plus,telegram]');

            if( $this->form_validation->run() == FALSE )
                throw new Exception( validation_errors() , 2);

            $data = $this->input->post('data');

            $case = $data['case'];
            $url  = $data['id'];

            $metaName = 'social_link_' . $case;
            $this->db->where('user_id',$user->id);
            $this->db->where('meta_name', $metaName);

            $metaData = array($metaName=>$url);
            $meta = $this->db->count_all_results('user_meta');

            if( $meta )
                $this->user->updateMeta($metaData,$user->id);
            elseif($url != '')
                $this->user->addMeta($metaData,$user->id);

            $this->tools->outS(0,'اطلاعات ذخیره شد',array('case'=>$data['case'],'value'=>$data['id']));
        }
        catch(Exception $e)
        {
            $this->tools->outE($e);
        }
    }

	public function changepass()
	{		
		$data = $this->input->post('data');	
		try
		{
			if( ! isset($this->user->data->id) ) exit('login');
			
			if( empty($data) OR ! isset($data['oldpass'],$data['newpass']) )
				throw new Exception("اطلاعات ارسالی صحیح نیست" , 2);
				
			if( do_hash($data['oldpass']) != $this->user->data->password )
				throw new Exception("رمز عبور فعلی اشتباه است" , 2);
				
			$this->load->library('form_validation');
			
			$this->form_validation->set_rules('data[newpass]', 'گزرواژه جدید', 'trim|xss_clean|required|min_length[4]|max_length[30]');
			
			if( $this->form_validation->run() == FALSE )
				throw new Exception( validation_errors(), 1);	
			
			$data = $this->input->post('data');	
				
			$udata = array('password'=>do_hash($data['newpass']));	
				
			if( ! $this->db->where('id',$this->user->user_id)->update('users',$udata) )
				throw new Exception("خطا در انجام عملیات" , 1);	
				
			$ldata = array(
				'username'=>$this->user->data->username,
				'password'=>$data['newpass']
			);	
			
			$this->user->logout();							
			$this->user->login($ldata);	
												
			$this->tools->outS(0,"گذرواژه جدید ذخیره شد");
		}
		catch(Exception $e)
		{
			$this->tools->outE($e);
		}					
	}

    public function updatepic()
    {
        $data = $this->input->post();
        try
        {
            if( ! isset( $data['userid'],$data['password'] ) )
                throw new Exception("اطلاعات ارسالی صحیح نیست !" , 2);

            $this->load->library('form_validation');

            $this->form_validation->set_rules('userid','شماره کاربری','trim|xss_clean|required|numeric');
            $this->form_validation->set_rules('password', 'گذرواژه','trim|xss_clean|required');
            $this->form_validation->set_rules('item', 'آیتم','trim|xss_clean|required|in_list[cover,avatar]');

            if( $this->form_validation->run() == FALSE )
                throw new Exception( validation_errors(), 1);

            $userid   = $this->input->post('userid');
            $password = $this->input->post('password');
            $item     = $this->input->post('item');

            if( $this->db->where('id',$userid)->where('password',$password)->count_all_results('users') != 1 )
                throw new Exception("نام کاربری یا گذرواژه اشتباه است !" , 1);

            $user = $this->db->where('id',$userid)->where('password',$password)->get('users')->row();

            if( ! isset($_FILES['file'],$_FILES['file']['tmp_name'],$_FILES['file']['name']) )
                throw new Exception("اطلاعات فایل ارسالی صحیح نیست" , 1);

            $this->load->model('admin/m_media','media');

            $file_temp = $_FILES['file']['tmp_name'];
            $file_name = $_FILES['file']['name'];

            $dir_arr = array("uploads","_ac",$user->username,);

            $directory = $this->media->mkDirArray($dir_arr);

            $file = $directory."/".$file_name;

            $file = $this->media->optimizedFileName($file);

            if( ! $this->media->isImage($file_temp) )
            {
                @unlink($file_temp);
                throw new Exception("لطفا یک تصویر انتخاب کنید" , 1);
            }

            if( ! move_uploaded_file($file_temp,$file) )
            {
                @unlink($file_temp);
                throw new Exception("خطا در بارگذاری تصویر" , 1);
            }

            $this->media->optimizeImage($file);

            $this->media->creatThumb($file);
            $this->db->where('id',$userid)->update('users',array($item => $file));

            $this->media->deleteFile($user->$item);

            $data = array('files'=>array('action'=>'done'),'url'=>base_url().$file);
            $this->tools->outS(0,"تصویر جدید ذخیره شد",$data);
        }
        catch(Exception $e)
        {
            $data = array('files'=>array('action'=>'fail'));
            $this->tools->outE($e,$data);
        }
    }

    public function resetpic($item = NULL , $userId = NULL)
	{
		try
		{
			if( ! $item OR ($item != 'cover' && $item != 'avatar') )
				throw new Exception("اطلاعات ارسالی صحیح نیست !" , 2);

            $userId = (int) $userId;

            $user = $this->_getUserToEdit($userId);

			$userid = $user->id;
			
			if( ! $this->db->where('id',$userid)->update('users',array($item=>"")) )
				throw new Exception("خطا در پردازش اطلاعات" , 1);
				
			$this->load->model('admin/m_media','media');
			
			$this->media->deleteFile($user->$item);
			
			$file = @$this->setting['default_user_'.$item];
			
			$data = array('url'=>base_url().$file);	
			$this->tools->outS(0,"تصویر حذف شد ",$data);			
		}
		catch(Exception $e)
		{
			$this->tools->outE($e);				
		}
	}

    public function delete_account()
    {
        try
        {
            if(!isset($this->user->data->id))
                throw new Exception("برای حذف حساب کاربری باید لاگین باشید" , 1);

            if( ! $this->user->deleteAccount($this->user->data->id) )
                throw new Exception("خطا در پردازش اطلاعات" , 1);

            $this->user->logout();

            $msg = "<span>حساب کاربری حذف شد</span><script> setTimeout(function(){location.replace('".site_url()."');},1500);</script>";
            $this->tools->outS(0,$msg);
        }
        catch(Exception $e)
        {
            $this->tools->outE($e);
        }
    }
	
	public function onlines()
	{
		$user_id =  @$this->user->user_id;
		$date = date("Y-m-d H:i:s");
		
		if( ! is_numeric($user_id) )
		{
			$user_id = 0;
			$username = $dispalyname =  NUll;
			
		}
		else
		{
			$username = $this->user->data->username;
			$dispalyname = $this->user->data->displayname;
			$this->db->where('id',$user_id);
			$this->db->update('users',array('last_seen'=>$date));
		}
		
		$ip = $this->input->ip_address();
		$agent = $this->input->user_agent();
		$minutes_ago = date("Y-m-d H:i:s",strtotime('-2 mins'));
		
		$this->db->where('user_id',$user_id);
		$this->db->where('ip',$ip);
		$this->db->or_where('date <',$minutes_ago);
		$this->db->delete('onlines');
		
		$data = array(
			'id'          => $this->db->last_id('onlines') ,
			'ip'          => $ip,
            'agent'       => $agent,
			'user_id'     => $user_id,
            'username'    => $username,
			'displayname' => $dispalyname,
            'date'        => $date
		);
		
		$this->db->insert('onlines',$data);
		
		$users = $this->input->post('data');
		if( is_array($users) )
		{
			$users = $this->user->areOnline($users);
		}
		else
		    $users = NULL;
		
		$return = array(
			'onlines' => $this->user->getOnlines(),
			'users'   => $users
		);
				
		echo $this->MakeJSON($return);
	}
	
	public function userinfo($id=NULL)
	{
		try
		{
			if( ! $id OR ! is_numeric($id) )
				throw new Exception("اطلاعات ارسالی صحیح نیست" , 2);
			
			$filter = array('id','avatar','cover','displayname','username','last_seen','level');	
			
			$user_id = isset($this->user->data->id) ? $this->user->user_id : $this->input->ip_address();
				
			$query =  	
			"SELECT " .implode(',',$filter). " ,
			 (SELECT COUNT(*) FROM ci_rates    WHERE `table`='users' AND row_id = u.id) AS rating   ,
			 (SELECT COUNT(*) FROM ci_onlines  WHERE user_id=u.id)                      AS isonline ,
			 (SELECT COUNT(*) FROM ci_rates    WHERE ((`table`='users' AND row_id = u.id) AND 
			                               (user_id = '$user_id' OR ip = '$user_id' ))) AS rated	 
			 FROM ci_users u
			 WHERE id=".$this->db->escape($id);				
			
			$query = $this->db->query($query);	
			
			if( $query->num_rows() < 1 )
				throw new Exception("کاربر پیدا نشد" , 1);			
			
			$info = $query->result(); $info = $info[0];

			$info->cover  = base_url().( $info->cover ? $info->cover:$this->setting['default_user_cover']) ;
			$info->avatar = base_url().( $info->avatar ? $info->avatar:$this->setting['default_user_avatar']);
			$info->role   = $this->user->getLevelName(NULL,$info->level);
			$info->link   = site_url('user/'.$info->username);
			$info->last_seen = $this->tools->Date($info->last_seen ,FALSE);
			
			$this->tools->outS(0,$info);
		}
		catch(Exception $e)
		{
			$this->tools->outE($e);
		}		
	}

	public function togglerate()
	{
		$data = $this->input->post('data');
		try
		{
			if( ! isset( $data['table'],$data['row'] ) )
				throw new Exception("اطلاعات ارسالی صحیح نیست" , 2);
			
			$res = $this->rate->toggleRate($data['table'],$data['row']);
				
			if( $res !== TRUE )
				throw new Exception($res !== FALSE ? $res:"خطا در انجام عملیات" , 1);			
			
			$rates = $this->rate->rateCount($data['table'],$data['row']);
			$this->tools->outS(0,$rates);
		}
		catch(Exception $e)
		{
			$this->tools->outE($e);
		}
	}
    /*==================================================
        INSTRUMENT
    ==================================================*/

    public function add_instrument()
    {
        $data = $this->input->post('data');
        try
        {
            if( empty( $data ) )
                throw new Exception("اطلاعات ارسالی صحیح نیست" , 1);

            if( $this->setting['cap_protect'] == 1 && ! $this->tools->checkCaptcha($this->input->post('data[captcha]')))
                throw new Exception( 'تصویر امنیتی اشتباه است' , 1);

            $isForSale = $this->input->post('data[fore_sale]') == '1';

            $this->load->library('form_validation');

            $this->form_validation->set_rules('data[name]'          , 'نام'        , 'trim|xss_clean|required|min_length[3]|max_length[255]');
            $this->form_validation->set_rules('data[description]'   , 'توضیحات'    , 'trim|xss_clean|required|min_length[3]|max_length[1000]');
            $this->form_validation->set_rules('data[price]'         , 'قیمت'       , 'trim|xss_clean|required');
            $this->form_validation->set_rules('data[state]'         , 'استان-شهر'  , 'trim|xss_clean|required|numeric');
            $this->form_validation->set_rules('data[category]'      , 'گروه'       , 'trim|xss_clean|required|numeric');
            $this->form_validation->set_rules('data[tel]'           , 'تلفن تماس'  , 'trim|xss_clean|required|max_length[50]');
            $this->form_validation->set_rules('data[email]'         , 'ایمیل'      , 'trim|xss_clean|valid_email');

            if(!$isForSale)
            {
                $this->form_validation->set_rules('data[price_1]'        , '[1 روز اجاره]'         , 'trim|xss_clean');
                $this->form_validation->set_rules('data[price_2]'        , '[10 روز اجاره]'        , 'trim|xss_clean');
                $this->form_validation->set_rules('data[price_3]'        , '[1 ماه اجاره]'         , 'trim|xss_clean');
                $this->form_validation->set_rules('data[min_day]'        , '[تعداد روز اجاره]'     , 'trim|xss_clean|numeric');
            }

            if( $this->form_validation->run() == FALSE )
                throw new Exception( validation_errors() , 2);

            $data     = $this->input->post('data');
            $state    = $this->input->post('data[state]');
            $category = $this->input->post('data[category]');

            $this->load->model('m_group','group');
            $meta = array(
                'state'    => $this->group->getParents($state,PROVINCE_ID),
                'category' => $this->group->getParents($category,INSTRUMENT_CAT_ID)
            );

            $data['for_sale'] = $isForSale ? 1:0;
            $data['thumb']    = '';
            $primary          = isset($data['img_primary']) ? (int)$data['img_primary']:0;
            $thumbs           = array();
            $this->load->model('admin/m_media','media');

            if(isset($_FILES['pic']))
            {
                $filesLen = count($_FILES['pic']['tmp_name']);

                if($filesLen > 4)
                    throw new Exception("حداکثر 4 عکس می توانید آپلود کنید" , 2);

                if($primary > $filesLen-1) $primary = 0;

                if(isset($this->user->data->username))
                    $dir_arr = array("uploads",$this->user->data->username,'instruments');
                else
                    $dir_arr = array("uploads","_in",date('Y'),date('m'));

                $directory = $this->media->mkDirArray($dir_arr);

                for($i=0;$i<$filesLen;$i++)
                {
                    $file_temp = $_FILES['pic']['tmp_name'][$i];
                    $file_name = $_FILES['pic']['name'][$i];

                    if( ! $this->media->isImage($file_temp) )
                    {
                        @unlink($file_temp);
                        throw new Exception("لطفا یک تصویر انتخاب کنید" , 1);
                    }

                    $file = $directory."/".$file_name;
                    $file = $this->media->optimizedFileName($file);

                    $thumbs[$file_temp] = $file;

                    if($i == $primary) $data['thumb'] = $file;
                }
            }

            if(!empty($thumbs))
            {
                foreach ($thumbs as $tmp=>$file)
                {
                    if(!move_uploaded_file($tmp,$file) )
                    {
                        @unlink($tmp);
                        foreach ($thumbs as $t=>$f )
                            $this->media->deleteFile($f);
                        throw new Exception("خطا در بارگذاری تصویر" , 1);
                    }
                    $this->media->optimizeImage($file);
                    $this->media->creatThumb($file);
                }
            }

            $price_format = array('price','price_1','price_2','price_3');
            foreach ($price_format as $pf)
            {
                if(isset($data[$pf]))
                    $data[$pf] = str_replace(',','',$data[$pf]);
            }

            $this->load->model('m_instrument','instrument');
            $id = $this->instrument->add($data);

            if(!$id)
            {
                foreach ($thumbs as $t=>$f )
                    $this->media->deleteFile($f);
                throw new Exception("خطا در ثبت اطلاعات" , 1);
            }

            $thumbs = array_values($thumbs);
            if(isset($thumbs[$primary]))
                unset($thumbs[$primary]);

            if(!empty($thumbs))
            {
                $thumbs = $this->MakeJSON($thumbs);
                $meta['thumb_json'] = $thumbs;
            }

            if(isset($meta))
                $this->instrument->addMeta($meta,$id);

            if(!isset($this->user->data->id))
            {
                $string = rand(10,999).substr(md5($id),0,8);
                $this->instrument->update($id,array('hash'=>do_hash($string)));
                $link   = site_url("tools/edit/$id/$string");
            }
            else
                $link = base_url('tools/edit/'.$id);

            $msg  = '<h4 class="mt-30 text-center"><i class="fa fa-check-circle"></i> <span>! اطلاعات شما با موفقیت ثبت شد</span></h4>';

            if(!isset($this->user->data->id))
            {
                $msg .= '<p class="text-center text-danger">کد پیگیری آگهی : <b class="en">'.$string.'</b></p>';
                $msg .= '<p class="text-center">از طریق لینک زیر می توانید آگهی خود را ویرایش کنید</p>';
                $msg .= '<p class="text-center text-danger">لینک زیر را کپی کنید تا در آینده بتوانید به آگهی خود دسترسی داشته باشید</p>';
            }
            else
            {
                $msg .= '<p class="text-center">از طریق لینک زیر می توانید آگهی خود را ویرایش کنید</p>';
            }
            
            $msg .= '<p class="en"><a href="'.$link.'">'.$link.'</a></p>';

            $msg .= '<p><a class="btn btn-warning btn-block mt-10" href="'.(site_url('tools/view/'.$id)).'">مشاهده آگهی</a></p>';

            $email = isset($this->user->data->email) ? $this->user->data->email:$this->input->post('data[email]');

            if($email)
            {
                $html    = '<h2>ضمن قدردانی از شما همراه گرامی، آگهی شما با موفقیت ثبت شد.</h2>';
                $html   .= '<p>عنوان آگهی : '.$data['name'].'</p>';
                $html   .= '<p>'.$data['description'].'</p><hr>';
                $html   .= '<a href="'.(site_url('tools/view/'.$id)).'"> مشاهده آگهی ';
                $html   .= ' &nbsp;  <a href="'.$link.'"> ویرایش آگهی ';

                if($this->setting['auto_submit_instrument'] == 0)
                    $html   .= '<hr><p>این آگهی بعد از بررسی و تایید تیم پشتیبانی در سایت قابل نمایش خواهد بود</p>';
				
                $subject = 'آگهی شما ثبت شد';
                $this->tools->sendEmail($email,$subject,$html);
            }

            $this->tools->outS(0,'اطلاعات با موفقیت ثبت شد',array('message'=>$msg));
            $this->tools->destroyCaptcha();
        }
        catch(Exception $e)
        {
            $this->tools->outE($e);
        }
    }

    public function edit_instrument()
    {
        $data = $this->input->post('data');
        try
        {
            if( empty( $data ) OR ! isset($data['id']) )
                throw new Exception("اطلاعات ارسالی صحیح نیست" , 1);

            if( $this->setting['cap_protect'] == 1 && ! $this->tools->checkCaptcha($this->input->post('data[captcha]')))
                throw new Exception( 'تصویر امنیتی اشتباه است' , 1);

            if(!$this->db->where('id',(int)$data['id'])->count_all_results('instruments'))
                throw new Exception('آیتم مورد نظر حذف شده است یا وجود ندارد' , 1);

            $me        = $this->user->logged ? $this->user->user_id:0;
            $row       = $this->db->where('id',(int)$data['id'])->get('instruments')->row();
            $isForSale = $row->for_sale;

            if(
                !($this->user->can('edit_instrument'))
                &&
                !($me && $row->user_id != '' && $me == $row->user_id)
                &&
                !(isset($data['hash']) && trim($data['hash']) != '' && $row->hash == trim($data['hash']))

            ) throw new Exception('شما نمی توانید این آیتم را ویرایش کنید' , 1);

            $this->load->library('form_validation');

            $this->form_validation->set_rules('data[name]'          , 'نام'        , 'trim|xss_clean|required|min_length[3]|max_length[255]');
            $this->form_validation->set_rules('data[description]'   , 'توضیحات'    , 'trim|xss_clean|required|min_length[3]|max_length[1000]');
            $this->form_validation->set_rules('data[price]'         , 'قیمت'       , 'trim|xss_clean|required');
            $this->form_validation->set_rules('data[state]'         , 'استان-شهر'  , 'trim|xss_clean|required|numeric');
            $this->form_validation->set_rules('data[category]'      , 'گروه'       , 'trim|xss_clean|required|numeric');
            $this->form_validation->set_rules('data[tel]'           , 'تلفن تماس'  , 'trim|xss_clean|required|max_length[50]');
            $this->form_validation->set_rules('data[active]'        , 'وضعیت'      , 'trim|xss_clean|required|in_list[0,1]');

            if(!$isForSale)
            {
                $this->form_validation->set_rules('data[price_1]' , '[1 روز اجاره]'         , 'trim|xss_clean');
                $this->form_validation->set_rules('data[price_2]' , '[10 روز اجاره]'        , 'trim|xss_clean');
                $this->form_validation->set_rules('data[price_3]' , '[1 ماه اجاره]'         , 'trim|xss_clean');
                $this->form_validation->set_rules('data[min_day]' , '[تعداد روز اجاره]'     , 'trim|xss_clean');
            }

            if( $this->form_validation->run() == FALSE )
                throw new Exception( validation_errors() , 2);

            $this->load->model('m_group','group');
            $this->load->model('admin/m_media','media');
            $this->load->model('m_instrument','instrument');

            $data      = $this->input->post('data');
            $state     = $this->input->post('data[state]');
            $category  = $this->input->post('data[category]');
            $row->meta = $this->instrument->getMeta($row->id);

            $meta = array(
                'state'    => $this->group->getParents($state,PROVINCE_ID),
                'category' => $this->group->getParents($category,INSTRUMENT_CAT_ID)
            );
            $this->instrument->updateMeta($meta,$row->id);

            $oldThumbs = array();
            if($row->thumb)
                $oldThumbs[0] = $row->thumb;

            if(isset($row->meta->thumb_json) && $this->tools->isJson($row->meta->thumb_json))
            {
                foreach (json_decode($row->meta->thumb_json) as $thumb)
                    $oldThumbs[] = $thumb;
            }

            $deletedImages = $this->input->post('data[deleted_images]');
            if($deletedImages)
            {
                $deletedImages = explode(',',trim($deletedImages,','));
                foreach ($deletedImages as $index)
                    if($index != '' && isset($oldThumbs[$index]))
                    {
                        $this->media->deleteFile($oldThumbs[$index]);
                        unset($oldThumbs[$index]);
                    }
            }

            $primary = isset($data['img_primary']) ? (int)$data['img_primary']:0;
            $thumbs  = array();

            if(isset($_FILES['pic']))
            {
                $filesLen  = count($_FILES['pic']['tmp_name']);
                $oFilesLen = count($oldThumbs);

                if($filesLen+$oFilesLen > 4)
                    throw new Exception("حداکثر 4 عکس می توانید آپلود کنید" , 2);

                if(isset($this->user->data->username))
                    $dir_arr = array("uploads",$this->user->data->username,'instruments');
                else
                    $dir_arr = array("uploads","_in",date('Y'),date('m'));

                $directory = $this->media->mkDirArray($dir_arr);

                for($i=0;$i<$filesLen;$i++)
                {
                    $file_temp = $_FILES['pic']['tmp_name'][$i];
                    $file_name = $_FILES['pic']['name'][$i];

                    if( ! $this->media->isImage($file_temp) )
                    {
                        @unlink($file_temp);
                        throw new Exception("لطفا یک تصویر انتخاب کنید" , 1);
                    }

                    $file = $directory."/".$file_name;
                    $file = $this->media->optimizedFileName($file);

                    $thumbs[$file_temp] = $file;
                }
            }

            if(!empty($thumbs))
            {
                foreach ($thumbs as $tmp=>$file)
                {
                    if(!move_uploaded_file($tmp,$file) )
                    {
                        @unlink($tmp);
                        foreach ($thumbs as $t=>$f )
                            $this->media->deleteFile($f);
                        throw new Exception("خطا در بارگذاری تصویر" , 1);
                    }
                    $this->media->optimizeImage($file);
                    $this->media->creatThumb($file);
                }
            }

            $id     = $row->id;
            $thumbs = array_merge($oldThumbs,array_values($thumbs));

            if($primary > count($thumbs)-1) $primary = 0;

            if(isset($thumbs[$primary]))
            {
                $data['thumb'] = $thumbs[$primary];
                unset($thumbs[$primary]);
            }
            else
                $data['thumb'] = NULL;

            $allowed            = array('name','description','price','price_1','price_2','price_3','min_day','thumb','tel','active');
            $uData              = array_intersect_key($data,array_flip($allowed));
            $uData['date']      = date('Y-m-d H:i:s');
            $uData['submitted'] = $this->setting['auto_submit_instrument'];

            $price_format = array('price','price_1','price_2','price_3');
            foreach ($price_format as $pf)
            {
                if(isset($data[$pf]))
                    $uData[$pf] = str_replace(',','',$uData[$pf]);
            }

            if(!$this->instrument->update($id,$uData))
            {
                foreach ($thumbs as $f)
                    if(!in_array($f,$oldThumbs))
                        $this->media->deleteFile($f);
                throw new Exception("خطا در ثبت اطلاعات" , 1);
            }

            if(!empty($thumbs))
                $this->instrument->updateMeta(array('thumb_json'=>$this->MakeJSON(array_values($thumbs))),$row->id);
            else
                $this->instrument->deleteMeta($row->id,'thumb_json');

            $link = site_url('tools/view/'.$row->id.'/'.(STU($data['name'])));
            $msg = "<script>setTimeout(function(){location.href='{$link}'},700)</script><span>اطلاعات با موفقیت ثبت شد</span>";
            $this->tools->outS(0,$msg);
            $this->tools->destroyCaptcha();
        }
        catch(Exception $e)
        {
            $this->tools->outE($e);
        }
    }
    
    public function delete_instrument()
    {
        $data = $this->input->post();
        try
        {
            if( empty( $data ) OR ! isset($data['id']) )
                throw new Exception("اطلاعات ارسالی صحیح نیست" , 1);

            if(!$this->db->where('id',(int)$data['id'])->count_all_results('instruments'))
                throw new Exception('آیتم مورد نظر حذف شده است یا وجود ندارد' , 1);

            $me        = $this->user->logged ? $this->user->user_id:0;
            $row       = $this->db->where('id',(int)$data['id'])->get('instruments')->row();

            if(
                !($this->user->can('edit_instrument'))
                &&
                !($me && $row->user_id != '' && $me == $row->user_id)
                &&
                !(isset($data['hash']) && trim($data['hash']) != '' && $row->hash == trim($data['hash']))

            ) throw new Exception('شما نمی توانید این آیتم را حذف کنید' , 1);

            $this->load->model('m_instrument','instrument');

            if(! $this->instrument->delete($row->id))
                throw new Exception('خطایی در هنگام حذف رخ داده است !' , 2);

            $link = site_url('tools');
            $msg = "<script>setTimeout(function(){location.href='{$link}'},500)</script><span>حذف شد</span>";
            $this->tools->outS(2,$msg);
        }
        catch(Exception $e)
        {
            $this->tools->outE($e);
        }
    }

    public function check_code()
    {
        $data = $this->input->post('data');
        try
        {
            if(!isset($data['code']))
                throw new Exception("اطلاعات ارسالی صحیح نیست" , 2);

            if( $this->setting['cap_protect'] == 1 && ! $this->tools->checkCaptcha($this->input->post('data[captcha]')))
                throw new Exception( 'تصویر امنیتی اشتباه است'  , 1);

            $code = $this->input->post('data[code]');
            $hash = do_hash($code);
            $row  = $this->db->select('id')->where('hash IS NOT NULL')->where('hash',$hash)->get('instruments',1)->row();
            if(empty($row))
                throw new Exception("کد پیگیری صحیح نیست <script>refresh_captcha()</script>" , 2);

            $link = site_url("tools/edit/{$row->id}/{$code}");

            $msg = "<span>لطفا صبر کنید ...</span><script>setTimeout(function(){location.href = '{$link}'},600);</script>";
            
            $this->tools->outS(0,$msg);
        }
        catch(Exception $e)
        {
            $this->tools->outE($e);
        }
    }
    /*==================================================
        COMMENTING
    ==================================================*/
	public function addcomment()
	{
		$data = $this->input->post('data');
		
		try
		{
			if( empty( $data ) OR ! isset($data['table'],$data['row_id'],$data['parent']) )
				throw new Exception("اطلاعات ارسالی صحیح نیست" , 1);
				
			if( ! isset( $this->user->data->id ) && $this->setting['user_can_comment'] == 0 )
				throw new Exception("برای نظر دادن باید ثبت نام کنید یا وارد شوید !" , 1);	
			
			$this->load->library('form_validation');
			
			$this->form_validation->set_rules('data[table]', 'TABLE', 
				'trim|xss_clean|required|in_list[posts,users,instruments]');
							
			$this->form_validation->set_rules('data[row_id]', 'ID', 'trim|required|xss_clean|numeric');
			$this->form_validation->set_rules('data[parent]', 'REPLY', 'trim|required|xss_clean|numeric');

            if($this->setting['cap_protect'] == 1)
            $this->form_validation->set_rules('data[captcha]', 'تصویر امنیتی', 'trim|required|xss_clean');
			
			if( ! isset( $this->user->data->id ) )
            {
                $this->form_validation->set_rules('data[name]', 'نام', 'trim|required|xss_clean|max_length[30]');
                $this->form_validation->set_rules('data[email]', 'ایمیل', 'trim|required|xss_clean|max_length[255]|valid_email');
            }

            if( isset($data['rating']) )
            {
                $this->form_validation->set_rules('data[rating]', 'امتیاز', 'trim|required|xss_clean|in_list[1,2,3,4,5]|numeric');
            }

			$this->form_validation->set_rules('data[text]', 'نظر', 'trim|required|xss_clean|max_length[3000]|min_length[3]');
				
			if( $this->form_validation->run() == FALSE )
				throw new Exception( validation_errors() , 2);

            if( $this->setting['cap_protect'] == 1 && ! $this->tools->checkCaptcha($this->input->post('data[captcha]')) )
                throw new Exception( 'تصویر امنیتی اشتباه است' , 1);

			$this->load->model('m_comment','comment');
			
			$data = $this->input->post('data');

            if( isset($data['rating']) )
            {
                if ( TRUE !== $re = $this->rate->setRating($data['table'],$data['row_id'],$data['rating']) )
                    throw new Exception( 'رای شما ثبت نشد : '.$re , 2);

                $RID = $this->db->insert_id();
            }

            if( ! $id = $this->comment->add($data) )
                throw new Exception("خطا در انجام عملیات" , 1);

            $CID = $this->db->insert_id();

            $mod = 'post-comments';

            if( isset($data['rating']) )
            {
                $this->db->insert('comment_rate',array(
                    'comment_id' => $CID,
                    'rate_id'    => $RID
                ));
                $mod = 'user-reviews';
            }

			if( $this->setting['auto_submit_comment'] == 1 && $cm = $this->comment->selectById($id,$mod))
			{
				$data = array('cm'=>$this->comment->htmlTemplate($cm));
				$this->tools->outS(0,'نظر شما ثبت شد !',$data);
			}
			else
			{
				$msg = "نظر شما ثبت شد بعد از تایید نمایش داده می شود !";
				$this->tools->outS(0,$msg);
			}
            $this->tools->destroyCaptcha();
		}
		catch(Exception $e)
		{
			$this->tools->outE($e);
		}
	}

    public function comments($rid=0,$parent=0,$table='',$from=0,$limit=10)
    {
        try
        {
            $rid    = (int)$rid;
            $parent = (int)$parent;
            $from   = (int)$from;
            $limit  = (int)$limit;

            if( $limit  > 100 ) $limit = 100;

            $this->load->model('m_comment','comment');

            ob_start();
            $this->comment->printPostComments($rid,$parent,$table,$from,$limit);
            $comments = ob_get_contents();
            ob_end_clean();

            $this->tools->outS(0,'OK',array('comments'=>$comments));

        }
        catch(Exception $e)
        {
            $this->tools->outE($e);
        }
    }

    public function delete_comment()
    {
        try
        {
            $id = (int)$this->input->post('id');

            if(!isset($this->user->data->id))
                throw new Exception("برای حذف نظر باید لاگین شوید یا ثبت نام کنید" , 1);

            $myId     = $this->user->data->id;
            $isAdmin  = $this->user->can('edit_user');

            if(!$isAdmin && !$this->db->where('id',$id)->where('user_id',$myId)->count_all_results('comments') )
                throw new Exception("شما فقط می توانید نظرات خودتان را حذف کنید" , 1);

            $this->load->model('m_comment','comment');
            $this->comment->delete($id);

            $this->tools->outS(0,'حذف شد');

        }
        catch(Exception $e)
        {
            $this->tools->outE($e);
        }
    }

    /*==================================================
        MISSIONS
    ==================================================*/
    public function addmission()
    {
        try
        {
            if( ! isset( $this->user->data->id ) )
                throw new Exception("برای ماموریت دادن باید ثبت نام کنید یا وارد شوید !" , 2);

            $this->load->library('form_validation');

            $this->form_validation->set_rules('data[to]', 'آی دی متخصص', 'trim|required|xss_clean|numeric');
            $this->form_validation->set_rules('data[text]', 'توضیحات', 'trim|required|xss_clean|max_length[3000]|min_length[3]');
            $this->form_validation->set_rules('data[tel]', 'تلفن', 'trim|required|xss_clean');
            $this->form_validation->set_rules('data[hidden]', 'مخفی بودن', 'trim|required|numeric|in_list[0,1]');

            if($this->setting['cap_protect'] == 1)
                $this->form_validation->set_rules('data[captcha]', 'تصویر امنیتی', 'trim|required|xss_clean');

            if( $this->form_validation->run() == FALSE )
                throw new Exception( validation_errors() , 2);

            if( $this->setting['cap_protect'] == 1 && ! $this->tools->checkCaptcha($this->input->post('data[captcha]')) )
                throw new Exception( 'تصویر امنیتی اشتباه است' , 1);

            $data = $this->input->post('data');

            if($this->db->where('id',$data['to'])->where('type','expert')->count_all_results('users') == 0)
                throw new Exception("متخصص مورد نظر پیدا نشد" , 2);

            if($data['to'] == $this->user->data->id)
                throw new Exception("شما نمی توانید به خودتان ماموریت بدهید" , 2);

            $data = array(
                'from'   => $this->user->user_id,
                'to'     => $data['to'],
                'text'   => $this->security->fixString($data['text']),
                'hidden' => $data['hidden'],
                'tel'    => $data['tel'],
            );

            $this->load->model('m_mission','mission');

            if( ! $id = $this->mission->add($data) )
                throw new Exception("خطا در انجام عملیات" , 1);

            if( $this->setting['auto_submit_mission'] == 1 && $ms = $this->mission->selectById($id))
            {
                $data = array('ms'=>$this->mission->htmlTemplate($ms));
                $this->tools->outS(0,' ماموریت ثبت شد !',$data);
            }
            else
            {
                $msg = "ماموریت شما ثبت شد بعد از تایید نمایش داده می شود";
                $this->tools->outS(0,$msg);
            }
            $this->tools->destroyCaptcha();
        }
        catch(Exception $e)
        {
            $this->tools->outE($e);
        }
    }

    public function delete_mission()
    {
        try
        {
            $id = (int)$this->input->post('id');

            if(!isset($this->user->data->id))
                throw new Exception("برای حذف باید لاگین شوید" , 1);

            $myId = $this->user->data->id;

            $isAdmin  = $this->user->can('edit_user');

            if(!$isAdmin && !$this->db->where("id={$id} AND done=0 AND (from={$myId} OR to={$myId})")->count_all_results('missions') )
                throw new Exception("شما نمی توانید این آیتم را حذف کنید" , 1);

            $this->db->where('id',$id)->delete('missions');

            $this->tools->outS(0,'حذف شد');
        }
        catch(Exception $e)
        {
            $this->tools->outE($e);
        }
    }

    public function done_mission()
    {
        try
        {
            $id = (int)$this->input->post('id');

            if(!isset($this->user->data->id))
                throw new Exception("برای حذف باید لاگین شوید" , 1);

            $myId = $this->user->data->id;

            if( ! $this->db->where("id={$id} AND done=0 AND from={$myId}")->count_all_results('missions') )
                throw new Exception("شما نمی توانید این آیتم را تغییر دهید" , 1);

            $this->db->set('done',1)->where('id',$id)->update('missions');

            $this->tools->outS(0,'OK');
        }
        catch(Exception $e)
        {
            $this->tools->outE($e);
        }
    }

    public function missions($do='to',$rid=0,$from=0,$limit=10)
    {
        try
        {
            $rid    = (int)$rid;
            $do     = $do == 'to' ? 'to':'from';
            $from   = (int)$from;
            $limit  = (int)$limit;

            if( $limit  > 100 ) $limit = 100;

            $this->load->model('m_mission','mission');

            ob_start();
            $this->mission->printUserMissions($do,$rid,$from,$limit);
            $missions = ob_get_contents();
            ob_end_clean();

            $this->tools->outS(0,'OK',array('missions'=>$missions));

        }
        catch(Exception $e)
        {
            $this->tools->outE($e);
        }
    }

    public function search()
	{
		$s = $this->input->post('search',TRUE);
		
		if( ! $s ) return NULL;
		
		$query =
            /** @lang text */
            "SELECT id,title
             ,(SELECT COUNT(*) FROM ci_logs  WHERE (`table`='posts' AND row_id = p.id)) AS view_count	
             FROM `ci_posts` p 
             WHERE published=1 AND type='post' AND title !='' 
             AND title LIKE '%" .$this->db->escape_like_str($s)."%'
             ORDER BY view_count DESC , date_modified DESC
             LIMIT 10";
		 
		$data = $this->db->query($query)->result();
		
		if( count($data) > 0 )
		{
			$this->load->model('admin/m_post','post');
			foreach( $data as $key=>$item )
			{
				$t = preg_replace("/($s)/i", "<span class='match'>$1</span>", $item->title);
				$data[$key]->text = $t ;
				$data[$key]->link = $this->post->link($data[$key]);
			}
		}
		else $data = NULL;
		echo $this->MakeJSON($data);
	}

     /****************************
             client
     ***************************/
    public function feedback()
    {
        $data = $this->input->post('data');
        try
        {
            if( empty( $data ) OR ! isset($data['email']) )
                throw new Exception( lang('incorrect data')  , 1);

            $this->load->library('form_validation');

            $this->form_validation->set_rules('data[subject]',     lang('subject') ,      'trim|required|xss_clean|max_length[255]');
            $this->form_validation->set_rules('data[message]',     lang('message') ,      'trim|required|xss_clean|max_length[3000]');
            $this->form_validation->set_rules('data[email]',       lang('email') ,        'trim|required|xss_clean|valid_email');
            $this->form_validation->set_rules('data[name]',        lang('name') ,         'trim|required|xss_clean|max_length[255]');

            if( $this->form_validation->run() == FALSE )
                throw new Exception( validation_errors() , 2);

            $data = $this->input->post('data');

            $data['email']    = strtolower($data['email']);

            /*$allowed = array('email','name','subject','message');
            $data = array_intersect_key($data,array_flip($allowed));*/

            $info = array(
                'subject'   => $data['subject'],
                'message'   => $data['message'],
                'name'      => $data['name'],
                'email'     => $data['email'],
                'visited'   => 0,
                'date'      => date('Y-m-d H:i:s'),
            );

            if( ! $this->db->insert('admin_inbox',$info) )
                throw new Exception( lang('operation failed') , 1);

            $this->tools->outS(0,'پیام شما با موفقیتت ثبت شد');
            $this->tools->destroyCaptcha();
        }
        catch(Exception $e)
        {
            $this->tools->outE($e);
        }
    }

    public function subgroup($id=NULL)
	{
		try
		{
			if( ! $id )
				throw new Exception("اطلاعات ارسالی صحیح نیست" , 2);
			
			$this->load->model('m_group','group');
			
			$name  = $this->input->post('name');
			$class = $this->input->post('class');
			
			$start = '<select name="group" class="form-control">';
			if( $name OR $class )
			{
				$start = '<select';
				$start .= ' name="'.($name ? $name:'group').'"';
				$start .= ' class="'.($class ? $class:'form-control').'"';
				$start .= '>';	
			}
			
			$res = $this->group->creatSelect($id,NULL,$start);
			
			$this->tools->outS(0,$res);
		}
		catch(Exception $e)
		{
			$this->tools->outE($e);
		}			
	}

    public function GetsubGroup($list){
		$out = [];
	}
    public function subGroupLi($book_id=NULL){
        try
        {
            if( !$book_id )
                throw new Exception("اطلاعات ارسالی صحیح نیست" , 2);

            $this->load->model('m_group','group');
			$g = $this->db->select('id')->where('book_id',$book_id)->order_by('id')->get('group')->row();
			$id = $g->id;
			$this->group->getChildren($id);
			$ids = (is_array($this->group->items) && count($this->group->items))?array_keys($this->group->items):array(0);

			$book_id = (int)$book_id;
			$this->db->select('p.id AS part_id, g.id, g.name,g.parent,g.position');
			$this->db->join('ci_book_meta p',"(p.index=g.id AND p.book_id={$book_id})",'left',FALSE);
			$this->db->where('g.id IN('.implode(',',$ids).')');
			$O = $this->db->get('group g')->result();
			$indexes = $this->group->items;
			foreach($O as $v){
				$indexes[$v->id]->part_id = $v->part_id;
				$parent = @$indexes[$v->parent]->name;
				if(strlen($parent)){
					$position = substr_count($parent.$indexes[$v->id]->name,'<br />');
					$position++;
					$indexes[$v->id]->name = $parent.($position?"\n".'<br />'.str_repeat('|_ ',$position):'').$indexes[$v->id]->name;
				}
			}
			if(empty($indexes))
			{
				$res = "<h5 style='color:#b70000;'>هیچ آیتمی ثبت نشده است</h5>";
			}
			else
			{
				$res = "<ul class=\"index-ul\">";
				foreach ($indexes as $index)
				{
					$res .= "<li data-id=\"{$index->id}\" data-part-id=\"{$index->part_id}\">{$index->name}</li>";
				}
				$res .= "</ul>";
			}
            $this->tools->outS(0,$res);
        }
        catch(Exception $e)
        {
            $this->tools->outE($e);
        }
    }

    public function groupSelectList($id=NULL)
    {
        try
        {
            if( ! $id )
                throw new Exception("اطلاعات ارسالی صحیح نیست" , 2);

            $this->load->model('m_group','group');

            $baseId = $this->input->post('base_id');
            $name   = $this->input->post('name');
            $class  = $this->input->post('class');

            $start = '<article class="group-holder"><select name="group" class="form-control">';
            if( $name OR $class )
            {
                $start  = '<article class="group-holder"><select';
                $start .= ' name="'.($name ? $name:'group').'"';
                $start .= ' class="'.($class ? $class:'form-control').'"';
                $start .= '>';
            }

            $end  = '</select></article>';
            $list = $this->group->getParents($id);
            $html = "";
            $i    = 0;

            foreach ($list as $sid)
            {
                $id = $i == 0 ? $baseId : $list[$i-1]; $i++;
                $html .= $this->group->creatSelect($id, NULL, $start, $end, FALSE, $sid);
            }

            $this->tools->outS(0,$html);
        }
        catch(Exception $e)
        {
            $this->tools->outE($e);
        }
    }

    private function _getUserToEdit($userId)
    {
        if( ! isset( $this->user->data->id ) ) exit('login');

        $userId = (int)$userId;

        if( $userId && $userId != $this->user->data->id )
        {
            if( ! $this->user->can('edit_users') )
                throw new Exception( 'شما نمی توانید کاربران دیگر را ویرایش کنید' , 1);

            if( $userId == 1 && $this->user->data->id != $userId )
                throw new Exception( 'شما نمی توانید اطلاعات مدیر را ویرایش کنید' , 1);

            $user = $this->db->where('id',$userId)->get('users')->row();

            if( ! $user )
                throw new Exception( 'کاربر مورد نظر پیدا نشد' , 1);
        }
        else
            $user = $this->user->data;

        return $user;
    }
	private function MakeJSON($data){
		return json_encode($data,JSON_UNESCAPED_UNICODE | JSON_HEX_QUOT | JSON_HEX_TAG);
	}//Alireza Balvardi
}
?>