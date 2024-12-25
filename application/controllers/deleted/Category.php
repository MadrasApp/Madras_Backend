<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Category extends CI_Controller {

	function __construct(){
		parent::__construct();
	}

	public function index($cId=NULL,$page=1)
	{
        $data['_title'] = "دسته بندی";
        $data['blog_title'] = "دسته بندی ها";

        $cId = (int)$cId;

        if( ! $cId ) show_404();

        //$ex = $this->db->where('id',$cId)->count_all_results('category');
        $category = $this->db->where('id',$cId)->get('category')->row();

		if( empty($category)  )
		{
			$data['view_key'] = FALSE;
			$data['view_msg'] = "اطلاعات ورودی درست نمی باشد";
			$data['category'] = NULL;
		}
		else		 
		{
            $type = $category->type;

			$category = $this->tools->buildQuery('category-page',"WHERE id=$cId");
			$category = $this->db->query($category)->row();
			$data['view_key']   = TRUE;
			$data['category']   = $category;
			$data['_title']     = $category->name;
			$data['blog_title'] = "<span>دسته بندی</span> <i class=\"fa fa-angle-double-left\"></i> <span>{$category->name}</span>";
            $data['current_post_type'] = $type;

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

            global $POST_TYPES;
            //======================================//
            if(isset($POST_TYPES[$type]['seo_url']))
                $this->bc->add($POST_TYPES[$type]['g_name'],site_url($POST_TYPES[$type]['seo_url']));

            $cid = $category->parent;

            while($cid != 0 && $cRow = $this->db->where('id',$cid)->get('category')->row()){
                $cid = $cRow->parent;
                $this->bc->add($cRow->name,site_url("category/{$cRow->id}") . "/" . STU($cRow->name));
            }

            $this->bc->add($category->name,site_url("category/{$category->id}") . "/" . STU($category->name));

            if($page > 1)
                $this->bc->add(' صفحه ' . $page ,current_url());
            //======================================//
			
			$cl = $this->tools->buildQuery('category-like',$cId);	
			
			$where = "type='$type' AND published=1 AND $cl";
			$order = "position DESC, date_modified DESC ";
			$limit = "$min,$perPage";
			
			$query = $this->tools->buildQuery('posts',"WHERE $where ORDER BY $order LIMIT $limit");
			
			$query = $this->db->query($query);
			$data['posts'] = $query->result();	

			//$cAdd = "WHERE parent={$category->id} AND type='$type' ORDER BY position ASC";
			$cAdd = "WHERE parent={$category->id} ORDER BY position ASC";

			$queryCat = $this->tools->buildQuery('category-page',$cAdd);
			
			$data['sub_cat'] = $this->db->query($queryCat)->result();
							
			$config = array(
				'page'       => $page,
				'perPage'    => $perPage, 
				'totalRows'  => $this->db->where($where)->count_all_results('posts'),
				'url'        => site_url('category')."/page/[PAGE]"
			);
			$data['pg'] = $this->tools->pagination($config);

			$data['templates'][] = 'v_category_header';
            $data['script'][]    = 'vendor/jquery.countTo.js';
            $data['script'][]    = 'vendor/jquery.appear.js';
		}
        $this->tools->view('v_blog',$data);

        $this->logs->addView('category');
	}
}
