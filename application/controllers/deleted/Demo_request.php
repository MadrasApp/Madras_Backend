<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Demo_request extends CI_Controller
{
    function __construct()
    {
        parent::__construct();
    }

    function index()
    {
        $this->load->model('m_group','group');
        $data['province']  = $this->group->getRow(1);
        $data['levels']    = $this->group->getRow(6);
        $data['yearsofex'] = $this->group->getRow(10);
        $data['Hours'] = $this->group->getRow(15);

        $this->tools->view('v_request_demo',$data);
    }
}