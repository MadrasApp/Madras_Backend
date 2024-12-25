<?php defined('BASEPATH') OR exit('No direct script access allowed');

if(!session_id()) session_start();

class M_ln extends CI_Model
{

    public $name;

    function __construct()
    {
        parent::__construct();
        $this->load->helper(array('cookie'));
        $this->setLang();
    }

    function setLang()
    {
        $ln = $this->input->get('ln');

        if( ! $ln )
            $ln = $this->session->userdata('site_lang');

        if( ! $ln )
            $ln = get_cookie('site_lang',TRUE);
		
		$ln = 'persian';

        $this->changeLn($ln);
    }

    function changeLn($ln='persian')
    {
        $allowed = array('persian','english');

        if( ! in_array($ln,$allowed) )
            $ln = 'persian';

        $this->session->set_userdata('site_lang', $ln);

        $expire = (100*24*3600);//Alireza Balvardi
        set_cookie('site_lang', $ln , $expire );
        $this->lang->load("site",$ln);
        $this->name = $ln;
        $this->config->set_item('language',$ln);
    }



}