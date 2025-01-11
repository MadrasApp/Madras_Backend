<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Upload extends CI_Controller {	
	
	public $setting;
	

	function __construct()
	{
		parent::__construct();
		$this->load->model('m_user','user');		
		$this->setting = $this->settings->data;			
	}
	
	public function index()
	{
		$key = FALSE; $msg = "";
		
		if($this->user->check_login())
		{
			if( isset($_FILES['file']))
			{
				$this->load->model('admin/m_media','media');

                $file_temp = $_FILES['file']['tmp_name'];
                $file_name = $_FILES['file']['name'];
                $dir       = $this->user->data->username;

                $dir_post = $this->input->post('dir');

                if($this->user->is_admin() && $dir_post)
                    $dir = $dir_post;

                $dir_arr = array(
                    "uploads/",
                    $dir,
                    date("Y"),
                    date("m")
                );
                
                $directory = $this->media->mkDirArray($dir_arr);

                $file = $directory."/".$file_name;

                $file = $this->media->optimizedFileName($file);

                /*************************************/

				if(move_uploaded_file($file_temp,$file)){
					
					if($this->media->isImage($file))
                    {
                        if( $this->input->post('optimize') == 1 )
                            $this->media->optimizeImage($file);

                        $this->media->creatThumb($file);
                    }
					
					$key = TRUE;
                    $msg = "ذخیره شد";
					touch($file);
				}
			}
			else
			{
				$msg = "فایلی ارسال نشده است";
            }
		}
		else
		{
			$msg = 'login-needed';
		}		
		
		$done = $key ? 'done':'fail';
		$response = 
		array(
			'files'=> array(
				'name'=>@$file,
				'url'=>@$file,
				'msg'=>$msg,
				'action'=>$done,
			)
		);

		echo json_encode($response,JSON_UNESCAPED_UNICODE | JSON_HEX_QUOT | JSON_HEX_TAG);				
	}
}