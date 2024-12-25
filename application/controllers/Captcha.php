<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Captcha extends CI_Controller
{
    function __construct()
    {
        parent::__construct();
    }

    public function  index()
    {
        $cap = $this->tools->newCaptcha();
        $filename = $cap['filename'];
        $file = "./_captcha/".$filename;
        $this->load->helper('file');
        $this->output->set_content_type(get_mime_by_extension($file));
        $this->output->set_output(file_get_contents($file));
        @unlink($file);
    }
}