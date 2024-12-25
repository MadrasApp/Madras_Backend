<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Includes extends CI_Controller
{
    function __construct()
    {
        parent::__construct();
    }

    public function css($css=NULL)
    {
        $css = explode('::',$css);

        $content = "";
        $start   = "/* |------------ Css Includes ------------ \n";

        foreach ($css as $cs) {

            $cs = str_replace(':', '/', $cs);

            if(!file_exists($cs))
            {
                $start .= " * | this file not exists : {$cs} \n";
                continue;
            }

            $start .= " * |  {$cs} \n";

            $con = file_get_contents($cs);
            $path = base_url() . dirname($cs).'/';

            $search = '#url\((?!\s*[\'"]?(?:https?:)?//)\s*([\'"])?#';
            $replace = "url($1{$path}";

            $con = preg_replace($search, $replace, $con);

            $content .= $con;
        }

        $content = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $content);
        $content = str_replace(': ', ':', $content);
        $content = str_replace(array("\r\n", "\r", "\n", "\t", /*'  ', */'    ', '    ','@charset "UTF-8";'), '', $content);

        $start .= " * |------------ Css Includes ------------ \n */ \n\r ";

        $this->output->set_content_type('text/css')->set_output($start.$content)/*->cache(20)*/;
    }

    public function js($files=NULL)
    {
        $session = $this->session->userdata('js_files');

        $files = explode('-',$files);

        $content = "";
        $start   = "/* |------------ Javascript Includes ------------ \n";

        foreach ($files as $js) {

            if(! isset($session[$js]))
            {
                $start .= " * | this index not exists : {$js} \n";
                continue;
            }

            $js = $session[$js];

            if(!file_exists($js))
            {
                $start .= " * | this file not exists : {$js} \n";
                continue;
            }

            $start .= " * |  {$js} \n";

            $content .= file_get_contents($js) ." \n\r ";
        }

        $start .= " * |------------ Javascript Includes ------------ \n */ \n\r ";


        $this->output->set_content_type('text/javascript')->set_output($start . $content);//->cache(20);
    }
}