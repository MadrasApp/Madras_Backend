<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Script extends CI_Controller
{
    function __construct()
    {
        parent::__construct();
    }

    public function  inc($js=NULL)
    {
        $jsfile = '';

        if( $js == 'ln' )
        {
            $jsfile = $this->lang->language;
            $jsfile = 'var ln= $.parseJSON(\''.json_encode($jsfile).'\');';
        }

        $this->output
             ->set_content_type('text/javascript')
             ->set_output($jsfile);
    }
}