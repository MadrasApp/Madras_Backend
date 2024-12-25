<?php defined('BASEPATH') OR exit('No direct script access allowed');


class MY_Exceptions extends CI_Exceptions{

    public function __construct()
    {
        parent::__construct();
    }

    /*function show_404($page = '')
    {
        $CI =& get_instance();
        header("HTTP/1.1 404 Not Found");
        $heading = "404 Page Not Found";
        $message = "The page you requested was not found ";

        $CI->load->view('');
        //echo 'oooooooooo';
    }*/
}