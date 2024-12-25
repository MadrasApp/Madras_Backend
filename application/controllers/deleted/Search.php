<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Search extends CI_Controller {
	
	
	function __construct(){
		parent::__construct();
	}

	public function index($page = 1)
	{
		$this->load->model('admin/m_post','post');

        $data['search_str'] = "";

		$s = $this->input->get('s',TRUE);
		if( $s )
		{
			$s = rawurldecode($s);
			$data['_title'] = $data['search_str'] = $s;
			$S = $this->db->escape_like_str($s);
		}
		else $S = NULL;
		
		if( $s )
		{
			$page = intval($page);
			
			$perPage = $this->settings->data['home_perpage'];
			
			if($page)
			{
				$min = $page == 1 ? 0 : $perPage*($page-1); 
			}
			else
			{
			   $min = 0;$page = 1;
			}

            //======================================//
            $this->bc->add('جستجوی : ' . $s,site_url("search")."?s=".$this->input->get('s',TRUE));

            if($page > 1)
                $this->bc->add(' صفحه ' . $page ,current_url());
            //======================================//

            global $POST_TYPES;

            $where = "(";
            foreach ($POST_TYPES as $k=>$item) {
                if( isset($item['single']) && $item['single'] !== FALSE )
                    $where .= " type='{$k}' OR";
            }
            $where .= ")";
            $where = str_replace('OR)',')' ,$where);

			$where .= " AND published=1  AND title !='' 
			 AND (title LIKE '%$S%' OR content_string LIKE '%$S%')" ;
			
			$order = "position DESC, date_modified DESC";
			$limit = "$min,$perPage";
			
			$query = $this->tools->buildQuery('search',"WHERE $where ORDER BY $order LIMIT $limit");
			
			$result = $this->db->query($query)->result();
			
			if( count($result) > 0 )
			{
				foreach( $result as $key=>$item )
				{
					$t = preg_replace("/($s)/i", "<span class=match>$1</span>", $item->title);
					$result[$key]->text    = $t ;
					$result[$key]->link    = $this->post->link($item);
					$result[$key]->content = $this->tools->searchStringCompile($item->content,$s);
					$result[$key]->thumb   = base_url().$this->post->thumb($item->thumb,150);
				}
				$data['result'] = $result;
			}			
			else $data['result'] = NULL;	
			
			$data['total'] = $this->db->where($where)->count_all_results('posts') ;
							
			$config = array(
				'page'       => $page,
				'perPage'    => $perPage, 
				'totalRows'  => $data['total'],
				'url'        => site_url('search/page/[PAGE]?s='.$s)
			);
			
			$data['pg'] = $this->tools->pagination($config);

		}
        $data['blog_title'] = "<span>جستجو</span> <i class=\"fa fa-angle-double-left\"></i> <span>{$s}</span>";

        $this->tools->view('v_search',$data);

        $this->logs->addView('search');
	}
}
