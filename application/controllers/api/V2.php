<?php defined('BASEPATH') or exit('No direct script access allowed');
//Alireza Balvardi
/*
V2::LogMe($_REQUEST);
		echo "<pre>";
		print_r();
		echo "</pre>";
		die;
*/
//test

class V2 extends CI_Controller
{

    public $setting;

    function __construct()
    {

        parent::__construct();

        // Load the Redis cache driver
        $this->load->driver('cache', array('adapter' => 'redis', 'backup' => 'file'));

        //if( ! $this->input->is_ajax_request() ) exit('No direct script access allowed');

        $this->load->model('m_user', 'user');
        $this->setting = $this->settings->data;
    }

    public function index()
    {
        try {
            //if( ! $this->user->check_login() && $this->uri->segment(4) != 'login' )
            //throw new Exception("login needed" , -1);

            $arg = func_get_args();

            if (!isset($arg[0]))
                throw new Exception("Invalid request", 2);

            $method = $arg[0];

            if (!method_exists($this, $method))
                throw new Exception("Not found", 2);

            unset($arg[0]);
            $_REQUEST["API"] = 1;
            call_user_func_array(array($this, $method), $arg);
        } catch (Exception $e) {
            $this->tools->outE($e);
        }
    }

    static function LogMe($data)
    {
        $fp = fopen('LogMe/data-' . date("Ymd") . '.txt', 'a+');
        fwrite($fp, print_r($data, true) . "\n=================================\n");
        fclose($fp);
    }

    public function getDicLang()
    {
        $results = $this->db->select('id,title')->get('diclang')->result();
        $this->tools->outS(0, $results);
    }

    public function neddUpdate()
    {
        $user_id = NULL;
        $user = $this->_loginNeed(TRUE, 'u.id');

        if ($user === FALSE)
            throw new Exception("برای دسترسی به این بخش باید وارد حساب کاربری خود شوید", -1);

        $uid = $user->id;
        $id = (int)$this->input->post('id');

        $results = $this->db->select('need_update')->where('book_id', $id)->where('user_id', $uid)->get('user_books')->row();
        $this->tools->outS(0, $results);
    }

    public function getKalameh()
    {
        $user = $this->_loginNeed(TRUE, 'u.id');
        if ($user === FALSE)
            throw new Exception("برای دسترسی به این بخش باید وارد حساب کاربری خود شوید", -1);
        $data = $this->input->post();
        if (empty($data))
            throw new Exception("اطلاعات ارسالی صحیح نیست", 1);

        $uid = $user->id;
        $kalameh = trim(@$data["kalameh"]);
        $translate = trim(@$data["translate"]);
        $Limit = is_numeric(@$data["Limit"]) ? $data["Limit"] : 100;
        $Limit = $Limit > 3000 ? 3000 : $Limit;
        $Limitstart = intval(@$data["Limitstart"]);
        $tolang = intval(@$data["tolang"]);
        $fromlang = intval(@$data["fromlang"]);

        $id = 0;
        $O = $this->db->select('id,title')->get('diclang')->result();
        $count = $this->db->count_all_results('dictionary');
        $diclang = array();
        foreach ($O as $k => $v) {
            $diclang[$v->id] = $v->title;
        }

        if ($kalameh) {
            $this->db->where('kalameh', $kalameh);
            if ($fromlang)
                $this->db->where('fromlang', $fromlang);
            if ($tolang)
                $this->db->where('tolang', $tolang);
            $this->db->select('*');
            $results = $this->db->get('dictionary')->result();
        } else {
            $this->db->order_by('fromlang,kalameh', 'ASC');
            $this->db->limit($Limit, $Limitstart);
            $this->db->select('*');
            if ($fromlang)
                $this->db->where('fromlang', $fromlang);
            if ($tolang)
                $this->db->where('tolang', $tolang);
            $results = $this->db->get('dictionary')->result();
        }

        if ($translate) {
            if (count($results)) {
                $id = $results[0]->id;
            }
            try {
                $this->load->library('form_validation');
                $this->form_validation->set_rules('id', 'ID', 'trim');
                if ($id)
                    $this->form_validation->set_rules('kalameh', 'کلمه', 'trim|required');
                else
                    $this->form_validation->set_rules('kalameh', 'کلمه', 'trim|required|is_unique[dictionary.kalameh]');
                $this->form_validation->set_rules('translate', 'ترجمه', 'trim|required');
                if ($this->form_validation->run() == FALSE) {
                    throw new Exception(implode(' | ', $this->form_validation->error_array()), 1);
                }
                $translate = trim(strip_tags($data["translate"]));
                $regdate = date("Y-m-d H:i:s");
                $data = array("kalameh" => $kalameh, "translate" => $translate, "regdate" => $regdate, "fromlang" => $fromlang, "tolang" => $tolang);
                $status = 0;
                $results = 'کلمه ' . $kalameh . ' ثبت شد';
                if ($id) {
                    if (!$this->db->where('id', $id)->update('dictionary', $data)) {
                        $status = 1;
                        $results = "خطا در انجام عملیات";
                    }
                } else {
                    $data["uid"] = (int)$uid;
                    $this->db->insert('dictionary', $data);
                }
            } catch (Exception $e) {
                $this->tools->outE($e);
            }
        }
        $this->tools->outS(0, $results, array("count" => $count));
    }

    public function getUserKalameh()
    {
        $user = $this->_loginNeed(TRUE, 'u.id');
        if ($user === FALSE)
            throw new Exception("برای دسترسی به این بخش باید وارد حساب کاربری خود شوید", -1);

        $data = $this->input->post();
        $uid = $user->id;
        if (!$uid || empty($data))
            throw new Exception("اطلاعات ارسالی صحیح نیست", 1);

        $kalameh = trim(@$data["kalameh"]);
        $translate = trim(@$data["translate"]);
        $Limit = is_numeric(@$data["Limit"]) ? $data["Limit"] : 100;
        $Limit = $Limit > 3000 ? 3000 : $Limit;
        $Limitstart = intval(@$data["Limitstart"]);
        $tolang = intval(@$data["tolang"]);
        $fromlang = intval(@$data["fromlang"]);
        $bookid = intval(@$data["bookid"]);
        $dicid = intval(@$data["dicid"]);

        // Fetch dictionary languages
        $O = $this->db->select('id,title')->get('diclang')->result();
        $diclang = array();
        foreach ($O as $k => $v) {
            $diclang[$v->id] = $v->title;
        }

        if ($bookid) {
            if ($kalameh) {
                $this->db->where('a.kalameh', $kalameh);
            } else {
                $this->db->order_by('a.fromlang,a.kalameh', 'ASC');
                $this->db->limit($Limit, $Limitstart);
            }

            // Selecting book title and thumbnail correctly
            $this->db->select('a.id, a.kalameh, a.translate, u.bookid, p.title AS book_title, p.thumb AS book_thumbnail');
            $this->db->from('userdictionary a');
            $this->db->join('userdicbook u', 'u.dicid = a.id', 'LEFT');
            $this->db->join('posts p', 'p.ID = u.bookid', 'LEFT');

            if ($fromlang)
                $this->db->where('a.fromlang', $fromlang);
            if ($tolang)
                $this->db->where('a.tolang', $tolang);

            $this->db->where('a.uid', $uid);
            $results = $this->db->get()->result();
        } else {
            // Fetching records including book title and thumbnail correctly
            $this->db->select('a.*, u.bookid, p.title AS book_title, p.thumb AS book_thumbnail');
            $this->db->from('userdictionary a');         // no 'ci_' here
            $this->db->join('userdicbook u', 'u.dicid = a.id', 'LEFT');
            $this->db->join('posts p', 'p.ID = u.bookid', 'LEFT');


            if ($kalameh) {
                $this->db->where('a.kalameh', $kalameh);
            }
            if ($fromlang)
                $this->db->where('a.fromlang', $fromlang);
            if ($tolang)
                $this->db->where('a.tolang', $tolang);

            $this->db->where('a.uid', $uid);
            $results = $this->db->get()->result();
        }

        // If translation exists, handle update or insert operation
        if ($translate) {
            if (count($results)) {
                $dicid = $results[0]->id;
            } else {
                $dicid = 0;
            }
            try {
                $this->load->library('form_validation');
                $this->form_validation->set_rules('id', 'ID', 'trim');
                $this->form_validation->set_rules('kalameh', 'کلمه', 'trim|required');
                $this->form_validation->set_rules('translate', 'ترجمه', 'trim|required');

                if ($this->form_validation->run() == FALSE) {
                    throw new Exception(implode(' | ', $this->form_validation->error_array()), 1);
                }

                $translate = trim(strip_tags($data["translate"]));
                $regdate = date("Y-m-d H:i:s");

                $data = array(
                    "kalameh" => $kalameh,
                    "translate" => $translate,
                    "regdate" => $regdate,
                    "fromlang" => $fromlang,
                    "tolang" => $tolang
                );

                $status = 0;
                $results = 'کلمه ' . $kalameh . ' ثبت شد';

                if ($dicid) {
                    if (!$this->db->where('id', $dicid)->update('userdictionary', $data)) {
                        $status = 1;
                        $results = "خطا در انجام عملیات";
                    }
                } else {
                    $data["uid"] = $uid;
                    $this->db->insert('userdictionary', $data);
                    $dicid = $this->db->insert_id();
                }

                if ($bookid) {
                    $data = array(
                        "uid" => $uid,
                        "bookid" => $bookid,
                        "dicid" => $dicid
                    );

                    $insert_query = $this->db->insert_string('userdicbook', $data);
                    $insert_query = str_replace('INSERT INTO', 'INSERT IGNORE INTO', $insert_query);
                    $this->db->query($insert_query);
                }
            } catch (Exception $e) {
                $this->tools->outE($e);
            }
        }

        $this->tools->outS(0, $results, array("dicid" => $dicid));
    }



    public function DeleteUserKalameh()
    {
        $user = $this->_loginNeed(TRUE, 'u.id');
        if ($user === FALSE)
            throw new Exception("برای دسترسی به این بخش باید وارد حساب کاربری خود شوید", -1);
        $data = $this->input->post();
        $uid = $user->id;
        if (!$uid || empty($data))
            throw new Exception("اطلاعات ارسالی صحیح نیست", 1);
        $dicid = (int)$data["dicid"];
        $bookid = (int)$data["bookid"];
        if ($bookid) {
            $this->db->where('dicid', $dicid)->where('uid', $uid)->where('bookid', $bookid)->delete('userdicbook');
            $result = $this->db->where('dicid', $dicid)->select('*')->get('userdicbook')->result();
            if (!count($result)) {
                $this->db->where('id', $dicid)->where('uid', $uid)->delete('userdictionary');
            }
        } else {
            $this->db->where('id', $dicid)->where('uid', $uid)->delete('userdictionary');
            $this->db->where('dicid', $dicid)->delete('userdicbook');
        }
        $this->tools->outS(0, "حذف شد");
    }

    public function sendOffer()
    {
        $user = $this->_loginNeed(TRUE, 'u.id');
        if ($user === FALSE)
            throw new Exception("برای دسترسی به این بخش باید وارد حساب کاربری خود شوید", -1);
        $data = $this->input->post();
        if (empty($data))
            throw new Exception("اطلاعات ارسالی صحیح نیست", 1);

        $uid = $user->id;
        $kalameh = trim($data["kalameh"]);
        $translate = trim($data["translate"]);
        $tolang = (int)$data["tolang"];
        $fromlang = (int)$data["fromlang"];

        $result = $this->db->where('kalameh', $kalameh)->where('fromlang', $fromlang)->where('tolang', $tolang)->select('*')->get('dictionary')->row();
        if (is_object($result)) {
            $id = $result->id;
            $data = array("offer" => $result->offer + 1);
            $this->db->where('id', $id)->update('dictionary', $data);
        } else {
            $regdate = date("Y-m-d H:i:s");
            $data = array("kalameh" => $kalameh, "regdate" => $regdate, "fromlang" => $fromlang, "tolang" => $tolang, "offer" => 1);
            $status = 0;
            $results = 'کلمه ' . $kalameh . ' ثبت شد';
            $data["uid"] = (int)$uid;
            $this->db->insert('dictionary', $data);
            $id = $this->db->insert_id();
        }

        $this->load->library('form_validation');

        $this->form_validation->set_rules('translate', 'ترجمه پیشنهادی', 'trim|xss_clean|required|min_length[2]');
        if ($this->form_validation->run() == FALSE)
            throw new Exception(implode('|', $this->form_validation->error_array()), 2);

        $result = $this->db->where('kid', $id)->where('translate', $translate)->select('*')->get('dicoffer')->result();
        if (count($result)) {
            throw new Exception('ترجمه پیشنهادی قبلا به ثبت رسیده است.', 2);
        }
        $regdate = date("Y-m-d H:i:s");
        $data = array("kid" => $id, "translate" => $translate, "regdate" => $regdate);
        $status = 0;
        $results = 'کلمه ' . $kalameh . ' ثبت شد';
        $data["uid"] = (int)$uid;
        $this->db->insert('dicoffer', $data);

        $this->tools->outS(0, 'پیشنهاد شما ثبت گردید.از شما متشکریم که به توسعه برنامه کمک می کنید');
    }

    /*===================================
		USERS
	===================================*/
    public function register()
    {
        $data = $this->input->post();
        //$this->LogMe($data);

        if (empty($data))
            throw new Exception("اطلاعات ارسالی صحیح نیست", 1);

        $this->load->library('form_validation');
        $this->load->library('myformvalidator');

        $this->form_validation->set_rules('username', 'نام کاربری', 'trim|xss_clean|required|alpha_dash|is_unique[users.username]|min_length[4]|max_length[30]');
        $this->myformvalidator->set_rules('mobile', 'شماره همراه', 'trim|xss_clean|required|valid_mobile|is_unique[users.tel]');
        $this->form_validation->set_rules('email', 'ایمیل', 'trim|xss_clean|required|valid_email|is_unique[users.email]');
        $this->form_validation->set_rules('password', 'گذرواژه', 'trim|xss_clean|required|min_length[4]|max_length[30]');
        //$this->myformvalidator->set_rules('mac'       , 'mac'         , 'trim|xss_clean|required|valid_mac|is_unique[logged_in.mac]');

        if ($this->form_validation->run() == FALSE)
            throw new Exception(implode('|', $this->form_validation->error_array()), 2);

        //throw new Exception( $data['mac'], 2);

        $data = array(
            'username' => $data['username'],
            'displayname' => '', //$data['username'],
            'tel' => $data['mobile'],
            'email' => $data['email'],
            'password' => do_hash($data['password']),
            'active' => 1,
            'level' => 'user',
            'date' => date('Y-m-d H:i:s'),
            'last_seen' => date('Y-m-d H:i:s'),
        );

        if (!$this->db->insert('users', $data))
            throw new Exception("خطا در انجام عملیات", 4);


        $this->db->select('id,username,displayname,displayname as fullname,gender,age,email,tel,national_code');
        $this->db->select('birthday,city,state,postal_code,address,avatar,date');
        $this->db->where('id', $this->db->insert_id());
        $user = $this->db->get('users', 1)->row();

        $this->load->helper('string');

        $token = random_string('alnum', 32);
        $mac = $this->input->post('mac');

        $this->db->insert('logged_in', [
            'user_id' => $user->id,
            'mac' => $mac,
            'token' => $token,
            'date' => time()
        ]);

        $re = array(
            'user' => $user,
            'highlights' => [],
            'books' => [],
            'notes' => [],
            'sounds' => [],
            'images' => [],
            'access' => [
                'mac' => $mac,
                'token' => $token
            ],
        );

        $this->tools->outS(0, "ثبت نام شما با موفقیت انجام شد", ['data' => $re]);
    }

    public function login()
    {
        $data = $this->input->post();

        if (empty($data))
            throw new Exception("اطلاعات ارسالی صحیح نیست", 1);

        $this->load->library('form_validation');
        $this->load->library('myformvalidator');

        $this->form_validation->set_rules('username', 'نام کاربری', 'trim|xss_clean|required');
        $this->form_validation->set_rules('password', 'گذرواژه', 'trim|xss_clean|required');
        //$this->myformvalidator->set_rules('mac'       , 'mac'         , 'trim|xss_clean|required|valid_mac');

        if ($this->form_validation->run() == FALSE)
            throw new Exception(implode('|', $this->form_validation->error_array()), 2);

        $data = $this->input->post();

        $where = array(
            'username' => $data['username'],
            'password' => do_hash($data['password']),
        );

        $this->db->select('id,username,displayname,displayname as fullname,gender,age,email,tel,national_code');
        $this->db->select('birthday,city,state,postal_code,address,avatar,date');
        $this->db->where($where);
        $user = $this->db->get('users', 1)->row();

        if (empty($user))
            throw new Exception("نام کاربری یا رمز عبور صحیح نیست", 3);

        /*
		if($this->db->where('user_id',$user->id)->where('mac !=',$data['mac'])->count_all_results('logged_in'))
			throw new Exception("امکان ورود به حساب همزمان با چند دستگاه وجود ندارد" , 4);
		*/
        $this->db->where('user_id', $user->id)->delete('logged_in');

        $this->load->helper('string');
        $token = random_string('alnum', 32);

        //if($user->id == 10) $token = 'C36ZKdE02Nf89MIylUpbgL5VDnjArHmX';

        $mac = $this->input->post('mac');
        $this->db->where('mac', $mac)->delete('logged_in');

        $this->db->insert('logged_in', [
            'user_id' => $user->id,
            'mac' => $mac,
            'token' => $token,
            'date' => time()
        ]);

        $this->load->model('m_book', 'book');

        $re = array(
            'user' => $user,
            'notes' => $this->book->getUserNotes($user->id),
            'highlights' => $this->book->getUserHighlights($user->id),
            'sounds' => $this->book->getUserfavSounds($user->id),
            'images' => $this->book->getUserfavImages($user->id),
            'books' => $this->book->getUserBooks($user->id),
            'access' => [
                'mac' => $mac,
                'token' => $token
            ],
        );

        $this->tools->outS(0, 'OK', ['data' => $re]);
    }

    public function logout()
    {
        $user = $this->_loginNeed();
        if ($user)
            $this->db->where('user_id', $user->id)->delete('logged_in');
        $this->tools->outS(0, 'OK');
    }

    public function resetPassword()
    {
        $email = $this->input->post('email');

        if (empty($email))
            throw new Exception("اطلاعات ارسالی صحیح نیست", 1);


        $this->load->library('form_validation');
        $this->form_validation->set_rules('email', 'ایمیل', 'trim|xss_clean|required|valid_email');

        if ($this->form_validation->run() == FALSE)
            throw new Exception(implode('|', $this->form_validation->error_array()), 2);

        if ($this->db->where('email', $email)->count_all_results('users') != 1)
            throw new Exception("کاربری با این ایمیل پیدا نشد", 3);

        $new_pass = rand(10000, 99999);

        $mail = "گذرواژه جدید شما $new_pass می باشد .
		با نام کاربری و گذرواژه جدید خود وارد شوید .";

        $hash_pass = do_hash($new_pass);

        if (!$this->tools->sendEmail($email, "رمز جدید", $mail))
            throw new Exception("در حال حاضر امکان ارسال ایمیل وجود ندارد", 4);

        if (!$this->db->where('email', $email)->update('users', array('password' => $hash_pass)))
            throw new Exception("خطا در انجام عملیات", 5);

        $this->tools->outS(0, "گذرواژه جدید به ایمیل شما ارسال شد ");
    }

    public function updateProfile()
    {
        $data = $this->input->post();
        if (empty($data))
            throw new Exception("اطلاعات ارسالی صحیح نیست", 1);

        $user = $this->_loginNeed();

        if ($user === FALSE)
            throw new Exception("برای دسترسی به این بخش باید وارد حساب کاربری خود شوید", -1);

        $this->load->library('form_validation');
        $this->load->library('myformvalidator');

        if ($user->username != $data['username'])
            $this->form_validation->set_rules('username', 'نام کاربری', 'trim|xss_clean|required|alpha_dash|is_unique[users.username]|min_length[4]|max_length[30]');
        /*
		if($user->email == $data['email'])
		$this->form_validation->set_rules('email'         , 'ایمیل'        , 'trim|xss_clean|valid_email|required');
		else
		$this->form_validation->set_rules('email'         , 'ایمیل'        , 'trim|xss_clean|valid_email|required|is_unique[users.email]');
        */
        if ($user->tel == str_replace('+98', '0', $data['mobile']))
            $this->myformvalidator->set_rules('mobile', 'موبایل', 'trim|xss_clean|required|valid_mobile');
        else
            $this->myformvalidator->set_rules('mobile', 'موبایل', 'trim|xss_clean|required|valid_mobile|is_unique[users.tel]');

        $this->form_validation->set_rules('avatar', 'تصویر پروفایل', 'trim');

        $this->form_validation->set_rules('fullname', 'نام', 'trim|xss_clean|max_length[50]');
        //$this->form_validation->set_rules('password'      , 'گذرواژه'       , 'trim|xss_clean|required|min_length[4]|max_length[30]');
        $this->form_validation->set_rules('gender', 'جنسیت', 'trim|in_list[0,1]');
        $this->form_validation->set_rules('age', 'سن', 'trim');
        $this->form_validation->set_rules('national_code', 'کدملی', 'trim|xss_clean|max_length[20]');
        $this->form_validation->set_rules('birthday', 'تارخ تولد', 'trim|xss_clean|max_length[20]');
        $this->form_validation->set_rules('city', 'شهر', 'trim|xss_clean|max_length[20]');
        $this->form_validation->set_rules('state', 'استان', 'trim|xss_clean|max_length[20]');
        $this->form_validation->set_rules('country', 'کشور', 'trim|xss_clean|max_length[20]');
        $this->form_validation->set_rules('postal_code', 'کدپستی', 'trim|xss_clean|max_length[20]');
        $this->form_validation->set_rules('address', 'آدرس', 'trim|xss_clean|max_length[1000]');
        if ($this->form_validation->run() == FALSE)
            throw new Exception(implode('|', $this->form_validation->error_array()), 2);

        $data = $this->input->post();

        $avatar = $user->avatar;

        if (isset($data['avatar']) && !empty($data['avatar'])) {
            // API endpoint for the upload server
            $uploadServerUrl = "https://hls.zipak.info/upload_files.php?username=_ac"; // Replace with actual URL
        
            // Convert Base64 to a Temporary File
            $imageData = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $data['avatar']));
            $tempFilePath = tempnam(sys_get_temp_dir(), 'avatar_') . ".jpg";
            file_put_contents($tempFilePath, $imageData);
     
            // Prepare File Upload Request
            $curlFile = new CURLFile($tempFilePath, 'image/jpeg', "profile-{$user->id}.jpg");
            $postData = [
                'file' => $curlFile,
            ];
        
