<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Contact_us extends CI_Controller
{
    function __construct()
    {
        parent::__construct();
    }

    function index()
    {
        $data['_title'] = 'تماس با ما';

        $data['script'][] = 'vendor/jquery.gmap3.min.js';

        //======================================//
        $this->bc->add('تماس با ما',site_url("contact-us"));
        //======================================//

        $this->tools->view('v_contact_us',$data);

        $this->logs->addView('contact-us');
    }
}