<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Influencer extends CI_Controller
{
    function __construct()
    {
        parent::__construct();

        $this->load->model('m_user','user');
    }

    function index()
    {
        if( ! $this->user->check_login() )
            redirect('influencer/login');

        $this->tools->view('v_request_demo',$data);
    }

    function login()
    {

        $this->load->helper('form');

        if( $this->user->check_login() )
        redirect( __CLASS__ );

        $this->tools->view('v_influencer_login');
    }

    function sign_up()
    {
        if( $this->user->check_login() )
        redirect( __CLASS__ );

        $this->tools->view('v_influencer_signup');
    }
}