            // Send File to Upload Server via cURL
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $uploadServerUrl);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
        
            // Delete the temporary file after upload
            @unlink($tempFilePath);
        
            // Handle response from the upload server
            $uploadResponse = json_decode($response, true);
            if ($httpCode !== 200 || empty($uploadResponse['file_name'])) {
                throw new Exception("خطا در بارگذاری تصویر پروفایل", 5);
            }
        
            $year = date("Y");  // Current year
            $month = date("m"); // Current month

            $avatar = "uploads/_ac/{$year}/{$month}/" . $uploadResponse['file_name'];

        }
        

        $udata = array(
            'username' => $data['username'],
            'email' => $data['email'],
            'displayname' => $data['fullname'],
            //'password'    => do_hash($data['password']) ,
            'avatar' => $avatar,
            'gender' => (int)$data['gender'],
            'age' => (int)$data['age'],
            'tel' => $data['mobile'],
            'national_code' => $data['national_code'],
            'birthday' => $data['birthday'],
            'city' => $data['city'],
            'state' => $data['state'],
            'country' => @$data['country'],
            'postal_code' => $data['postal_code'],
            'address' => $data['address'],
        );

        if (!$this->db->where('id', (int)$user->id)->update('users', $udata))
            throw new Exception("خطا در انجام عملیات", 3);

        $this->db->select('id,username,displayname,displayname as fullname,gender,age,email,tel,national_code,birthday,city,state,country,postal_code,address,avatar,date');
        $this->db->where('id', $user->id);
        $user = $this->db->get('users', 1)->row();

        $this->tools->outS(0, "اطلاعات به روز شد", ['data' => $user]);
    }
    
    /*===================================
		Eitaa AutoAuth
	===================================*/
	public function ema_auto_auth_user() {
	    // Load the UserModel
	    $userModel = new M_user();
	    $eitaa_token = EITAA_TOKEN;
	   // $eitta_token2 is just for test, must be deleted later
	    $eitaa_token2 = '61101070:ad]r(7#cP-xUVh4O9o3-vIfpRCdqv-whFaQH4pR-ul9NjebzU-3sESD}OD1-Aw4LkqYe7-TGL2lr61{-YKqmL(.3u-Br}sdOjyo-wPBVVW)ez-JB%M6JKIF-GOmXm,t]v-MFAMKHbuy-gsgz';
	    $eitta_data = $this->input->post('eitaa_data');
	    
	    // Remove escaped backslashes
        $eitta_data = preg_replace('/\\\\"/', '"', $eitta_data);
        
        if ($eitaa_token) {
            $valid_data2 = false;
            $valid_data = $this->ema_validate_eitta_data($eitta_data, $eitaa_token);
            if (!$valid_data){
                $valid_data2 = $this->ema_validate_eitta_data($eitta_data, $eitaa_token2);
            }
        } else {
            $this->tools->outS(0, 'ایتا توکن الزامی می باشد!');
        }
        
        if ($valid_data || $valid_data2) {
            $parsedData = $this->ema_extract_data($eitta_data);

            $eitaa_id = $parsedData['user']['id'];
            $first_name = $parsedData['user']['first_name'];
            $last_name = $parsedData['user']['last_name'];
            $username = $parsedData['user']['username'] ?? 'user_' . $eitaa_id;
            $email = $parsedData['user']['email'] ?? $username . '@eitaa.com';
            
            if (!empty($eitaa_id)) {
                $meta_key = 'eitaa_id';
                
                $user_meta = $this->db->select('user_id')
                      ->where('meta_name', $meta_key)
                      ->where('meta_value', $eitaa_id)
                      ->get('user_meta')
                      ->row();

                if ($user_meta && isset($user_meta->user_id)) {
                    $user_id = (int) $user_meta->user_id;
                    $user = $this->db->select('*')
                     ->where('id', $user_id)
                     ->get('users')
                     ->row();
                    
                    $response = [
                        'login' => true,
                        'user' => $user,
                    ];

                    $this->tools->outS(0,$response);
                } else {
                    // User creation logic
                    $user_data = ["username" => $username, "tel" => '', "displayname" => $first_name .' '. $last_name, "name" => $first_name , "family" => $last_name  , "email" => $email];

                    // Insert new user into the users table
                    if (!$this->db->insert('users', $user_data)) {
                        throw new Exception("خطا در انجام عملیات", 4);
                    }
    
                    // Get the newly created user's ID
                    $new_user_id = $this->db->insert_id();

                    // After creating the user, attempt to log in again
                    // $user = $this->_loginNeed(TRUE, 'u.id');

                    $meta_key = 'eitaa_id';
                    // Prepare the data array
                    $data = [
                        $meta_key => $eitaa_id
                    ];
                    
                    // Call the updateMeta function
                    $userModel->updateMeta($data, $new_user_id);
                    
                    $this->db->select('*');
                    $this->db->where('id', $new_user_id);
                    $user = $this->db->get('users')->row();
                    
                    $response = [
                        'register' => true,
                        'user' => $user,
                    ];

                    $this->tools->outS(0,$response);
                }
            } else {
                $this->tools->outS(0, 'آیدی ایتا معتبر نمی باشد!');
            }
        } else {
            $this->tools->outS(0, 'دیتا معتبر نمی باشد!');
        }
	}

    public function ema_contact_check() {
        // Load the UserModel
        $this->load->model('M_user'); // Ensure the model is loaded properly
        $eitaa_token = EITAA_TOKEN;
        $contact_data = $this->input->post('contact_data');

        // Validate input data
        if (empty($eitaa_token) || empty($contact_data)) {
            $this->tools->outS(0, 'اطلاعات کافی نمی باشد!');
            return;
        }
    
        // Remove escaped backslashes from contact_data
        $contact_data = preg_replace('/\\\\"/', '"', $contact_data);
    
        // Validate contact data
        $valid_contact_data = $this->ema_validate_eitta_data($contact_data, $eitaa_token);
        if (!$valid_contact_data) {
            $this->tools->outS(0, 'دیتا معتبر نمی باشد!');
            // $this->tools->outS(0, $valid_contact_data);
            return;
        }
    
        // Extract the phone number and other details from contact_data
        $extracted_contact_data = $this->ema_extract_contact_data($contact_data);
    
        if (!isset($extracted_contact_data['phone_number'], $extracted_contact_data['eitaa_id'])) {
            $this->tools->outS(0, 'اطلاعات تماس معتبر نمی باشد!');
            return;
        }
    
        $phone_number = $extracted_contact_data['phone_number'];
        $eitaa_id = $extracted_contact_data['eitaa_id'];
        
        $normalized_tel = $this->ema_replace_country_code_with_zero($phone_number);
        $existing_user = $this->M_user->selectUserByNeme($normalized_tel);
        if ($existing_user) {
            $existing_user_id = (int)$existing_user->id;
        }
        
        if ($existing_user_id) {
            $meta_key = 'eitaa_id';
            // Prepare the data array
            $data = [
                $meta_key => $eitaa_id
            ];
            
            // Call the updateMeta function
            $this->M_user->updateMeta($data, $existing_user_id);
  
            $username = 'user_' . $eitaa_id;
            $user = $this->M_user->selectUserByNeme($username);
            
            if ($user) {
                // Update the user's phone number
                $user_id = (int)$user->id;
                $this->db->where('user_id', $user_id);
                $this->db->where('meta_name', 'eitaa_id');
                $this->db->delete('user_meta');

                $updated_user = $this->M_user->selectUserByNeme($normalized_tel);
                
                // Respond with the user object
                $response = [
                    'user' => $updated_user,
                ];
                $this->tools->outS(1, $response); // 1 indicates success
            } else {
                $this->tools->outS(1, 'کاربر یافت نشد!');
            }

        } else {
            // Generate username and find the user
            $username = 'user_' . $eitaa_id;
            $user = $this->M_user->selectUserByNeme($username);
        
            if (!$user) {
                $this->tools->outS(0, 'کاربر یافت نشد!');
                return;
            }
        
            // Update the user's phone number
            $user_id = (int)$user->id;
            $user_data = array(
                'username' => $normalized_tel,
                'tel' => $normalized_tel,
            );
        
            $this->db->where('id', $user_id);
            $update_result = $this->db->update('users', $user_data);
            
            $updated_user = $this->M_user->selectUserByNeme($normalized_tel);
            
            // Respond with the user object
            $response = [
                'user' => $updated_user,
            ];
            $this->tools->outS(1, $response); // 1 indicates success
        }

    }
    
    private function ema_replace_country_code_with_zero($phone) {
        $phone = (string)$phone;
        // Check if the number starts with '98'
        if (substr($phone, 0, 2) === '98') {
            // Replace '98' with '0' at the beginning of the string
            $phone = '0' . substr($phone, 2);
        }
        return $phone;
    }

	private function ema_validate_eitta_data($eitta_data, $eitaa_token) {
	    parse_str($eitta_data, $data);

        $receivedHash = $data['hash'];
        unset($data['hash']);
    
        ksort($data);
        $dataCheckString = [];
        foreach($data as $key => $value) {
            $dataCheckString[] = "$key=$value";
        }
    
        $secretKey = hash_hmac('sha256', $eitaa_token, "WebAppData", true);
        $generatedHash = hash_hmac('sha256', implode("\n", $dataCheckString), $secretKey);
    
        return hash_equals($generatedHash, $receivedHash);
	}
	
	private function ema_extract_data($eitta_data) {
	    parse_str($eitta_data, $queryParams);
    
        return [
            'query_id' => $queryParams['query_id'] ?? null,
            'user' => $queryParams['user'] ? json_decode($queryParams['user'], true) : null,
            'auth_date' => $queryParams['auth_date'] ?? null,
            'hash' => $queryParams['hash'] ?? null,
        ];
	}

    // Extract Telegram/Eitaa data
    private function ema_extract_contact_data($queryString) {
        parse_str($queryString, $queryParams);

        return [
            'phone_number' => json_decode($queryParams['contact'])->phone ?? null,
            'eitaa_id' => json_decode($queryParams['contact'])->user_id ?? null,
        ];
    }
    
    public function ema_get_user_by_username() {
        // Load the UserModel
        $this->load->model('M_user'); // Ensure the model is loaded properly
        $secret_key = SECRET_KEY;
        $data = $this->input->post('data');
        $iv = $this->input->post('iv');
        
        // Check for missing parameters
        if (empty($encryptedData) || empty($iv)) {
            $this->tools->outS(0, 'دیتا کامل نیست!');
        }
        
        $username = $this->ema_decrypt_data(hex2bin($data), $iv, $secret_key);
        
        if ($username) {
            $user = $this->M_user->selectUserByNeme($username);
        } else {
            $this->tools->outS(0, 'عملیات ناموفق!');
        }
        
        // Respond with the user object
        $response = [
            'user' => $user,
        ];
        $this->tools->outS(1, $response); // 1 indicates success
    }
    
    // Function to decrypt data
    private function ema_decrypt_data($encryptedData, $iv, $secretKey) {
        // Ensure inputs are valid
        if (empty($encryptedData) || empty($iv) || empty($secretKey)) {
            return null;
        }

        // Convert secret key and IV to binary format
        $secretKey = hex2bin($secretKey);
        $iv = hex2bin($iv);

        // Decrypt the data using AES-256-CBC
        $decryptedData = openssl_decrypt(
            $encryptedData,
            'aes-256-cbc',
            $secretKey,
            OPENSSL_RAW_DATA,
            $iv
        );

        return $decryptedData ? json_decode($decryptedData, true) : null;
    }
    
    public function ema_change_mobile()
    {
        $user = $this->_loginNeed(TRUE, 'u.id');
        if ($user === FALSE)
            throw new Exception("برای دسترسی به این بخش باید وارد حساب کاربری خود شوید", -1);
        $userid = $user->id;
        $this->load->library('form_validation');
        $this->form_validation->set_rules('tel', 'شماره همراه جدید', 'trim|numeric|required');
        $this->form_validation->set_rules('code', 'کد اعتبار سنجی', 'trim|numeric');
        if ($this->form_validation->run() == FALSE)
            throw new Exception(implode('|', $this->form_validation->error_array()), 1);
    
        $post = $this->input->post();
    
        $this->db->select("a.id");
        $this->db->where("a.id != $userid");
        $this->db->where("a.tel", $post['tel']);
        $existingUser = $this->db->get('users a')->row() ? true : false;
    
        $code = $post['code'];
        if ($code) {
            $this->db->select("a.*");
            $this->db->where("a.id", $userid);
            $this->db->where("a.code", $code);
            $isValidCode = $this->db->count_all_results('users a');
    
            if ($isValidCode) {
                $data = ["tel" => $post["tel"]];
                $this->db->where('id', $userid)->update('users', $data);
    
                $response = [
                    "existingUser" => $existingUser,
                    "res" => "جایگذاری شماره همراه جدید با موفقیت انجام شد"
                ];
                $this->tools->outS(0, $response);
            } else {
                throw new Exception("کد تاییدیه صحیح نمی باشد", 1);
            }
        } else {
            $this->db->select('*');
            $this->db->where('id', $userid);
            $user = $this->db->get('users', 1)->row();
            $config = $this->settings->data;
            $mobile = $post["tel"];
            $name = $user->name;
            $family = $user->family;
            $displayname = $user->displayname;
            $username = $user->username;
            $code = $user->code;
            $sendtime = $user->sendtime;
            $gender = $user->gender ? 'جناب آقای' : 'سرکار خانم';
            $message = $config["smstextpassrec"];
            $waitTime = (int)$config["waitTime"];
            if ($waitTime && (time() - $sendtime) < $waitTime * 60) {
                $r = $waitTime * 60 - (time() - $sendtime);
                $message = sprintf("لطفا تا %s دقیقه و %s ثانیه دیگر صبر کنید.شما هر %s دقیقه یکبار می توانید درخواست احراز هویت نمایید", intval($r / 60), $r % 60, $waitTime);
                throw new Exception($message, 1);
            } else {
                $code = rand(1111, 9999);
                $data = array(
                    'code' => $code,
                    'sendtime' => time()
                );
                $this->db->where('id', $user->id)->update('users', $data);
            }
            $from = array('{gender}', '{name}', '{family}', '{code}', '{displayname}', '{username}');
            $to = array($gender, $name, $family, $code, $displayname, $username);
            $message = str_replace($from, $to, $message);
            $message = str_replace('  ', ' ', $message);
            $message = str_replace('  ', ' ', $message);
            $message = str_replace('  ', ' ', $message);
            $message = trim($message);
            $smsdata = array("mobile" => $mobile, "text" => $message);
            $re = $this->SendSMS($smsdata, $userid, 2);
    
            $response = [
                "existingUser" => $existingUser,
                "res" => $re
            ];
            $this->tools->outS(0, $response);
        }
    }

    /*===================================
		GROUP
	===================================*/
    public function getGroup($id = 0)
    {
        $results = $this->db->where_in('parent', $id)->select('id,name')->get('group')->result();
        $this->tools->outS(0, $results);
    }

    public function getCategory($id = 0)
    {
        $results = $this->db->where_in('parent', $id)->select('id,name')->order_by('position')->get('category')->result();
        $this->tools->outS(0, $results);
    }

    public function getAllPayeh()
    {
        $results = $this->db->select('id,name,IF(parent = 0,1,0) AS sath,IF(parent = 0,0,1) AS payeh')->get('category')->result();
        $this->tools->outS(0, $results);
    }

    /*===================================
		Geo
	===================================*/
    public function getGeo()
    {
        $section = $this->input->post('section');
        $parent = (int)$this->input->post('parent');
        $allow = ['country', 'province', 'city'];
        if (!in_array($section, $allow))
            throw new Exception('موقعیت جغرافیایی را مشخص نمایید');
        $this->db->select('id,title');
        if ($section == 'country')
            $this->db->select('summary');
        if ($parent)
            $this->db->where('parent', $parent);
        $results = $this->db->get($section)->result();
        $this->tools->outS(0, $results);
    }

    /*===================================
		Discount
	===================================*/
    public function UseDiscountCode1()
    {
        $user = $this->_loginNeed(TRUE, 'u.id');
        //$this->LogMe(array("RecoverUsersBooks"=>$_REQUEST));
        $level_id = (int)$this->input->post('level_id');
        $discountCode = $this->input->post('code');

        if ($user === FALSE)
            throw new Exception("برای دسترسی به این بخش باید وارد حساب کاربری خود شوید", -1);

        $discount = $this->db->where('code', $discountCode)->get('discounts')->row();

        if (!isset($discount->id))
            throw new Exception("کد تخفیف وارد شده معتبر نیست", 5);

        if (!$discount->expdate || $discount->expdate < time())
            throw new Exception("کد تخفیف وارد شده منقضی شده است", 5);


        if ($discount->used == $discount->maxallow)
            throw new Exception("سقف استفاده از کد تخفیف وارد شده تکمیل شده است", 5);

        if ($discount->category_id && $discount->category_id != $level_id)
            throw new Exception("کد تخفیف وارد شده برای خرید این سطح نیست", 5);

        $discount_id = $discount->id;
        $user_id = $user->id;
        $discountused = $this->db
            ->where('user_id', $user_id)
            ->where('discount_id', $discount_id)
            ->get('discount_used')->row();
        if ($discountused)
            throw new Exception("شما از کد تخفیف وارد شده قبلا استفاده کردید", 5);
        $data = array('user_id' => $user_id, 'discount_id' => $discount_id, 'udate' => time());
        $this->db->insert('discount_used', $data);
        $this->db->set('used', $discount->used + 1);
        $this->db->where("code", $discountCode);
        $this->db->update('discounts');

        $this->tools->outS(0, 'OK');
    }

    public function ExpDiscountCode()
    {
        $user = $this->_loginNeed(TRUE, 'u.id');
        //$this->LogMe(array("RecoverUsersBooks"=>$_REQUEST));
        if ($user === FALSE)
            throw new Exception("برای دسترسی به این بخش باید وارد حساب کاربری خود شوید", -1);

        $discountCode = $this->input->post('code');
        $this->db->set('expdate', '0');
        $this->db->where("code", $discountCode);
        $this->db->update('discounts');

        $this->tools->outS(0, 'OK');
    }

    public function removeMyBook()
    {
        $user = $this->_loginNeed(TRUE, 'u.id');

        $post = $this->input->post();
        $mac = $this->input->post('mac');
        $token = $this->input->post('token');
        $bookid = $this->input->post('bookid');
        $this->load->library('form_validation');
        $this->form_validation->set_rules('bookid', 'شماره کتاب', 'trim|required|numeric');
        if ($this->form_validation->run() == FALSE)
            throw new Exception(implode('|', $this->form_validation->error_array()), 1);
        $result = $this->db->where('user_id', $user->id)->where('book_id', $bookid)->delete('user_books');
        $this->tools->outS(0, 'OK');
    }

    public function UpdateAppVer()
    {
        $user = $this->_loginNeed(TRUE, 'u.id');

        $post = $this->input->post();
        $mac = $this->input->post('mac');
        $token = $this->input->post('token');
        $mobilemodel = $this->input->post('mobilemodel');
        $this->load->library('form_validation');
        $this->form_validation->set_rules('AppVer', 'نسخه برنامه', 'trim|required');
        if ($this->form_validation->run() == FALSE)
            throw new Exception(implode('|', $this->form_validation->error_array()), 1);
        $data = array(
            'user_id' => $user->id,
            'mac' => $mac,
            'token' => $token,
            'date' => time()
        );
        if (@$post["mobilemodel"])
            $data['mobilemodel'] = $post["mobilemodel"];
        if (@$post["android"])
            $data['android'] = $post["android"];
        if (@$post["AppVer"])
            $data['AppVer'] = $post["AppVer"];
        $this->db->select("id");
        $this->db->where("`user_id` = $user->id");
        $this->db->where("`token` = '$token'");
        $this->db->where("`mobilemodel` = '$mobilemodel'");
        $result = $this->db->get('user_mobile')->result();
        if (!count($result)) {
            $this->db->insert('user_mobile', $data);
        } else {
            $this->db->where('id', $result[0]->id)->update('user_mobile', $data);
        }
        $data = $this->getUserMobiles($user->id);
        $this->tools->outS(0, "برزرسانی شد", array("mobiles" => $data));
    }

    public function supportBooks()
    {
        $user = $this->_loginNeed(TRUE, 'u.id');
        if ($user === FALSE) {
            throw new Exception("برای دسترسی به این بخش باید وارد حساب کاربری خود شوید", -1);
        }
        $dataid = $user->id;
        $this->db->select('*');
        $this->db->where('id', $dataid);
        $user = $this->db->get('users', 1)->row();
        if (!$user->support) {
            throw new Exception("این بخش ویژه پشتیبان می باشد", -1);
        }

        if ($this->db->where('published IN (0,2)')->where('type', 'book')->count_all_results('posts') == 0)
            throw new Exception("کتاب منتشر نشده ای وجد ندارد", 2);

        $this->load->model('m_book', 'book');

        //get the book info
        $books = $this->post->getPosts([
            'type' => 'book',
            'order' => 'p.id',
            'where' => ['p.published' => 0],
            'published' => 0
        ]);
        $booksTest = $this->post->getPosts([
            'type' => 'book',
            'order' => 'p.id',
            'where' => ['p.published' => 2],
            'published' => 2
        ]);
        $this->tools->outS(0, $books, array("test" => $booksTest));
    }

    /*===================================
		SMS
	===================================*/
    public function SendSupportSMS()
    {
        $user = $this->_loginNeed(TRUE, 'u.id');

        if ($user === FALSE) {
            throw new Exception("برای دسترسی به این بخش باید وارد حساب کاربری خود شوید", -1);
        }

        $dataid = $user->id;
        $this->db->select('*');
        $this->db->where('id', $dataid);
        $user = $this->db->get('users', 1)->row();
        $dataid = $user->id;
        $mobile = $user->tel;
        $name = $user->name;
        $family = $user->family;
        $displayname = $user->displayname;
        $username = $user->username;
        $displayname = $user->displayname;
        $code = $user->code;
        $sendtime = $user->sendtime;
        $gender = $user->gender ? 'جناب آقای' : 'سرکار خانم';

        $this->load->library('form_validation');
        $this->load->library('myformvalidator');
        $this->form_validation->set_rules('MessageType', 'نوع پیام', 'trim|required|numeric');
        $this->form_validation->set_rules('Message', 'پیام', 'trim');
        $this->myformvalidator->set_rules('mac', 'شماره همراه', 'trim|valid_mobile');
        $this->myformvalidator->set_rules('support', 'شماره همراه', 'trim|xss_clean|required|valid_mobile|is_unique[users.tel]');
        if ($this->form_validation->run() == FALSE)
            throw new Exception(implode('|', $this->form_validation->error_array()), 1);
        $config = $this->settings->data;
        $post = $this->input->post();
        $support = $this->input->post('support');
        $this->db->select('*');
        $this->db->where('tel', $support);
        $this->db->where('support', '1');
        $supportuser = $this->db->get('users', 1)->row();
        if (!isset($supportuser->id)) {
            throw new Exception('فقط مدیران اجازه دارند', 1);
        }
        switch ($post["MessageType"]) {
            case 0:
                throw new Exception('نوع ارسال مشخص نیست', 1);
                break;
            case 1:
                throw new Exception("بایستی احراز هویت را ارسال نمایید", -1);
                break;
            case 2:
                $mobile = $post["mac"];
                $message = $config["smstextpassrec"];
                $waitTime = (int)$config["waitTime"];
                if ($waitTime && (time() - $sendtime) < $waitTime * 60) {
                    $r = $waitTime * 60 - (time() - $sendtime);
                    $message = sprintf("لطفا تا %s دقیقه و %s ثانیه دیگر صبر کنید.شما هر %s دقیقه یکبار می توانید درخواست احراز هویت نمایید", intval($r / 60), $r % 60, $waitTime);
                    throw new Exception($message, 1);
                } else {
                    $code = rand(1111, 9999);
                    $data = array(
                        'code' => $code,
                        'sendtime' => time()
                    );
                    $this->db->where('id', $user->id)->update('users', $data);
                }
                break;
        }
        $from = array('{gender}', '{name}', '{family}', '{code}', '{displayname}', '{username}');
        $to = array($gender, $name, $family, $code, $displayname, $username);
        $message = str_replace($from, $to, $message);
        $message = str_replace('  ', ' ', $message);
        $message = str_replace('  ', ' ', $message);
        $message = str_replace('  ', ' ', $message);
        $message = trim($message);
        $smsdata = array("mobile" => $support, "text" => $message);
        $re = $this->SendSMS($smsdata, $dataid, $post["MessageType"]);

        $this->tools->outS(0, $re);
    }

    public function SendUserSMS()
    {
        $user = $this->_loginNeed(TRUE, 'u.id');

        $post = $this->input->post();
        $mac = $this->input->post('mac');
        $token = $this->input->post('token');
        $email = $this->input->post('email');
        if ($user === FALSE) {
            //throw new Exception("برای دسترسی به این بخش باید وارد حساب کاربری خود شوید" , -1);
            $data = ["username" => $mac, "tel" => $mac, "displayname" => $mac];
            $this->db->insert('users', $data);

            $user = $this->_loginNeed(TRUE, 'u.id');
        }

        $dataid = $user->id;
        $this->db->select('*');
        $this->db->where('id', $dataid);
        $user = $this->db->get('users', 1)->row();
        $dataid = $user->id;
        $mobile = $user->tel;
        $name = $user->name;
        $family = $user->family;
        $displayname = $user->displayname;
        $username = $user->username;
        $displayname = $user->displayname;
        $code = $user->code;
        $sendtime = $user->sendtime;
        $gender = $user->gender ? 'جناب آقای' : 'سرکار خانم';

        $this->load->library('form_validation');
        $this->load->library('myformvalidator');
        $this->form_validation->set_rules('MessageType', 'نوع پیام', 'trim|required|numeric');
        $this->form_validation->set_rules('Message', 'پیام', 'trim');
        $this->myformvalidator->set_rules('mac', 'شماره همراه', 'trim|valid_mobile');
        if ($this->form_validation->run() == FALSE)
            throw new Exception(implode('|', $this->form_validation->error_array()), 1);
        $config = $this->settings->data;
        switch ($post["MessageType"]) {
            case 0:
                throw new Exception('نوع ارسال مشخص نیست', 1);
                break;
            case 1:
                $mobile = $user->tel;
                $this->myformvalidator->valid_mobile($mobile);
                $message = trim(strip_tags($post["Message"]));
                if (!$this->form_validation->required($message))
                    throw new Exception('متن پیام نمی تواند خالی باشد', 1);
                break;
            case 2:
                $mobile = $post["mac"];
                $message = strlen($email) ? $config["emailtextpassrec"] : $config["smstextpassrec"];
                $waitTime = (int)$config["waitTime"];
                if ($waitTime && (time() - $sendtime) < $waitTime * 60) {
                    $r = $waitTime * 60 - (time() - $sendtime);
                    $message = sprintf("لطفا تا %s دقیقه و %s ثانیه دیگر صبر کنید.شما هر %s دقیقه یکبار می توانید درخواست احراز هویت نمایید", intval($r / 60), $r % 60, $waitTime);
                    throw new Exception($message, 1);
                } else {
                    $code = rand(1111, 9999);
                    $data = array(
                        'code' => $code,
                        'sendtime' => time()
                    );
                    if ($email) {
                        $data["email"] = $email;
                        $this->db->select("id");
                        $this->db->where("`email` = '$email'");
                        $result = $this->db->get('users')->result();
                        if (count($result)) {
                            foreach ($result as $k => $v) {
                                if ($user->id != $v->id) {
                                    $message = sprintf("ایمیل %s قبلا استفاده شده است", $email);
                                    throw new Exception($message, 0);
                                }
                            }
                        }
                    }
                    $this->db->where('id', $user->id)->update('users', $data);
                }

                $this->db->select("id");
                $this->db->where("`user_id` = $user->id");
                $this->db->where("`token` = '$token'");
                $this->db->where("mobilemodel", @$post["mobilemodel"]);
                $result = $this->db->get('user_mobile')->result();
                $data = array(
                    'user_id' => $user->id,
                    'mac' => $mac,
                    'token' => $token,
                    'mobilemodel' => @$post["mobilemodel"],
                    'android' => @$post["android"],
                    'AppVer' => @$post["AppVer"],
                    'date' => time()
                );

                if (!count($result)) {
                    $this->db->insert('user_mobile', $data);
                } else {
                    $this->db->where('id', $result[0]->id)->update('user_mobile', $data);
                }
                break;
        }
        $from = array('{gender}', '{name}', '{family}', '{code}', '{displayname}', '{username}');
        $to = array($gender, $name, $family, $code, $displayname, $username);
        $message = str_replace($from, $to, $message);
        $message = str_replace('  ', ' ', $message);
        $message = str_replace('  ', ' ', $message);
        $message = str_replace('  ', ' ', $message);
        $message = trim($message);
        $smsdata = array("mobile" => $mobile, "text" => $message);
        if ($email) {
            $smsdata["email"] = $email;
            $re = $this->SendEMail($smsdata, $dataid, $post["MessageType"]);
        } else {
            $re = $this->SendSMS($smsdata, $dataid, $post["MessageType"]);
        }
        $data = $this->getUserMobiles($user->id);
        $this->tools->outS(0, $re, array("mobiles" => $data));
    }

    public function SendEMail($smsdata, $dataid = 0, $side = 0)
    {
        $config = $this->settings->data;
        $title = $config['emailtitlepassrec'];
        $X = "اطلاعات ارسالی صحیح نمی باشند";
        $email = $smsdata["email"];
        $message = nl2br($smsdata["text"]);
        if ($message && $email) {
            if (!$this->tools->sendEmail($email, $title, $message))
                throw new Exception("در حال حاضر امکان ارسال ایمیل وجود ندارد", 4);
            $regdate = date("Y-m-d H:i:s");
            $data = array("mobile" => $email, "message" => $message, "dataid" => $dataid, "side" => $side, "regdate" => $regdate, "delivery" => time(), "status" => 1);
            $X = "پیام با موفقیت ارسال شد";
            $this->db->insert('sended', $data);
        }
        return $X;
    }

    public function SendSMS($smsdata, $dataid = 0, $side = 0)
    {
        $config = $this->settings->data;
        $smsCenter = $config['smsCenter'];
        $smsUN = $config['smsUN'];
        $smsPass = $config['smsPass'];
        $smsNumber = $config['smsNumber'];
        $smsType = $config['smsType'];
        $res = 0;
        $mobile = $smsdata["mobile"];
        if ($smsType && $mobile) {
            $path = BASEPATH;
            $path = str_replace(DIRECTORY_SEPARATOR . 'system', '', $path);
            $file = $path . DIRECTORY_SEPARATOR . 'sms' . DIRECTORY_SEPARATOR . $smsType . ".php";
            $file = str_replace("/", DIRECTORY_SEPARATOR, $file);
            include_once($file);
            $message = $smsdata["text"];
            if (!strlen($mobile))
                return "Error : Mobile number empty";
            $smsType = str_replace(".php", "", $smsType);
            $Panel = [];
            eval("\$Panel = new $smsType;");
            $X = $Panel->LoadSmsPanel("send", $smsUN, $smsPass, $smsNumber, $smsCenter, array($mobile), $message);
            $regdate = date("Y-m-d H:i:s");
            if (is_array($X) && count($X) == 2) {
                list($res, $delivery) = $X;
                if (is_array($delivery))
                    $delivery = $delivery["message"];
                $delivery = str_replace("'", "", $delivery);
                $data = array("mobile" => $mobile, "message" => $message, "dataid" => $dataid, "side" => $side, "regdate" => $regdate, "delivery" => $delivery, "status" => 1);
                $X = "پیام با موفقیت ارسال شد";
            } else {
                $data = array("mobile" => $mobile, "message" => $message, "dataid" => $dataid, "side" => $side, "regdate" => $regdate);
            }
            $this->db->insert('sended', $data);
        }
        return $X;
    }

    public function VerifySMS()
    {
        $user = $this->_loginNeed(TRUE, 'u.id');

        if ($user === FALSE)
            throw new Exception("برای دسترسی به این بخش باید وارد حساب کاربری خود شوید", -1);

        $this->load->library('form_validation');
        $this->load->library('myformvalidator');
        $this->form_validation->set_rules('Message', 'کد احراز هویت', 'trim|required|numeric');
        if ($this->form_validation->run() == FALSE)
            throw new Exception(implode('|', $this->form_validation->error_array()), 1);

        $post = $this->input->post();

        $this->db->select('*');
        $this->db->where('id', $user->id);
        $user = $this->db->get('users', 1)->row();
        $code = $user->code == $post["Message"];

        $message = $code ? "OK" : "کد ارسالی صحیح نیست";
        $status = $code ? 0 : 1;
        $re = array();
        if (!$status) {
            $data = array('forcelogout' => 0);
            $this->db->where('user_id', $user->id)->limit(1)->order_by('date', 'DESC')->update('user_mobile', $data);
            $this->ForceLogOut(1);
            $this->db->where('user_id', $user->id)->delete('logged_in');
            $mac = $user->username;
            $token = $this->input->post('token');
            $this->db->insert('logged_in', [
                'user_id' => $user->id,
                'mac' => $mac,
                'token' => $token,
                'date' => time()
            ]);


            $this->load->model('m_book', 'book');

            $re = array(
                'user' => $user,
                'notes' => $this->book->getUserNotes($user->id),
                'highlights' => $this->book->getUserHighlights($user->id),
                'sounds' => $this->book->getUserfavSounds($user->id),
                'images' => $this->book->getUserfavImages($user->id),
                'books' => $this->book->getUserBooks($user->id),
                'mobiles' => $this->getUserMobiles($user->id),
                'access' => [
                    'mac' => $mac,
                    'token' => $token
                ],
            );
        }
        $this->tools->outS($status, $message, array("data" => $re));
    }

    public function getUserMobiles($userid = 0)
    {
        if (!$userid) {
            $user = $this->_loginNeed(TRUE, 'u.id');

            if ($user === FALSE)
                throw new Exception("برای دسترسی به این بخش باید وارد حساب کاربری خود شوید", -1);
            $userid = $user->id;
        }
        $this->db->select("a.*");
        $this->db->where("a.user_id = $userid");
        $this->db->where("`forcelogout` = 0");
        $this->db->order_by("a.date");
        $mobiles = $this->db->get('user_mobile a')->result();
        return $mobiles;
    }

    public function getUserMobile()
    {
        $user = $this->_loginNeed(TRUE, 'u.id');

        if ($user === FALSE)
            throw new Exception("برای دسترسی به این بخش باید وارد حساب کاربری خود شوید", -1);
        $userid = $user->id;
        $this->db->select("a.*");
        $this->db->where("a.user_id = $userid");
        $this->db->where("`forcelogout` = 0");
        $this->db->order_by("a.date");
        $mobiles = $this->db->get('user_mobile a')->result();
        $this->tools->outS(0, $mobiles);
    }

    /*===================================
		Azmoon
	===================================*/
    public function SaveAzmoon()
    {
        $user = $this->_loginNeed(TRUE, 'u.id');

        if ($user === FALSE)
            throw new Exception("برای دسترسی به این بخش باید وارد حساب کاربری خود شوید", -1);
        $userid = $user->id;
        $this->load->library('form_validation');
        $this->form_validation->set_rules('term', 'ترم', 'trim|required|numeric');
        $this->form_validation->set_rules('bookid', 'آی دی کتاب', 'trim|required|numeric');
        $this->form_validation->set_rules('azmoon_type', 'مدل آزمون', 'trim|required|numeric');
        $this->form_validation->set_rules('azmoon_time', 'مدت آزمون', 'trim|required|numeric');
        $this->form_validation->set_rules('azmoon_questions', 'تعداد سوالات آزمون', 'trim|required|numeric');
        $this->form_validation->set_rules('azmoon_true', 'تعداد جواب درست', 'trim|required|numeric');
        $this->form_validation->set_rules('azmoon_false', 'تعداد جواب نادرست', 'trim|required|numeric');
        $this->form_validation->set_rules('azmoon_none', 'تعداد سوال بدون پاسخ', 'trim|required|numeric');
        $this->form_validation->set_rules('azmoon_result', 'نمره آزمون', 'trim|required|numeric');
        $this->form_validation->set_rules('azmoon_final', 'نمره کل آزمون', 'numeric');
        $this->form_validation->set_rules('azmoon_mahdoode', 'محدوده آزمون', 'trim|xss_clean');
        $this->form_validation->set_rules('azmoon_date', 'زمان برگزاری آزمون', 'trim');
        if ($this->form_validation->run() == FALSE)
            throw new Exception(implode('|', $this->form_validation->error_array()), 1);

        $data = $this->input->post();

        $newdata = array(
            'userid' => $userid,
            'term' => $data['term'],
            'bookid' => $data['bookid'],
            'azmoon_type' => $data['azmoon_type'],
            'azmoon_time' => $data['azmoon_time'],
            'azmoon_questions' => $data['azmoon_questions'],
            'azmoon_true' => $data['azmoon_true'],
            'azmoon_false' => $data['azmoon_false'],
            'azmoon_none' => $data['azmoon_none'],
            'azmoon_result' => $data['azmoon_result'],
            'azmoon_mahdoode' => $data['azmoon_mahdoode'],
            'azmoon_date' => (strlen($data['azmoon_date']) ? $data['azmoon_date'] : date("Y-m-d H:i:s"))
        );
        if (isset($data['azmoon_final']))
            $newdata['azmoon_final'] = $data['azmoon_final'];

        if (!$this->db->insert('azmoon_result', $newdata))
            throw new Exception("خطا در ثبت اطلاعات", 3);

        $re = [
            'data' => [
                'insert_id' => $this->db->insert_id()
            ]
        ];

        $this->tools->outS(0, "آزمون ثبت شد", $re);
    }

    public function GetUserAzmoon()
    {
        $user = $this->_loginNeed(TRUE, 'u.id');

        if ($user === FALSE)
            throw new Exception("برای دسترسی به این بخش باید وارد حساب کاربری خود شوید", -1);
        $userid = $user->id;
        $this->load->library('form_validation');
        $this->form_validation->set_rules('term', 'ترم', 'trim|numeric');
        $this->form_validation->set_rules('bookid', 'آی دی کتاب', 'trim|numeric');
        $this->form_validation->set_rules('azmoon_type', 'مدل آزمون', 'trim|numeric');
        $this->form_validation->set_rules('azmoon_time', 'مدت آزمون', 'trim|numeric');
        if ($this->form_validation->run() == FALSE)
            throw new Exception(implode('|', $this->form_validation->error_array()), 1);

        $data = $this->input->post();

        $this->db->select("a.*,pdate(`azmoon_date`) AS `shamsidate`");
        $this->db->where("a.userid = $userid");
        if (isset($data['term']) && (int)$data['term'])
            $this->db->where("a.term = " . $data['term']);
        if (isset($data['azmoon_type']) && (int)$data['azmoon_type'])
            $this->db->where("a.azmoon_type = " . $data['azmoon_type']);
        if (isset($data['azmoon_time']) && (int)$data['azmoon_time'])
            $this->db->where("a.azmoon_time = " . $data['azmoon_time']);
        if (isset($data['bookid']) && (int)$data['bookid'])
            $this->db->where("a.bookid = " . $data['bookid']);
        $this->db->order_by('a.azmoon_date DESC');
        //$this->db->limit(5,0);
        $azmoons = $this->db->get('azmoon_result a')->result();

        $this->tools->outS(0, array("azmoon" => $azmoons));
    }

    /*===================================
		MOBILE
	===================================*/
    public function ChangeMobile()
    {
        $user = $this->_loginNeed(TRUE, 'u.id');
        if ($user === FALSE)
            throw new Exception("برای دسترسی به این بخش باید وارد حساب کاربری خود شوید", -1);
        $userid = $user->id;
        $this->load->library('form_validation');
        $this->form_validation->set_rules('tel', 'شماره همراه جدید', 'trim|numeric');
        $this->form_validation->set_rules('code', 'کد اعتبار سنجی', 'trim|numeric');
        if ($this->form_validation->run() == FALSE)
            throw new Exception(implode('|', $this->form_validation->error_array()), 1);

        $post = $this->input->post();

        $this->db->select("a.*");
        $this->db->where("a.id != $userid");
        $this->db->where("a.tel = " . $post['tel']);
        $ctel = $this->db->count_all_results('users a');

        if ($ctel)
            throw new Exception("شماره " . $post['tel'] . " قبلا توسط کاربر دیگری مورد استفاده قرار گرفته است", 1);
        $code = $post['code'];
        if ($code) {
            $this->db->select("a.*");
            $this->db->where("a.id = $userid");
            $this->db->where("a.code = '$code'");
            $ctel = $this->db->count_all_results('users a');

            if ($ctel) {
                $data = ["tel" => $post["tel"]];
                $this->db->where('id', $userid)->update('users', $data);
                $this->tools->outS(0, 'جایگذاری شماره همراه جدید با موفقیت انجام شد');
            } else {
                throw new Exception("کد تاییدیه صحیح نمی باشد", 1);
            }
        } else {
            $this->db->select('*');
            $this->db->where('id', $userid);
            $user = $this->db->get('users', 1)->row();
            $config = $this->settings->data;
            $mobile = $post["tel"];
            $name = $user->name;
            $family = $user->family;
            $displayname = $user->displayname;
            $username = $user->username;
            $displayname = $user->displayname;
            $code = $user->code;
            $sendtime = $user->sendtime;
            $gender = $user->gender ? 'جناب آقای' : 'سرکار خانم';
            $message = $config["smstextpassrec"];
            $waitTime = (int)$config["waitTime"];
            if ($waitTime && (time() - $sendtime) < $waitTime * 60) {
                $r = $waitTime * 60 - (time() - $sendtime);
                $message = sprintf("لطفا تا %s دقیقه و %s ثانیه دیگر صبر کنید.شما هر %s دقیقه یکبار می توانید درخواست احراز هویت نمایید", intval($r / 60), $r % 60, $waitTime);
                throw new Exception($message, 1);
            } else {
                $code = rand(1111, 9999);
                $data = array(
                    'code' => $code,
                    'sendtime' => time()
                );
                $this->db->where('id', $user->id)->update('users', $data);
            }
            $from = array('{gender}', '{name}', '{family}', '{code}', '{displayname}', '{username}');
            $to = array($gender, $name, $family, $code, $displayname, $username);
            $message = str_replace($from, $to, $message);
            $message = str_replace('  ', ' ', $message);
            $message = str_replace('  ', ' ', $message);
            $message = str_replace('  ', ' ', $message);
            $message = trim($message);
            $smsdata = array("mobile" => $mobile, "text" => $message);
            $re = $this->SendSMS($smsdata, $userid, 2);

            $this->tools->outS(0, $re);
        }
    }

    /*===================================
		BOOKS
	===================================*/
    public function RecoverUsersBooks()
    {
        $user = $this->_loginNeed(TRUE, 'u.id');
        //$this->LogMe(array("RecoverUsersBooks"=>$_REQUEST));
        if ($user === FALSE)
            throw new Exception("برای دسترسی به این بخش باید وارد حساب کاربری خود شوید", -1);

        $this->db->select('p.id,p.need_update,p.user_id,p.book_id');
        $this->db->order_by('p.user_id,p.book_id');
        $rows = $this->db->get('user_books p')->result();
        $books = array(0);
        $deleteid = array(0);
        foreach ($rows as $k => $v) {
            if (!isset($books[$v->user_id . '-' . $v->book_id]))
                $books[$v->user_id . '-' . $v->book_id] = $v->id;
            else
                $deleteid[] = $v->id;
        }

        $this->db->where("id IN (" . implode(",", $deleteid) . ")");
        $this->db->delete('user_books');

        $this->tools->outS(0, 'OK');
    }

    /*===================================*/
    private function LoadNashr($bookscontroller, &$books, $bookids)
    {
        global $POST_TYPES;
        $this->db->select('*');

        if (count($bookids))
            $this->db->where("post_id IN(" . implode(",", $bookids) . ")");
        $post_nashr = $this->db->get('post_nashr')->result();
        $nashr = $POST_TYPES["book"]["nashr"];
        foreach ($post_nashr as $k => $v) {
            if (isset($nashr[$v->nashr_key]) && isset($bookscontroller[$v->post_id])) {
                $books[$bookscontroller[$v->post_id]]->nashr[$v->nashr_key] = array("title" => $nashr[$v->nashr_key]['name'], "value" => $v->nashr_value);
            }
        }
    }

    /*===================================*/
    public function allbookList()
    {
        $user = $this->_loginNeed(TRUE, 'u.id');
        if ($user === FALSE)
            throw new Exception("برای دسترسی به این بخش باید وارد حساب کاربری خود شوید", -1);

        $book_ids = [];
        $price = (int)$this->input->post('price');
        $catid = (int)$this->input->post('catid');
        $order = $this->input->post('order');
        $limit = (int)$this->input->post('limit');
        $limitstart = (int)$this->input->post('limitstart');
        if (!strlen($order)) {
            $order = 'p.date_modified desc';
        }
        $category = $this->db->where('id', $catid)->get('category')->row();
        $categories = $this->db->order_by('parent,name ASC')->get('category')->result();
        $cats = array();
        $reversecats = array();
        $parentcategorys = array();
        $parentcategoryid = array();
        foreach ($categories as $k => $v) {
            $prefix = "";
            if (isset($cats[$v->parent])) {
                $prefix = $cats[$v->parent] . " » ";
                $parentcategorys[$v->id] = $cats[$v->parent];
                $parentcategoryid[$v->id] = $v->parent;
                $reversecats[$v->parent][] = $v->id;
            }
            $cats[$v->id] = $prefix . $v->name;
        }
        if ($catid) {
            if (isset($parentcategoryid[$catid])) {
                $xcatid = $parentcategoryid[$catid];
            } elseif (isset($reversecats[$catid])) {
                $xcatid = implode(",", $reversecats[$catid]);
            }
        }
        $ids = array();
        $bookids = array();
        switch ($price) {
            case 1 :
                $this->db->where("p.price = 0");
                break;
            case 2 :
                $this->db->where("p.price > 0");
                break;
        }

        $this->db->select('p.*,p.excerpt description,p.category parentcategoryid,p.category parentcategoryname,p.category categoryid,p.category categoryname');
        if ($catid)
            $this->db->where("(p.category = '$catid' OR p.category IN('$xcatid') )");
        $this->db->where("p.published = 1");
        $this->db->where("p.type = 'book'");
        $this->db->where("p.category > 0");
        $this->db->order_by($order);
        if (count($ids))
            $this->db->where("p.id IN (" . implode(",", $ids) . ")");
        if ($limit || $limitstart) {
            $this->db->limit($limit, $limitstart);
            $bookids = array(0);
        }
        $books = $this->db->get('posts p')->result();
        $count = count($books);
        if ($limit || $limitstart) {
            $this->db->select('p.*');
            if ($catid)
                $this->db->where("(p.category = '$catid' OR p.category IN('$xcatid') )");
            $this->db->where("p.published = 1");
            $this->db->where("p.type = 'book'");
            $this->db->order_by($order);
            if (count($ids))
                $this->db->where("p.id IN (" . implode(",", $ids) . ")");
            $count = $this->db->count_all_results('posts p');
        }
        $bookscontroller = array();
        if ($limit || $limitstart) {
            foreach ($books as $k => $v) {
                if (!isset($cats[$v->categoryid])) {
                    unset($books[$k]);
                    continue;
                }
                $bookids[] = $v->id;
                $books[$k]->nashr = array();
                $bookscontroller[$v->id] = $k;
            }
        }
        /*
		$this->db->select('book_id,sound,video,image,description');
		$this->db->distinct('book_id');

		$book_meta = $this->db->get('book_meta')->result();
		$book_ids = array(0);
		$hasDescription = array();
		$hasSound = array();
		$hasVideo = array();
		$hasImage = array();
		foreach($book_meta as $k=>$v){
			if(!isset($hasDescription[$v->book_id])){
				$book_ids[] = $v->book_id;
				$hasDescription[$v->book_id] = 0;
				$hasSound[$v->book_id] = 0;
				$hasVideo[$v->book_id] = 0;
				$hasImage[$v->book_id] = 0;
			}
			$hasDescription[$v->book_id] = $hasDescription[$v->book_id]?$hasDescription[$v->book_id]:($v->description?'true':0);
			$hasSound[$v->book_id] = $hasSound[$v->book_id]?$hasSound[$v->book_id]:($v->sound?'true':0);
			$hasVideo[$v->book_id] = $hasVideo[$v->book_id]?$hasVideo[$v->book_id]:($v->video?'true':0);
			$hasImage[$v->book_id] = $hasImage[$v->book_id]?$hasImage[$v->book_id]:($v->image?'true':0);
		}
        */
        $author = [];
        $startpage = [];
        $finaltest = [];
        $timesecond = [];
        $acceptpercent = [];
        $isvideo = [];

        $post_meta = $this->db->select('*')->where('post_id IN(' . implode(',', $bookids) . ')')
            ->where("meta_key IN ('author','startpage','finaltest','timesecond','acceptpercent','isvideo')")
            ->get('post_meta')->result();
        $meta = [];
        foreach ($post_meta as $k => $v) {
            $meta[$v->meta_key][$v->post_id] = $v->meta_value;
            //eval('$'.$v->meta_key.'['.$v->post_id.'] = "'.$v->meta_value.'";');
        }
        foreach ($books as $k => $v) {
            if (!isset($cats[$v->categoryid])) {
                unset($books[$k]);
                continue;
            }
            $books[$k]->categoryname = $cats[$v->categoryname];
            $books[$k]->parentcategoryname = $parentcategorys[$v->parentcategoryname];
            $books[$k]->parentcategoryid = $parentcategoryid[$v->parentcategoryid];
            $books[$k]->has_description = intval($v->has_description) ? "true" : "false";
            $books[$k]->has_sound = intval($v->has_sound) ? "true" : "false";
            $books[$k]->has_video = intval($v->has_video) ? "true" : "false";
            $books[$k]->has_image = intval($v->has_image) ? "true" : "false";
            $books[$k]->author = @$author[$v->id];
            //$books[$k]->price = @$price[$v->id];
            $books[$k]->startpage = @$meta["startpage"][$v->id];
            $books[$k]->finaltest = @$meta["finaltest"][$v->id];
            $books[$k]->timesecond = @$meta["timesecond"][$v->id];
            $books[$k]->acceptpercent = @$meta["acceptpercent"][$v->id];
            $books[$k]->isvideo = @$isvideo[$v->id];

            $books[$k]->has_test = intval($v->has_test) ? "true" : "false";
            $books[$k]->has_tashrihi = intval($v->has_tashrihi) ? "true" : "false";

            if ($v->thumb) {
                $books[$k]->thumb = $v->thumb;
                $books[$k]->cover300 = thumb($v->thumb, 300);
            }
        }

        $this->LoadNashr($bookscontroller, $books, $book_ids);

        $pagination = array();
        $pagination["limitstart"] = $limitstart;
        $pagination["limit"] = $limit;
        $pagination["total"] = $count;

        $this->tools->outS(0, 'OK', ['books' => array_values($books), 'pagination' => $pagination]);
    }

    public function bookList()
    {
        $user = $this->_loginNeed(TRUE, 'u.id');
        $base = CDN_URL;

        $hasmembership = (int)$this->input->post('hasmembership');
        $limit = (int)$this->input->post('limit');
        $limitstart = (int)$this->input->post('limitstart');

        if ($user === FALSE) {
            throw new Exception("برای دسترسی به این بخش باید وارد حساب کاربری خود شوید", -1);
        }

        $this->load->model('m_book', 'book');

        $this->db->select('ub.book_id,ub.need_update, UNIX_TIMESTAMP(ub.expiremembership) as expiremembership');
        $this->db->where('ub.user_id', $user->id);
        if (!$hasmembership) {
            $this->db->where("(ISNULL(ub.expiremembership))");
        }
        $UB = $this->db->get('user_books ub');
        $allbooks = $UB->result();
        $total = count($allbooks);
        $this->db->select('ub.book_id,ub.need_update, UNIX_TIMESTAMP(ub.expiremembership) as expiremembership');
        $this->db->where('ub.user_id', $user->id);
        if (!$hasmembership) {
            $this->db->where("(ISNULL(ub.expiremembership))");
        }
        $this->db->join('ci_factors f', '(ub.factor_id=f.id AND f.status=0)', 'inner', FALSE);
        if ($limit || $limitstart) {
            $this->db->limit($limit, $limitstart);
        }
        $results = $this->db->get('user_books ub')->result();
        $bookids = [0];
        $accessUnixTimes = [0];
        $need_update = [];
        $expiremembership = [];
        foreach ($results as $k => $v) {
            $bookids[$v->book_id] = $v->book_id;
            $need_update[$v->book_id] = $v->need_update;
            $expiremembership[$v->book_id] = $v->expiremembership;
            $is_expired[$v->book_id] = ($v->expiremembership && $v->expiremembership < time()) ? true : false;
            $accessUnixTimes[$v->book_id] = $v->accessUnixTime ? intval($v->accessUnixTime) : null;
        }
        $books = $this->db->where('p.id IN(' . implode(',', $bookids) . ')')->where('p.published', 1)->get('posts p')->result();
        $post_meta = $this->db->where('p.post_id IN(' . implode(',', $bookids) . ')')->get('post_meta p')->result();

        $meta = array();

        foreach ($post_meta as $k => $v) {

            if (!isset($meta[$v->post_id]["pagecount"])) {
                $meta[$v->post_id]["pagecount"] = 0;
            }
            if (!isset($meta[$v->post_id]["isvideo"])) {
                $meta[$v->post_id]["isvideo"] = 0;
            }
            if (!isset($meta[$v->post_id]["startpage"])) {
                $meta[$v->post_id]["startpage"] = 0;
            }
            if (!isset($meta[$v->post_id]["author"])) {
                $meta[$v->post_id]["author"] = 0;
            }
            if (!isset($meta[$v->post_id]["finaltest"])) {
                $meta[$v->post_id]["finaltest"] = 0;
            }
            if (!isset($meta[$v->post_id]["timesecond"])) {
                $meta[$v->post_id]["timesecond"] = 0;
            }
            if (!isset($meta[$v->post_id]["acceptpercent"])) {
                $meta[$v->post_id]["acceptpercent"] = 0;
            }
            if (!isset($meta[$v->post_id]["allowpage"])) {
                $meta[$v->post_id]["allowpage"] = 0;
            }
            if (!isset($meta[$v->post_id]["allowbuy"])) {
                $meta[$v->post_id]["allowbuy"] = 0;
            }
            if (!isset($meta[$v->post_id]["allowmembership"])) {
                $meta[$v->post_id]["allowmembership"] = 0;
            }
            if ($v->meta_key == "pages") {
                $v->meta_key = "pagecount";
                $v->meta_value = count(explode(",", $v->meta_value));
            }
            $meta[$v->post_id][$v->meta_key] = $v->meta_value;
        }

        $O = $this->db->get('category')->result();
        $category = array();
        $sath = array();
        foreach ($O as $k => $v) {
            $category[$v->id] = $v->name;
            $sath[$v->id] = $v->parent;
        }

        $bookids = array(0);
        $bookscontroller = array();
        foreach ($books as $k => $v) {
            $bookids[] = $v->id;
            $books[$k]->nashr = array();
            $bookscontroller[$v->id] = $k;
            $books[$k]->cover = $v->thumb ? $base . $v->thumb : null;
            $books[$k]->cover300 = $v->thumb ? $base . thumb($v->thumb, 300) : null;
            $books[$k]->need_update = $need_update[$v->id];
            $books[$k]->expiremembership = $expiremembership[$v->id];
            $books[$k]->is_expired = $is_expired[$v->id];
            $books[$k]->pagecount = $meta[$v->id]["pagecount"];
            $books[$k]->isvideo = $meta[$v->id]["isvideo"];
            $books[$k]->startpage = $meta[$v->id]["startpage"];
            $books[$k]->author = $meta[$v->id]["author"];
            $books[$k]->finaltest = $meta[$v->id]["finaltest"];
            $books[$k]->timesecond = $meta[$v->id]["timesecond"];
            $books[$k]->acceptpercent = $meta[$v->id]["acceptpercent"];
            $books[$k]->allowpage = $meta[$v->id]["allowpage"];
            $books[$k]->allowbuy = $meta[$v->id]["allowbuy"];
            $books[$k]->allowmembership = $meta[$v->id]["allowmembership"];
            $books[$k]->category_name = $category[$v->category];
            $books[$k]->sath = $sath[$v->category];
            $books[$k]->sath_name = $category[$sath[$v->category]];
            $books[$k]->has_sound = $v->has_sound ? true : false;
            $books[$k]->has_video = $v->has_video ? true : false;
            $books[$k]->has_image = $v->has_image ? true : false;
            $books[$k]->has_test = $v->has_test ? true : false;
            $books[$k]->has_tashrihi = $v->has_tashrihi ? true : false;
            $books[$k]->has_description = $v->has_description ? true : false;
            $books[$k]->accessUnixTime = $accessUnixTimes[$v->id];
            unset($v->thumb);
        }
        $this->LoadNashr($bookscontroller, $books, $bookids);

        $pagination = array();
        $pagination["limitstart"] = $limitstart;
        $pagination["limit"] = $limit;
        $pagination["total"] = $total;

        $this->tools->outS(0, 'OK', ['books' => $books, 'pagination' => $pagination]);
    }

    public function getPriceMix()
    {
        $post = $this->input->post();
        $case = $post["case"];
        $id = $post["id"];
        $this->getPrice($case, $id);
    }

    public function Pre($data, $die = 1)
    {
        echo "<pre>";
        print_r($data);
        echo "</pre>";
        if ($die) {
            die();
        }
    }

    public function getPrice($case = 'level', $id = 0)
    {
        $user_id = NULL;
        $user = $this->_loginNeed(TRUE, 'u.id');
        if ($user === FALSE) {
            $user = $this->_loginNeed(FALSE, 'u.id');
        }

        if (isset($user->id)) {
            $user_id = $user->id;
        }
        $discountCode = $this->input->post('code');
        if ($discountCode == NULL) {
            $discountCode = $this->input->get('code');
        }


        $this->load->model('m_book', 'book');

        if ($case == 'book') {
            $ids = explode('-', $id);
            $price = $this->book->getBookPrice($ids);
            $discount_id = (int)$this->book->checkDiscountCode($discountCode, -1, $user_id, $id);
            $discount_id = $discount_id ? $discount_id : (int)$this->book->checkDiscountCode($discountCode, -2, $user_id);
            $discount = 0;
            $discountfee = 0;
            if (is_numeric($discount_id)) {
                $O = $this->db->select('percent,fee')->where('id', $discount_id)->get('discounts')->row();
                $discount = (float)$O->percent;
                $discountfee = (float)$O->fee;
            }
            if ($discount > 100) $discount = 100;
            if ($discount < 0) $discount = 0;

            $c_price = $price;

            $price = $c_price - $c_price * ($discount / 100);
            if ($discountfee > $price)
                $discountfee = $price;
            if ($discountfee)
                $price = $c_price - $discountfee;
            if ($price < 0)
                $price = 0;
            $result = [
                'price' => $c_price,
                'discount' => $discountfee ? $discountfee : $discount,
                'final_price' => $price
            ];
        } else {
            $id = (int)$id;

            if ($this->db->where('id', $id)->where('type', 'book')->count_all_results('category') == 0) {
                throw new Exception("شماره سطح صحیح نیست", 1);
            }


            if ($discountCode != NULL) {
                if (!$user_id)
                    throw new Exception("برای دسترسی به این بخش باید وارد حساب کاربری خود شوید", -1);

                $discount_id = $this->book->checkDiscountCode($discountCode, $id, $user_id);

                if (!is_numeric($discount_id))
                    throw new Exception($discount_id, 1);

                $result = $this->book->getCategoryPrice($id, $user_id, $discount_id);
            } else {
                $result = $this->book->getCategoryPrice($id, $user_id);
            }
        }

        $this->tools->outS(0, NULL, ['data' => $result]);
    }

    public function getCategoryArray($parent = 0, $post_type = 'book')
    {
        $categories = $this->post->getCategoryArray((int)$parent, $post_type);
        //$categories = $this->post->setCategoryPostsCount($categories);
        $this->tools->outS(0, NULL, ['data' => $categories]);
    }
    
    public function getCategoryArrayWithLimit($parent = 0, $post_type = 'book', $limit = 1)
    {
        $categories = $this->post->getCategoryArrayWithLimit((int)$parent, $post_type, $limit = 1);
        //$categories = $this->post->setCategoryPostsCount($categories);
        $this->tools->outS(0, NULL, ['data' => $categories]);
    }

    public function getCategoryBooks($category = NULL)
    {
        if ($category === NULL) {
            $posts = [];
        } else {
            if (strpos($category, '-') != FALSE)
                $category = explode('-', $category);
            else
                $category = (int)$category;

            if (empty($category) or !$category)
                $posts = [];
            else {
                $posts = $this->post->getPosts([
                    'type' => 'book',
                    'category' => $category,
                    'order' => 'p.date_modified desc'
                ]);
            }
        }

        $user = $this->_loginNeed(TRUE, 'u.id');
        $uid = (int)@$user->id;
        $results = $this->db->select('need_update,book_id')->where('user_id', $uid)->get('user_books')->result();
        $books = array();
        foreach ($results as $k => $v) {
            $books[$v->book_id] = $v->need_update;
        }
        foreach ($posts as $k => $v) {
            if (isset($books[$v->id]) && $books[$v->id]) {
                $posts[$k]->need_update = $books[$v->id];
            }
        }
        $this->tools->outS(0, NULL, ['data' => $posts]);
    }

    public function getQuestion($id = NULL)
    {
        $type = $this->input->post('type', 'json');;

        $this->output->set_header('Last-Modified: ' . gmdate("D, d M Y H:i:s") . ' GMT');
        $this->output->set_header('Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0');
        $this->output->set_header('Pragma: no-cache');
        $this->output->set_header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");


        $id = (int)$id;
        $filename = md5($id);

        if ($id && $this->db->where('id', $id)->where('qid', 0)->where('published', 1)->count_all_results('questions') == 0)
            throw new Exception("مطلبی یافت نشد", 1);
        elseif ($this->db->where('qid', 0)->where('published', 1)->count_all_results('questions') == 0)
            throw new Exception("مطلبی یافت نشد", 1);

        //get the book info
        if ($id)
            $questions = $this->db->where("(id = $id OR qid = $id)")->where('published', 1)->get('questions')->result();
        else
            $questions = $this->db->where('published', 1)->get('questions')->result();

        $data['questions'] = $questions;

        if ($type == 'json')
            return $this->tools->outS(0, NULL, ['data' => $data]);

        $this->load->library('zip');
        $appendedfile = array();
        foreach ($data['questions'] as $k => $v) {
            if (!in_array($v->image, $appendedfile) && $v->image) {
                $appendedfile[] = $v->image;
                $baseName = 'images/' . basename($v->image);
                $this->zip->read_file($v->image, $baseName);
            }
            if (!in_array($v->sound, $appendedfile) && $v->sound) {
                $appendedfile[] = $v->sound;
                $baseName = 'sounds/' . basename($v->sound);
                $this->zip->read_file($v->sound, $baseName);
            }
        }

        $this->zip->add_data('info.json', $this->MakeJSON($data['questions']));

        $temp = 'temp/questions/' . $filename . '.zip';
        $dir = 'temp/questions';
        if (!is_dir($dir))
            mkdir($dir);
        $this->zip->archive($temp);

        $filesize = filesize($temp);
        header('Content-Type: application/x-zip');
        header('Content-Disposition: attachment; filename="' . $filename . '.zip"');
        header('Expires: 0');
        header('Content-Transfer-Encoding: binary');
        header('Content-Length: ' . $filesize);
        header("Content-Range: 0-" . ($filesize - 1) . "/" . $filesize);
        header('Pragma: no-cache');

        readfile($temp);

        @unlink($temp);
        exit;

        //$this->zip->download($filename);
    }

    public function getCatQuest()
    {
        $type = $this->input->post('type', 'json');

        $this->output->set_header('Last-Modified: ' . gmdate("D, d M Y H:i:s") . ' GMT');
        $this->output->set_header('Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0');
        $this->output->set_header('Pragma: no-cache');
        $this->output->set_header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");


        $id = (int)$this->input->post('id');
        $filename = md5($id);

        if ($this->db->count_all_results('catquest') == 0)
            throw new Exception("مطلبی یافت نشد", 1);

        //get the book info
        $catquest = $this->db->get('catquest')->result();

        return $this->tools->outS(0, $catquest);

    }

    public function getQuestions($id = NULL)
    {
        $type = $this->input->post('type', 'json');
        $id = $this->input->post('id', $id);

        $this->output->set_header('Last-Modified: ' . gmdate("D, d M Y H:i:s") . ' GMT');
        $this->output->set_header('Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0');
        $this->output->set_header('Pragma: no-cache');
        $this->output->set_header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");


        $id = (int)$id;
        $filename = md5($id);

        if ($id && $this->db->where('catid', $id)->where('published', 1)->count_all_results('questions') == 0) {
            throw new Exception("مطلبی یافت نشد", 1);
        }
        //get the book info
        if ($id) {
            $questions = $this->db->where("(catid = $id OR qid = $id)")->where('published', 1)->get('questions')->result();
            $qid = array();
            foreach ($questions as $k => $v) {
                $qid[$v->id] = $v->id;
            }
            if (count($qid)) {
                $questions = $this->db->where("(id IN(" . implode(",", $qid) . ") OR qid IN(" . implode(",", $qid) . "))")->where('published', 1)->order_by('id', 'ASC')->get('questions')->result();
            } else {
                throw new Exception("مطلبی یافت نشد", 1);
            }
        } else {
            throw new Exception("مطلبی یافت نشد", 1);
        }

        $data['questions'] = $questions;

        if ($type == 'json')
            return $this->tools->outS(0, NULL, ['data' => $data]);

        $this->load->library('zip');
        $appendedfile = array();
        foreach ($data['questions'] as $k => $v) {
            if (!in_array($v->image, $appendedfile) && $v->image) {
                $appendedfile[] = $v->image;
                $baseName = 'images/' . basename($v->image);
                $this->zip->read_file($v->image, $baseName);
            }
            if (!in_array($v->sound, $appendedfile) && $v->sound) {
                $appendedfile[] = $v->sound;
                $baseName = 'sounds/' . basename($v->sound);
                $this->zip->read_file($v->sound, $baseName);
            }
        }

        $this->zip->add_data('info.json', $this->MakeJSON($data['questions']));

        $temp = 'temp/questions/' . $filename . '.zip';
        $dir = 'temp/questions';
        if (!is_dir($dir))
            mkdir($dir);
        $this->zip->archive($temp);

        $filesize = filesize($temp);
        header('Content-Type: application/x-zip');
        header('Content-Disposition: attachment; filename="' . $filename . '.zip"');
        header('Expires: 0');
        header('Content-Transfer-Encoding: binary');
        header('Content-Length: ' . $filesize);
        header("Content-Range: 0-" . ($filesize - 1) . "/" . $filesize);
        header('Pragma: no-cache');

        readfile($temp);

        @unlink($temp);
        exit;

        //$this->zip->download($filename);
    }

    public function ema_getBook($id = NULL, $type = 'zip')
    {
        $user = $this->_loginNeed(TRUE, 'u.id');
        $userid = $user->id;

        // Prevent browser caching
        $this->output->set_header('Last-Modified: ' . gmdate("D, d M Y H:i:s") . ' GMT');
        $this->output->set_header('Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0');
        $this->output->set_header('Pragma: no-cache');
        $this->output->set_header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");

        // Get book ID and type from POST request
        $id = (int) ($id ? $id : $this->input->post('id'));
        $type = $this->input->post('type') ? $this->input->post('type') : $type;
        $filename = md5($id);

        // Generate a unique Redis cache key
        $cache_key = "book_data_{$id}_{$type}";

        // Check if the data is cached in Redis
        $cached_data = $this->cache->redis->get($cache_key);
        if ($cached_data !== FALSE) {
            return $this->tools->outS(0, "Book Data (Cached)", ['data' => json_decode($cached_data, true)]);
        }

        // Validate book existence and published status
        if ($this->db->where('id', $id)->where('type', 'book')->count_all_results('posts') == 0) {
            throw new Exception("Invalid book id", 2);
        }
        if ($this->db->where('id', $id)->where('type', 'book')->where('published', 1)->count_all_results('posts') == 0) {
            throw new Exception("This book is not active", 1);
        }

        // Load book model
        $this->load->model('m_book', 'book');

        // Check if user has full access to the book
        $userBooks = $this->book->getUserBooks($userid);
        $userBookIds = array_column($userBooks, 'id');
        $fullAccess = in_array($id, $userBookIds);

        // Get book information
        $book = $this->post->getPosts([
            'type' => 'book',
            'order' => 'p.date_modified desc',
            'where' => ['p.id' => $id],
            'limit' => 1
        ])[0];

        // Get associated class online data
        $classonlines = $this->db->select('*')
            ->where_in('id', $this->db->select('cid')->where_in('data_type', ['book', 'hamniaz'])->where('data_id', $id)->get_compiled_select('classonline_data'))
            ->get('classonline')
            ->result();
        $book->classonline = $classonlines;

        // Get associated classrooms
        $classrooms = $this->db->select('*')
            ->where_in('id', $this->db->select('cid')->where_in('data_type', ['book', 'hamniaz'])->where('data_id', $id)->get_compiled_select('classroom_data'))
            ->get('classroom')
            ->result();
        $book->classroom = $classrooms;

        // Retrieve additional book data
        $data['book'] = $book;
        $data['indexes'] = $this->book->getBookIndexesById($id);
        $data['parts'] = $this->book->getBookPartsById($id);
        $data['tests'] = $this->book->getBookTests($id);

        // Restrict book content for unauthorized users
        if (!$fullAccess) {
            $limitedPages = array_slice($data['book']->pages['array'], 0, 3, true);
            $data['book']->pages['array'] = $limitedPages;
            
            $offset = [];
            $currentPage = 0;
            foreach ($limitedPages as $key => $value) {
                $currentPage += count($value);
                $offset[] = $currentPage - 1;
            }
            $data['book']->pages['offset'] = implode(',', $offset);
            
            $totalPartsInLimitedPages = 0;
            foreach ($limitedPages as $value) {
                $totalPartsInLimitedPages += count($value);
            }
            
            $data['parts'] = array_slice($data['parts'], 0, $totalPartsInLimitedPages);
        }

        foreach ($data['parts'] as $pk => $part) {
            $data['parts'][$pk]->description = base64_encode($part->description);
        }

        $data['tests'] = base64_encode($this->MakeJSON($data['tests']));

        // Cache the book data in Redis for 1 hour
        $this->cache->redis->save($cache_key, json_encode($data), 3600);

        // Return JSON response if requested
        if ($type == 'json') {
            return $this->tools->outS(0, NULL, ['data' => $data]);
        }

        // Prepare ZIP download
        $this->load->library('zip');
        if (!empty($data['parts'])) {
            foreach ($data['parts'] as $k => $v) {
                $baseName = 'images/' . basename($v->image);
                $this->zip->read_file($v->image, $baseName);
            }
        }

        $this->zip->add_data('info.json', $this->MakeJSON($data['book']));
        $this->zip->add_data('content.json', $this->MakeJSON($data['parts']));
        $this->zip->add_data('tests.json', $data['tests']);
        $this->zip->add_data('index.json', $this->MakeJSON($data['indexes']));

        $temp = 'temp/book/' . $filename . '.zip';
        $dir = 'temp/book';
        if (!is_dir($dir)) mkdir($dir);
        $this->zip->archive($temp);

        $ubData = ['need_update' => 0];
        $this->db->where('book_id', $id)->where('user_id', $userid)->update('user_books', $ubData);

        $filesize = filesize($temp);
        header('Content-Type: application/x-zip');
        header('Content-Disposition: attachment; filename="' . $filename . '.zip"');
        header('Expires: 0');
        header('Content-Transfer-Encoding: binary');
        header('Content-Length: ' . $filesize);
        header("Content-Range: 0-" . ($filesize - 1) . "/" . $filesize);
        header('Pragma: no-cache');

        readfile($temp);
        @unlink($temp);
        exit;
    }

    public function getBook($id = NULL, $type = 'zip')
    {
        $user = $this->_loginNeed(TRUE, 'u.id');

        $userid = $user->id;
        $this->output->set_header('Last-Modified: ' . gmdate("D, d M Y H:i:s") . ' GMT');
        $this->output->set_header('Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0');
        $this->output->set_header('Pragma: no-cache');
        $this->output->set_header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");

        $id = (int)$id;
        $id = $id ? $id : (int)$this->input->post('id');
        $type = $this->input->post('type') ? $this->input->post('type') : $type;
        $filename = md5($id);

        if ($this->db->where('id', $id)->where('type', 'book')->count_all_results('posts') == 0)
            throw new Exception("Invalid book id", 2);


        if ($this->db->where('id', $id)->where('type', 'book')->where('published', 1)->count_all_results('posts') == 0)
            throw new Exception("This book is not active", 1);

        $this->load->model('m_book', 'book');

        //get the book info
        $book = $this->post->getPosts([
            'type' => 'book',
            'order' => 'p.date_modified desc',
            'where' => ['p.id' => $id],
            'limit' => 1
        ])[0];

        $classonlines = $this->db->select('cid')
            ->where_in('data_type',['book','hamniaz'])
            ->where('data_id',$id)
            ->get('classonline_data')
            ->result();

        $classonline_ids = [0];
        foreach ($classonlines as $classonline){
            $classonline_ids[$classonline->cid] = $classonline->cid;
        }

        $classonlines = $this->db->select('*')
            ->where_in('id',$classonline_ids)
            ->get('classonline')
            ->result();


        $book->classonline = $classonlines;

        $classrooms = $this->db->select('cid')
            ->where_in('data_type',['book','hamniaz'])
            ->where('data_id',$id)
            ->get('classroom_data')
            ->result();

        $classroom_ids = [0];
        foreach ($classrooms as $classroom){
            $classroom_ids[$classroom->cid] = $classroom->cid;
        }

        $classrooms = $this->db->select('*')
            ->where_in('id',$classroom_ids)
            ->get('classroom')
            ->result();


        $book->classroom = $classrooms;

        $data['book'] = $book;

        //get the book indexes
        $data['indexes'] = $this->book->getBookIndexesById($id);

        //get the book parts
        $data['parts'] = $this->book->getBookPartsById($id);

        $data['tests'] = $this->book->getBookTests($id);

        foreach ($data['parts'] as $pk => $part) {
            $data['parts'][$pk]->description = base64_encode($part->description);
        }

        $data['tests'] = base64_encode($this->MakeJSON($data['tests']));


        if ($type == 'json')
            return $this->tools->outS(0, NULL, ['data' => $data]);

        $this->load->library('zip');

        /*
		 *
		 *
		if(isset($book->sample_questions) && ! empty($book->sample_questions))
		{
			foreach ($book->sample_questions as $ak=>$attachment)
			{
				$baseName = 'sample_questions/' . basename($attachment['path']);
				$this->zip->read_file($attachment['path'],$baseName);
				$book->sample_questions[$ak]['path'] = $baseName;
			}
		}

		if(isset($book->attachments) && ! empty($book->attachments))
		{
			foreach ($book->attachments as $ak=>$attachment)
			{
				$baseName = 'attachments/' . basename($attachment['path']);
				$this->zip->read_file($attachment['path'],$baseName);
				$book->attachments[$ak]['path'] = $baseName;
			}
		}
		 *
		 */

        if (!empty($data['parts'])) {
            foreach ($data['parts'] as $k => $v) {
                // Only proceed if $v->image is set and not empty
                if (!empty($v->image)) {
                    $fullPath = '/lexoya/var/www/html/' . $v->image;
                    // Check if the file exists and is a file
                    if (is_file($fullPath)) {
                        // Set the file name inside the ZIP (keeping the images folder structure)
                        $baseName = 'images/' . basename($fullPath);
                        $this->zip->read_file($fullPath, $baseName);
                    }
                }
            }
        }

        $this->zip->add_data('info.json', $this->MakeJSON($data['book']));
        $this->zip->add_data('content.json', $this->MakeJSON($data['parts']));
        $this->zip->add_data('tests.json', $data['tests']);
        $this->zip->add_data('index.json', $this->MakeJSON($data['indexes']));

        $temp = 'temp/book/' . $filename . '.zip';
        $dir = 'temp/book';
        if (!is_dir($dir))
            mkdir($dir);
        $this->zip->archive($temp);

        $ubData = array(
            'need_update' => 0,
        );
        $this->db->where('book_id', $id)->where('user_id', $userid)->update('user_books', $ubData);

        $filesize = filesize($temp);
        header('Content-Type: application/x-zip');
        header('Content-Disposition: attachment; filename="' . $filename . '.zip"');
        header('Expires: 0');
        header('Content-Transfer-Encoding: binary');
        header('Content-Length: ' . $filesize);
        header("Content-Range: 0-" . ($filesize - 1) . "/" . $filesize);
        header('Pragma: no-cache');

        readfile($temp);

        @unlink($temp);
        exit;

        //$this->zip->download($filename);
    }

    public function getBookInfo($id = NULL)
    {
        $id = (int)$id;

        if ($this->db->where('id', $id)->where('type', 'book')->count_all_results('posts') == 0)
            throw new Exception("Invalid book id", 2);


        if ($this->db->where('id', $id)->where('type', 'book')->where('published', 1)->count_all_results('posts') == 0)
            throw new Exception("This book is not active", 1);

        $book = $this->post->getPosts([
            'type' => 'book',
            'order' => 'p.date_modified desc',
            'where' => ['p.id' => $id],
            'limit' => 1
        ])[0];

        $this->tools->outS(0, NULL, ['data' => ['book' => $book]]);
    }

    public function buyBook()
    {
        $user = $this->_loginNeed();

        if ($user === FALSE)
            throw new Exception("برای دسترسی به این بخش باید وارد حساب کاربری خود شوید", -1);

        $level_id = (int)$this->input->post('level_id');
        $book_id = (int)$this->input->post('book_id');

        if (!$book_id && !$level_id)
            throw new Exception("شماره کتاب یا شماره سطح الزامی است", 1);

        if ($book_id) {
            $author = $this->db->where('id', $book_id)->where('type', 'book')->select('author')->get('posts', 1)->row();
        } elseif ($level_id) {
            $O = $this->db->where('parent', $level_id)->where('type', 'book')->get('category')->result();
            $category = array(0);
            foreach ($O as $v)
                $category[] = $v->id;
            $author = $this->db->where('type', 'book')->where('category IN(' . implode(',', $category) . ')')->select('author')->get('posts', 1)->row();
        }

        $owner = (int)@$author->author;
        $discount_id = NULL;

        $this->load->model('m_book', 'book');

        if ($book_id) {
            if ($this->db->where('id', $book_id)->where('type', 'book')->count_all_results('posts') == 0)
                throw new Exception("شماره کتاب صحیح نمی باشد", 2);

            if ($this->db->where('id', $book_id)->where('type', 'book')->where('published', 1)->count_all_results('posts') == 0)
                throw new Exception("این کتاب درحال حاضر در دسترس نیست", 3);

            if ($this->book->isBought($user->id, $book_id))
                throw new Exception("کتاب قبلا خریداری شده است", 4);

            if ($this->db->where('post_id', $book_id)->where('meta_key', 'allowbuy')->where('meta_value', '0')->count_all_results('post_meta'))
                throw new Exception("این کتاب درحال حاضر قابل فروش نیست", 3);

            $discountCode = $this->input->post('code');
            $discount_id = (int)$this->book->checkDiscountCode($discountCode, -1, $user->id, $book_id);
            $discount_id = $discount_id ? $discount_id : (int)$this->book->checkDiscountCode($discountCode, -2, $user->id);
            $cf = $this->book->createFactor($user->id, $book_id, NULL, $discount_id, $owner);
        } else {
            $allowlevel = $this->db->where('id', $level_id)->where('type', 'book')->count_all_results('category');
            if ($allowlevel == 0)
                throw new Exception("شماره سطح صحیح نمی باشد", 2);

            $discountCode = $this->input->post('code');
            $discount_id = $this->book->checkDiscountCode($discountCode, $level_id, $user->id);
            $discount_id = is_numeric($discount_id) ? $discount_id : null;
            $cf = $this->book->createFactor($user->id, NULL, $level_id, $discount_id, $owner);
        }

        if ($cf['done'] == FALSE)
            throw new Exception($cf['msg'], 5);

        $factor = $cf['factor'];
        $data = ['factor' => $factor];


        if ($factor->price == 0) {
            $this->book->updatetFactor($factor->id, [
                'state' => $discount_id != NULL ? "خرید کامل با کد تخفیف (<span class=\"text-warning\">{$discountCode}</span>)" : 'رایگان',
                'status' => 0,
                'pdate' => time()
            ]);

            if ($discount_id != NULL)
                $this->book->setDiscountUsed($discount_id, $factor->id);

            $data['free'] = TRUE;
            $data['link'] = NULL;

        } else
            $data['link'] = site_url('payment/paybook/' . $factor->id);

        $this->tools->outS(0, "فاکتور ایجاد شد", ['data' => $data]);
    }
    
    public function getUserBooks($user_id = NULL)
    {
        $user_id = (int)$user_id;
        if ($this->db->where('id', $user_id)->count_all_results('users') == 0)
            throw new Exception("Invalid user id", 1);

        $this->load->model('m_book', 'book');

        $books = $this->book->getUserBooks($user_id);

        $this->tools->outS(0, NULL, ['data' => $books]);
    }

    public function getPartData()
    {
        $user = $this->_loginNeed();
        /*
		if($user === FALSE)
			throw new Exception("برای دسترسی به این بخش باید وارد حساب کاربری خود شوید" , -1);
		*/
        $part_id = (int)$this->input->post('id');
        $case = $this->input->post('case');

        if (!in_array($case, ['description', 'sound']))
            throw new Exception("درخواست نا معتبر می باشد", 1);

        if ($this->db->where('id', $part_id)->count_all_results('book_meta') == 0)
            throw new Exception("شماره پاراگراف نامعتبر است", 2);

        $this->db->select('id,book_id');
        $this->db->select($case);
        $this->db->where('id', $part_id);
        $part = $this->db->get('book_meta', 1)->row();

        $this->load->model('m_book', 'book');

        /*
		if(!$this->book->isBought($user->id,$part->book_id))
			throw new Exception("کتاب خریداری نشده است", 3);
		*/
        if (!isset($part->{$case}) or empty($part->{$case}))
            throw new Exception("فیلد مورد نظر خالی است", 4);

        if ($case == 'sound') {
            if (!file_exists($part->sound))
                throw new Exception("فایل مورد نظر در سرور وجود ندارد", 5);

            //$this->load->helper('download');
            //force_download($part->sound,NULL);
            $this->tools->outS(0, NULL, ['data' => base_url() . $part->sound]);
        } elseif ($case == 'image') {
            if (!file_exists($part->image))
                throw new Exception("فایل مورد نظر در سرور وجود ندارد", 5);

            //$this->load->helper('download');
            //force_download($part->image,NULL);
            $this->tools->outS(0, NULL, ['data' => base_url() . $part->image]);
        } else {
            $this->tools->outS(0, NULL, ['data' => $part->{$case}]);
        }
    }

    public function getPartSound($part_id = NULL)
    {
        $user = $this->_loginNeed(FALSE);

        if ($user === FALSE)
            throw new Exception("برای دسترسی به این بخش باید وارد حساب کاربری خود شوید", -1);

        $part_id = (int)$part_id;

        if ($this->db->where('id', $part_id)->count_all_results('book_meta') == 0)
            throw new Exception("شماره پاراگراف نامعتبر است", 2);

        $this->db->select('id,book_id,sound');
        $this->db->where('id', $part_id);
        $part = $this->db->get('book_meta', 1)->row();

        $this->load->model('m_book', 'book');

        if (!$this->book->isBought($user->id, $part->book_id))
            throw new Exception("کتاب خریداری نشده است", 3);

        if (!isset($part->sound) or empty($part->sound))
            throw new Exception("فیلد مورد نظر خالی است", 4);

        if (!file_exists($part->sound))
            throw new Exception("فایل مورد نظر در سرور وجود ندارد", 5);

        //$this->load->helper('download');
        //force_download($part->sound,NULL);

        $this->tools->outS(0, NULL, ['data' => base_url() . $part->sound]);
    }

    public function getPartImage($part_id = NULL)
    {
        $user = $this->_loginNeed(FALSE);

        if ($user === FALSE)
            throw new Exception("برای دسترسی به این بخش باید وارد حساب کاربری خود شوید", -1);

        $part_id = (int)$part_id;

        if ($this->db->where('id', $part_id)->count_all_results('book_meta') == 0)
            throw new Exception("شماره پاراگراف نامعتبر است", 2);

        $this->db->select('id,book_id,image');
        $this->db->where('id', $part_id);
        $part = $this->db->get('book_meta', 1)->row();

        $this->load->model('m_book', 'book');

        if (!$this->book->isBought($user->id, $part->book_id))
            throw new Exception("کتاب خریداری نشده است", 3);

        if (!isset($part->image) or empty($part->image))
            throw new Exception("فیلد مورد نظر خالی است", 4);

        if (!file_exists($part->image))
            throw new Exception("فایل مورد نظر در سرور وجود ندارد", 5);

        //$this->load->helper('download');
        //force_download($part->image,NULL);

        $this->tools->outS(0, NULL, ['data' => base_url() . $part->image]);
    }

    public function getExtPdf($book_id = 0, $request = '')
    {
        $password = $this->input->get('mac');
        $book_id = (int)$book_id;
        $user = $this->_loginNeed(FALSE);

        if ($password == '' or $request == '' && $book_id == 0)
            throw new Exception("اطلاعات ارسالی صحیح نمی باشد", 1);

        if ($user === FALSE)
            throw new Exception("برای دسترسی به این بخش باید وارد حساب کاربری خود شوید", -1);

        $this->load->model('m_book', 'book');

        if (!$this->book->isBought($user->id, $book_id))
            throw new Exception("این کتاب در لیست کتابهای خریداری شده شما نیست", 1);

        $meta = $this->db->select('meta_value')->where(['post_id' => $book_id, 'meta_key' => 'dl_book'])->get('post_meta', 1)->row();

        if (empty($meta))
            throw new Exception("کتاب مورد نظر پیدا نشد", 1);

        $meta = $meta->meta_value;

        $meta = $this->tools->jsonDecode($meta);

        if (!is_array($meta) && !is_object($meta))
            throw new Exception("اطلاعات این بخش از کتاب دچار مشکل شده و قابل خواندن نیست", 1);

        $file = NULL;
        foreach ($meta as $attachment) {
            if (md5($attachment['file']) . sha1($attachment['file']) == $request) {
                $file = $attachment['file'];
                break;
            }
        }

        if ($file === NULL)
            throw new Exception("فایل درخواستی در لیست فایلهای این کتاب نیست", 1);

        if (!file_exists($file))
            throw new Exception("فایل مورد نظر از سرور حذف شده است", 1);


        require_once('fpdi/FPDI_Protection.php');

        $pdf = new FPDI_Protection();

        $pagecount = $pdf->setSourceFile($file);

        for ($loop = 1; $loop <= $pagecount; $loop++) {
            $tpl = $pdf->importPage($loop);
            $pdf->addPage();
            $pdf->useTemplate($tpl);
        }

        $pdf->SetProtection(array(), $password);
        $pdf->Output();
    }

    public function getBookTest($id = NULL)
    {
        try {
            if (!$id)
                throw new Exception('اطلاعاتی ارسال نشده', 1);
            $this->db->select('t.id,t.testnumber,t.category,t.question,t.true_answer,t.answer_1,t.answer_2,t.answer_3,t.answer_4,t.page,t.term');
            $test = $this->db->where('book_id', (int)$id)->order_by('id', 'DESC')->get('tests t')->result();//->order_by('category','asc')
            $this->tools->outS(0, 'OK', array('test' => $test));
        } catch (Exception $e) {
            $this->tools->outE($e);
        }
    }//Alireza Balvardi

    public function getBookTashrihi($id = NULL)
    {
        try {
            if (!$id)
                throw new Exception('اطلاعاتی ارسال نشده', 1);
            $this->db->select('t.id,t.testnumber,t.category,t.question,t.answer,t.page,t.term,t.barom');
            $tashrihi = $this->db->where('book_id', (int)$id)->order_by('id', 'DESC')->get('tashrihi t')->result();//->order_by('category','asc')
            $this->tools->outS(0, 'OK', array('tashrihi' => $tashrihi));
        } catch (Exception $e) {
            $this->tools->outE($e);
        }
    }//Alireza Balvardi

    /*===================================
		NOTES HIGHLIGHTS
	===================================*/
    public function addNote()
    {
        $user = $this->_loginNeed();

        if ($user === FALSE)
            throw new Exception("برای دسترسی به این بخش باید وارد حساب کاربری خود شوید", -1);

        $this->load->library('form_validation');
        $this->form_validation->set_rules('text_id', 'شماره پاراگراف', 'trim|required|numeric');
        $this->form_validation->set_rules('not_text', 'متن', 'trim|xss_clean|required');
        $this->form_validation->set_rules('not_text_user', 'یادداشت', 'trim|xss_clean|required');
        $this->form_validation->set_rules('title', 'عنوان', 'trim|required|max_length[255]');
        $this->form_validation->set_rules('notstart', 'شروع', 'trim|required|numeric');
        $this->form_validation->set_rules('end', 'پایان', 'trim|required|numeric');
        $this->form_validation->set_rules('sharh', 'شرح', 'trim|required|numeric');

        if ($this->form_validation->run() == FALSE)
            throw new Exception(implode('|', $this->form_validation->error_array()), 1);

        $data = $this->input->post();

        $data = array(
            'part_id' => $data['text_id'],
            'user_id' => $user->id,
            'title' => $data['title'],
            'text' => $data['not_text'],
            'user_text' => $data['not_text_user'],
            'start' => $data['notstart'],
            'end' => $data['end'],
            'sharh' => $data['sharh']
        );

        if ($this->db->where('id', $data['part_id'])->count_all_results('book_meta') == 0)
            throw new Exception("شماره پاراگراف اشتباه است", 2);

        if (!$this->db->insert('notes', $data))
            throw new Exception("خطا در ثبت اطلاعات", 3);

        $re = [
            'data' => [
                'insert_id' => $this->db->insert_id()
            ]
        ];

        $this->tools->outS(0, "یادداشت ثبت شد", $re);
    }

    public function addHighlight()
    {
        $user = $this->_loginNeed();

        if ($user === FALSE)
            throw new Exception("برای دسترسی به این بخش باید وارد حساب کاربری خود شوید", -1);

        $this->load->library('form_validation');
        $this->form_validation->set_rules('highlight_id', 'آی دی ویرایش هایلایت', 'trim|numeric');
        $this->form_validation->set_rules('text_id', 'شماره پاراگراف', 'trim|required|numeric');
        $this->form_validation->set_rules('highlight_color', 'رنگ', 'trim|required|numeric');
        $this->form_validation->set_rules('highlight_title', 'عنوان', 'trim|xss_clean');
        $this->form_validation->set_rules('highlight_text', 'متن', 'trim|required|xss_clean');
        $this->form_validation->set_rules('highlight_description', 'توضیحات', 'trim|xss_clean');
        $this->form_validation->set_rules('highlight_start', 'شروع', 'trim|required|numeric');
        $this->form_validation->set_rules('highlight_end', 'پایان', 'trim|required|numeric');
        $this->form_validation->set_rules('sharh', 'شرح', 'trim|required|numeric');

        if ($this->form_validation->run() == FALSE)
            throw new Exception(implode('|', $this->form_validation->error_array()), 1);

        $data = $this->input->post();

        $data = array(
            'id' => $data['highlight_id'],
            'part_id' => $data['text_id'],
            'user_id' => $user->id,
            'title' => $data['highlight_title'],
            'text' => $data['highlight_text'],
            'description' => $data['highlight_description'],
            'color' => $data['highlight_color'],
            'start' => $data['highlight_start'],
            'end' => $data['highlight_end'],
            'sharh' => $data['sharh']
        );

        if ($this->db->where('id', $data['part_id'])->count_all_results('book_meta') == 0)
            throw new Exception("شماره پاراگراف اشتباه است", 2);

        if (!$data['id'] && !$this->db->insert('highlights', $data))
            throw new Exception("خطا در ثبت اطلاعات", 3);

        if ($data['id'] && !$this->db->where('id', $data['id'])->update('highlights', $data))
            throw new Exception("خطا در ثبت اطلاعات", 3);
        $re = [
            'data' => [
                'insert_id' => $this->db->insert_id()
            ]
        ];

        $this->tools->outS(0, "یادداشت ثبت شد", $re);
    }

    public function addHighTag()
    {
        $user = $this->_loginNeed();

        if ($user === FALSE)
            throw new Exception("برای دسترسی به این بخش باید وارد حساب کاربری خود شوید", -1);

        $this->load->library('form_validation');
        $this->form_validation->set_rules('hightag_id', 'آی دی ویرایش تگ', 'trim|numeric');
        $this->form_validation->set_rules('highlight_id', 'شماره هایلایت', 'trim|numeric');
        $this->form_validation->set_rules('hightag_title', 'عنوان', 'trim|xss_clean');
        $this->form_validation->set_rules('public', 'عمومی باشد', 'trim|numeric');
        $this->form_validation->set_rules('tags', 'tags', 'trim');

        if ($this->form_validation->run() == FALSE)
            throw new Exception(implode('|', $this->form_validation->error_array()), 1);

        $data = $this->input->post();

        if (isset($data['tags']) && strlen($data['tags'])) {
            $tags = $this->tools->jsonDecode($data['tags']);
            $count = 0;
            if (is_array($tags) && count($tags)) {
                foreach ($tags as $k => $v) {
                    $data = array(
                        'id' => $v['hightag_id'],
                        'hid' => $v['highlight_id'],
                        'user_id' => $user->id,
                        'title' => $v['hightag_title'],
                        'public' => $v['public']
                    );
                    if ($this->db->where('id', $data['hid'])->count_all_results('highlights') == 0)
                        throw new Exception("شماره هایلایت اشتباه است", 2);
                    if (!$data['id'] && !$this->db->insert('hightag', $data))
                        throw new Exception("خطا در ثبت اطلاعات", 3);

                    if ($data['id'] && !$this->db->where('id', $data['id'])->update('hightag', $data))
                        throw new Exception("خطا در بروزرسانی اطلاعات", 4);
                    $count++;
                }
            }
            $this->tools->outS(0, sprintf("تعداد %s تگ ثبت گردید", $count));
        } else {
            $data = array(
                'id' => $data['hightag_id'],
                'hid' => $data['highlight_id'],
                'user_id' => $user->id,
                'title' => $data['hightag_title'],
                'public' => $data['public']
            );

            if ($this->db->where('id', $data['hid'])->count_all_results('highlights') == 0)
                throw new Exception("شماره هایلایت اشتباه است", 2);

            if (!$data['id'] && !$this->db->insert('hightag', $data))
                throw new Exception("خطا در ثبت اطلاعات", 3);

            if ($data['id'] && !$this->db->where('id', $data['id'])->update('hightag', $data))
                throw new Exception("خطا در بروزرسانی اطلاعات", 4);
            $re = [
                'data' => [
                    'insert_id' => $this->db->insert_id()
                ]
            ];

            $this->tools->outS(0, "تگ هایلایت ثبت شد", $re);
        }
    }

    public function addfavSound()
    {
        $user = $this->_loginNeed();

        if ($user === FALSE)
            throw new Exception("برای دسترسی به این بخش باید وارد حساب کاربری خود شوید", -1);

        $this->load->library('form_validation');
        $this->form_validation->set_rules('text_id', 'شماره پاراگراف', 'trim|required|numeric');

        if ($this->form_validation->run() == FALSE)
            throw new Exception(implode('|', $this->form_validation->error_array()), 1);

        $part_id = (int)$this->input->post('text_id');

        $data = array(
            'part_id' => $part_id,
            'user_id' => $user->id,
        );

        if ($this->db->where('id', $part_id)->count_all_results('book_meta') == 0)
            throw new Exception("شماره پاراگراف اشتباه است", 2);

        if ($this->db->where('id', $part_id)->where('sound IS NOT NULL')->count_all_results('book_meta') == 0)
            throw new Exception("پاراگراف مورد نظر صوت ندارد", 3);

        if ($this->db->where($data)->count_all_results('fav_sounds') == 1)
            throw new Exception("قبلا به لیست صوت های محبوب اضافه شده است", 4);

        if (!$this->db->insert('fav_sounds', $data))
            throw new Exception("خطا در ثبت اطلاعات", 5);

        $re = [
            'data' => [
                'insert_id' => $this->db->insert_id()
            ]
        ];

        $this->tools->outS(0, "به لیست صوت های محبوب اضافه شد", $re);
    }

    public function addfavImage()
    {
        $user = $this->_loginNeed();

        if ($user === FALSE)
            throw new Exception("برای دسترسی به این بخش باید وارد حساب کاربری خود شوید", -1);

        $this->load->library('form_validation');
        $this->form_validation->set_rules('text_id', 'شماره پاراگراف', 'trim|required|numeric');

        if ($this->form_validation->run() == FALSE)
            throw new Exception(implode('|', $this->form_validation->error_array()), 1);

        $part_id = (int)$this->input->post('text_id');

        $data = array(
            'part_id' => $part_id,
            'user_id' => $user->id,
        );

        if ($this->db->where('id', $part_id)->count_all_results('book_meta') == 0)
            throw new Exception("شماره پاراگراف اشتباه است", 2);

        if ($this->db->where('id', $part_id)->where('image IS NOT NULL')->count_all_results('book_meta') == 0)
            throw new Exception("پاراگراف مورد نظر صوت ندارد", 3);

        if ($this->db->where($data)->count_all_results('fav_images') == 1)
            throw new Exception("قبلا به لیست صوت های محبوب اضافه شده است", 4);

        if (!$this->db->insert('fav_images', $data))
            throw new Exception("خطا در ثبت اطلاعات", 5);

        $re = [
            'data' => [
                'insert_id' => $this->db->insert_id()
            ]
        ];

        $this->tools->outS(0, "به لیست صوت های محبوب اضافه شد", $re);
    }

    public function deleteItem()
    {
        $user = $this->_loginNeed();

        if ($user === FALSE)
            throw new Exception("برای دسترسی به این بخش باید وارد حساب کاربری خود شوید", -1);

        $this->load->library('form_validation');

        $this->form_validation->set_rules('id', 'شماره ID', 'trim|required|numeric');
        $this->form_validation->set_rules('item', 'آیتم', 'trim|required|in_list[sound,image,highlight,tag,note]');

        if ($this->form_validation->run() == FALSE)
            throw new Exception(implode('|', $this->form_validation->error_array()), 1);


        $id = (int)$this->input->post('id');
        $item = $this->input->post('item');
        $table = '';

        switch ($item) {
            case 'sound':
                $table = 'fav_sounds';
                break;
            case 'image':
                $table = 'fav_images';
                break;
            case 'highlight':
                $table = 'highlights';
                $this->db->where('user_id', (int)$user->id);
                $this->db->where('hid', $id);
                $this->db->delete('hightag');

                break;
            case 'tag':
                $table = 'hightag';
                break;
            case 'note':
                $table = 'notes';
                break;
        }

        $this->db->where('user_id', (int)$user->id);
        $this->db->where('id', $id);

        if (!$this->db->delete($table))
            throw new Exception("خطا در حذف", 5);

        $this->tools->outS(0, "حذف شد");
    }
    
    public function ema_deleteItem()
    {
        $user = $this->_loginNeed();

        if ($user === FALSE)
            throw new Exception("برای دسترسی به این بخش باید وارد حساب کاربری خود شوید", -1);

        $this->load->library('form_validation');

        $this->form_validation->set_rules('id', 'شماره ID', 'trim|required');
        $this->form_validation->set_rules('item', 'آیتم', 'trim|required|in_list[sound,image,highlight,tag,note]');

        if ($this->form_validation->run() == FALSE)
            throw new Exception(implode('|', $this->form_validation->error_array()), 1);

        $id = $this->input->post('id');
        $item = $this->input->post('item');
        $table = '';

        switch ($item) {
            case 'sound':
                $table = 'fav_sounds';
                break;
            case 'image':
                $table = 'fav_images';
                break;
            case 'highlight':
                $table = 'highlights';
                $this->db->where('user_id', (int)$user->id);
                $this->db->where('hid', (int)$id);
                $this->db->delete('hightag');

                break;
            case 'tag':
                $table = 'hightag';

                // Handle multiple IDs for tags
                $ids = json_decode($id, true);
                if (!is_array($ids) || empty($ids)) {
                    throw new Exception("فرمت شناسه ها نامعتبر است", 1);
                }

                $this->db->where('user_id', (int)$user->id);
                $this->db->where_in('id', $ids);

                if (!$this->db->delete($table)) {
                    throw new Exception("خطا در حذف", 5);
                }

                $this->tools->outS(0, "حذف شد");
                return;

            case 'note':
                $table = 'notes';
                break;
        }

        // Handle single ID for other items
        $id = (int)$id;
        $this->db->where('user_id', (int)$user->id);
        $this->db->where('id', $id);

        if (!$this->db->delete($table)) {
            throw new Exception("خطا در حذف", 5);
        }

        $this->tools->outS(0, "حذف شد");
    }

    public function HNS()
    {
        $user = $this->_loginNeed();

        if ($user === FALSE)
            throw new Exception("برای دسترسی به این بخش باید وارد حساب کاربری خود شوید", -1);


        $this->load->library('form_validation');
        $this->form_validation->set_rules('data', 'data', 'trim|required');

        if ($this->form_validation->run() == FALSE)
            throw new Exception(implode('|', $this->form_validation->error_array()), 1);


        $data = $this->input->post('data');


        $data = $this->tools->jsonDecode($data);


        if (!is_array($data) && !is_object($data))
            throw new Exception("اطلاعات ارسالی ناقص می باشد. امکان پردازش اطلاعات وجود ندارد.", 2);

        //================== highlights ================//
        if ($this->_checkJsonArray($data, 'highlights')) {
            if ($this->_checkJsonArray($data['highlights'], 'added')) {
                foreach ($data['highlights']['added'] as $k => $h) {
                    $result = $this->_add_edit_h($h, $user->id, 'add');

                    $re = array('highlight_id' => $h['highlight_id']);

                    if (is_numeric($result)) {
                        $re['done'] = TRUE;
                        $re['new_id'] = $result;
                    } else {
                        $re['done'] = FALSE;
                        $re['error'] = $result;
                    }
                    $data['highlights']['added'][$k] = $re;
                }
            }

            if ($this->_checkJsonArray($data['highlights'], 'edited')) {
                foreach ($data['highlights']['edited'] as $k => $h) {
                    $result = $this->_add_edit_h($h, $user->id, 'edit');

                    $re = array('highlight_id' => $h['highlight_id']);

                    if ($result === TRUE) {
                        $re['done'] = TRUE;
                    } else {
                        $re['done'] = FALSE;
                        $re['error'] = $result;
                    }
                    $data['highlights']['edited'][$k] = $re;
                }
            }

            if ($this->_checkJsonArray($data['highlights'], 'deleted')) {
                foreach ($data['highlights']['deleted'] as $k => $h) {
                    $re = array('highlight_id' => $h);

                    $this->db->where('id', (int)$h);
                    $this->db->where('user_id', $user->id);

                    if ($this->db->count_all_results('highlights') == 0) {
                        $re['done'] = FALSE;
                        $re['error'] = 'آیتم مورد نظر پیدا نشد.';
                    } else {
                        $this->db->where('id', (int)$h)->delete('highlights');
                        $re['done'] = TRUE;
                    }
                    $data['highlights']['deleted'][$k] = $re;
                }
            }
        }

        //================== nots ================//
        if ($this->_checkJsonArray($data, 'notes')) {
            if ($this->_checkJsonArray($data['notes'], 'added')) {
                foreach ($data['notes']['added'] as $k => $n) {
                    $result = $this->_add_edit_n($n, $user->id, 'add');

                    $re = array('not_id' => $n['not_id']);

                    if (is_numeric($result)) {
                        $re['done'] = TRUE;
                        $re['new_id'] = $result;
                    } else {
                        $re['done'] = FALSE;
                        $re['error'] = $result;
                    }
                    $data['notes']['added'][$k] = $re;
                }
            }

            if ($this->_checkJsonArray($data['notes'], 'edited')) {
                foreach ($data['notes']['edited'] as $k => $n) {
                    $result = $this->_add_edit_n($n, $user->id, 'edit');

                    $re = array('not_id' => $n['not_id']);

                    if ($result === TRUE) {
                        $re['done'] = TRUE;
                    } else {
                        $re['done'] = FALSE;
                        $re['error'] = $result;
                    }
                    $data['notes']['edited'][$k] = $re;
                }
            }

            if ($this->_checkJsonArray($data['notes'], 'deleted')) {
                foreach ($data['notes']['deleted'] as $k => $n) {
                    $re = array('not_id' => $n);

                    $this->db->where('id', (int)$n);
                    $this->db->where('user_id', $user->id);

                    if ($this->db->count_all_results('notes') == 0) {
                        $re['done'] = FALSE;
                        $re['error'] = 'آیتم مورد نظر پیدا نشد.';
                    } else {
                        $this->db->where('id', (int)$n)->delete('notes');
                        $re['done'] = TRUE;
                    }
                    $data['notes']['deleted'][$k] = $re;
                }
            }
        }

        //================== sounds and images ================//
        if ($this->_checkJsonArray($data, 'notes')) {
            if ($this->_checkJsonArray($data['sounds'], 'added')) {
                foreach ($data['sounds']['added'] as $k => $s) {
                    $error = NULL;
                    $part_id = (int)$s['text_id'];

                    $sData = [
                        'part_id' => $part_id,
                        'user_id' => $user->id,
                    ];

                    if ($this->db->where('id', $part_id)->count_all_results('book_meta') == 0)
                        $error = "شماره پاراگراف اشتباه است";

                    if ($error === NULL && $this->db->where('id', $part_id)->where('sound IS NOT NULL')->count_all_results('book_meta') == 0)
                        $error = "پاراگراف مورد نظر صوت ندارد";

                    if ($error === NULL && $this->db->where($sData)->count_all_results('fav_sounds') == 1)
                        $error = "قبلا به لیست صوت های محبوب اضافه شده است";

                    if ($error === NULL && !$this->db->insert('fav_sounds', $sData))
                        $error = "خطا در ثبت اطلاعات";

                    if ($error === NULL)
                        $re = [
                            'text_id' => $part_id,
                            'done' => TRUE
                        ];
                    else
                        $re = [
                            'text_id' => $part_id,
                            'done' => FALSE,
                            'error' => $error
                        ];

                    $data['sounds']['added'][$k] = $re;
                }
            }
            if (isset($data['images']) && $this->_checkJsonArray($data['images'], 'added')) {
                foreach ($data['images']['added'] as $k => $s) {
                    $error = NULL;
                    $part_id = (int)$s['text_id'];

                    $sData = [
                        'part_id' => $part_id,
                        'user_id' => $user->id,
                    ];

                    if ($this->db->where('id', $part_id)->count_all_results('book_meta') == 0)
                        $error = "شماره پاراگراف اشتباه است";

                    if ($error === NULL && $this->db->where('id', $part_id)->where('image IS NOT NULL')->count_all_results('book_meta') == 0)
                        $error = "پاراگراف مورد نظر صوت ندارد";

                    if ($error === NULL && $this->db->where($sData)->count_all_results('fav_images') == 1)
                        $error = "قبلا به لیست صوت های محبوب اضافه شده است";

                    if ($error === NULL && !$this->db->insert('fav_images', $sData))
                        $error = "خطا در ثبت اطلاعات";

                    if ($error === NULL)
                        $re = [
                            'text_id' => $part_id,
                            'done' => TRUE
                        ];
                    else
                        $re = [
                            'text_id' => $part_id,
                            'done' => FALSE,
                            'error' => $error
                        ];

                    $data['images']['added'][$k] = $re;
                }
            }
            if ($this->_checkJsonArray($data['sounds'], 'deleted')) {
                foreach ($data['sounds']['deleted'] as $k => $s) {
                    $re = array('text_id' => (int)$s);

                    $this->db->where('part_id', (int)$s);
                    $this->db->where('user_id', $user->id);

                    if ($this->db->count_all_results('fav_sounds') == 0) {
                        $re['done'] = FALSE;
                        $re['error'] = 'آیتم مورد نظر پیدا نشد.';
                    } else {
                        $this->db->where('part_id', (int)$s)->delete('fav_sounds');
                        $re['done'] = TRUE;
                    }
                    $data['sounds']['deleted'][$k] = $re;
                }
            }
            if (isset($data['images']) && $this->_checkJsonArray($data['images'], 'deleted')) {
                foreach ($data['images']['deleted'] as $k => $s) {
                    $re = array('text_id' => (int)$s);

                    $this->db->where('part_id', (int)$s);
                    $this->db->where('user_id', $user->id);

                    if ($this->db->count_all_results('fav_images') == 0) {
                        $re['done'] = FALSE;
                        $re['error'] = 'آیتم مورد نظر پیدا نشد.';
                    } else {
                        $this->db->where('part_id', (int)$s)->delete('fav_images');
                        $re['done'] = TRUE;
                    }
                    $data['images']['deleted'][$k] = $re;
                }
            }
        }


        /*$this->output
            ->set_content_type('application/json')
            ->set_status_header(200)
            ->set_output($this->MakeJSON($data));*/
        $this->tools->outS(0, NULL, ['result' => $data]);
    }

    private function _checkJsonArray($data, $name)
    {
        return (isset($data[$name]) && (is_array($data[$name]) || is_object($data[$name])));
    }

    private function _add_edit_h($data, $user_id, $action = 'add')
    {

        $this->load->library('form_validation');
        $this->form_validation->set_data($data);

        if ($action == 'add')
            $this->form_validation->set_rules('text_id', 'شماره پاراگراف', 'trim|required|numeric');
        else
            $this->form_validation->set_rules('highlight_id', 'ID', 'trim|required|numeric');

        $this->form_validation->set_rules('highlight_color', 'رنگ', 'trim|required|numeric');
        $this->form_validation->set_rules('highlight_title', 'عنوان', 'trim|xss_clean');
        $this->form_validation->set_rules('highlight_text', 'متن', 'trim|required|xss_clean');
        $this->form_validation->set_rules('highlight_description', 'توضیحات', 'trim|xss_clean');
        $this->form_validation->set_rules('highlight_start', 'شروع', 'trim|required|numeric');
        $this->form_validation->set_rules('highlight_end', 'پایان', 'trim|required|numeric');
        $this->form_validation->set_rules('sharh', 'شرح', 'trim|required|numeric');

        if ($this->form_validation->run() == FALSE)
            return implode('|', $this->form_validation->error_array());

        $newData = array(
            'part_id' => @$data['text_id'],
            'user_id' => $user_id,
            'title' => $data['highlight_title'],
            'text' => $data['highlight_text'],
            'description' => $data['highlight_description'],
            'color' => $data['highlight_color'],
            'start' => $data['highlight_start'],
            'end' => $data['highlight_end'],
            'sharh' => $data['sharh']
        );

        if ($action == 'edit')
            unset($newData['part_id']);

        if ($action == 'add' && $this->db->where('id', $newData['part_id'])->count_all_results('book_meta') == 0)
            return "شماره پاراگراف اشتباه است";

        if ($action == 'edit') {

            $this->db->where('id', (int)$data['highlight_id']);
            $this->db->where('user_id', $user_id);
            if ($this->db->count_all_results('highlights') == 0)
                return 'آیتم مورد نظر پیدا نشد.';

            $this->db->where('id', (int)$data['highlight_id']);
            $this->db->where('user_id', $user_id);
        }


        $do = $action == 'add' ? 'insert' : 'update';

        if (!$this->db->{$do}('highlights', $newData))
            return "خطا در ثبت اطلاعات";

        return $action == 'add' ? $this->db->insert_id() : TRUE;
    }

    private function _add_edit_n($data, $user_id, $action = 'add')
    {

        $this->load->library('form_validation');
        $this->form_validation->set_data($data);

        if ($action == 'add')
            $this->form_validation->set_rules('text_id', 'شماره پاراگراف', 'trim|required|numeric');
        else
            $this->form_validation->set_rules('not_id', 'ID', 'trim|required|numeric');

        $this->form_validation->set_rules('not_text', 'متن', 'trim|xss_clean|required');
        $this->form_validation->set_rules('not_text_user', 'یادداشت', 'trim|xss_clean|required');
        $this->form_validation->set_rules('title', 'عنوان', 'trim|required|max_length[255]');
        $this->form_validation->set_rules('notstart', 'شروع', 'trim|required|numeric');
        $this->form_validation->set_rules('end', 'پایان', 'trim|required|numeric');
        $this->form_validation->set_rules('sharh', 'شرح', 'trim|required|numeric');

        if ($this->form_validation->run() == FALSE)
            return implode('|', $this->form_validation->error_array());

        $newData = array(
            'part_id' => @$data['text_id'],
            'user_id' => $user_id,
            'title' => $data['title'],
            'text' => $data['not_text'],
            'user_text' => $data['not_text_user'],
            'start' => $data['notstart'],
            'end' => $data['end'],
            'sharh' => $data['sharh']
        );

        if ($action == 'edit')
            unset($newData['part_id']);

        if ($action == 'add' && $this->db->where('id', $newData['part_id'])->count_all_results('book_meta') == 0)
            return "شماره پاراگراف اشتباه است";

        if ($action == 'edit') {
            $this->db->where('id', (int)$data['not_id']);
            $this->db->where('user_id', $user_id);
            if ($this->db->count_all_results('notes') == 0)
                return 'آیتم مورد نظر پیدا نشد.';

            $this->db->where('id', (int)$data['not_id']);
            $this->db->where('user_id', $user_id);
        }

        $do = $action == 'add' ? 'insert' : 'update';

        if (!$this->db->{$do}('notes', $newData))
            return "خطا در ثبت اطلاعات";

        return $action == 'add' ? $this->db->insert_id() : TRUE;
    }

    public function hnsExample()
    {
        $data = [];

        $data['highlights']['added'][] = [
            'highlight_id' => 1,
            'text_id' => 514,
            'highlight_title' => 'text',
            'highlight_text' => 'text',
            'highlight_description' => 'text',
            'highlight_color' => 1,
            'highlight_start' => 10,
            'highlight_end' => 20,
            'sharh' => 'sharh'
        ];
        $data['highlights']['edited'][] = [
            'highlight_id' => 1234,
            //'text_id'         => 514,
            'highlight_title' => 'text',
            'highlight_text' => 'text',
            'highlight_description' => 'text',
            'highlight_color' => 1,
            'highlight_start' => 10,
            'highlight_end' => 20,
            'sharh' => 'sharh'
        ];
        $data['highlights']['deleted'] = [10, 11, 12, 13];

        //============== notes ===================//
        $data['notes']['added'][] = [
            'not_id' => 1,
            'text_id' => 513,
            'not_text' => 'text',
            'not_text_user' => 'user text',
            'title' => 'title',
            'notstart' => 10,
            'end' => 20,
            'sharh' => 'sharh'
        ];
        $data['notes']['edited'][] = [
            'not_id' => 12,
            //'text_id'       => 513,
            'not_text' => 'text',
            'not_text_user' => 'user text',
            'title' => 'title',
            'notstart' => 10,
            'end' => 20,
            'sharh' => 'sharh'
        ];
        $data['notes']['deleted'] = [5, 6, 7, 8, 9];

        //============== sounds ===================//
        $data['sounds']['added'][] = [
            'text_id' => 514
        ];
        $data['sounds']['deleted'] = [513];

        //============== images ===================//
        $data['images']['added'][] = [
            'text_id' => 514
        ];
        $data['images']['deleted'] = [513];

        $this->output
            ->set_content_type('application/json')
            ->set_status_header(200)
            ->set_output($this->MakeJSON($data));
    }

    public function Highlights()
    {
        $user = $this->_loginNeed();

        if ($user === FALSE)
            throw new Exception("برای دسترسی به این بخش باید وارد حساب کاربری خود شوید", -1);
        $this->load->model('m_book', 'book');
        $data = array(
            'notes' => $this->book->getUserNotes($user->id),
            'highlights' => $this->book->getUserHighlights($user->id),
            'sounds' => $this->book->getUserfavSounds($user->id),
            'images' => $this->book->getUserfavImages($user->id),
            'user' => $user->id
        );
        $status = 0;
        $message = "OK";
        $this->tools->outS($status, $message, array("data" => $data));
    }

    public function Tags()
    {
        $user = $this->_loginNeed();

        if ($user === FALSE)
            throw new Exception("برای دسترسی به این بخش باید وارد حساب کاربری خود شوید", -1);
        $this->db->select('*');
        $this->db->where('user_id', $user->id);
        $data = $this->db->get('hightag')->result();
        $status = 0;
        $message = "OK";
        $this->tools->outS($status, $message, array("data" => $data));
    }

    /*===================================
		OTHER HELPERS
	===================================*/
    public function rateApp($rating = 0)
    {
        $data = $this->input->post();

        if (empty($data))
            throw new Exception("اطلاعات ارسالی صحیح نیست", 1);

        $this->load->library('form_validation');
        $this->load->library('myformvalidator');

        $this->form_validation->set_rules('rating', 'ستاره', 'trim|numeric|greater_than[0]|less_than[6]');
        //$this->myformvalidator->set_rules('mac'    , 'mac'   , 'trim|xss_clean|required|valid_mac');
        $this->form_validation->set_rules('text', 'نظر', 'trim|xss_clean|required');
        $this->form_validation->set_rules('name', 'نام', 'trim|xss_clean|max_length[50]');
        $this->form_validation->set_rules('email', 'ایمیل', 'trim|xss_clean|valid_email');

        if ($this->form_validation->run() == FALSE)
            throw new Exception(implode('|', $this->form_validation->error_array()), 2);

        $mac = $this->input->post('mac');
        $rating = $this->input->post('rating');
        $text = $this->input->post('text');
        $name = $this->input->post('name');
        $email = $this->input->post('email');

        $where = array(
            'ip' => $mac,
            'table' => 'APP',
            'row_id' => 0
        );

        $commentData = array(
            'user_id' => 0,
            'ip' => $mac,
            'table' => 'APP',
            'row_id' => 0,
            'submitted' => 1,
            'name' => $name,
            'email' => $email,
            'text' => $text,
            'date' => date('Y-m-d H:i:s'),
            'parent' => 0,
        );

        $rateData = array(
            'user_id' => 0,
            'ip' => $mac,
            'table' => 'APP',
            'row_id' => 0,
            'rating' => $rating
        );

        $this->db->select('c.id, cr.rate_id');
        $this->db->where($where);
        $this->db->join('ci_comment_rate cr', 'c.id=cr.comment_id', 'left', FALSE);
        $comment = $this->db->get('comments c', 1)->row();

        if (isset($comment->id)) {
            $this->db->where('id', $comment->id)->update('comments', $commentData);
            $comment_id = $comment->id;
        } else {
            $this->db->insert('comments', $commentData);
            $comment_id = $this->db->insert_id();
        }

        if (isset($comment->rate_id) && $this->db->where('id', (int)$comment->rate_id)->count_all_results('rates')) {
            $this->db->where('id', (int)$comment->rate_id)->update('rates', $rateData);
        } else {
            $this->db->insert('rates', $rateData);
            $rate_id = $this->db->insert_id();

            $this->db->insert('comment_rate', [
                'comment_id' => $comment_id,
                'rate_id' => $rate_id
            ]);
        }

        $this->db->select("COUNT(r.id) AS total_rates");
        $this->db->select("ROUND((AVG(r.rating)),1) AS app_rating", FALSE);

        $this->db->where('r.table', 'APP');
        $this->db->where('r.rating !=', 0);
        $this->db->where('r.row_id', 0);

        $data = $this->db->get('rates r', 1)->row();

        $this->tools->outS(0, "نظر شما ثبت شد", ['data' => $data]);
    }

    public function getNotification($type = 'unread')
    {
        $posts = $this->post->getPosts([
            'type' => 'notification',
            'order' => 'p.date_modified desc',
            'views' => FALSE,
            'comments' => FALSE,
            'likes' => FALSE,
            'rating' => FALSE
        ]);

        $this->tools->outS(0, NULL, ['data' => $posts]);
    }

    public function getSetting()
    {
        $allowed = array(
            'title',
            'meta_key',
            'meta_description',
            'slogan',
            'time_format',
            'date_format',
            'time_zone',
            'site_logo',
            'default_user_avatar',
            'default_category_pic',
            'default_post_thumb',
            'site_tel',
            'site_email',
            'site_address',
            'site_map_address',
            'site_about',
            'site_facebook',
            'site_google_plus',
            'site_twitter',
            'site_instagram',
            'site_pinterest',
            'site_linkedin',
            'app_last_version',
            'app_min_version',
            'app_file',
            'tz_offset',
        );

        $files = array(
            'site_logo',
            'default_user_avatar',
            'default_category_pic',
            'default_post_thumb',
        );

        $setting = array_intersect_key($this->setting, array_flip($allowed));

        foreach ($files as $k)
            $setting[$k] = base_url() . $setting[$k];

        $this->tools->outS(0, NULL, ['data' => $setting]);
    }

    /*===================================
		PRIVATE FUNCTIONS
	===================================*/
    private function _check_mobile($mobile)
    {

        if (substr($mobile, 0, 3) == '+98') {
            if (strlen($mobile) != 13)
                throw new Exception("شماره موبایل با پیشوند +98 باید 13 رقم باشد", 1);
        } elseif (substr($mobile, 0, 1) == '0') {
            if (strlen($mobile) != 11)
                throw new Exception("شماره موبایل بدون پیشوند +98 باید 11 رقم باشد", 1);

            $mobile = '+98' . substr($mobile, 1, 11);
        } else
            throw new Exception("شماره موبایل معتبر نیست", 2);

        return $mobile;
    }

    private function _outFile($status, $done, $msg, $filename = 'book', $data = NULL)
    {
        $result = array(
            'done' => $done,
            'status' => $status,
            'msg' => $msg
        );

        if (is_array($data)) $result = array_merge($result, $data);

        header('Content-Disposition: attachment; filename="' . $filename . '"');

        $this->output->set_content_type('application/java-archive')->set_output($this->MakeJSON($result));
    }

    private function _loginNeed($post = TRUE, $select = NULL)
    {
        if ($post) {
            $mac = $this->input->post('mac');
            $token = $this->input->post('token');
        } else {
            $mac = $this->input->get('mac');
            $token = $this->input->get('token');
        }
        if ($mac == '' or $token == '')
            return FALSE;

        if ($select === NULL)
            $this->db->select('u.*');
        else
            $this->db->select($select);

        $this->db->join('ci_logged_in l', 'u.id=l.user_id', 'LEFT', FALSE);
        $this->db->or_where([
            //"l.mac = '$mac' OR u.username = '$mac'"
            'u.username' => $mac,
            'u.tel' => $mac,
            'l.mac' => $mac,
            //'l.token' => $token
        ]);

        $user = $this->db->get('users u')->row();

        return isset($user->id) ? $user : FALSE;
    }

    private function _valid_mac($mac)
    {
        return $mac;//Alireza Balvardi
        if (strlen(str_replace(array(':', '-', '.'), '', $mac)) !== 12)
            return FALSE;

        if (
            preg_match('/^([a-fA-F0-9]{2}:){5}[a-fA-F0-9]{2}$/', $mac) or
            preg_match('/^([a-fA-F0-9]{2}\-){5}[a-fA-F0-9]{2}$/', $mac) or
            preg_match('/^[a-fA-F0-9]{12}$/', $mac) or
            preg_match('/^([a-fA-F0-9]{4}\.){2}[a-fA-F0-9]{4}$/', $mac)
        ) {
            $mac = $this->_normalize_mac($mac);
            if (strlen($mac) === 17) return $mac;
        }

        return FALSE;
    }

    private function _normalize_mac($mac)
    {
        $mac = str_replace(".", "", $mac);
        $mac = str_replace("-", ":", $mac);

        $colon_count = substr_count($mac, ":");

        if ($colon_count == 0) {
            $mac = substr_replace($mac, ":", 2, 0);
            $mac = substr_replace($mac, ":", 5, 0);
            $mac = substr_replace($mac, ":", 8, 0);
            $mac = substr_replace($mac, ":", 11, 0);
            $mac = substr_replace($mac, ":", 14, 0);
        }

        return strtoupper($mac);
    }

    private function MakeJSON($data)
    {
        return json_encode($data, JSON_UNESCAPED_UNICODE | JSON_HEX_QUOT | JSON_HEX_TAG);
    }

    /*===================================
		Extra function
	===================================*/
    public function addUserBooks($data)
    {
        try {
            $bid = isset($data['bid']) ? $data['bid'] : 0;
            $uid = isset($data['uid']) ? $data['uid'] : 0;
            $ref = isset($data['factor']) ? $data['factor'] : 0;
            $price = isset($data['price']) ? $data['price'] : 0;
            if (!$bid || !$uid || !strlen($ref))
                throw new Exception('اطلاعاتی ارسال نشده', 1);

            $data = array("user_id" => $uid, "state" => "ثبت شده توسط APP", "ref_id" => $ref, "price" => $price, "cprice" => $price, "cdate" => time(), "section" => 'book', "status" => 0);
            $this->db->select("id");
            $this->db->where("`user_id` = $uid");
            $this->db->where("`price` = $price");
            $this->db->where("`ref_id` = '$ref'");
            $result = $this->db->get('factors')->result();

            if (!count($result)) {
                $this->db->insert('factors', $data);
                $factor_id = $this->db->insert_id();
            } else {
                $factor_id = $result[0]->id;
            }

            $this->db->select("id");
            $this->db->where("`user_id` = $uid");
            $this->db->where("`book_id` = $bid");
            $result = $this->db->get('user_books')->result();

            if (!count($result)) {
                $data = array("user_id" => $uid, "book_id" => $bid, "factor_id" => $factor_id);
                $this->db->insert('user_books', $data);
            }
        } catch (Exception $e) {
            //$this->tools->outE($e);
        }
    }//Alireza Balvardi

    public function showUserBooks()
    {
        $user = $this->_loginNeed();
        $this->db->select('ub.id ubid,ub.need_update,b.id,b.title,c.name AS cname,d.name AS sath');
        $this->db->join('ci_posts b', 'ub.book_id=b.id', 'right', FALSE);
        $this->db->join('ci_category c', 'b.category=c.id', 'right', FALSE);
        $this->db->join('ci_category d', 'c.parent=d.id', 'right', FALSE);
        $this->db->where('ub.user_id', $user->id);
        $books = $this->db->get('user_books ub')->result();
        $this->tools->outS(0, 'OK', array("books" => $books));
    }

    public function AcceptBazarPay()
    {
        $user = $this->_loginNeed();
        $data = $this->input->post();
        //$this->LogMe($data);
        $mac = @$data["mac"];
        $token = @$data["token"];
        $type = @$data["type"];
        $id = (int)@$data["id"];
        $factor = @$data["factor"];
        $price = (int)@$data["price"];
        if (!strlen($mac) || !$user->id || !strlen($factor) || !$id || !strlen($type) || !strlen($token)) {
            throw new Exception("خطا در انجام عملیات : اطلاعات ارسالی صحیح نمی باشند", 4);
        }
        switch ($type) {
            case "book":
                $data = array();
                $data['bid'] = $id;
                $data['uid'] = $user->id;
                $data['factor'] = $factor;
                $data['price'] = $price;
                $this->addUserBooks($data);
                break;
            case "payeh":
                $this->db->select('id');
                $this->db->where('category', $id);
                $books = $this->db->get('posts')->result();
                $data = array();
                $data['uid'] = $user->id;
                $data['factor'] = $factor;
                $data['price'] = $price;
                foreach ($books as $k => $v) {
                    $data['bid'] = $v->id;
                    $this->addUserBooks($data);
                }
                break;
            case "sath":
                $ids = array($id);
                $this->db->select('id');
                $this->db->where('parent', $id);
                $categories = $this->db->get('category')->result();
                foreach ($categories as $k => $v)
                    $ids[] = $v->id;
                $this->db->select('id');
                $this->db->where('category IN (' . implode(",", $ids) . ')');
                $books = $this->db->get('posts')->result();
                $data = array();
                $data['uid'] = $user->id;
                $data['factor'] = $factor;
                $data['price'] = $price;
                foreach ($books as $k => $v) {
                    $data['bid'] = $v->id;
                    $this->addUserBooks($data);
                }
                break;
            default:
                throw new Exception("خطا در انجام عملیات : اطلاعات ارسالی صحیح نمی باشند", 4);
        }
        $this->db->select('ub.id ubid,ub.need_update,b.id,b.title,c.name AS cname,d.name AS sath');
        $this->db->join('ci_posts b', 'ub.book_id=b.id', 'right', FALSE);
        $this->db->join('ci_category c', 'b.category=c.id', 'right', FALSE);
        $this->db->join('ci_category d', 'c.parent=d.id', 'right', FALSE);
        $this->db->where('ub.user_id', $user->id);
        $books = $this->db->get('user_books ub')->result();
        $this->tools->outS(0, 'OK', array("books" => $books));
        return;
    }

    /* LetBox */
    public function getLeitbox()
    {
        $user = $this->_loginNeed(TRUE, 'u.id');

        if ($user === FALSE)
            throw new Exception("برای دسترسی به این بخش باید وارد حساب کاربری خود شوید", -1);

        $uid = $user->id;
        $results = $this->db->select('id,title,remember')->where('user_id', $uid)->get('leitbox')->result();
        $this->tools->outS(0, $results);
    }

    public function addLeitbox()
    {
        $user = $this->_loginNeed(TRUE, 'u.id');
        $data = $this->input->post();
        if ($user === FALSE)
            throw new Exception("برای دسترسی به این بخش باید وارد حساب کاربری خود شوید", -1);
        $this->load->library('form_validation');
        $this->form_validation->set_rules('id', 'شماره جعبه لایتنر', 'trim|required');
        $this->form_validation->set_rules('title', 'نام جعبه لایتنر', 'trim|required');
        $this->form_validation->set_rules('remember', 'یادآوری', 'trim|required');
        if ($this->form_validation->run() == FALSE) {
            throw new Exception(implode(' | ', $this->form_validation->error_array()), 1);
        }

        $uid = $user->id;
        $results = $this->db->select('id')->where('user_id', $uid)->where('title', $data["title"])->get('leitbox')->result();
        if (count($results) && !$data["id"]) {
            throw new Exception("نام جعبه تکراری می باشد", -1);
        }
        $xdata = array();
        $xdata["user_id"] = (int)$uid;
        $xdata["title"] = $data["title"];
        $xdata["remember"] = $data["remember"];
        if (!$data["id"]) {
            $this->db->insert('leitbox', $xdata);
        } else {
            $this->db->where('id', $data["id"])->update('leitbox', $xdata);
        }

        $this->tools->outS(0, "جعبه ثبت شد");
    }

    public function delLeitbox()
    {
        $user = $this->_loginNeed(TRUE, 'u.id');
        $data = $this->input->post();
        if ($user === FALSE)
            throw new Exception("برای دسترسی به این بخش باید وارد حساب کاربری خود شوید", -1);
        $this->load->library('form_validation');
        $this->form_validation->set_rules('id', 'شماره جعبه', 'trim|required');
        if ($this->form_validation->run() == FALSE) {
            throw new Exception(implode(' | ', $this->form_validation->error_array()), 1);
        }

        $uid = $user->id;
        $this->db->where('id', $data["id"])->where('user_id', $uid)->delete('leitbox');
        $this->db->where('lid', $data["id"])->where('user_id', $uid)->delete('leitner');

        $this->tools->outS(0, "جعبه حذف شد");
    }

    /* Letner */
    public function getLeitner()
    {
        $user = $this->_loginNeed(TRUE, 'u.id');
        $data = $this->input->post();
        if ($user === FALSE)
            throw new Exception("برای دسترسی به این بخش باید وارد حساب کاربری خود شوید", -1);
        $this->load->library('form_validation');
        $this->form_validation->set_rules('lid', 'شماره جعبه لایتنر', 'trim|required');
        if ($this->form_validation->run() == FALSE) {
            throw new Exception(implode(' | ', $this->form_validation->error_array()), 1);
        }
        $uid = $user->id;
        if ($data['lid']) {
            $results = $this->db->select('*')->where('user_id', $uid)->where('lid', $data['lid'])->get('leitner')->result();
        } else {
            $results = $this->db->select('*')->where('user_id', $uid)->get('leitner')->result();
        }
        $data = array();
        foreach ($results as $k => $v) {
            $dest = is_numeric($v->description) ? $v->description : 0;
            $v->data = new stdClass;
            if ($dest) {
                switch ($v->catid) {
                    case 1://یادداشت
                        break;
                    case 2://لغت
                        break;
                    case 3://سوال تستی
                        $v->data = array();
                        $v->data = $this->db->where('id', (int)$dest)->order_by('id', 'ASC')->get('tests')->row();
                        break;
                    case 4://سوال تشریحی
                        $v->data = $this->db->where('id', (int)$dest)->order_by('id', 'ASC')->get('tashrihi')->row();
                        break;
                }
                $data[] = $v;
            }
        }
        $this->tools->outS(0, $results);
    }

    public function addLeitner()
    {
        $user = $this->_loginNeed(TRUE, 'u.id');
        $data = $this->input->post();
        if ($user === FALSE)
            throw new Exception("برای دسترسی به این بخش باید وارد حساب کاربری خود شوید", -1);

        $id = intval($data["id"]);
        $lid = intval($data["lid"]);
        $message = "";
        $uid = $user->id;
        if (!$id) {
            $results = $this->db->select('id')->where('user_id', $uid)->where('lid', $lid)->where('title', $data["title"])->get('leitner')->result();
            if (count($results)) {
                throw new Exception("این عنوان در جعبه انتخابی تکراری می باشد", -1);
            }
        }
        $xdata = array();
        $xdata["lid"] = $data["lid"];
        $xdata["catid"] = $data["catid"];
        $xdata["level"] = $data["level"];
        $xdata["user_id"] = (int)$uid;
        $xdata["title"] = $data["title"];
        $xdata["description"] = $data["description"];
        $xdata["bookid"] = $data["bookid"];
        $xdata["tag"] = $data["tag"];
        $this->load->library('form_validation');
        if (!$id) {
            $this->form_validation->set_rules('title', 'عنوان لایتنر', 'trim|required');
            $this->form_validation->set_rules('description', 'توضیحات لایتنر', 'trim|required');
            if ($this->form_validation->run() == FALSE) {
                throw new Exception(implode(' | ', $this->form_validation->error_array()), 1);
            }
            $this->db->insert('leitner', $xdata);
            $id = $this->db->insert_id();
            $message = "لایتنر ثبت شد";
        } else {
            $this->form_validation->set_rules('answertrue', 'جواب لایتنر', 'trim|required');
            $results = $this->db->select('*')->where('id', $id)->where('user_id', $uid)->get('leitner')->row();
            if (!isset($results->id)) {
                throw new Exception("این لایتنر وجود ندارد", -1);
            }
            $xdata = array(
                "id" => $data["id"],
                "readdate" => date("Y-m-d H:i:s"),
                "readcount" => $results->readcount + 1,
                "trueanswer" => $results->trueanswer + ($data["answertrue"] ? 1 : 0),
                "falseanswer" => $results->falseanswer + ($data["answertrue"] ? 0 : 1),
            );
            if (strlen($data["title"])) {
                $xdata["title"] = $data["title"];
            }
            if (strlen($data["description"])) {
                $xdata["description"] = $data["description"];
            }
            if (intval($data["catid"])) {
                $xdata["catid"] = $data["catid"];
            }
            if (intval($data["level"])) {
                $xdata["level"] = $data["level"];
            }
            if (intval($data["bookid"])) {
                $xdata["bookid"] = $data["bookid"];
            }
            if (strlen($data["tag"])) {
                $xdata["tag"] = $data["tag"];
            }
            $results = $this->db->where('id', $id)->update('leitner', $xdata);
            $message = "لایتنر بروزرسانی شد";
        }
        $data = array();
        $data["id"] = $id;
        $this->tools->outS(0, $message, array("data" => $data));
    }

    public function delLeitner()
    {
        $user = $this->_loginNeed(TRUE, 'u.id');
        $data = $this->input->post();
        if ($user === FALSE)
            throw new Exception("برای دسترسی به این بخش باید وارد حساب کاربری خود شوید", -1);
        $this->load->library('form_validation');
        $this->form_validation->set_rules('id', 'شماره لایتنر', 'trim|required');
        if ($this->form_validation->run() == FALSE) {
            throw new Exception(implode(' | ', $this->form_validation->error_array()), 1);
        }

        $uid = $user->id;
        $this->db->where('id', $data["id"])->where('user_id', $uid)->delete('leitner');

        $this->tools->outS(0, "لایتنر حذف شد");
    }

    //=========================================
    public function getPublisher()
    {
        $user = $this->_loginNeed(TRUE, 'u.id');
        if ($user === FALSE)
            throw new Exception("برای دسترسی به این بخش باید وارد حساب کاربری خود شوید", -1);
        $results = $this->db->select('id,title')->get('publisher')->result();
        $this->tools->outS(0, $results);
    }

    public function getWriter()
    {
        $user = $this->_loginNeed(TRUE, 'u.id');
        if ($user === FALSE)
            throw new Exception("برای دسترسی به این بخش باید وارد حساب کاربری خود شوید", -1);
        $results = $this->db->select('id,title')->get('writer')->result();
        $this->tools->outS(0, $results);
    }

    public function getTranslator()
    {
        $user = $this->_loginNeed(TRUE, 'u.id');
        if ($user === FALSE)
            throw new Exception("برای دسترسی به این بخش باید وارد حساب کاربری خود شوید", -1);
        $results = $this->db->select('id,title')->get('ranslator')->result();
        $this->tools->outS(0, $results);
    }

    //=========================================
    public function ForceLogOut($return = 0)
    {
        $user = $this->_loginNeed(TRUE, 'u.id');

        $post = $this->input->post();
        $mac = $this->input->post('mac');
        $token = $this->input->post('token');
        $id = (int)$this->input->post('id');
        $this->db->where('user_id', $user->id)->where('id', $id)->delete('user_mobile');
        $this->db->select("id");
        $this->db->where("`user_id` = $user->id");
        $this->db->where("`forcelogout` = 0");
        $result = $this->db->get('user_mobile')->result();
        if (count($result) > 3) {
            $data = array("forcelogout" => 1);
            $this->db->where("user_id", $user->id)->update('user_mobile', $data);

            $data = array("forcelogout" => 0);
            $this->db->where("user_id", $user->id)->where("`id` != $id")->limit(3)->order_by("date", "DESC")->update('user_mobile', $data);

        }
        if (!$return) {
            $data = $this->getUserMobiles($user->id);
            $this->tools->outS(0, $result, array("mobiles" => $data));
        }
    }

    //=========================================
    public function getExtraBook()
    {
        $user = $this->_loginNeed(TRUE, 'u.id');
        if ($user === FALSE) throw new Exception("برای دسترسی به این بخش باید وارد حساب کاربری خود شوید", -1);

        $userid = $user->id;
        $this->output->set_header('Last-Modified: ' . gmdate("D, d M Y H:i:s") . ' GMT');
        $this->output->set_header('Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0');
        $this->output->set_header('Pragma: no-cache');
        $this->output->set_header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");

        $id = (int)$this->input->post('id');

        if ($this->db->where('id', $id)->where('type', 'book')->where('published', 1)->count_all_results('posts') == 0) {
            throw new Exception("This book is not active", 1);
        }

        $book_meta = $this->db->select('id,sound')->where('book_id', $id)->where("LENGTH(sound) > 0")->order_by('id')->get('book_meta')->result();

        return $this->tools->outS(0, NULL, ['data' => $book_meta]);
    }

    //=========================================
    public function getResetLeitner()
    {
        $user = $this->_loginNeed(TRUE, 'u.id');
        if ($user === FALSE) throw new Exception("برای دسترسی به این بخش باید وارد حساب کاربری خود شوید", -1);
        $user_id = $user->id;
        $lid = (int)$this->input->post('lid');
        $level = (int)$this->input->post('level');
        $data = array(
            'level' => $level
        );
        if ($lid) {
            $this->db->where('user_id', $user_id)->where('lid', $lid)->update('leitner', $data);
        } else {
            $this->db->where('user_id', $user_id)->update('leitner', $data);
        }
        return $this->tools->outS(0, "اطلاعات کلیه لایتنرهای منتخب بروزرسانی شد");
    }

    //=========================================
    public function getResult()
    {
        $gender = array();
        $gender[0] = $this->db->where('gender', 0)->count_all_results('users');
        $gender[1] = $this->db->where('gender', 1)->count_all_results('users');
        $data["gender"] = $gender;
        $data["gender_all"] = $gender[0] + $gender[1];

        $doreh = $this->db->count_all_results('doreh');
        $data["doreh"] = $doreh;

        $dorehclass = $this->db->count_all_results('dorehclass');
        $data["dorehclass"] = $dorehclass;

        $factors = $this->db->where('paid > 0')->count_all_results('factors');
        $data["factors"] = $factors;

        $free = $this->db->where('paid = 0')->count_all_results('factors');
        $data["free"] = $free;

        $books = $this->db->where('published', 1)->where('type', 'book')->count_all_results('posts');
        $data["books"] = $books;

        return $this->tools->outS(0, "اطلاعات کلی", ["data" => $data]);
    }

    //=========================================
    public function getFavorite()
    {
        $user = $this->_loginNeed(TRUE, 'u.id');
        if ($user === FALSE) throw new Exception("برای دسترسی به این بخش باید وارد حساب کاربری خود شوید", -1);
        //
        $user_id = (int)$user->id;
        $base = CDN_URL;
        $action = $this->input->post('action');
        $section = $this->input->post('section');
        $sectionid = (int)$this->input->post('sectionid');
        $data = array(
            'user_id' => $user_id,
            'section' => $section,
            'section_id' => $sectionid
        );

        switch ($action) {
            case 'add':
                $this->load->library('form_validation');
                $this->form_validation->set_rules('section', 'نام بخش', 'trim|required');
                $this->form_validation->set_rules('sectionid', 'آی دی کنترل', 'trim|required');
                if ($this->form_validation->run() == FALSE) {
                    throw new Exception(implode(' | ', $this->form_validation->error_array()), 1);
                }
                $this->db->insert('favorites', $data);
                return $this->tools->outS(0, "اطلاعات علاقه مندی ها بروزرسانی شد");
                break;
            case 'remove':
                $this->load->library('form_validation');
                $this->form_validation->set_rules('section', 'نام بخش', 'trim|required');
                $this->form_validation->set_rules('sectionid', 'آی دی کنترل', 'trim|required');
                if ($this->form_validation->run() == FALSE) {
                    throw new Exception(implode(' | ', $this->form_validation->error_array()), 1);
                }
                $this->db->where('user_id', $user_id)->where('section', $section)->where('section_id', $sectionid)->delete('favorites');
                return $this->tools->outS(0, "اطلاعات علاقه مندی ها بروزرسانی شد");
                break;
            default:
                $db = $this->db;
                if ($section)
                    $db->where('section', $section);
                if ($sectionid)
                    $db->where('section_id', $sectionid);
                $db->where('user_id', $user_id);
                $rows = $db->get('favorites')->result();
                $data = array();
                $ids = array(0);
                foreach ($rows as $row) {
                    $ids[] = $row->section_id;
                }
                switch ($section) {
                    case "book":
                        $db = $this->db;
                        $db->select('p.id,p.title,p.thumb,p.price,p.author,p.part_count,p.has_description,p.has_sound,p.has_video,p.has_image,p.has_test,p.has_tashrihi,p.has_download');
                        $db->where("p.published = 1");
                        $db->where("p.id IN(" . implode(",", $ids) . ")");
                        $db->where("p.type = 'book'");
                        $rows = $db->get('posts p')->result();
                        foreach ($rows as $X) {
                            $X->thumb = $X->thumb ? $base . $X->thumb : null;
                            $data[] = $X;
                        }
                        break;
                    case "category":
                        $db = $this->db;
                        $db->select('p.id,p.name,p.pic');
                        $db->where("p.id IN(" . implode(",", $ids) . ")");
                        $rows = $db->get('category p')->result();
                        foreach ($rows as $X) {
                            $X->pic = $X->pic ? $base . $X->pic : null;
                            $data[] = $X;
                        }
                        break;
                    case "classroom":
                        $db = $this->db;
                        $db->select('p.id,p.title');
                        $db->where("p.id IN(" . implode(",", $ids) . ")");
                        $rows = $db->get('classroom p')->result();
                        foreach ($rows as $X) {
                            $data[] = $X;
                        }
                        break;
                    case "doreh":
                        $db = $this->db;
                        $db->select('d.id,t.name,d.image');
                        $db->order_by('d.id', 'desc');
                        $db->where("d.id IN(" . implode(",", $ids) . ")");
                        $db->join('ci_doreh d', 't.id=d.tecatid', 'inner', FALSE);
                        $rows = $db->get('tecat t')->result();
                        foreach ($rows as $X) {
                            $X->image = $X->image ? $base . $X->image : null;
                            $data[] = $X;
                        }
                        break;
                    case "dorehclass":
                        break;
                    case "jalasat":
                        break;
                    case "subjalasat":
                        break;
                    case "mecat":
                        break;
                    case "tecat":
                        break;
                    case "ostad":
                        break;
                    case "questions":
                        break;
                    case "supplier":
                        break;
                    case "tashrihi":
                        break;
                    case "tests":
                        break;
                    case "publisher":
                        break;
                    case "writer":
                        break;
                    case "translator":
                        break;
                }

                return $this->tools->outS(0, "اطلاعات علاقه مندی ها", ["data" => $data]);
        }
    }

    //=========================================
    public function getSupplierTypeData($db, $datatype)
    {
        $suppliertype = $this->db->where("s.datatype = '$datatype'")->get('suppliertype s')->result();
        $place = array(0);
        $placeTitle = array('');
        foreach ($suppliertype as $k => $v) {
            $place[$v->id] = $v->id;
            $placeTitle[$v->id] = $v->title;
        }
        return array($place, $placeTitle);
    }


    //=========================================
    public function fetchFile(){
        $base_path = base_url() ; // Base path for this script
        $requested_url = $_SERVER['REQUEST_URI']; // Full request URI
        $relative_path = str_replace($base_path, '', $requested_url); // Remove base path
        $relative_path = ltrim($relative_path, '/'); // Remove leading slash if present
        $outputString = str_replace("api/v2/fetchFile/", "", $relative_path);
        $file_path = '/lexoya/var/www/html/'. $outputString;
        $file_path = str_replace("api/v2/fetchFile/", "", $file_path);


        if (file_exists($file_path)) {
            $mime_type = mime_content_type($file_path);
            header("Content-Type: $mime_type");
            header("Content-Length: " . filesize($file_path));
            header("Content-Disposition: inline; filename=\"" . basename($file_path) . "\""); // Display file directly in browser
            readfile($file_path);
            exit;
        } else {
            header("HTTP/1.0 404 Not Found");
            echo $file_path;
            exit;
        }
    }

    public function siteHealth() {
        return $this->tools->outS(200, 'ok');
    }

    public function collabrationMessageEitaa() {
        // توکن بات خود را اینجا وارد کنید
        $token = EITAA_COLABRATION_TOKEN;
    
        // آیدی چت یا کاربری که می‌خواهید پیام ارسال شود
        $chat_id = 10406720;
    
        // متن پیام که از طریق ورودی ارسال شده
        $text = $this->input->post('text');
    
        // URL صحیح برای API ایتا یار
        $apiUrl = 'https://eitaayar.ir/api/' . $token . '/sendMessage';
    
        // مقداردهی cURL
        $request = curl_init();
    
        // تنظیمات cURL
        curl_setopt($request, CURLOPT_URL, $apiUrl); // آدرس API
        curl_setopt($request, CURLOPT_POST, true); // ارسال درخواست POST
        curl_setopt($request, CURLOPT_SSL_VERIFYHOST, 0); // غیرفعال کردن تأیید SSL (در صورت نیاز)
        curl_setopt($request, CURLOPT_SSL_VERIFYPEER, false); // غیرفعال کردن تأیید SSL (در صورت نیاز)
        curl_setopt($request, CURLOPT_POSTFIELDS, http_build_query([
            'chat_id' => $chat_id,
            'text' => $text,
        ])); // ارسال داده‌ها به API
        curl_setopt($request, CURLOPT_RETURNTRANSFER, true); // دریافت پاسخ API
    
        // اجرای درخواست و دریافت پاسخ
        $response = curl_exec($request);
    
        // بررسی خطاهای احتمالی cURL
        if (curl_errno($request)) {
            echo 'Curl error: ' . curl_error($request);
        } else {
            echo $response; // نمایش پاسخ API
        }
    
        // بستن cURL
        curl_close($request);
    }
   



    //=========================================

    //=========================================
    public function getLastClasses()
    {
        $limit = (int)$this->input->post('limit');
        $start = (int)$this->input->post('start');
        $did = (int)$this->input->post('dorehid');
        $title = $this->input->post('title');
        $limit = $limit < 1000 ? $limit : 1000;
        $section = $this->input->post('section');
        $baseurl = CDN_URL;

        // Limit the number of results to 1000 to avoid large cache keys
        $limit = $limit < 1000 ? $limit : 1000;

        $allowsection = array(
            'doreh',
            'classroom',
            'favclass',
            'dorehclass',
            'ostad',
            'place',
            'book',
            'favbook',
            'bookupdate',
            'supplierbook',
            'supplierfavbook',
            'specialbook',
            'samembook',
            'sametbook',
            'choosedclass',
            'offerclass',
            'lastclass',
            'updatedclass'
        );

        if (!in_array($section, $allowsection)) {
            throw new Exception("لطفا در ارسال اطلاعات دقت نمایید", 1);
        }

        // Generate a unique cache key based on the request parameters
        $cache_key = "last_classes_{$section}_{$limit}_{$start}_{$did}_{$title}";

        // Check if data is cached in Redis
        $cached_data = $this->cache->redis->get($cache_key);
        
        if ($cached_data !== FALSE) {
            // Cache hit, return the cached data
            return $this->tools->outS(0, "اطلاعات کلی", ["data" => $cached_data["data"], "pagination" => $cached_data["pagination"]]);
        }

        switch ($section) {
            case 'ostad':
                $db = $this->db;
                if ($title) {
                    $db->where("title LIKE '%$title%'");
                }
                $ostads = $db->where('optype = 1')->order_by('id', 'desc')->limit($limit, $start)->get('supplier')->result();
                if ($title) {
                    $db->where("title LIKE '%$title%'");
                }
                $count = $db->where('optype = 1')->count_all_results('supplier');
                $data['ostad'] = array();
                foreach ($ostads as $k => $v) {
                    $data['ostad'][$k]["id"] = $v->id;
                    $data['ostad'][$k]["title"] = $v->title;
                    $data['ostad'][$k]["image"] = $v->image ? $baseurl . $v->image : null;
                }
                break;
            case 'doreh':
                $db = $this->db;
                if ($title) {
                    $db->where("t.name LIKE '%$title%'");
                }
                $dorehs = $db->order_by('d.id', 'desc')->join('ci_doreh d', 't.id=d.tecatid', 'inner', FALSE)->limit($limit, $start)->get('tecat t')->result();
                if ($title) {
                    $db->where("t.name LIKE '%$title%'");
                    $db->join('ci_tecat t', 't.id=d.tecatid', 'inner', FALSE);
                }
                $count = $db->count_all_results('doreh d');
                $data['doreh'] = array();
                foreach ($dorehs as $k => $v) {
                    $data['doreh'][$k]["id"] = $v->id;
                    $data['doreh'][$k]["title"] = $v->name;
                    $data['doreh'][$k]["image"] = $v->image ? $baseurl . $v->image : null;
                    $data['doreh'][$k]["year"] = $v->tahsili_year . '-' . ($v->tahsili_year + 1);
                }
                break;
            case 'book':
                $db = $this->db;
                $db->where("p.published = 1");
                if ($did) {
                    $db->where("p.author", $did);
                }
                if ($title) {
                    $db->where("p.title LIKE '%$title%'");
                }
                $db->where("p.type = 'book'");
                $books = $db->order_by('p.id', 'desc')->limit($limit, $start)->get('posts p')->result();

                $db->where("p.published = 1");
                if ($did) {
                    $db->where("p.author", $did);
                }
                if ($title) {
                    $db->where("p.title LIKE '%$title%'");
                }
                $db->where("p.type = 'book'");
                $count = $db->count_all_results('posts p');
                $data['book'] = array();

                foreach ($books as $k => $v) {
                    $data['book'][$k]["id"] = $v->id;
                    $data['book'][$k]["title"] = $v->title;
                    $data['book'][$k]["price"] = intval($v->price);
                    $data['book'][$k]["sharh"] = intval($v->has_description) ? 1 : 0;
                    $data['book'][$k]["sound"] = intval($v->has_sound) ? 1 : 0;
                    $data['book'][$k]["video"] = intval($v->has_video) ? 1 : 0;
                    $data['book'][$k]["image"] = intval($v->has_image) ? 1 : 0;
                    $data['book'][$k]["thumb"] = $v->thumb ? $baseurl . $v->thumb : null;
                    $data['book'][$k]["cover300"] = $v->thumb ? thumb($v->thumb, 300) : null;
                }
                break;
            case 'favbook':
                $db = $this->db;
                $db->where("p.published = 1");
                $db->where("f.price > 0");
                if ($did) {
                    $db->where("p.author", $did);
                }
                if ($title) {
                    $db->where("title LIKE '%$title%'");
                }
                $db->where("p.type = 'book'");
                $favbooks = $db->join('ci_factor_detail f', 'p.id=f.book_id', 'inner', FALSE)->group_by('p.id')->order_by('p.id', 'desc')->limit($limit, $start)->get('posts p')->result();

                $db->where("p.published = 1");
                $db->where("f.price > 0");
                if ($did) {
                    $db->where("p.author", $did);
                }
                if ($title) {
                    $db->where("title LIKE '%$title%'");
                }
                $db->where("p.type = 'book'");
                $count = $db->select('p.id')->join('ci_factor_detail f', 'p.id=f.book_id', 'inner', FALSE)->group_by('p.id')->count_all_results('posts p');
                $data['favbook'] = array();
                foreach ($favbooks as $k => $v) {
                    $data['favbook'][$k]["id"] = $v->book_id;
                    $data['favbook'][$k]["title"] = $v->title;
                    $data['favbook'][$k]["price"] = intval($v->price);
                    $data['favbook'][$k]["sharh"] = intval($v->has_description) ? 1 : 0;
                    $data['favbook'][$k]["thumb"] = $v->thumb ? $baseurl . $v->thumb : null;
                    $data['favbook'][$k]["cover300"] = $v->thumb ? thumb($v->thumb, 300) : null;
                }
                break;
            case 'favclass':
                $db = $this->db;
                $db->where("p.published = 1");
                $db->where("f.price > 0");
                if ($did) {
                    $db->where("p.dorehid", $did);
                }
                if ($title) {
                    $db->where("title LIKE '%$title%'");
                }
                $favclass = $db->join('ci_classfactor_detail f', 'p.id=f.class_id', 'inner', FALSE)->group_by('p.id')->order_by('p.id', 'desc')->limit($limit, $start)->get('classroom p')->result();

                $db->where("p.published = 1");
                $db->where("f.price > 0");
                if ($did) {
                    $db->where("p.author", $did);
                }
                if ($title) {
                    $db->where("title LIKE '%$title%'");
                }
                $count = $db->select('p.id')->join('ci_classfactor_detail f', 'p.id=f.class_id', 'inner', FALSE)->group_by('p.id')->count_all_results('classroom p');
                $data['favclass'] = array();
                foreach ($favclass as $k => $v) {
                    $data['favclass'][$k]["id"] = $v->id;
                    $data['favclass'][$k]["title"] = $v->title;
                    $data['favclass'][$k]["thumb"] = $v->image ? $baseurl . $v->image : null;
                }
                break;
            case 'bookupdate':
                $db = $this->db;
                $db->where("p.published = 1");
                if ($did) {
                    $db->where("p.author", $did);
                }
                if ($title) {
                    $db->where("p.title LIKE '%$title%'");
                }
                $db->where("p.type = 'book'");
                $bookupdates = $db->order_by('p.date_modified', 'desc')->limit($limit, $start)->get('posts p')->result();

                $db->where("p.published = 1");
                if ($did) {
                    $db->where("p.author", $did);
                }
                if ($title) {
                    $db->where("p.title LIKE '%$title%'");
                }
                $db->where("p.type = 'book'");
                $count = $db->count_all_results('posts p');
                $data['bookupdate'] = array();
                foreach ($bookupdates as $k => $v) {
                    $data['bookupdate'][$k]["id"] = $v->id;
                    $data['bookupdate'][$k]["title"] = $v->title;
                    $data['bookupdate'][$k]["price"] = intval($v->price);
                    $data['bookupdate'][$k]["sharh"] = intval($v->has_description) ? 1 : 0;
                    $data['bookupdate'][$k]["thumb"] = $v->thumb ? $baseurl . $v->thumb : null;
                    $data['bookupdate'][$k]["cover300"] = $v->thumb ? thumb($v->thumb, 300) : null;
                }
                break;
            case 'supplierbook':
                $db = $this->db;
                $db->where("p.author", $did);
                $db->where("p.published = 1");
                if ($title) {
                    $db->where("title LIKE '%$title%'");
                }
                $db->where("p.type = 'book'");
                $supplierbooks = $db->order_by('p.id', 'desc')->limit($limit, $start)->get('posts p')->result();
                $db->where("p.author", $did);
                $db->where("p.published = 1");
                if ($title) {
                    $db->where("title LIKE '%$title%'");
                }
                $db->where("p.type = 'book'");
                $count = $db->select('p.id')->count_all_results('posts p');
                $data['supplierbook'] = array();
                foreach ($supplierbooks as $k => $v) {
                    $data['supplierbook'][$k]["id"] = $v->id;
                    $data['supplierbook'][$k]["title"] = $v->title;
                    $data['supplierbook'][$k]["price"] = intval($v->price);
                    $data['supplierbook'][$k]["sharh"] = intval($v->has_description) ? 1 : 0;
                    $data['supplierbook'][$k]["thumb"] = $v->thumb ? $baseurl . $v->thumb : null;
                    $data['supplierbook'][$k]["cover300"] = $v->thumb ? thumb($v->thumb, 300) : null;
                }
                break;
            case 'specialbook':
                $db = $this->db;
                $db->where("p.special > 0");
                $db->where("p.published = 1");
                if ($did) {
                    $db->where("p.author", $did);
                }
                $db->where("p.type = 'book'");
                $specialbooks = $db->order_by('special', 'desc')->limit($limit, $start)->get('posts p')->result();

                $db->where("p.special > 0");
                $db->where("p.published = 1");
                if ($did) {
                    $db->where("p.author", $did);
                }
                $db->where("p.type = 'book'");
                $count = $db->count_all_results('posts p');
                $data['specialbook'] = array();
                foreach ($specialbooks as $k => $v) {
                    $data['specialbook'][$k]["id"] = $v->id;
                    $data['specialbook'][$k]["title"] = $v->title;
                    $data['specialbook'][$k]["price"] = intval($v->price);
                    $data['specialbook'][$k]["sharh"] = intval($v->has_description) ? 1 : 0;
                    $data['specialbook'][$k]["thumb"] = $v->thumb ? $baseurl . $v->thumb : null;
                    $data['specialbook'][$k]["cover300"] = $v->thumb ? thumb($v->thumb, 300) : null;
                }
                break;
            case 'samembook':
                $db = $this->db;

                $db->where("p.published = 1");
                $db->where("d.cid = $did");
                $db->where("p.type = 'book'");
                $samembooks = $db->select('p.*,d.data_type')->join('ci_classroom_data d', 'p.id=d.data_id', 'inner', FALSE)->order_by('d.data_type,p.title')->limit($limit, $start)->get('posts p')->result();

                $db->where("p.published = 1");
                $db->where("c.cid = $did");
                $db->where("p.type = 'book'");
                $count = $db->select('p.id')->join('ci_classroom_data c', 'p.id=c.data_id', 'inner', FALSE)->count_all_results('posts p');
                $data['samembook'] = array();//'sql'=>$db->queries
                foreach ($samembooks as $k => $v) {
                    $data['samembook'][$k]["id"] = $v->id;
                    $data['samembook'][$k]["title"] = $v->title;
                    $data['samembook'][$k]["price"] = intval($v->price);
                    $data['samembook'][$k]["sharh"] = intval($v->has_description) ? 1 : 0;
                    $data['samembook'][$k]["thumb"] = $v->thumb ? $baseurl . $v->thumb : null;
                    $data['samembook'][$k]["cover300"] = $v->thumb ? thumb($v->thumb, 300) : null;
                    $data['samembook'][$k]["data_type"] = $v->data_type;
                }
                break;
            case 'sametbook':
                $db = $this->db;

                $db->where("d.dorehid", $did);
                $O = $db->select('c.data_id,d.*,c.data_type')->join('ci_classroom_data c', 'c.cid=d.classid', 'inner', FALSE)->get('dorehclass d')->result();
                $books = array(0);
                $data_type = array();
                foreach ($O as $k => $v) {
                    $books[$v->data_id] = $v->data_id;
                    $data_type[$v->data_id] = $v->data_type;
                }

                $db->where("p.published = 1");
                $db->where("p.id IN(" . implode(",", $books) . ")");
                $db->where("p.type = 'book'");
                $sametbooks = $db->order_by('title', 'desc')->limit($limit, $start)->get('posts p')->result();

                $db->where("p.published = 1");
                $db->where("p.id IN(" . implode(",", $books) . ")");
                $db->where("p.type = 'book'");
                $count = $db->select('p.id')->count_all_results('posts p');
                $data['sametbook'] = array();
                foreach ($sametbooks as $k => $v) {
                    $data['sametbook'][$k]["id"] = $v->id;
                    $data['sametbook'][$k]["title"] = $v->title;
                    $data['sametbook'][$k]["price"] = intval($v->price);
                    $data['sametbook'][$k]["sharh"] = intval($v->has_description) ? 1 : 0;
                    $data['sametbook'][$k]["thumb"] = $v->thumb ? $baseurl . $v->thumb : null;
                    $data['sametbook'][$k]["cover300"] = $v->thumb ? thumb($v->thumb, 300) : null;
                    $data['sametbook'][$k]["data_type"] = $data_type[$v->id];
                }
                break;
            case 'choosedclass':
                $db = $this->db;

                if ($did)
                    $db->where("d.ostadid", $did);
                $O = $db->select('d.*,c.title classTitle')
                    ->join('ci_classroom c', 'c.id=d.classid', 'inner', FALSE)
                    ->limit($limit, $start)
                    ->get('dorehclass d')
                    ->result();

                $ids = array(0);
                $kids = array();
                foreach ($O as $k => $v) {
                    $ids[$v->id] = $v->id;
                    $kids[$v->id] = $k;
                    $v->image = $v->image ? $baseurl . $v->image : null;
                    $O[$k] = $v;
                }
                $xids = implode(",", $ids);

                $db->where("f.dorehclassid IN($xids)");
                $F = $db
                    ->select('f.dorehclassid,COUNT(f.dorehclassid) C')
                    ->order_by('C DESC')
                    ->group_by('f.dorehclassid')
                    ->get('classfactor_detail f')->result();

                $dorehclass = array();
                $data['choosedclass'] = array();
                foreach ($F as $k => $v) {
                    $dorehclass[$v->dorehclassid] = $v->C;
                    $O[$kids[$v->dorehclassid]]->Bought = $v->C;
                    $data['choosedclass'][] = $O[$kids[$v->dorehclassid]];
                }
                break;
            case 'offerclass':
                $db = $this->db;

                if ($did)
                    $db->where("d.ostadid", $did);
                $db->where("d.offer", 1);
                $O = $db->select('d.*,c.title classTitle')
                    ->join('ci_classroom c', 'c.id=d.classid', 'inner', FALSE)
                    ->limit($limit, $start)
                    ->get('dorehclass d')
                    ->result();


                foreach ($O as $k => $v) {
                    $v->image = $v->image ? $baseurl . $v->image : null;
                    $O[$k] = $v;
                }

                $data['offerclass'] = $O;
                break;
            case 'lastclass':
                $db = $this->db;

                if ($did)
                    $db->where("d.ostadid", $did);
                $db->order_by("d.id DESC");
                $O = $db->select('d.*,c.title classTitle')
                    ->join('ci_classroom c', 'c.id=d.classid', 'inner', FALSE)
                    ->limit($limit, $start)
                    ->get('dorehclass d')
                    ->result();


                foreach ($O as $k => $v) {
                    $v->image = $v->image ? $baseurl . $v->image : null;
                    $O[$k] = $v;
                }

                $data['lastclass'] = $O;
                break;
            case 'updatedclass':
                $db = $this->db;

                if ($did)
                    $db->where("d.ostadid", $did);
                $db->where("d.upddate > " . (time() - 7 * 24 * 3600));
                $O = $db->select('d.*,c.title classTitle')
                    ->join('ci_classroom c', 'c.id=d.classid', 'inner', FALSE)
                    ->limit($limit, $start)
                    ->get('dorehclass d')
                    ->result();


                foreach ($O as $k => $v) {
                    $v->image = $v->image ? $baseurl . $v->image : null;
                    $O[$k] = $v;
                }

                $data['updatedclass'] = $O;
                break;
            case 'supplierfavbook':
                $db = $this->db;
                $db->where("p.author", $did);
                $db->where("f.price > 0");
                if ($title) {
                    $db->where("title LIKE '%$title%'");
                }
                $db->where("p.type = 'book'");
                $supplierfavbooks = $db->join('ci_factor_detail f', 'p.id=f.book_id', 'inner', FALSE)->group_by('p.id')->order_by('p.id', 'desc')->limit($limit, $start)->get('posts p')->result();
                $db->where("p.author", $did);
                $db->where("f.price > 0");
                if ($title) {
                    $db->where("title LIKE '%$title%'");
                }
                $db->where("p.type = 'book'");
                $count = $db->select('p.id')->join('ci_factor_detail f', 'p.id=f.book_id', 'inner', FALSE)->group_by('p.id')->count_all_results('posts p');
                $data['supplierfavbook'] = array();
                foreach ($supplierfavbooks as $k => $v) {
                    $data['supplierfavbook'][$k]["id"] = $v->id;
                    $data['supplierfavbook'][$k]["title"] = $v->title;
                    $data['supplierfavbook'][$k]["price"] = intval($v->price);
                    $data['supplierfavbook'][$k]["sharh"] = intval($v->has_description) ? 1 : 0;
                    $data['supplierfavbook'][$k]["thumb"] = $v->thumb ? $baseurl . $v->thumb : null;
                    $data['supplierfavbook'][$k]["cover300"] = $v->thumb ? thumb($v->thumb, 300) : null;
                }
                break;
            case 'place':
                $db = $this->db;
                $datatype = 'place';
                list($place, $placeTitle) = $this->getSupplierTypeData($db, $datatype);

                if ($title) {
                    $db->where("s.title LIKE '%$title%'");
                }
                $places = $db->select('s.id,s.title,r.type_id')
                    ->where("r.type_id IN(" . implode(',', $place) . ")")
                    ->join('ci_supplierrules r', 's.id=r.sup_id', 'inner', FALSE)
                    ->limit($limit, $start)
                    ->order_by('s.id', 'desc')
                    ->get('supplier s')
                    ->result();
                if ($title) {
                    $db->where("s.title LIKE '%$title%'");
                }
                $count = $db->where("r.type_id IN(" . implode(',', $place) . ")")
                    ->join('ci_supplierrules r', 's.id=r.sup_id', 'inner', FALSE)
                    ->count_all_results('supplier s');
                $data['place'] = array();
                foreach ($places as $k => $v) {
                    $data['place'][$k]["id"] = $v->id;
                    $data['place'][$k]["title"] = $v->title;
                    $data['place'][$k]["image"] = $v->image ? $baseurl . $v->image : null;
                }
                break;
            case 'classroom':
                $db = $this->db;
                if ($title) {
                    $db->where("title LIKE '%$title%'");
                }
                $classrooms = $db->order_by('id', 'desc')->limit($limit, $start)->get('classroom')->result();
                if ($title) {
                    $db->where("title LIKE '%$title%'");
                }
                $count = $db->count_all_results('classroom');
                $data['classroom'] = array();
                foreach ($classrooms as $k => $v) {
                    $data['classroom'][$k]["id"] = $v->id;
                    $data['classroom'][$k]["title"] = $v->title;
                }
                break;
            case 'dorehclass':
                $db = $this->db;
                if ($title) {
                    $db->where("c.title LIKE '%$title%'");
                }
                if ($did) {
                    $db->where('d.dorehid', $did);
                }
                $dorehclasss = $db->join('ci_classroom c', 'c.id=d.classid', 'inner', FALSE)->select('d.*,pdate(FROM_UNIXTIME(d.startdate)) AS `shamsidate`')->order_by('id', 'desc')->limit($limit, $start)->get('dorehclass d')->result();
                if ($title) {
                    $db->where("c.title LIKE '%$title%'");
                }
                if ($did) {
                    $db->where('d.dorehid', $did);
                }
                $count = $db->join('ci_classroom c', 'c.id=d.classid', 'inner', FALSE)->count_all_results('dorehclass d');

                $dorehid = array(0);
                $classid = array(0);
                $ostadid = array(0);
                $placeid = array(0);
                foreach ($dorehclasss as $k => $v) {
                    $dorehid[$v->dorehid] = $v->dorehid;
                    $classid[$v->classid] = $v->classid;
                    $ostadid[$v->ostadid] = $v->ostadid;
                    $placeid[$v->placeid] = $v->placeid;
                }
                $ostads = $this->db->order_by('title', 'asc')->where('id IN (' . implode(',', $ostadid) . ')')->get('supplier')->result();
                $data['ostad'] = array();
                foreach ($ostads as $k => $v) {
                    $data['ostad'][$v->id] = $v->title;
                }

                $dorehs = $this->db->order_by('t.name', 'asc')->where('d.id IN (' . implode(',', $dorehid) . ')')->join('ci_doreh d', 't.id=d.tecatid', 'inner', FALSE)->get('tecat t')->result();
                $data['doreh'] = array();
                foreach ($dorehs as $k => $v) {
                    $data['doreh'][$v->id]["id"] = $v->id;
                    $data['doreh'][$v->id]["title"] = $v->name;
                    $data['doreh'][$v->id]["image"] = $v->image ? $baseurl . $v->image : null;
                    $data['doreh'][$v->id]["year"] = $v->tahsili_year . '-' . ($v->tahsili_year + 1);
                }

                $datatype = 'place';
                list($place, $placeTitle) = $this->getSupplierTypeData($db, $datatype);

                $places = $db->select('s.id,s.title,r.type_id')
                    ->where('s.id IN (' . implode(',', $placeid) . ')')
                    ->where("r.type_id IN(" . implode(',', $place) . ")")
                    ->join('ci_supplierrules r', 's.id=r.sup_id', 'inner', FALSE)
                    ->limit($limit, $start)
                    ->order_by('s.id', 'desc')
                    ->get('supplier s')
                    ->result();

                $data['place'] = array();
                foreach ($places as $k => $v) {
                    $data['place'][$v->id] = $v->title;
                }

                $classrooms = $this->db->order_by('title', 'asc')->where('id IN (' . implode(',', $classid) . ')')->get('classroom')->result();
                $data['classroom'] = array();
                foreach ($classrooms as $k => $v) {
                    $data['classroom'][$v->id] = $v->title;
                }

                $data['dorehclass'] = array();
                foreach ($dorehclasss as $k => $v) {
                    $data['dorehclass'][$k]["id"] = $v->id;
                    $data['dorehclass'][$k]["doreh"] = $data['doreh'][$v->dorehid];
                    $data['dorehclass'][$k]["ostad"] = $data['ostad'][$v->ostadid];
                    $data['dorehclass'][$k]["place"] = $data['place'][$v->placeid];
                    $data['dorehclass'][$k]["classroom"] = $data['classroom'][$v->classid];
                    $data['dorehclass'][$k]["jalasat"] = $v->jalasat;
                    $data['dorehclass'][$k]["startdate"] = $v->shamsidate;
                    $data['dorehclass'][$k]["image"] = $v->image ? $baseurl . $v->image : null;
                }
                break;
        }
        $pagination = array();
        $pagination["start"] = $start;
        $pagination["limit"] = $limit;
        $pagination["total"] = $count;

        // Cache the result for 1 hour (3600 seconds)
        $cache_data = [
            "data" => $data[$section],
            "pagination" => $pagination
        ];
        $this->cache->redis->save($cache_key, $cache_data, 3600);  // Cache the result for 1 hour

        return $this->tools->outS(0, "اطلاعات کلی", ["data" => $data[$section], "pagination" => $pagination]);
    }

    //=========================================
    public function getJalasat()
    {
        try {
            $dorehid = (int)$this->input->post('dorehid');
            $classid = (int)$this->input->post('classid');
            $dorehclassid = (int)$this->input->post('dorehclassid');
            if (!$dorehid && !$classid && !$dorehclassid)
                throw new Exception('اطلاعاتی ارسال نشده', 1);

            if ($dorehid && !$this->db->where('dorehid', $dorehid)->count_all_results('dorehclass')) {
                throw new Exception('اطلاعاتی برای نمایش وجود ندارد', 1);
            }
            if ($classid && !$this->db->where('classid', $classid)->count_all_results('dorehclass')) {
                throw new Exception('اطلاعاتی برای نمایش وجود ندارد', 2);
            }
            if ($dorehclassid && !$this->db->where('id', $dorehclassid)->count_all_results('dorehclass')) {
                throw new Exception('اطلاعاتی برای نمایش وجود ندارد', 2);
            }

            $db = $this->db;
            if ($dorehid) {
                $db->where('dorehid', $dorehid);
            }
            if ($classid) {
                $db->where('classid', $classid);
            }
            if ($dorehclassid) {
                $db->where('id', $dorehclassid);
            }
            $dorehclass = $db->get('dorehclass')->result();
            $book_id = array(0);
            $jalasat = array();
            foreach ($dorehclass as $k => $v) {
                $O =
                    $this->db->select('j.*,d.id,d.jid,d.bookid,d.pages,d.image,d.pdf,d.audio,d.video')
                        ->where('j.dorehclassid', $v->id)
                        ->where('j.published = 1')
                        ->join('ci_jalasat_data d', 'd.jid=j.id', 'INNER', FALSE)
                        ->order_by('j.id')
                        ->get('jalasat j')
                        ->result();
                if (count($O)) {
                    $finaljalasat = array();
                    foreach ($O as $k0 => $v0) {
                        $startdate = date("Y-m-d", $v0->startdate);
                        $startdate = explode("-", $startdate);
                        list($j_y, $j_m, $j_d) = gregorian_to_jalali($startdate[0], $startdate[1], $startdate[2]);
                        $startdate = "$j_y-$j_m-$j_d";
                        $finaljalasat[$v0->jid]["id"] = $v0->jid;
                        $finaljalasat[$v0->jid]["dorehid"] = $v->dorehid;
                        $finaljalasat[$v0->jid]["classid"] = $v->classid;
                        $finaljalasat[$v0->jid]["dorehclassid"] = $v0->dorehclassid;
                        $finaljalasat[$v0->jid]["title"] = $v0->title;
                        $finaljalasat[$v0->jid]["startdate"] = $v0->startdate;
                        $finaljalasat[$v0->jid]["starttime"] = $v0->starttime;
                        $finaljalasat[$v0->jid]["description"] = $v0->description;
                        $finaljalasat[$v0->jid]["subjalase"] = $v0->subjalase;
                        if ($v0->audio && !$v0->audio_duration) {
                            include_once("mp3file.class.php");
                            $mp3file = new MP3File(FCPATH . $v0->audio);
                            $duration = $mp3file->getDuration();
                            $data = array("audio_duration" => $duration);
                            $this->db->where('id', $v0->id)->update('jalasat_data', $data);
                            $v0->audio_duration = $duration;
                        }
                        $data = array();
                        $data["bookid"] = $v0->bookid;
                        $data["pages"] = $v0->pages;
                        $data["image"] = $v0->image;
                        $data["pdf"] = $v0->pdf;
                        $data["audio"] = $v0->audio;
                        $data["audio_duration"] = $v0->audio_duration;
                        $data["video"] = $v0->video;
                        $finaljalasat[$v0->jid]["data"][] = $data;
                        $book_id[$v0->bookid] = $v0->bookid;
                    }
                    $jalasat = array_merge($jalasat, $finaljalasat);
                }
            }
            $O = $this->db->select('p.id,p.title,p.price,p.has_description sharh')
                ->where('p.id IN(' . implode(',', $book_id) . ')')
                ->order_by('p.title')
                ->get('posts p')->result();
            $bookdata = array();
            foreach ($O as $k => $v) {
                $bookdata[$v->id] = $v;
            }
            foreach ($jalasat as $k => $v) {
                foreach ($v["data"] as $k0 => $v0) {
                    $jalasat[$k]["data"][$k0]["book"] = @$bookdata[$v0["bookid"]];
                }
            }
            $this->tools->outS(0, 'OK', array('jalasat' => $jalasat));
        } catch (Exception $e) {
            $this->tools->outE($e);
        }
    }

    //=========================================
    public function getSubJalasat()
    {
        try {
            $id = (int)$this->input->post('id');
            if (!$id)
                throw new Exception('اطلاعاتی ارسال نشده', 1);

            if (!$this->db->where('id', $id)->count_all_results('jalasat'))
                throw new Exception('اطلاعاتی برای نمایش وجود ندارد', 2);

            $jalasat = $this->db->where('id', $id)->get('jalasat')->row();
            if (!is_object($jalasat))
                throw new Exception('برای این جلسه فایل صوتی مشخص نشده است', 2);

            $O = $this->db->where('LENGTH(`audio`) > 0')->where('jid', $id)->get('jalasat_data')->result();

            $startdate = date("Y-m-d", $jalasat->startdate);
            $startdate = explode("-", $startdate);
            list($j_y, $j_m, $j_d) = gregorian_to_jalali($startdate[0], $startdate[1], $startdate[2]);
            $jalasat->startdate = "$j_y-$j_m-$j_d";

            $jalasat_datas = array();
            foreach ($O as $k => $v) {
                $pages[$v->bookid] = $v->pages;
                $jalasat_datas[$v->bookid] = $v;
            }
            $paragraphdata = array();
            $page = 0;
            $dest = 0;
            $paragraphtitle = 1;
            $subjalasat = $this->db->order_by('bookid,paragraphid')->where('jalasatid', $id)->get('subjalasat')->result();
            if (count($subjalasat)) {
                $order = array();
                $book_id = array(0);

                foreach ($subjalasat as $k => $v) {
                    $book_id[$v->bookid] = $v->bookid;
                }

                $O = $this->db->select('p.id,p.title,p.price,p.has_description sharh')
                    ->where('p.id IN(' . implode(',', $book_id) . ')')
                    ->order_by('p.title')
                    ->get('posts p')->result();
                $bookdata = array();
                foreach ($O as $k => $v) {
                    $bookdata[$v->id] = $v;
                }

                $db = $this->db;
                $db->select('b.book_id,b.id,b.order,b.text');
                foreach ($subjalasat as $k => $v) {
                    $db->or_where("(b.book_id = $v->bookid AND b.order IN($v->paragraphid))");
                }

                $db->join('ci_classroom_data c', 'c.cid=d.classid', 'INNER', FALSE);
                $db->join('ci_book_meta b', 'c.data_id = b.book_id', 'INNER', FALSE);
                $db->group_by('c.data_id,b.order');
                $db->order_by('c.data_id,b.order');
                $O = $db->get('dorehclass d')->result();
                foreach ($O as $k => $v) {
                    $page = $v->order + 1;
                    $datapages = explode(',', $jalasat_datas[$v->book_id]->pages);
                    if ($page) {
                        if (isset($pages[$page])) {
                            $v->nextpage = $pages[$page];
                        }
                        $v->page = $page;
                        $v->book = $bookdata[$v->book_id]->title;
                        $v->paragraphtitle = "بخش $paragraphtitle";
                        $v->jid = $jalasat_datas[$v->book_id]->jid;
                        $v->sjid = $jalasat_datas[$v->book_id]->id;
                        $v->paragraphid = $v->order;
                        $v->audio = $jalasat_datas[$v->book_id]->audio;
                        $v->title = $jalasat->title;
                        $v->text = str_replace(array("\n", "\t", chr(10), chr(13), "   ", "  "), " ", $v->text);
                        $paragraphdata[$v->book_id][$v->order] = $v;
                        $paragraphtitle++;
                    }
                }
            }
            foreach ($subjalasat as $k => $v) {
                $subjalasat[$k]->audio = $jalasat_datas[$v->bookid]->audio;
                $subjalasat[$k]->duration = ceil($v->endPos - $v->startPos);
                $subjalasat[$k]->paragraph = array("id" => $paragraphdata[$v->bookid][$v->paragraphid]->id, "bookid" => $v->bookid, "book" => $bookdata[$v->bookid]->title, "text" => $paragraphdata[$v->bookid][$v->paragraphid]->text);
                unset($subjalasat[$k]->bookid);
                unset($subjalasat[$k]->paragraphid);
            }
            $this->tools->outS(0, 'OK', array('subjalasat' => $subjalasat, "bookdata" => $bookdata));
        } catch (Exception $e) {
            $this->tools->outE($e);
        }
    }

    //=========================================
    public function getClassJalasatDetail0($id)
    {
        $base = CDN_URL;

        $SO = $this->db
            ->select('j.*,dc.ostadid,dc.placeid,s.paragraphid,dc.dorehid,dc.classid,dc.image,s.id sid,d.pages,s.bookid,d.id dataid,d.image,d.pdf,d.audio,d.audio_duration,d.video,d.video_duration,s.id subjalasattid,s.paragraphid page,s.description sdescription,s.startPos,s.endPos')
            ->order_by('dc.dorehid,dc.classid,s.bookid,s.paragraphid')
            ->join('ci_jalasat j', 'j.id=s.jalasatid', 'INNER', FALSE)
            ->join('ci_jalasat_data d', 'd.jid=j.id', 'INNER', FALSE)
            ->join('ci_dorehclass dc', 'dc.id=j.dorehclassid', 'INNER', FALSE)
            ->where('j.dorehclassid', $id)
            ->group_by('s.id')
            ->get('subjalasat s')
            ->result();
        if (!count($SO)) {
            return array();
        }
        $sid = array(0);
        foreach ($SO as $k => $v) {
            $sid[$v->ostadid] = $v->ostadid;
            $sid[$v->placeid] = $v->placeid;
        }
        $O = $this->db->where("id IN(" . implode(",", $sid) . ")")->get('supplier')->result();
        $supplier = array("ostad" => array(), "place" => array());
        $suppliers = array();
        foreach ($O as $k => $v) {
            $v->image = $v->image ? $base . $v->image : null;
            $suppliers[$v->id] = $v;
        }

        $bookdata = $this->db->select('p.id,p.price,p.title,IF(p.has_description,1,0) sharh')
            ->group_by('p.id')
            ->where('d.id', $id)
            ->join('ci_classroom_data c', 'c.cid=d.classid', 'INNER', FALSE)
            ->join('ci_posts p', 'p.id = c.data_id', 'INNER', FALSE)
            ->get('dorehclass d')->result();

        $book_ids = array(0);

        $O = $this->db
            ->select('t.id,t.name,d.id did,d.tahsili_year,d.description')
            ->join('ci_doreh d', 'd.tecatid=t.id', 'inner', FALSE)
            ->get('tecat t')
            ->result();
        $tecat = array();
        foreach ($O as $k => $v) {
            $tecat[$v->did] = $v->name . "[" . $v->tahsili_year . "-" . ($v->tahsili_year + 1) . "]";
            $tecatdescription[$v->did] = $v->description;
        }

        $O = $this->db
            ->select('c.id,c.title,c.description')
            ->join('ci_dorehclass d', 'd.classid=c.id', 'inner', FALSE)
            ->get('classroom c')
            ->result();
        $classdata = array();
        $classroom = array();
        foreach ($O as $k => $v) {
            $classroom[$v->id] = $v->title;
            $classroomdescription[$v->id] = $v->description;
        }
        foreach ($bookdata as $k => $v) {
            if ($v->thumb) {
                $bookdata[$k]->thumb = base_url() . $v->thumb;
                $bookdata[$k]->cover300 = thumb($v->thumb, 300);
            }
            $book_ids[] = $v->id;
            $books[$v->id] = $bookdata[$k];
        }

        $O = $this->db->where("meta_key", 'pages')->where("post_id IN (" . implode(",", $book_ids) . ")")->get('post_meta')->result();
        $post_meta = array();
        foreach ($O as $k => $v) {
            if ($v->meta_key == 'pages') {
                $pages = explode(",", $v->meta_value);
                $post_meta[$v->post_id] = $pages;
            }
        }

        $jalasat = array();
        $subjalasat = array();
        foreach ($SO as $k0 => $v0) {
            $v = new stdClass;
            if ($v0->audio && !$v0->audio_duration) {
                include_once("mp3file.class.php");
                $mp3file = new MP3File(FCPATH . $v0->audio);
                $duration = $mp3file->getDuration();
                $data = array("audio_duration" => $duration);
                $this->db->where('id', $v0->dataid)->update('jalasat_data', $data);
                $v0->audio_duration = $duration;
            }
            $v->sid = $v0->sid;
            $v->sdescription = $v0->sdescription;

            $v->audio = $v0->audio ? $base . $v0->audio : null;
            $v->duration = $v0->audio ? $v0->audio_duration : 0;
            $v->startPos = $v0->startPos;
            $v->endPos = $v0->endPos;

            $v->jid = $v0->id;
            $v->title = $v0->title;
            $v->jdescription = $v0->description;


            $v->dorehid = $v0->dorehid;
            $v->dorehtitle = $tecat[$v0->dorehid];
            $v->dorehdescription = $tecatdescription[$v0->dorehid];

            $v->classid = $v0->classid;
            $v->classtitle = $classroom[$v0->classid];
            $v->cdescription = $classroomdescription[$v0->classid];
            $v->image = $v0->image ? $base . $v0->image : null;

            $classdata[$v->classid] = array("id" => $v->classid, "title" => $v->classtitle, "description" => $v->cdescription, "image" => $v->image);

            $startdate = date("Y-m-d", $v0->startdate);
            $startdate = explode("-", $startdate);
            list($j_y, $j_m, $j_d) = gregorian_to_jalali($startdate[0], $startdate[1], $startdate[2]);
            $startdate = "$j_y-$j_m-$j_d";

            $v->starttime = $v0->starttime;
            $v->startdate = $v0->startdate;
            $v->startdateshamsi = $startdate;

            $v->bookid = $v0->bookid;
            $v->booktitle = $books[$v0->bookid]->title;
            $order = 1;
            foreach ($post_meta[$v0->bookid] as $k1 => $page) {
                if (intval($page) < intval($v0->paragraphid)) {
                    $order++;
                }
            }
            $v->page = $order;

            $jalasat[$v->jid]["info"] = array("id" => $v0->id, "title" => $v0->title, "description" => $v0->description);
            $jalasat[$v->jid]["data"][] = $v;

            $supplier["ostad"][$v->ostadid] = $suppliers[$v0->ostadid];
            $supplier["place"][$v->placeid] = $suppliers[$v0->placeid];

            //$jalasat[$v0->id][$v0->title][$v0->sid] = $v;
            $subjalasat[] = $v;
        }
        $classdata = array_values($classdata);
        $jalasat = array_values($jalasat);

        return array("bookdata" => $bookdata, "ostad" => $supplier["ostad"], "place" => $supplier["place"], "classdata" => $classdata, "jalasat" => $jalasat, "subjalasat" => $subjalasat);
    }

    //=========================================
    public function getClassDetail($id = 0, $return = 0, $group = 1)
    {
        try {
            $id = $id ? $id : (int)$this->input->post('id');
            if (!$id) {
                if ($return) {
                    return 'اطلاعاتی ارسال نشده';
                }
                throw new Exception('اطلاعاتی ارسال نشده', 1);
            }

            if (!$this->db->where('classid', $id)->count_all_results('dorehclass')) {
                if ($return) {
                    return "دوره کلاس $id حذف شده است";
                }
                throw new Exception("دوره کلاس $id حذف شده است", 2);
            }
            $base = CDN_URL;
            $books = array();
            $book_ids = array(0);
            $db = $this->db;
            $db->select('p.id,p.title,c.data_type,p.thumb,p.price');
            if ($group) {
                $db->group_by('p.id,p.title,c.data_type,p.thumb,p.price');
            }
            $bookdata = $db
                ->where('d.classid', $id)
                ->join('ci_classroom_data c', 'c.cid=d.classid', 'INNER', FALSE)
                ->join('ci_posts p', 'p.id = c.data_id', 'INNER', FALSE)
                ->get('dorehclass d')->result();
            foreach ($bookdata as $k => $v) {
                if ($v->thumb) {
                    $bookdata[$k]->price = intval($v->price);
                    $bookdata[$k]->sharh = intval($v->has_description) ? 1 : 0;
                    $bookdata[$k]->thumb = base_url() . $v->thumb;
                    $bookdata[$k]->cover300 = thumb($v->thumb, 300);
                }
                $book_ids[] = $v->id;
                $books[$v->id] = $bookdata[$k];
            }

            $O = $this->db->where("book_id IN (" . implode(",", $book_ids) . ")")->get('group')->result();
            $group = array();

            foreach ($O as $k => $v) {
                $group[$v->book_id][$v->id] = $v->name;
            }

            $O = $this->db->where("post_id IN (" . implode(",", $book_ids) . ")")->get('post_meta')->result();
            $post_meta = array();
            foreach ($O as $k => $v) {
                if ($v->meta_key == 'pages')
                    $post_meta[$v->post_id] = explode(",", $v->meta_value);
                if ($v->meta_key == 'price')
                    $books[$v->post_id]->price = intval($v->meta_value);
            }

            $dorehclass = $this->db
                ->select("dc.*")
                ->where('dc.classid', $id)
                ->get('dorehclass dc')->row();

            $O = $this->db
                ->select("a.*")
                ->where('a.id', $dorehclass->classid)
                ->get('classroom a')->row();
            $dorehclass->title = @$O->title;

            $doreh = $this->db
                ->select("a.*")
                ->where('a.id', $dorehclass->dorehid)
                ->get('doreh a')->row();
            $dorehclass->tahsili_year = $doreh ? $doreh->tahsili_year . "-" . ($doreh->tahsili_year + 1) : null;

            $O = $this->db
                ->select("a.*")
                ->where('a.id', $doreh->tecatid)
                ->get('tecat a')->row();
            $dorehclass->dorehtitle = @$O->name;

            $O = $this->db
                ->select("a.*")
                ->where('a.id', $dorehclass->ostadid)
                ->get('supplier a')->row();
            $dorehclass->ostadtitle = @$O->title;

            $O = $this->db
                ->select("a.*")
                ->where('a.id', $dorehclass->placeid)
                ->get('supplier a')->row();
            $dorehclass->placetitle = @$O->title;

            $startdate = date("Y-m-d", $dorehclass->startdate);
            $startdate = explode("-", $startdate);
            list($j_y, $j_m, $j_d) = gregorian_to_jalali($startdate[0], $startdate[1], $startdate[2]);
            $dorehclass->startdateshamsi = "$j_y-$j_m-$j_d";
            $dorehclass->image = $dorehclass->image ? $base . $dorehclass->image : null;

            $jalasats = array();
            $O =
                $this->db->select('j.*,d.audio,d.bookid,CEIL(d.audio_duration) duration,d.pages')
                    ->where('j.dorehclassid', $id)
                    ->where('j.published = 1')
                    ->join('ci_jalasat_data d', 'j.id=d.jid', 'INNER', FALSE)
                    ->order_by('j.startdate DESC')
                    //->group_by('j.id')
                    ->get('jalasat j')
                    ->result();

            $paragraph = array(0);
            $audio = array();
            $db = $this->db;
            foreach ($O as $k => $v) {
                $Z = explode(",", $v->pages);
                foreach ($Z as $zx => $zv)
                    $audio[$v->bookid][$zv] = $v->audio ? $base . $v->audio : null;
                $db->or_where("(book_id = $v->bookid AND order IN($v->pages))");
            }
            if (!count($O)) {
                $db->where('0');
            }

            $XO = $db->order_by('book_id,order')->get('book_meta')->result();

            $book_meta = array();
            $book_id = 0;
            $page = 1;
            $index = 0;
            foreach ($XO as $k => $v) {
                $pages = $post_meta[$v->book_id];
                if ($book_id != $v->book_id) {
                    $book_id = $v->book_id;
                    $page = 1;
                    $paragraphtitle = 1;
                }
                if (!$v->index) {
                    $v->index = $index;
                }
                $book_meta[$v->book_id][$v->order] = array($v->id, $v->index, $v->order, $page, $paragraphtitle, $audio[$v->book_id][$v->order]);
                $book_id = $v->book_id;
                $index = $v->index;
                $paragraphtitle++;
                if (in_array($v->order, $pages)) {
                    if ($v->order)
                        $page++;
                }
            }

            $fehresttitle = "";
            $fehrest = array();
            $paragraph = array();
            $FinalJalasat = array();
            foreach ($O as $k => $jalasat) {
                if (in_array($jalasat->id, $FinalJalasat))
                    continue;
                $startdate = date("Y-m-d", $jalasat->startdate);
                $startdate = explode("-", $startdate);
                list($j_y, $j_m, $j_d) = gregorian_to_jalali($startdate[0], $startdate[1], $startdate[2]);
                $jalasat->startdateshamsi = "$j_y-$j_m-$j_d";
                $pages = explode(",", $jalasat->pages);
                unset($jalasat->pages);
                foreach ($pages as $kp => $order) {
                    if (in_array($jalasat->id, $FinalJalasat))
                        continue;
                    $fehrestid = intval(@$book_meta[$jalasat->bookid][$order][1]);
                    $page = intval($book_meta[$jalasat->bookid][$order][3]);
                    $zaudio = $book_meta[$jalasat->bookid][$order][5];
                    $jalasat->page = $page;
                    if (isset($book_meta[$jalasat->bookid][$order][1])) {
                        $fehresttitle = $group[$jalasat->bookid][$book_meta[$jalasat->bookid][$order][1]];
                        $fehrest = array(
                            "id" => intval($book_meta[$jalasat->bookid][$order][1]),
                            "text" => $fehresttitle,
                            "page" => $page,
                            "audio" => $zaudio
                        );
                    } else {
                        continue;
                    }
                    $jalasats[$fehrestid]["fehrest"] = $fehrest;
                    $jalasats[$fehrestid]["book"] = $books[$jalasat->bookid];
                    $jalasats[$fehrestid]["data"][] = $jalasat;
                    $FinalJalasat[] = $jalasat->id;
                }
            }
            $temp = $jalasats;
            $jalasats = array();
            foreach ($temp as $k => $v) {
                $jalasats[] = $v;
            }
            $allbooks = array();
            foreach ($books as $k => $v) {
                $allbooks[] = $v;
            }
            $data = array('dorehclass' => $dorehclass, 'jalasat' => $jalasats, 'book' => $allbooks);
            if ($return) {
                return $data;
            }
            $this->tools->outS(0, 'OK', $data);
        } catch (Exception $e) {
            $this->tools->outE($e);
        }
    }

    //=========================================
    public function UpdateBookFehrests()
    {
        $rows = $this->db->get('posts')->result();
        if (!isset($_SESSION["UpdateBookFehrests"])) {
            $_SESSION["UpdateBookFehrests"] = array();
        }
        $j = 0;
        foreach ($rows as $k => $row) {
            if (!in_array($row->id, $_SESSION["UpdateBookFehrests"]) && $j < 10) {
                echo "ID : $row->id<br />";
                self::UpdateBookFehrest($row->id);
                $j++;
                $_SESSION["UpdateBookFehrests"][] = $row->id;
            }
        }
    }

    //=========================================
    public function UpdateBookFehrest($id)
    {
        $bm = $this->db
            ->select("bm.*")
            ->where('bm.paragraph', 0)
            ->where('bm.book_id', $id)
            ->get('book_meta bm')->row();
        if (!$bm) {
            return;
        }

        $pm = $this->db
            ->select("pm.*")
            ->where('pm.post_id', $id)
            ->where('pm.meta_key', 'pages')
            ->get('post_meta pm')->row();
        if (!$pm) {
            return;
        }
        $pages = explode(",", $pm->pages);

        $rows = $this->db->
        select('bm.id,bm.order,bm.index,bm.page,bm.paragraph')->
        order_by('bm.order')->
        where('bm.book_id', $id)->
        get('book_meta bm')->
        result();

        $pc = 0;
        $page = 1;
        $paragraph = 1;
        $fehrest = 0;
        foreach ($rows as $k => $v) {
            $kx = $k;
            $fehrest = $v->index ? $v->index : $fehrest;
            if (!$fehrest) {
                do {
                    $fehrest = intval($rows[$kx]->index);
                    $kx = $fehrest ? $kx : $kx + 1;
                } while ($fehrest == 0 && isset($rows[$kx]));
            }
            $pc = $pc ? $pc : array_shift($pages);
            $data = array(
                "page" => $page,
                "paragraph" => $paragraph,
                "fehrest" => $fehrest
            );
            $this->db->where('id', $v->id)->update('book_meta', $data);
            if ($pc <= $v->order) {
                $page++;
                $pc = 0;
                $paragraph = 0;
            }
            $paragraph++;
        }
    }

    //=========================================
    public function getClassJalasatDetail($id = 0, $return = 0)
    {
        try {
            $id = $id ? $id : (int)$this->input->post('id');
            if (!$id)
                throw new Exception('اطلاعاتی ارسال نشده', 1);

            if (!$this->db->where('id', $id)->count_all_results('dorehclass'))
                throw new Exception('این عرضه کننده حذف شده است', 2);
            $base = CDN_URL;
            $books = array();
            $book_ids = array(0);
            $db = $this->db;
            $db->select('p.id,p.title,c.data_type,p.thumb,p.price');
            $bookdata = $db
                ->where('d.id', $id)
                ->join('ci_classroom_data c', 'c.cid=d.classid', 'INNER', FALSE)
                ->join('ci_posts p', 'p.id = c.data_id', 'INNER', FALSE)
                ->get('dorehclass d')->result();
            foreach ($bookdata as $k => $v) {
                self::UpdateBookFehrest($v->id);
                $bookdata[$k]->price = intval($v->price);
                $bookdata[$k]->sharh = intval($v->has_description) ? 1 : 0;
                $bookdata[$k]->thumb = $v->thumb ? base_url() . $v->thumb : null;
                $bookdata[$k]->cover300 = $v->thumb ? thumb($v->thumb, 300) : null;
                $book_ids[] = $v->id;
                $books[$v->id] = $bookdata[$k];
            }
            $O = $this->db->where("book_id IN (" . implode(",", $book_ids) . ")")->where('book_id > 0')->order_by('book_id')->get('group')->result();
            $group = array();

            foreach ($O as $k => $v) {
                $group[$v->book_id][$v->id] = $v->name;
            }

            $O = $this->db->where("post_id IN (" . implode(",", $book_ids) . ")")->where('post_id > 0')->where("meta_key IN('price','pages')")->get('post_meta')->result();

            $post_meta = array();
            foreach ($O as $k => $v) {
                if ($v->meta_key == 'pages')
                    $post_meta[$v->post_id] = explode(",", $v->meta_value);
                if ($v->meta_key == 'price')
                    $books[$v->post_id]->price = intval($v->meta_value);
            }

            $dorehclass = $this->db
                ->select("dc.*")
                ->where('dc.id', $id)
                ->get('dorehclass dc')->row();

            $O = $this->db
                ->select("a.*")
                ->where('a.id', $dorehclass->classid)
                ->get('classroom a')->row();
            $dorehclass->title = @$O->title;

            $doreh = $this->db
                ->select("a.*")
                ->where('a.id', $dorehclass->dorehid)
                ->get('doreh a')->row();
            $dorehclass->tahsili_year = $doreh ? $doreh->tahsili_year . "-" . ($doreh->tahsili_year + 1) : null;

            $O = $this->db
                ->select("a.*")
                ->where('a.id', $doreh->tecatid)
                ->get('tecat a')->row();
            $dorehclass->dorehtitle = @$O->name;

            $O = $this->db
                ->select("a.*")
                ->where('a.id', $dorehclass->ostadid)
                ->get('supplier a')->row();
            $dorehclass->ostadtitle = @$O->title;

            $O = $this->db
                ->select("a.*")
                ->where('a.id', $dorehclass->placeid)
                ->get('supplier a')->row();
            $dorehclass->placetitle = @$O->title;

            $startdate = date("Y-m-d", $dorehclass->startdate);
            $startdate = explode("-", $startdate);
            list($j_y, $j_m, $j_d) = gregorian_to_jalali($startdate[0], $startdate[1], $startdate[2]);
            $dorehclass->startdateshamsi = "$j_y-$j_m-$j_d";
            $dorehclass->image = $dorehclass->image ? $base . $dorehclass->image : null;

            $audio = array();
            $jalasats = array();
            $db = $this->db;
            $db->select('j.*,sj.bookid,sj.duration,sj.paragraphid')
                ->where('j.dorehclassid', $id)
                ->where('j.published = 1')
                ->order_by('j.id,j.startdate DESC,sj.jalasatid,sj.bookid,sj.paragraphid');
            $db->join('ci_subjalasat sj', 'j.id=sj.jalasatid', 'INNER', FALSE);
            $db->select('sj.id sjid,sj.description sjdescription,sj.duration,sj.startPos,sj.endPos');
            $db->group_by('sj.id');
            $O = $db->get('jalasat j')->result();
            $results = array();
            foreach ($O as $k => $v) {
                $subjalasat = array(
                    "id" => $v->sjid,
                    "description" => $v->sjdescription,
                    "bookid" => $v->bookid,
                    "book" => $books[$v->bookid],
                    "duration" => $v->duration,
                    "paragraphid" => $v->paragraphid,
                    "startPos" => $v->startPos,
                    "endPos" => $v->endPos
                );
                if (!isset($results[$v->id][$v->bookid])) {
                    $JDO = $this->db
                        ->select("jd.*")
                        ->where('jd.jid', $v->id)
                        ->where('jd.bookid', $v->bookid)
                        ->get('jalasat_data jd')->row();
                    $v->image = $JDO ? $JDO->image : null;
                    $v->pdf = $JDO ? $JDO->pdf : null;
                    $v->video = $JDO ? $JDO->video : null;
                    $v->audio = $JDO ? $JDO->audio : null;
                    $v->pages = $JDO ? $JDO->pages : null;
                    $results[$v->id][$v->bookid] = array(
                        "id" => $v->id,
                        "dorehclassid" => $v->dorehclassid,
                        "title" => $v->title,
                        "startdate" => $v->startdate,
                        "starttime" => $v->starttime,
                        "description" => $v->description,
                        "image" => $v->image,
                        "pdf" => $v->pdf,
                        "audio" => $v->audio,
                        "video" => $v->video,
                        "pages" => $v->pages,
                        "subjalasecount" => $v->subjalase,
                        "subjalasat" => array($subjalasat)
                    );
                } else {
                    $results[$v->id][$v->bookid]["subjalasat"][] = $subjalasat;
                }
                if ($results[$v->id][$v->bookid]["pages"]) {
                    $vpages = explode(",", $results[$v->id][$v->bookid]["pages"]);
                    foreach ($vpages as $ka => $va) {
                        $audio[$v->bookid][$va] = $results[$v->id][$v->bookid]["audio"];
                    }
                }
            }

            $paragraph = array(0);
            $db = $this->db;
            foreach ($O as $k => $v) {
                $v->audio = $audio[$v->bookid][$v->paragraphid];
                $db->or_where("(book_id = $v->bookid AND order IN($v->paragraphid))");
            }
            if (!count($O)) {
                $db->where('0');
            }

            $XO = $db->order_by('book_id,order')->get('book_meta')->result();

            $book_meta = array();
            $book_id = 0;
            $page = 1;
            $index = 0;
            foreach ($XO as $k => $v) {
                $book_meta[$v->book_id][$v->order] = array("id" => $v->id, "index" => $v->index, "order" => $v->order, "page" => $page, "paragraph" => $v->paragraph, "fehrest" => $v->fehrest, "audio" => $audio[$v->book_id][$v->order]);
            }

            $fehresttitle = "";
            $fehrest = array();
            $paragraph = array();
            $FinalJalasat = array();
            foreach ($O as $k => $subjalasat) {
                $startdate = date("Y-m-d", $subjalasat->startdate);
                $startdate = explode("-", $startdate);
                list($j_y, $j_m, $j_d) = gregorian_to_jalali($startdate[0], $startdate[1], $startdate[2]);
                $subjalasat->startdateshamsi = "$j_y-$j_m-$j_d";

                $fehrestid = intval($book_meta[$subjalasat->bookid][$subjalasat->paragraphid]["fehrest"]);
                $page = intval($book_meta[$subjalasat->bookid][$subjalasat->paragraphid]["page"]);
                $zaudio = $book_meta[$subjalasat->bookid][$subjalasat->paragraphid]["audio"];
                $subjalasat->page = $page;
                if ($fehrestid) {
                    $fehresttitle = $group[$subjalasat->bookid][$fehrestid];

                    $jalasat = array(
                        "id" => $subjalasat->sjid,
                        "image" => $subjalasat->image,
                        "pdf" => $subjalasat->pdf,
                        "audio" => $subjalasat->audio,
                        "video" => $subjalasat->video,
                        "duration" => $subjalasat->duration,
                        "paragraphid" => $subjalasat->paragraphid,
                        "description" => $subjalasat->sjdescription,
                        "startPos" => $subjalasat->startPos,
                        "endPos" => $subjalasat->endPos,
                        "page" => $subjalasat->page
                    );
                    if (!isset($jalasats[$fehrestid])) {
                        $fehrest = array(
                            "id" => $fehrestid,
                            "text" => $fehresttitle,
                            "page" => $page,
                            "audio" => $zaudio
                        );
                        $jalaseh = array(
                            "id" => $subjalasat->id,
                            "title" => $subjalasat->title,
                            "startdate" => $subjalasat->startdate,
                            "startdateshamsi" => $subjalasat->startdateshamsi,
                            "starttime" => $subjalasat->starttime,
                            "description" => $subjalasat->description,
                            "dorehclassid" => $subjalasat->dorehclassid,
                            "subjalase" => $subjalasat->subjalase
                        );
                        $jalasats[$fehrestid]["fehrest"] = $fehrest;
                        $jalasats[$fehrestid]["book"] = $books[$subjalasat->bookid];
                        $jalasats[$fehrestid]["jalaseh"] = $jalaseh;
                    }
                    $jalasats[$fehrestid]["subjalasat"][] = $jalasat;
                }
            }
            $temp = $jalasats;
            $jalasats = array();
            foreach ($temp as $k => $v) {
                $jalasats[] = $v;
            }
            $allbooks = array();
            foreach ($books as $k => $v) {
                $allbooks[] = $v;
            }
            $data = array('dorehclass' => $dorehclass, 'book' => $allbooks, 'data' => $jalasats);
            if ($return) {
                return $data;
            }
            $this->tools->outS(0, 'OK', $data);
        } catch (Exception $e) {
            $this->tools->outE($e);
        }
    }

    //=========================================
    public function getBookDetail()
    {
        try {
            $user = $this->_loginNeed(TRUE, 'u.id');
            $uid = (int)@$user->id;
            $id = (int)$this->input->post('id');
            $limit = 20;
            if (!$id) {
                throw new Exception('اطلاعاتی ارسال نشده', 1);
            }

            if (!$this->db->where('id', $id)->where("type = 'book'")->count_all_results('posts')) {
                throw new Exception('این عرضه کننده حذف شده است', 2);
            }
            $baseurl = CDN_URL;

            $book = $this->db
                ->select("p.*")
                ->where('p.id', $id)
                ->get('posts p')->row();
            if ($book->thumb) {
		$book->thumb = str_replace('/lexoya/var/www/html/', '', $book->thumb);
		$book->thumb = CDN_URL . $book->thumb;
            }

            $category = $book->category;
            do {
                $categorydata = $this->db->select('*')->where('id', $category)->get('category')->row();
                if ($categorydata->parent) {
                    $category = $categorydata->parent;
                }
            } while ($categorydata->parent);
            $membership = [
                "category_id" => $categorydata->id,
                "name" => $categorydata->name,
                "membership1" => $categorydata->membership1, "discountmembership1" => $categorydata->discountmembership1,
                "membership3" => $categorydata->membership3, "discountmembership3" => $categorydata->discountmembership3,
                "membership6" => $categorydata->membership6, "discountmembership6" => $categorydata->discountmembership6,
                "membership12" => $categorydata->membership12, "discountmembership12" => $categorydata->discountmembership12
            ];

            $video = $this->db->where('book_id', $id)->where('NOT ISNULL(`video`)')->count_all_results('book_meta');
            $book->video = $video ? "1" : "0";

            $book->startdate = strtotime($book->date);

            $O = $this->db->select('m.*')
                ->where('m.post_id', $id)
                ->get('post_meta m')->result();
            $postmeta = ["allowmembership" => 0];
            foreach ($O as $k => $v) {
                $meta_value = $v->meta_value;
                if ($v->meta_key == 'pages') {
                    $pages = explode(',', $meta_value);
                    $book->pages = count($pages);
                } else {
                    $postmeta[$v->meta_key] = $meta_value;
                }
            }
            $book->postmeta = $postmeta;

            $O = $this->db->select('s.id,s.title')
                ->get('supplier s')->result();
            $suppliers = array();
            foreach ($O as $k => $v) {
                $suppliers[$v->id] = $v->title;
            }

            $O = $this->db->select('n.*')
                ->where('n.post_id', $id)
                ->get('post_nashr n')->result();

            $nashr = array("publisher" => array(), "writer" => array(), "translator" => array());

            foreach ($O as $k => $v) {
                $nashr[$v->nashr_key] = $v->nashr_value;
                if ($v->nashr_key == 'publishdate' && $v->nashr_value) {
                    list($jy, $jm, $jd) = explode('-', $v->nashr_value);
                    list($year, $month, $day) = jalali_to_gregorian($jy, $jm, $jd);
                    $x = strtotime("$year-$month-$day");
                    $nashr[$v->nashr_key] = $x;
                }
                if (in_array($v->nashr_key, array("publisher", "writer", "translator")) && strlen($v->nashr_value)) {
                    $nashr[$v->nashr_key] = $this->db->select("s.id,s.title,IF(LENGTH(s.image),CONCAT('" . $baseurl . "',s.image),NULL) image")
                        ->where("s.id IN($v->nashr_value)")
                        ->get('supplier s')->result();
                }
            }
            $book->nashr = $nashr;

            $fehrest = $this->db
                ->where('g.book_id', $id)
                ->get('group g')->result();
            $book->fehrest = $fehrest;
            $classroom_data = $this->db->select('c.*,cd.data_type')
                ->where('cd.data_id', $id)
                ->join('ci_classroom c', 'c.id=cd.cid', 'INNER', FALSE)
                ->get('classroom_data cd')->result();
            $class = array();
            foreach ($classroom_data as $k => $v) {
                $class[$v->id] = $v;
            }
            $dorehclass = array();
            $doreh = array();
            if (count($class)) {
                $ids = array_keys($class);
                $dorehclass = $this->db->select('dc.*,t.name dorehname')
                    ->order_by('dc.classid')
                    ->join('ci_doreh d', 'd.id=dc.dorehid', 'INNER', FALSE)
                    ->join('ci_tecat t', 't.id=d.tecatid', 'INNER', FALSE)
                    ->where("dc.classid IN(" . implode(",", $ids) . ")")
                    ->get('dorehclass dc')->result();

                foreach ($dorehclass as $k => $v) {
                    $dorehclass[$k]->classname = $class[$v->classid]->title;
                    $dorehclass[$k]->ostadname = @$suppliers[$v->ostadid];
                    $dorehclass[$k]->placename = @$suppliers[$v->placeid];
                    $doreh[$v->dorehid] = $v->dorehid;
                }

            }
            /*
			$O = $this->db
							->select('j.*,d.id dataid,d.image,d.pdf,d.audio,d.audio_duration,d.video,d.video_duration,s.id subjalasattid,s.paragraphid page,s.description sdescription')
							->join('ci_jalasat j','j.id=s.jalasatid','INNER',FALSE)
							->join('ci_jalasat_data d','d.jid=j.id','INNER',FALSE)
							->where('s.bookid',$id)
							->where('d.bookid',$id)
							->get('subjalasat s')
							->result();
			$subjalasat = array();
			foreach($O as $k0=>$v0){
						if($v0->audio && !$v0->audio_duration){
							include_once("mp3file.class.php");
							$mp3file = new MP3File(FCPATH.$v0->audio);
							$duration = $mp3file->getDuration();
							$data = array("audio_duration"=>$duration);
							$this->db->where('id',$v0->dataid)->update('jalasat_data',$data);
							$v0->audio_duration = $duration;
						}
						$subjalasat[$v0->page] = $v0;
			}
			$fehrestsubjalasat = array();
			foreach($fehrest as $k0=>$v0){
				if(isset($subjalasat[$v0->position])){
					$fehrestsubjalasat[$v0->position] = array_merge((array)$subjalasat[$v0->position],(array)$v0);
				}
			}
			foreach($fehrestsubjalasat as $k0=>$v0){
				$v0 = (object)$v0;
				$startdate = date("Y-m-d",$v0->startdate);
				$startdate = explode("-",$startdate);
				list($j_y,$j_m,$j_d)=gregorian_to_jalali($startdate[0],$startdate[1],$startdate[2]);
				$v0->startdateshamsi = "$j_y-$j_m-$j_d";
				$book->fehrestsubjalasat[] = $v0;
			}
			$bookdata = $this->db->select('p.id,p.title,c.data_type')
					->group_by('p.id')
					->where('d.id',$id)
					->join('ci_classroom_data c','c.cid=d.classid','INNER',FALSE)
					->join('ci_posts p','p.id = c.data_id','INNER',FALSE)
					->get('dorehclass d')->result();
			*/

            $same_mozoe = array();
            if (count($class)) {
                $ids = array_keys($class);
                $db = $this->db;

                $samembooks = $db->select('p.id,p.title,p.price,p.has_description,p.thumb')->
                join('ci_classroom_data d', 'p.id=d.data_id', 'inner', FALSE)
                    ->order_by('p.title')
                    ->where("p.published = 1")
                    ->where("d.cid IN(" . implode(",", $ids) . ")")
                    ->where("p.id != $id")
                    ->where("p.type = 'book'")
                    ->group_by("p.id")
                    ->get('posts p')->result();

                $data['samembook'] = array();//'sql'=>$db->queries
                foreach ($samembooks as $k => $v) {
                    $data['samembook'][$k]["id"] = $v->id;
                    $data['samembook'][$k]["title"] = $v->title;
                    $data['samembook'][$k]["price"] = intval($v->price);
                    $data['samembook'][$k]["sharh"] = intval($v->has_description) ? "1" : "0";
                    $data['samembook'][$k]["thumb"] = $v->thumb ? $baseurl . $v->thumb : null;
                    $data['samembook'][$k]["cover300"] = $v->thumb ? $baseurl . thumb($v->thumb, 300) : null;
                }
                $same_mozoe = $data['samembook'];
            }

            $same_onvan = array();
            if (count($doreh)) {
                $db = $this->db;
                $O = $db->select('c.data_id,d.*,c.data_type')
                    ->where("d.dorehid IN(" . implode(",", $doreh) . ")")
                    ->join('ci_classroom_data c', 'c.cid=d.classid', 'inner', FALSE)->get('dorehclass d')->result();
                $books = array(0);
                foreach ($O as $k => $v) {
                    $books[$v->data_id] = $v->data_id;
                }
                $samembooks = $db->select('p.id,p.title,p.price,p.has_description,p.thumb')
                    ->order_by('p.title')
                    ->where("p.published = 1")
                    ->where("p.id != $id")
                    ->where("p.id IN(" . implode(",", $books) . ")")
                    ->where("p.type = 'book'")
                    ->limit($limit)
                    ->get('posts p')->result();

                $data['samembook'] = array();//'sql'=>$db->queries
                foreach ($samembooks as $k => $v) {
                    $data['samembook'][$k]["id"] = $v->id;
                    $data['samembook'][$k]["title"] = $v->title;
                    $data['samembook'][$k]["price"] = intval($v->price);
                    $data['samembook'][$k]["sharh"] = intval($v->has_description) ? "1" : "0";
                    $data['samembook'][$k]["thumb"] = $v->thumb ? $baseurl . $v->thumb : null;
                    $data['samembook'][$k]["cover300"] = $v->thumb ? $baseurl . thumb($v->thumb, 300) : null;
                }
                $same_onvan = $data['samembook'];
            }
            
            $classonlines = $this->db->select('cid')
                ->where_in('data_type', ['book', 'hamniaz'])
                ->where('data_id', $id)
                ->get('classonline_data')
                ->result();

            $classonline_ids = [0];
            foreach ($classonlines as $classonline) {
                $classonline_ids[$classonline->cid] = $classonline->cid;
            }
            $classonlines = $this->db->select('*')
                ->where_in('data_type', ['dayofweek'])
                ->where_in('cid', $classonline_ids)
                ->order_by('cid,dayofweek,starttime')
                ->get('classonline_data')
                ->result();
            $dayofweeks = [
                0 => "شنبه",
                1 => "یکشنبه",
                2 => "دوشنبه",
                3 => "سه شنبه",
                4 => "چهارشنبه",
                5 => "پنجشنبه",
                6 => "جمعه"
            ];

            $classonline_dayofweeks = [];
            foreach ($classonlines as $key => $classonline) {
                if ($classonline->data_type == "dayofweek") {
                    $classonline_dayofweeks[$classonline->cid][] = ["dayofweek" => $classonline->dayofweek, "dayname" => $dayofweeks[$classonline->dayofweek], "starttime" => $classonline->starttime, "endtime" => $classonline->endtime];
                }
            }

            $this->db->select('c.id AS value,c.displayname as text');
            $this->db->where_in('c.id');
            $teachers = $this->db->get('users c')->result();

            $tempteachers = [];
            foreach ($teachers as $teacher) {
                $tempteachers[$teacher->value] = $teacher;
            }
            $teachers = $tempteachers;

            $classonlines = $this->db->select('*')
                ->where_in('id', $classonline_ids)
                ->get('classonline')
                ->result();

            foreach ($classonlines as $key => $classonline) {
                if (isset($classonline->teachername) && isset($teachers[$classonline->teachername])) {
                    $classonlines[$key]->teachername = $teachers[$classonline->teachername];
                } else {
                    $classonlines[$key]->teachername = null; // Set to null if teacher is not found
                }
                $classaccount = $this->db->where('user_id', 0)->where('classonline_id', $classonline->id)->count_all_results('classaccount');
                $classonlines[$key]->capacity = $classaccount;
                $classonlines[$key]->program = $classonline_dayofweeks[$classonline->id];
            }

            $book->classonline = $classonlines;

            $classrooms = $this->db->select('cid')
                ->where_in('data_type', ['book', 'hamniaz'])
                ->where('data_id', $id)
                ->get('classroom_data')
                ->result();

            $classroom_ids = [0];
            foreach ($classrooms as $classroom) {
                $classroom_ids[$classroom->cid] = $classroom->cid;
            }

            $classrooms = $this->db->select('*')
                ->where_in('id', $classroom_ids)
                ->get('classroom')
                ->result();


            $book->classroom = $classrooms;
            $dayofweek = [
                0 => "شنبه",
                1 => "یکشنبه",
                2 => "دوشنبه",
                3 => "سه شنبه",
                4 => "چهارشنبه",
                5 => "پنجشنبه",
                6 => "جمعه"
            ];


            $userbook = $this->db->select('*,UNIX_TIMESTAMP(expiremembership) AS ExpTime')
                ->where('book_id', $id)
                ->where('user_id', $uid)
                ->where('(ISNULL(expiremembership) OR (NOT ISNULL(expiremembership) AND expiremembership > CURDATE()))')
                ->get('user_books')
                ->row();
                
            $this->tools->outS(0, 'OK', 
                [
                    "book" => $book,
                    "dayofweek" => $dayofweek,
                    "classroom_data" => $classroom_data,
                    "dorehclass" => $dorehclass,
                    "same_mozoe" => $same_mozoe,
                    "same_onvan" => $same_onvan,
                    "membership" => $membership,
                    "userbook" => $userbook
                ]
            );
        } catch (Exception $e) {
            $this->tools->outE($e);
        }
    }

    public function ema_getBookClassOnlines()
    {
        try {
            // Check if the user is logged in
            $user = $this->_loginNeed(TRUE, 'u.id');
            if (!$user) {
                throw new Exception('User authentication required', 401);
            }

            $id = (int)$this->input->post('id');
            if (!$id) {
                throw new Exception('Book ID is required', 1);
            }

            // Retrieve related classonlines IDs
            $classonlines = $this->db->select('cid')
                ->where_in('data_type', ['book', 'hamniaz'])
                ->where('data_id', $id)
                ->get('classonline_data')
                ->result();

            $classonline_ids = [];
            foreach ($classonlines as $classonline) {
                $classonline_ids[] = $classonline->cid;
            }

            if (empty($classonline_ids)) {
                return $this->tools->outS(0, 'No classonlines found', ['classonlines' => []]);
            }

            // Fetch classonlines based on retrieved IDs
            $classonlines = $this->db->select('*')
                ->where_in('id', $classonline_ids)
                ->get('classonline')
                ->result();

            // Prepare day-of-week mapping
            $dayofweeks = [
                0 => "شنبه",
                1 => "یکشنبه",
                2 => "دوشنبه",
                3 => "سه شنبه",
                4 => "چهارشنبه",
                5 => "پنجشنبه",
                6 => "جمعه"
            ];

            // Fetch and organize schedule data
            $classonline_schedule = $this->db->select('*')
                ->where_in('data_type', ['dayofweek'])
                ->where_in('cid', $classonline_ids)
                ->order_by('cid, dayofweek, starttime')
                ->get('classonline_data')
                ->result();

            $classonline_dayofweeks = [];
            foreach ($classonline_schedule as $schedule) {
                $classonline_dayofweeks[$schedule->cid][] = [
                    "dayofweek" => $schedule->dayofweek,
                    "dayname" => $dayofweeks[$schedule->dayofweek],
                    "starttime" => $schedule->starttime,
                    "endtime" => $schedule->endtime
                ];
            }

            // Fetch teacher names
            $teacher_ids = array_unique(array_column($classonlines, 'teachername'));
            if (!empty($teacher_ids)) {
                $teachers = $this->db->select('c.id AS value, c.displayname AS text')
                    ->where_in('c.id', $teacher_ids)
                    ->get('users c')
                    ->result();

                $tempteachers = [];
                foreach ($teachers as $teacher) {
                    $tempteachers[$teacher->value] = $teacher->text;
                }
                $teachers = $tempteachers;
            } else {
                $teachers = [];
            }

            // Attach teacher names and schedules to classonlines
            foreach ($classonlines as $key => $classonline) {
                $classonlines[$key]->teachername = $teachers[$classonline->teachername] ?? null;
                $classonlines[$key]->program = $classonline_dayofweeks[$classonline->id] ?? [];
            }

            // Return the filtered classonlines
            return $this->tools->outS(0, 'OK', ['classonlines' => $classonlines]);

        } catch (Exception $e) {
            return $this->tools->outE($e);
        }
    }


    //=========================================
    public function extlogin()
    {
        $user = $this->_loginNeed(TRUE, 'u.id');

        if ($user === FALSE)
            throw new Exception("برای دسترسی به این بخش باید وارد حساب کاربری خود شوید", -1);

        $this->load->library('form_validation');
        $this->load->library('myformvalidator');
        $this->form_validation->set_rules('Message', 'کد احراز هویت', 'trim|required|numeric');
        if ($this->form_validation->run() == FALSE)
            throw new Exception(implode('|', $this->form_validation->error_array()), 1);

        $post = $this->input->post();

        $this->db->select('*');
        $this->db->where('id', $user->id);
        $user = $this->db->get('users', 1)->row();
        $code = $user->code == $post["Message"];

        $message = $code ? "OK" : "کد ارسالی صحیح نیست";
        $status = $code ? 0 : 1;
        $re = array();
        if (!$status) {
            $data = array('forcelogout' => 0);
            $this->db->where('user_id', $user->id)->limit(1)->order_by('date', 'DESC')->update('user_mobile', $data);
            $this->ForceLogOut(1);
            $this->db->where('user_id', $user->id)->delete('logged_in');
            $mac = $user->username;
            $token = $this->input->post('token');
            $this->db->insert('logged_in', [
                'user_id' => $user->id,
                'mac' => $mac,
                'token' => $token,
                'date' => time()
            ]);


            $user->fullname = $user->displayname;
            $re = array(
                'user' => $user,
            );
        }
        $this->tools->outS($status, $message, array("data" => $re));
    }

    //=========================================
    public function buyBookBazar()
    {
        $user = $this->_loginNeed();

        if ($user === FALSE) {
            throw new Exception("برای دسترسی به این بخش باید وارد حساب کاربری خود شوید", -1);
        }

        $action = $this->input->post('action');
        $ref_id = $this->input->post('ref_id');
        $bookid = (int)$this->input->post('bookid');

        if (!$bookid) {
            throw new Exception("شماره کتاب الزامی است", 1);
        }

        if (!strlen($action)) {
            throw new Exception("کد فعالیت الزامی می باشد", 1);
        }

        if (!strlen($ref_id)) {
            throw new Exception("کد رهگیری الزامی می باشد", 1);
        }

        $price = 0;
        $owner = 0;

        $this->db->select("p.id,p.title,c.name,m.meta_value price");
        $this->db->join('ci_category c', 'p.category=c.id', 'left', FALSE);
        $this->db->order_by('p.title', 'asc');
        $this->db->join('ci_post_meta m', 'm.post_id=p.id', 'left', FALSE);
        $this->db->where("m.meta_key = 'price'");
        $this->db->where("m.meta_value != '0'");
        $this->db->where('p.id', $bookid);
        $result = $this->db->get('posts p')->result();

        if (!count($result)) {
            throw new Exception("شماره کتاب ارسالی صحیح نیست", 1);
        }
        foreach ($result as $v) {
            $price += $v->price;
        }

        $discountCode = $this->input->post('code');

        $discount_id = 0;
        $category_id = -1;

        $this->load->model('m_book', 'book');
        if ($discountCode) {
            $discount_id = (int)$this->book->checkDiscountCode($discountCode, $category_id, $user->id, $bookid);
        }

        $cf = $this->book->createFactor($user->id, $bookid, NULL, $discount_id, $owner);

        if ($cf['done'] == FALSE) {
            throw new Exception($cf['msg'], 5);
        }

        $factor = $cf['factor'];
        $data = ['factor' => $factor];

        $data['link'] = site_url('payment/paybook/' . $factor->id);

        if (in_array($action, ["bazar", "myket"]) && $ref_id) {
            $factor->state = 'پرداخت موفق';
            $factor->status = 0;
            $factor->pdate = $factor->cdate;
            $factor->paid = $factor->price;
            $factor->ref_id = $action . ":" . $ref_id;
            $this->book->updatetFactor($factor->id, [
                'state' => $factor->state,
                'status' => $factor->status,
                'pdate' => $factor->pdate,
                'paid' => $factor->paid,
                'ref_id' => $factor->ref_id
            ]);
            $data = [];
            $data['factor'] = $factor;
            $data['link'] = '';
        }
        $this->tools->outS(0, "فاکتور ایجاد شد", ['data' => $data]);
    }

    //=========================================
    public function buyClass()
    {
        $user = $this->_loginNeed();

        if ($user === FALSE)
            throw new Exception("برای دسترسی به این بخش باید وارد حساب کاربری خود شوید", -1);

        $dorehclassid = (int)$this->input->post('dorehclassid');
        $dorehid = (int)$this->input->post('dorehid');

        if (!$dorehclassid && !$dorehid)
            throw new Exception("شماره دوره یا شماره دوره کلاس الزامی است", 1);

        $price = 0;

        if ($dorehclassid) {
            $price = $this->db->where('id', $dorehclassid)->select('price')->get('dorehclass', 1)->row();
        } elseif ($dorehid) {
            $O = $this->db->where('dorehid', $dorehid)->select('price')->get('dorehclass')->result();
            foreach ($O as $v)
                $price += $v->price;
        }

        $owner = 0;

        $this->load->model('m_doreh', 'doreh');

        if ($dorehid) {
            if ($this->db->where('dorehid', $dorehid)->count_all_results('dorehclass') == 0)
                throw new Exception("شماره دوره صحیح نمی باشد", 2);

            if ($this->db->where('dorehid', $dorehid)->where('published', 1)->count_all_results('dorehclass') == 0)
                throw new Exception("این دوره درحال حاضر در دسترس نیست", 3);

            if ($this->doreh->isBought($user->id, $dorehid))
                throw new Exception("دوره قبلا خریداری شده است", 4);

            $discountCode = $this->input->post('code');
            $discount_id = (int)$this->doreh->checkDiscountCode($discountCode, -5, $user->id, $dorehid);
            $discount_id = $discount_id ? $discount_id : (int)$this->doreh->checkDiscountCode($discountCode, -2, $user->id);
            $cf = $this->doreh->createFactor($user->id, $dorehid, NULL, $discount_id, $owner);
        } else {
            if ($this->db->where('id', $dorehclassid)->count_all_results('dorehclass') == 0)
                throw new Exception("شماره دوره کلاس صحیح نمی باشد", 2);
            if ($this->db->where('id', $dorehclassid)->where('published', 1)->count_all_results('dorehclass') == 0)
                throw new Exception("این دوره کلاس درحال حاضر در دسترس نیست", 3);

            $discountCode = $this->input->post('code');
            $discount_id = (int)$this->doreh->checkDiscountCode($discountCode, -6, $user->id, $dorehid);
            $cf = $this->doreh->createFactor($user->id, NULL, $dorehclassid, $discount_id, $owner);
        }

        if ($cf['done'] == FALSE) {
            throw new Exception($cf['msg'], 5);
        }

        $factor = $cf['factor'];
        $data = ['factor' => $factor];

        if ($factor->price == 0) {
            $this->doreh->updatetFactor($factor->id, [
                'state' => $discount_id != NULL ? "خرید کامل با کد تخفیف (<span class=\"text-warning\">{$discountCode}</span>)" : 'رایگان',
                'status' => 0,
                'pdate' => time()
            ]);

            if ($discount_id != NULL)
                $this->doreh->setDiscountUsed($discount_id, $factor->id);

            $data['free'] = TRUE;
            $data['link'] = NULL;

        } else
            $data['link'] = site_url('payment/paydorehclass/' . $factor->id);
        $this->tools->outS(0, "فاکتور ایجاد شد", ['data' => $data]);
    }

    //=========================================
    public function userClasses()
    {
        try {
            $user_id = NULL;
            $user = $this->_loginNeed(TRUE, 'u.id');

            if ($user === FALSE)
                throw new Exception("برای دسترسی به این بخش باید وارد حساب کاربری خود شوید", -1);

            $uid = $user->id;

            if (!$this->db->where('user_id', $uid)->count_all_results('classfactors'))
                throw new Exception('در حال حاظر شما در هیچ کلاسی ثبت نام نکرده اید', 2);

            $O = $this->db
                ->select("c.*")
                ->where('c.status', 0)
                ->where('c.user_id', $uid)
                ->get('classfactors c')->result();

            $classfactors = array();
            $factors = array(0);
            foreach ($O as $k => $v) {
                $classfactors[$v->id] = $v;
                $factors[$v->id] = $v->id;
            }

            $dorehclassid = array();
            $O = $this->db
                ->select("u.*")
                ->where("u.factor_id IN(" . implode(",", $factors) . ")")
                ->get('classfactor_detail u')->result();
            foreach ($O as $k => $v) {
                $data = self::getClassDetail($v->class_id, 1);
                if (count($data))
                    $dorehclassid[] = $data;
            }

            $this->tools->outS(0, 'OK', array('dorehclasses' => $dorehclassid));
        } catch (Exception $e) {
            $this->tools->outE($e);
        }
    }

    //=========================================
    public function getSupplier($id = 0)
    {
        try {
            $data = $this->input->post();
            $baseurl = CDN_URL;
            $limit = (int)$this->input->post('limit');
            $limitstart = (int)$this->input->post('limitstart');
            $id = intval($id) ? $id : $data["id"];

            if (!$id)
                throw new Exception('اطلاعاتی ارسال نشده', 1);

            if (!$this->db->where('id', $id)->count_all_results('supplier'))
                throw new Exception('موردی یافت نشد', 2);


            $supplier = $this->db->where('id', $id)->get('supplier')->row();
            $supplier->image = $supplier->image ? $baseurl . $supplier->image : null;

            $data = array();

            $db = $this->db;
            $db->where("d.placeid", $id);
            $db->or_where("d.ostadid", $id);
            $O = $db->select('d.*,c.data_id')->join('ci_classroom_data c', 'c.cid=d.classid', 'inner', FALSE)->get('dorehclass d')->result();
            $bookids = array(0);
            $dorehclass = array();
            foreach ($O as $k => $v) {
                $bookids[$v->data_id] = $v->data_id;
                if ($v->ostadid == $id) {
                    $v->action = 'استاد';
                } else {
                    $v->action = 'مکان برگزاری';
                }

                $data["dorehclass"][$v->id] = $v;
            }

            $data["dorehclass"] = array_values($data["dorehclass"]);

            $O = $db->select('st.*')->where("sr.sup_id", $id)->join('ci_suppliertype st', 'sr.type_id=st.id', 'inner', FALSE)->get('supplierrules sr')->result();
            $supplierrules = array();
            foreach ($O as $k => $v) {
                $supplierrules[] = array($v->id, $v->title);
            }

            $supplier->rules = $supplierrules;

            $db->where("p.published = 1");
            $db->where("p.id IN(" . implode(",", $bookids) . ")");
            $db->where("p.type = 'book'");
            $books = $db->order_by('title', 'desc')->limit($limit, $limitstart)->get('posts p')->result();

            $db->where("p.published = 1");
            $db->where("p.id IN(" . implode(",", $bookids) . ")");
            $db->where("p.type = 'book'");
            $count = $db->select('p.id')->count_all_results('posts p');
            $data['books'] = array();
            foreach ($books as $k => $v) {
                $data['books'][$k]["id"] = $v->id;
                $data['books'][$k]["title"] = $v->title;
                $data['books'][$k]["price"] = intval($v->price);
                $data['books'][$k]["sharh"] = intval($v->has_description) ? 1 : 0;
                $data['books'][$k]["thumb"] = $v->thumb ? $baseurl . $v->thumb : null;
                $data['books'][$k]["cover300"] = $v->thumb ? thumb($v->thumb, 300) : null;
            }

            $this->tools->outS(0, 'OK', ["supplier" => $supplier, "data" => $data]);
        } catch (Exception $e) {
            $this->tools->outE($e);
        }
    }

    //=========================================
    public function getSuppliers()
    {
        try {
            $data = $this->input->post();
            $base = CDN_URL;
            $offer = intval($data["offer"]);
            $db = $this->db;
            if ($offer) {
                $db->where('offer', $offer - 1);
            }

            $suppliers = $db->get('supplier')->result();

            if (!count($suppliers))
                throw new Exception('موردی یافت نشد', 2);

            foreach ($suppliers as $k => $supplier) {
                $suppliers[$k]->image = $supplier->image ? $base . $supplier->image : null;
            }

            $this->tools->outS(0, 'OK', ["data" => $suppliers]);
        } catch (Exception $e) {
            $this->tools->outE($e);
        }
    }

    //=========================================
    public function getDoreh($id = 0)
    {
        //offer
        try {
            $data = $this->input->post();
            $baseurl = CDN_URL;
            $id = intval($data["id"]);
            $offer = intval($data["offer"]);

            $limit = (int)$this->input->post('limit');
            $limitstart = (int)$this->input->post('limitstart');

            if (!$id)
                throw new Exception('اطلاعاتی ارسال نشده', 1);

            if (!$this->db->where('supplierid', $id)->count_all_results('doreh'))
                throw new Exception('موردی یافت نشد', 2);

            $db = $this->db;
            $data = array();

            switch ($offer) {
                case 0:
                    $db->where('d.supplierid', $id)->join('ci_doreh d', 't.id=d.tecatid', 'inner', FALSE);
                    $db->order_by('t.name', 'asc');
                    break;
                case 1:
                    $db->where('d.supplierid', $id)->join('ci_doreh d', 't.id=d.tecatid', 'inner', FALSE);
                    $db->order_by('d.tahsili_year', 'DESC');
                    $db->order_by('d.id', 'DESC');
                    break;
                case 2:
                    $O = $db->select('f.dorehid,SUM(f.price) p')->join('ci_doreh d', 'd.id=f.dorehid', 'inner', FALSE)->where('d.supplierid', $id)->group_by('f.dorehid')->order_by('f.price', 'desc')->get('classfactor_detail f')->result();
                    $order = array("", "CASE d.id");
                    foreach ($O as $k => $v) {
                        $order[] = "WHEN $v->dorehid THEN $k";
                    }
                    $order[] = "END";
                    $db->where('d.supplierid', $id)->join('ci_doreh d', 't.id=d.tecatid', 'inner', FALSE);
                    if (count($O)) {
                        $order = implode("\n", $order);
                        $db->order_by($order, false, false);
                    }
                    break;
                case 3:
                    $db->where('d.supplierid', $id)->join('ci_doreh d', 't.id=d.tecatid', 'inner', FALSE);
                    $db->order_by('d.offer', 'DESC');
                    break;
            }
            $db->select('t.name,d.*');
            $db->limit($limit, $limitstart);
            $dorehs = $db->get('tecat t')->result();

            $data['doreh'] = array();
            $data['dorehclass'] = array();

            $dorehid = array(0);
            $classid = array(0);
            $ostadid = array(0);
            $placeid = array(0);

            foreach ($dorehs as $k => $v) {
                $data['doreh'][$v->id]["id"] = $v->id;
                $data['doreh'][$v->id]["title"] = $v->name;
                $data['doreh'][$v->id]["image"] = $v->image ? $baseurl . $v->image : null;
                $data['doreh'][$v->id]["year"] = $v->tahsili_year . '-' . ($v->tahsili_year + 1);
                $data['doreh'][$v->id]["price"] = 0;
                $db->where('d.dorehid', $v->id);
                $dorehclasss = $db->join('ci_classroom c', 'c.id=d.classid', 'inner', FALSE)->select('d.*,pdate(FROM_UNIXTIME(d.startdate)) AS `shamsidate`')->order_by('id', 'desc')->limit($limit, $limitstart)->get('dorehclass d')->result();
                foreach ($dorehclasss as $kd => $vd) {
                    $data['dorehclass'][$kd]["id"] = $vd->id;
                    $data['dorehclass'][$kd]["doreh"] = $data['doreh'][$vd->dorehid];
                    $data['dorehclass'][$kd]["ostad"] = $data['ostad'][$vd->ostadid];
                    $data['dorehclass'][$kd]["place"] = $data['place'][$vd->placeid];
                    $data['dorehclass'][$kd]["classroom"] = $data['classroom'][$vd->classid];
                    $data['dorehclass'][$kd]["jalasat"] = $vd->jalasat;
                    $data['dorehclass'][$kd]["startdate"] = $vd->shamsidate;
                    $data['dorehclass'][$kd]["image"] = $vd->image ? $baseurl . $vd->image : null;
                    $data['doreh'][$v->id]["price"] += $vd->price;
                    $dorehid[$vd->dorehid] = $vd->dorehid;
                    $classid[$vd->classid] = $vd->classid;
                    $ostadid[$vd->ostadid] = $vd->ostadid;
                    $placeid[$vd->placeid] = $vd->placeid;
                }
            }

            $ostads = $this->db->order_by('title', 'asc')->where('id IN (' . implode(',', $ostadid) . ')')->get('supplier')->result();
            $data['ostad'] = array();
            foreach ($ostads as $k => $v) {
                $data['ostad'][$v->id] = array("id" => $v->id, "title" => $v->title);
            }

            $data['ostad'] = array_values($data['ostad']);
            $data['doreh'] = array_values($data['doreh']);
            $this->tools->outS(0, 'OK', ["data" => $data]);
        } catch (Exception $e) {
            $this->tools->outE($e);
        }
    }

    //=========================================
    public function getAdvertise($priority = 0)
    {
        try {
            $baseurl = CDN_URL;
            $data = $this->input->post();
            $priority = intval($priority) ? $priority : $data["priority"];

            if ($priority)
                $advertise = $this->db->where('priority', $priority)->get('advertise')->result();
            else
                $advertise = $this->db->limit(10)->order_by('showed/priority', 'asc', false)->get('advertise')->result();

            if (!count($advertise)) {
                throw new Exception('موردی یافت نشد', 2);
            }
            foreach ($advertise as $k => $v) {
                $original_url = $baseurl . $v->image;
                $new_url = preg_replace('/\/lexoya\/.*?\/uploads/', 'uploads', $original_url);
                $advertise[$k]->image = $v->image ? $new_url : null;
                if ($v->section) {
                    $out = new stdClass();
                    $text["category"] = "دسته بندی";
                    $text["classonline"] = "کلاس آنلاین";
                    $text["classroom"] = "کلاس عادی";
                    $text["tecat"] = "دسته بندی عنوانی";
                    $text["supplier"] = "عرضه کنندگان";
                    $text["membership"] = "اشتراک";
                    $text["link"] = "آدرس وب";
                    if (intval($v->link)) {
                        $section = $v->section;
                        if ($section == "membership") {
                            $section = "category";
                        }
                        switch ($section) {
                            case "tecat":
                            case "category":
                                $select = "id AS value,name AS text,pic,icon";
                                break;
                            case "classroom":
                            case "supplier":
                            case "membership":
                            case "post":
                            case "classonline":
                                $select = "id AS value,title AS text,pic,icon";
                                break;
                        }
                        $this->db->select($select);
                        $this->db->where("id", $v->link);
                        $out = (object)$this->db->get($section)->row();
                    } else {
                        $out->id = 0;
                        $out->text = $v->link;
                    }
                }
                $out->section = $v->section;
                $out->sectiontitle = $text[$v->section];
                $advertise[$k]->data = $out;
                $data = array();
                $data["showed"] = $v->showed + 1;
                $this->db->where('id', $v->id)->update('advertise', $data);
            }
            $this->tools->outS(0, 'OK', ["data" => $advertise]);
        } catch (Exception $e) {
            $this->tools->outE($e);
        }
    }

    //=========================================
    public function getMembership($id = 0)
    {
        try {
            $baseurl = CDN_URL;

            $limit = (int)$this->input->post('limit');
            $limitstart = (int)$this->input->post('limitstart');

            $db = $this->db;
            $data = array();

            $db->select('m.*');
            $db->where("published", 1);
            $db->limit($limit, $limitstart);
            $data['membership'] = $db->get('membership m')->result();

            $this->tools->outS(0, 'OK', ["data" => $data]);
        } catch (Exception $e) {
            $this->tools->outE($e);
        }
    }

    //=========================================
    public function buyMembership()
    {
        $user = $this->_loginNeed();

        if ($user === FALSE)
            throw new Exception("برای دسترسی به این بخش باید وارد حساب کاربری خود شوید", -1);

        $membership_id = (int)$this->input->post('membership_id');
        if (!$membership_id)
            throw new Exception("شماره دوره یا شماره دوره کلاس الزامی است", 1);

        $membership = $this->db->where('id', $membership_id)->where('published', 1)->select('price')->get('membership', 1)->row();

        $price = 0;
        if (isset($membership->price)) {
            $price = $membership->price;
        }

        $this->load->model('m_membership', 'membership');

        if ($this->membership->isBought($user->id, $membership_id))
            throw new Exception("اشتراک قبلا خریداری شده است", 3);

        $discountCode = $this->input->post('code');
        $discount_id = 0;
        if ($discountCode) {
            $discount_id = (int)$this->membership->checkDiscountCode($discountCode, -7, $user->id);
        }
        $cf = $this->membership->createFactor($user->id, $membership_id, $discount_id);

        if ($cf['done'] == FALSE) {
            throw new Exception($cf['msg'], 5);
        }

        $factor = $cf['factor'];
        $data = ['factor' => $factor];

        if ($factor->price == 0) {
            $this->membership->updatetFactor($factor->id, [
                'state' => $discount_id != NULL ? "خرید کامل با کد تخفیف (<span class=\"text-warning\">{$discountCode}</span>)" : 'رایگان',
                'status' => 0,
                'pdate' => time()
            ]);

            if ($discount_id != NULL)
                $this->membership->setDiscountUsed($discount_id, $factor->id);

            $data['free'] = TRUE;
            $data['link'] = NULL;

        } else {
            $data['link'] = site_url('payment/paymembership/' . $factor->id);
        }
        $this->tools->outS(0, "فاکتور ایجاد شد", ['data' => $data]);
    }

    //=========================================
    public function getCategories($id = 0)
    {
        try {
            $baseurl = CDN_URL;

            $limit = (int)$this->input->post('limit');
            $limitstart = (int)$this->input->post('limitstart');

            $db = $this->db;
            $data = array();

            $db->select('c.*');
            $db->limit($limit, $limitstart);
            $data['categories'] = $db->get('category c')->result();

            $this->tools->outS(0, 'OK', ["data" => $data]);
        } catch (Exception $e) {
            $this->tools->outE($e);
        }
    }

    //=========================================
    public function buyCategory()
    {
        $user = $this->_loginNeed();

        if ($user === FALSE)
            throw new Exception("برای دسترسی به این بخش باید وارد حساب کاربری خود شوید", -1);

        $phone_number = (int)$this->input->post('mac');
        $category_id = $this->input->post('category_id');
        $category_id = explode(",", $category_id);
        $plan_id = $this->input->post('plan_id');
        $plan_id = explode(",", $plan_id);
        if (!count($category_id) || !count($plan_id)) {
            throw new Exception("شماره دسته بندی یا شماره پلن عضویت الزامی است", 1);
        }

        $this->load->model('m_category', 'category');
        
        // if ($this->category->isBought($user->id, $category_id, $plan_id)) {
        //     $data = $this->db
        //         ->where_in('cat_id', $category_id)
        //         ->where('enddate > NOW()')
        //         ->order_by('enddate DESC')
        //         ->get("user_catmembership")->row();
        //     $this->tools->outS(5, "اشتراک قبلا خریداری شده است", ['data' => $data]);
        //     //throw new Exception("اشتراک قبلا خریداری شده است", 5);
        // } else {
            $discountCode = $this->input->post('code');
            $discount_id = 0;
            if ($discountCode) {
                $discount_id = $this->category->checkDiscountCode($discountCode, "-8", $plan_id, $category_id, $user->id);
            }
            if (!isset($discount_ids["allowed"])) {
                $discount_ids = [];
            } else {
                $discount_ids = $discount_ids["allowed"];
            }
            $cf = $this->category->createFactor($user->id, $category_id, $plan_id, $discount_id);
    
            if ($cf['done'] == FALSE) {
                throw new Exception($cf['msg'], 5);
            }
    
            $factor = $cf['factor'];
            $data = ['factor' => $factor];
    
            if ($factor->price == 0) {
                $this->category->updatetFactor($factor->id, [
                    'state' => $discount_id != NULL ? "خرید کامل با کد تخفیف (<span class=\"text-warning\">{$discountCode}</span>)" : 'رایگان',
                    'status' => 0,
                    'pdate' => time()
                ]);
    
                if ($discount_id != NULL) {
                    $this->category->setDiscountUsed($discount_id, $factor->id);
                }
    
                $data['free'] = TRUE;
                $data['link'] = NULL;
    
            } else {
                $data['link'] = site_url('payment/paycategory/' . $factor->id);
            }
            $this->tools->outS(0, "فاکتور ایجاد شد", ['data' => $data]);
        // }
    }
    
    public function buyCategoryBazar()
    {
        $user = $this->_loginNeed();

        if ($user === FALSE) {
            throw new Exception("برای دسترسی به این بخش باید وارد حساب کاربری خود شوید", -1);
        }
        $action = $this->input->post('action');
        if (!strlen($action)) {
            throw new Exception("کد فعالیت الزامی می باشد", 1);
        }
        $ref_id = $this->input->post('ref_id');
        if (!strlen($ref_id)) {
            throw new Exception("کد رهگیری الزامی می باشد", 1);
        }

        $category_id = $this->input->post('category_id');
        $category_id = explode(",", $category_id);
        $plan_id = $this->input->post('plan_id');
        $plan_id = explode(",", $plan_id);
        if (!count($category_id) || !count($plan_id)) {
            throw new Exception("شماره دسته بندی یا شماره پلن عضویت الزامی است", 1);
        }

        $this->load->model('m_category', 'category');

        if ($this->category->isBought($user->id, $category_id, $plan_id)) {
            $data = $this->db
                ->where_in('cat_id', $category_id)
                ->where('enddate > NOW()')
                ->order_by('enddate DESC')
                ->get("user_catmembership")->row();
            $this->tools->outS(5, "اشتراک قبلا خریداری شده است", ['data' => $data]);
            //throw new Exception("اشتراک قبلا خریداری شده است", 5);
        } else {
            $discountCode = $this->input->post('code');
            $discount_ids = [];
            if ($discountCode) {
                $discount_ids = $this->category->checkDiscountCode($discountCode, "-8", $plan_id, $category_id, $user->id);
            }
            if (!isset($discount_ids["allowed"])) {
                $discount_ids = [];
            } else {
                $discount_ids = $discount_ids["allowed"];
            }
            $cf = $this->category->createFactor($user->id, $category_id, $plan_id, $discount_ids);

            if ($cf['done'] == FALSE) {
                throw new Exception($cf['msg'], 5);
            }

            $factor = $cf['factor'];
            $data = ['factor' => $factor];

            if ($factor->price == 0) {
                $this->category->updatetFactor($factor->id, [
                    'state' => count($discount_ids) ? "خرید کامل با کد تخفیف (<span class=\"text-warning\">{$discountCode}</span>)" : 'رایگان',
                    'status' => 0,
                    'pdate' => time()
                ]);

                if (count($discount_ids)) {
                    $this->category->setDiscountUsed($discount_ids, $factor->id);
                }

                $data['free'] = TRUE;
                $data['link'] = NULL;

            } else {
                $data['link'] = site_url('payment/paycategory/' . $factor->id);
            }
            if (in_array($action, ["bazar", "myket"]) && $ref_id) {
                $factor->state = 'پرداخت موفق';
                $factor->status = 0;
                $factor->pdate = $factor->cdate;
                $factor->paid = $factor->price;
                $factor->ref_id = $action . ":" . $ref_id;
                $this->category->updatetFactor($factor->id, [
                    'state' => $factor->state,
                    'status' => $factor->status,
                    'pdate' => $factor->pdate,
                    'paid' => $factor->paid,
                    'ref_id' => $factor->ref_id
                ]);
                $data = [];
                $data['factor'] = $factor;
                $data['link'] = '';
            }
            $this->tools->outS(0, "فاکتور ایجاد شد", ['data' => $data]);
        }
    }

    //=========================================
    public function BoughtCategory()
    {
        $user = $this->_loginNeed();

        if ($user === FALSE)
            throw new Exception("برای دسترسی به این بخش باید وارد حساب کاربری خود شوید", -1);

        $this->load->model('m_category', 'category');
        $data = [];
        $data["categories"] = $this->db->where("parent", 0)->get('category c')->result();
        $data['catmembership'] = $this->db->where("user_id", $user->id)->get('user_catmembership')->result();

        $this->tools->outS(0, "لیست دسته بندی های خریداری شده", ['data' => $data]);
    }

    //=========================================
    // public function getClassOnline($id = 0)
    // {
    //     try {
    //         $baseurl = CDN_URL;

    //         $limit = (int)$this->input->post('limit');
    //         $limitstart = (int)$this->input->post('limitstart');

    //         $db = $this->db;
    //         $data = array();
            
    //         $dayofweek = [
    //             0 => "شنبه",
    //             1 => "یکشنبه",
    //             2 => "دوشنبه",
    //             3 => "سه شنبه",
    //             4 => "چهارشنبه",
    //             5 => "پنجشنبه",
    //             6 => "جمعه"
    //         ];
    //         $data["dayofweek"] = $dayofweek;

    //         $db->select('id AS value,displayname AS text');
    //         $db->where('level', 'teacher');
    //         $db->limit($limit, $limitstart);
    //         $data['teacher'] = $db->get('users')->result();

    //         $db->select('c.*');
    //         $db->where('c.published', 1);
    //         $db->limit($limit, $limitstart);
    //         $classonlines = $db->get('classonline c')->result();
    //         foreach ($classonlines as $key => $classonline) {
    //             $classaccounts = $db->where('user_id', 0)->where('classonline_id', $classonline->id)->get('classaccount')->num_rows();
    //             $classonlines[$key]->classaccounts["free"] = $classaccounts;
    //             $classaccounts = $db->where('user_id > 0')->where('classonline_id', $classonline->id)->get('classaccount')->num_rows();
    //             $classonlines[$key]->classaccounts["used"] = $classaccounts;
    //         }
    //         $data['classonlines'] = $classonlines;

    //         $this->tools->outS(0, 'OK', ["data" => $data]);
    //     } catch (Exception $e) {
    //         $this->tools->outE($e);
    //     }
    // }
    public function getClassOnline()
    {
        try {
            $baseurl = CDN_URL;

            $limit = (int)$this->input->post('limit');
            $limitstart = (int)$this->input->post('limitstart');
            $id = (int)$this->input->post('id');

            $db = $this->db;
            $data = array();
            $dayofweek = [
                0 => "شنبه",
                1 => "یکشنبه",
                2 => "دوشنبه",
                3 => "سه شنبه",
                4 => "چهارشنبه",
                5 => "پنجشنبه",
                6 => "جمعه"
            ];
            $data["dayofweek"] = $dayofweek;

            $db->select('id AS value,displayname AS text');
            $db->where('level', 'teacher');
            $teachers = $db->get('users')->result();

            $tempteachers = [];
            foreach ($teachers as $teacher) {
                $tempteachers[$teacher->value] = $teacher;
            }
            $teachers = $tempteachers;

            $db->select('c.classonline_id');
            $db->where('c.user_id', 0);
            $db->group_by('c.classonline_id');
            $classaccounts = $db->get('classaccount c')->result();
            $classonline_id = [-1];
            foreach ($classaccounts as $classaccount) {
                $classonline_id[] = $classaccount->classonline_id;
            }

            $classonlines = $this->db->select('*')
                ->where_in('data_type', ['dayofweek'])
                ->where_in('cid', $classonline_id)
                ->order_by('cid,dayofweek,starttime')
                ->get('classonline_data')
                ->result();
            $dayofweeks = [
                0 => "شنبه",
                1 => "یکشنبه",
                2 => "دوشنبه",
                3 => "سه شنبه",
                4 => "چهارشنبه",
                5 => "پنجشنبه",
                6 => "جمعه"
            ];

            $classonline_dayofweeks = [];
            foreach ($classonlines as $classonline) {
                if ($classonline->data_type == "dayofweek") {
                    $classonline_dayofweeks[$classonline->cid][] = ["dayofweek" => $classonline->dayofweek, "dayname" => $dayofweeks[$classonline->dayofweek], "starttime" => $classonline->starttime, "endtime" => $classonline->endtime];
                }
            }

            $db->select('c.*');
            $db->where('c.published', 1);
            if ($id) {
                $db->where('id', $id);
            } else {
                $db->where_in('id', $classonline_id);
            }
            $db->limit($limit, $limitstart);
            $classonlines = $db->get('classonline c')->result();
            foreach ($classonlines as $key => $classonline) {
                $classaccounts = $db->where('user_id', 0)->where('classonline_id', $classonline->id)->get('classaccount')->num_rows();
                $classonlines[$key]->classaccounts["free"] = $classaccounts;
                $classaccounts = $db->where('user_id > 0')->where('classonline_id', $classonline->id)->get('classaccount')->num_rows();
                $classonlines[$key]->classaccounts["used"] = $classaccounts;
                $classonlines[$key]->program = $classonline_dayofweeks[$classonline->id];
                $classonlines[$key]->teachername = $teachers[$classonline->teachername];

            }
            $data['classonlines'] = $classonlines;

            $this->tools->outS(0, 'OK', ["data" => $data]);
        } catch (Exception $e) {
            $this->tools->outE($e);
        }
    }

    //=========================================
    public function buyAccountClassOnline()
    {
        $user = $this->_loginNeed();

        if ($user === FALSE)
            throw new Exception("برای دسترسی به این بخش باید وارد حساب کاربری خود شوید", -1);

        $classonline_id = (int)$this->input->post('classonline_id');
        if (!$classonline_id) {
            throw new Exception("شماره دسته بندی یا شماره پلن عضویت الزامی است", 1);
        }

        $this->load->model('m_classonline', 'classonline');

        if ($this->classonline->isBought($user->id, $classonline_id)) {
            throw new Exception("اشتراک قبلا خریداری شده است", 5);
        }

        $discountCode = $this->input->post('code');
        $discount_id = 0;
        if ($discountCode) {
            $discount_id = (int)$this->classonline->checkDiscountCode($discountCode, "-9", $user->id);
        }
        $cf = $this->classonline->createFactor($user->id, $classonline_id, $discount_id);

        if ($cf['done'] == FALSE) {
            throw new Exception($cf['msg'], 5);
        }

        $factor = $cf['factor'];
        $data = ['factor' => $factor];

        if ($factor->price == 0) {
            $this->classonline->updatetFactor($factor->id, [
                'state' => $discount_id != NULL ? "خرید کامل با کد تخفیف (<span class=\"text-warning\">{$discountCode}</span>)" : 'رایگان',
                'status' => 0,
                'pdate' => time()
            ]);

            if ($discount_id != NULL) {
                $this->classonline->setDiscountUsed($discount_id, $factor->id);
            }

            $data['free'] = TRUE;
            $data['link'] = NULL;

        } else {
            $data['link'] = site_url('payment/payclassonline/' . $factor->id);
        }
        $this->tools->outS(0, "فاکتور ایجاد شد", ['data' => $data]);
    }

    public function buyAccountClassOnlineBazar()
    {
        $user = $this->_loginNeed();

        if ($user === FALSE) {
            throw new Exception("برای دسترسی به این بخش باید وارد حساب کاربری خود شوید", -1);
        }

        $classonline_id = (int)$this->input->post('classonline_id');
        if (!$classonline_id) {
            throw new Exception("شماره دسته بندی یا شماره پلن عضویت الزامی است", 1);
        }
        $action = $this->input->post('action');
        if (!strlen($action)) {
            throw new Exception("کد فعالیت الزامی می باشد", 1);
        }
        $ref_id = $this->input->post('ref_id');
        if (!strlen($ref_id)) {
            throw new Exception("کد رهگیری الزامی می باشد", 1);
        }

        $this->load->model('m_classonline', 'classonline');

        if ($this->classonline->isBought($user->id, $classonline_id)) {
            throw new Exception("اشتراک قبلا خریداری شده است", 5);
        }

        $discountCode = $this->input->post('code');
        $discount_id = 0;
        if ($discountCode) {
            $discount_id = (int)$this->classonline->checkDiscountCode($discountCode, "-9", $user->id);
        }
        $cf = $this->classonline->createFactor($user->id, $classonline_id, $discount_id);

        if ($cf['done'] == FALSE) {
            throw new Exception($cf['msg'], 5);
        }

        $factor = $cf['factor'];
        $data = ['factor' => $factor];

        if ($factor->price == 0) {
            $this->classonline->updatetFactor($factor->id, [
                'state' => $discount_id != NULL ? "خرید کامل با کد تخفیف (<span class=\"text-warning\">{$discountCode}</span>)" : 'رایگان',
                'status' => 0,
                'pdate' => time()
            ]);

            if ($discount_id != NULL) {
                $this->classonline->setDiscountUsed($discount_id, $factor->id);
            }

            $data['free'] = TRUE;
            $data['link'] = NULL;

        } else {
            $data['link'] = site_url('payment/payclassonline/' . $factor->id);
        }
        if (in_array($action, ["bazar", "myket"]) && $ref_id) {
            $factor->state = 'پرداخت موفق';
            $factor->status = 0;
            $factor->pdate = $factor->cdate;
            $factor->paid = $factor->price;
            $factor->ref_id = $action . ":" . $ref_id;
            $this->classonline->updatetFactor($factor->id, [
                'state' => $factor->state,
                'status' => $factor->status,
                'pdate' => $factor->pdate,
                'paid' => $factor->paid,
                'ref_id' => $factor->ref_id
            ]);
            $data = [];
            $data['factor'] = $factor;
            $data['link'] = '';
        }
        $this->tools->outS(0, "فاکتور ایجاد شد", ['data' => $data]);
    }

    //=========================================
    public function getClassOnlineAccounts($id = 0)
    {
        try {
            $user = $this->_loginNeed();

            if ($user === FALSE) {
                throw new Exception("برای دسترسی به این بخش باید وارد حساب کاربری خود شوید", -1);
            }

            $limit = (int)$this->input->post('limit');
            $limitstart = (int)$this->input->post('limitstart');

            $db = $this->db;
            $data = array();

            $db->select('c.*');
            $db->where('c.user_id', $user->id);
            if ($limit || $limitstart) {
                $db->limit($limit, $limitstart);
            }
            $classaccounts = $db->get('classaccount c')->result();

            $classonline_ids = [0];
            foreach ($classaccounts as $classaccount){
                $classonline_ids[] = $classaccount->classonline_id;
            }

            $db->select('c.*');
            $db->where_in('c.id', $classonline_ids);
            $classonlines = $db->get('classonline c')->result();

            $teacher_ids = [0];
            $tempclassonlines = [];
            foreach ($classonlines as $classonline){
                if($classonline->teachername) {
                    $teacher_ids[] = $classonline->teachername;
                }
                $tempclassonlines[$classonline->id] = $classonline;
            }
            $classonlines = $tempclassonlines;

            $db->select('c.*');
            $db->where_in('c.id', $teacher_ids);
            $teachers = $db->get('users c')->result();

            $tempteachers = [];
            foreach ($teachers as $teacher) {
                $tempteachers[$teacher->id] = ["value" => $teacher->id, "text" => $teacher->displayname];
            }
            $teachers = $tempteachers;

            foreach ($classaccounts as $key=>$classaccount){
                $classaccounts[$key]->teacher = @$teachers[$classonlines[$classaccount->classonline_id]->teachername];
                $classaccounts[$key]->classonline = @$classonlines[$classaccount->classonline_id];
            }

            $data['classaccounts'] = $classaccounts;

            $this->tools->outS(0, 'OK', ["data" => $data]);
        } catch (Exception $e) {
            $this->tools->outE($e);
        }
    }

    public function ema_getClassOnlineAccounts()
    {
        try {
            $user = $this->_loginNeed();

            if ($user === FALSE) {
                throw new Exception("برای دسترسی به این بخش باید وارد حساب کاربری خود شوید", -1);
            }

            $limit = (int)$this->input->post('limit');
            $limitstart = (int)$this->input->post('limitstart');
            $id         = (int) $this->input->post('id');

            $db = $this->db;
            $data = array();

            // Select the classaccounts for the logged in user
            $db->select('c.*');
            $db->where('c.user_id', $user->id);
            
            // If an id is provided (not 0) then filter by that classonline id
            if ($id != 0) {
                $db->where('c.classonline_id', $id);
            }
            
            if ($limit || $limitstart) {
                $db->limit($limit, $limitstart);
            }
            $classaccounts = $db->get('classaccount c')->result();

            // Gather the classonline ids from the user's accounts
            $classonline_ids = [0];
            foreach ($classaccounts as $classaccount) {
                $classonline_ids[] = $classaccount->classonline_id;
            }

            // Get classonline records based on the collected ids
            $db->select('c.*');
            $db->where_in('c.id', $classonline_ids);
            $classonlines = $db->get('classonline c')->result();

            // Prepare teacher ids and an associative array for classonlines
            $teacher_ids = [0];
            $tempclassonlines = [];
            foreach ($classonlines as $classonline) {
                if ($classonline->teachername) {
                    $teacher_ids[] = $classonline->teachername;
                }
                $tempclassonlines[$classonline->id] = $classonline;
            }
            $classonlines = $tempclassonlines;

            // Get teacher data
            $db->select('c.*');
            $db->where_in('c.id', $teacher_ids);
            $teachers = $db->get('users c')->result();

            $tempteachers = [];
            foreach ($teachers as $teacher) {
                $tempteachers[$teacher->id] = [
                    "value" => $teacher->id, 
                    "text"  => $teacher->displayname
                ];
            }
            $teachers = $tempteachers;

            // Append teacher and classonline details to each classaccount
            foreach ($classaccounts as $key => $classaccount) {
                $teacherId = @$classonlines[$classaccount->classonline_id]->teachername;
                $classaccounts[$key]->teacher = @$teachers[$teacherId];
                $classaccounts[$key]->classonline = @$classonlines[$classaccount->classonline_id];
            }

            $data['classaccounts'] = $classaccounts;

            $this->tools->outS(0, 'OK', ["data" => $data]);
        } catch (Exception $e) {
            $this->tools->outE($e);
        }
    }

    //=========================================
    public function DayClassOnline($id = 0)
    {
        try {
            $baseurl = CDN_URL;

            $user = $this->_loginNeed();

            if ($user === FALSE) {
                throw new Exception("برای دسترسی به این بخش باید وارد حساب کاربری خود شوید", -1);
            }

            $dayofweek = (int)$this->input->post('dayofweek');
            $user_id = (int)$user->id;

            $db = $this->db;
            $data = array();
            $dayofweeks = [
                0 => "شنبه",
                1 => "یکشنبه",
                2 => "دوشنبه",
                3 => "سه شنبه",
                4 => "چهارشنبه",
                5 => "پنجشنبه",
                6 => "جمعه"
            ];

            $db->select('id AS value,displayname AS text');
            $db->where('level', 'teacher');
            $teachers = $db->get('users')->result();

            $tempteachers = [];
            foreach ($teachers as $teacher) {
                $tempteachers[$teacher->value] = $teacher;
            }
            $teachers = $tempteachers;

            $db->select('c.classonline_id');
            $db->where('c.user_id', $user_id);
            $db->group_by('c.classonline_id');
            $classonline_id = [-1];
            $classaccounts = $db->get('classaccount c')->result();
            foreach ($classaccounts as $classaccount) {
                $classonline_id[] = $classaccount->classonline_id;
            }
            $classonlinedatas = $this->db->select('*')
                ->where_in('data_type', ['dayofweek'])
                ->where('dayofweek', $dayofweek)
                ->where_in('cid', $classonline_id)
                ->order_by('cid,dayofweek,starttime')
                ->get('classonline_data')
                ->result();
            $classonline_id = [-1];
            foreach ($classonlinedatas as $classonlinedata) {
                $classonline_id[] = $classonlinedata->cid;
            }

            $classonlinedatas = $this->db->select('*')
                ->where_in('data_type', ['dayofweek'])
                ->where_in('cid', $classonline_id)
                ->order_by('cid,dayofweek,starttime')
                ->get('classonline_data')
                ->result();
            $classonline_dayofweeks = [];
            foreach ($classonlinedatas as $classonlinedata) {
                $classonline_dayofweeks[$classonlinedata->cid][] = ["dayofweek" => $classonlinedata->dayofweek, "dayname" => $dayofweeks[$classonlinedata->dayofweek], "starttime" => $classonlinedata->starttime, "endtime" => $classonlinedata->endtime];
            }

            $db->select('c.*');
            //$db->where('c.published', 1);
            $db->where_in('id', $classonline_id);
            $classonlines = $db->get('classonline c')->result();
            foreach ($classonlines as $key => $classonline) {
                $classaccounts = $db->where('user_id', 0)->where('classonline_id', $classonline->id)->get('classaccount')->num_rows();
                $classonlines[$key]->classaccounts["free"] = $classaccounts;
                $classaccounts = $db->where('user_id > 0')->where('classonline_id', $classonline->id)->get('classaccount')->num_rows();
                $classonlines[$key]->classaccounts["used"] = $classaccounts;
                $classonlines[$key]->program = $classonline_dayofweeks[$classonline->id];
                $classonlines[$key]->teachername = $teachers[$classonline->teachername];

            }
            $data['classonlines'] = $classonlines;

            $this->tools->outS(0, 'OK', ["data" => $data]);
        } catch (Exception $e) {
            $this->tools->outE($e);
        }
    }

    public function DeleteUser()
    {
        $user = $this->_loginNeed(TRUE, 'u.id');
        if ($user === FALSE) {
            throw new Exception("برای دسترسی به این بخش باید وارد حساب کاربری خود شوید", -1);
        }
        $mobile = $this->input->post('mac');
        $id = $user->id;
        $status = 1;
        $data = ["active" => 0];
        $this->db
            ->where("id='$id'")
            ->where("active=1")
            ->update('users', $data);
        $user = $this->db->affected_rows();

        $message = "در ورود اطلاعات دقت نمایید.شماره همراه %s وجود ندارد";
        $message = sprintf($message, $mobile);
        if ($user) {
            $status = 0;
            $message = "اطلاعات شماره همراه %s با موفقیت حذف شد";
            $message = sprintf($message, $mobile);
        }
        $this->tools->outS($status, $message);
    }

    public function getMediaSftp() {
        $sftp_host = 'idrgwvlp.lexoyacloud.ir';
        $sftp_port = 30046;
        $sftp_user = 'sftp';
        $sftp_pass = '6fbnDYuFVN1ElCRY7sBVQqZcieQV2wDr';
        $remote_file = 'uploads/makahani/2022/11/2022-11-24-5B11.23-5D.jpg'; // File on the SFTP server

        // Connect to the SFTP server
        $sftp = new \phpseclib3\Net\SFTP($sftp_host, $sftp_port);
        // $sftp = new SFTP($sftp_host, $sftp_port);

        if (!$sftp->login($sftp_user, $sftp_pass)) {
            die('SFTP login failed.');
        }

        // Retrieve the file contents
        $file_contents = $sftp->get($remote_file);

        if ($file_contents === false) {
            die('Failed to retrieve the file from SFTP.');
        }

        $this->tools->outS(0, "shosh", ['data' => $file_contents]);

    }
}
