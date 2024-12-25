<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Notfound  extends CI_Controller {
    public function __construct() {
        parent::__construct();
    }

    public function index(){

        $data['title'] = "برگه مورد نظر پیدا نشد";

        $this->output->set_status_header('404');
        

        $this->tools->view('v_404',$data);
    }
}