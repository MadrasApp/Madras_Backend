<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Bc {
    private $breadcrumbs = array();
    private $separator = '<i class="fa fa-angle-double-left bc-sep"></i>'; //
    private $start = '<div id="breadcrumb">';
    private $end = '</div>';

    public function __construct($params = array()){
        if (count($params) > 0){
            $this->initialize($params);
        }
        $this->add('<i class="fa fa-home fa-fw"></i> خانه',site_url());
    }

    private function initialize($params = array()){
        if (count($params) > 0){
            foreach ($params as $key => $val){
                if (isset($this->{'_' . $key})){
                    $this->{'_' . $key} = $val;
                }
            }
        }
    }

    function add($title, $href){
        if (!$title OR !$href) return;
        $this->breadcrumbs[] = array('title' => $title, 'href' => $href);
    }

    function output(){
        if(count($this->breadcrumbs) < 2 ) return '';
        if ($this->breadcrumbs) {

            $output = $this->start;

            foreach ($this->breadcrumbs as $key => $crumb) {
                if ($key){
                    $output .= $this->separator;
                }
                /*if ($key < count($this->breadcrumbs)) {
                    $output .= '<span class="bc-item">' . $crumb['title'] . '</span>';
                } else {*/
                    $output .=
                        '<span itemscope="" itemtype="http://data-vocabulary.org/Breadcrumb" class="bc-item ellipsis">'.
                            '<a  href="' . $crumb['href'] . '" title="'.html_escape($crumb['title']).'" itemprop="url">' .
                                '<span itemprop="title">'.$crumb['title'].'</span>' .
                            '</a>'.
                        '</span>';
               /* }*/
            }

            return $output . $this->end . PHP_EOL;
        }

        return '';
    }

}