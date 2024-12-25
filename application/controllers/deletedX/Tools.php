<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Tools extends CI_Controller{
	
	function __construct(){
		parent::__construct();
	}

	public function index($page=1)
    {

        $data['_title'] = "ابزارها";

        $this->load->model('m_instrument', 'instrument');

        $isAjax = $this->input->is_ajax_request();
        $user_id = isset($this->user->data->id) ? $this->user->user_id : 0;
        $ip = $this->input->ip_address();

        $page = intval($page);

        $perPage = $this->settings->data['tools_perpage'];

        if ($page) {
            $min = $page == 1 ? 0 : $perPage * ($page - 1);
        } else {
            $min = 0;
            $page = 1;
        }

        /** filters  */
        $state    = (int)$this->input->get('state');
        $category = (int)$this->input->get('category');
        $min_day  = (int)$this->input->get('min_day');
        $user     = (int)$this->input->get('user');
        $price_f  = $this->input->get('price_f');
        $price_t  = $this->input->get('price_t');
        $sale     = $this->input->get('sale');
        $rent     = $this->input->get('rent');
        $order    = $this->input->get('order');
        $s        = $this->input->get('s',TRUE);

        //======================================//
        $this->bc->add('ابزارها',site_url("tools"));

        if($state)
            $this->bc->add($this->db->get_field('name','group',$state),site_url("tools") ."?state={$state}");

        if($category)
            $this->bc->add($this->db->get_field('name','group',$category),site_url("tools") ."?category={$category}");

        if($page > 1)
            $this->bc->add(' صفحه ' . $page ,current_url());
        //======================================//

        if ($s)
        {
            $s = $this->db->escape_like_str($s);
        }

        $this->db->start_cache();

        $price_f = m_int($price_f);
        if((int)$price_f == 0) $price_f = FALSE;

        $price_t = m_int($price_t);
        if((int)$price_t == 0) $price_t = FALSE;


        if($state)    $this->db->join( $this->db->dbprefix('instrument_meta') . ' ms',"(ms.instrument_id=i.id AND ms.meta_name='state' AND ms.meta_value='$state')",'inner',FALSE);
        if($category) $this->db->join( $this->db->dbprefix('instrument_meta') . ' mk',"(mk.instrument_id=i.id AND mk.meta_name='category' AND mk.meta_value='$category')",'inner',FALSE);
        if($price_f)  $this->db->where('i.price_1 >=',$price_f);
        if($price_t)  $this->db->where("(i.price_1 <= $price_t ".((int)$price_f==0 ? 'OR i.price_1 IS NULL':'' )." )",NULL,FALSE);
        if($min_day)  $this->db->where('i.min_day <=',$min_day);
        if($user)     $this->db->where('i.user_id',$user);
        if($s)        $this->db->where("(i.name LIKE '%$s%' OR i.description LIKE '%$s%')",NULL,FALSE);

        if($sale==1 && $rent!=1)  $this->db->where('i.for_sale',0);
        if($rent==1 && $sale!=1)  $this->db->where('i.for_sale',1);

        if(!$this->user->can('read_tools'))
        {
            $this->db->where('i.submitted',1);
            $this->db->where('i.active',1);
        }


        $totalRows = $this->db->count_all_results('instruments i');
        $this->db->stop_cache();

        $this->db->select("i.id, i.name, i.thumb, i.price, i.price_1, i.min_day, i.for_sale, i.date");
        $this->db->select("(SELECT COUNT(*)   FROM ci_rates r   WHERE (r.table='instruments' AND row_id = i.id)) AS rate_count");
        $this->db->select("(SELECT COUNT(*)   FROM ci_logs  l   WHERE (l.table='instruments' AND row_id = i.id)) AS view_count");
        $this->db->select("(SELECT COUNT(*)   FROM ci_rates r   WHERE (r.table='instruments' AND row_id = i.id AND (user_id ='$user_id' OR ip='$ip'))) AS is_rated");
        $this->db->select("(SELECT meta_value FROM ci_instrument_meta m WHERE(m.meta_name='thumb_json'    AND m.instrument_id=i.id) )   AS thumb_json");

        $this->db->select("(SELECT meta_value FROM ci_instrument_meta m WHERE(m.meta_name='state' AND m.instrument_id=i.id) ORDER BY id ASC LIMIT 1) AS province");
        $this->db->select("(SELECT name       FROM ci_group           g WHERE g.id=province ) AS province");

        $this->db->select("(SELECT meta_value FROM ci_instrument_meta m WHERE(m.meta_name='state' AND m.instrument_id=i.id) ORDER BY id DESC LIMIT 1) AS state");
        $this->db->select("(SELECT name       FROM ci_group           g WHERE g.id=state ) AS state");

        switch ($order)
        {
            case'cheep':
                $this->db->order_by('i.price','asc');
                break;
            case'expencive':
                $this->db->order_by('i.price','desc');
                break;
            case'oldest':
                $this->db->order_by('i.date','asc');
                break;
            case 'newest':
            default :
                $this->db->order_by('i.date','desc');
                break;
        }

        $this->db->group_by('i.id');

        $this->db->limit($perPage,$min);

        $items = $this->db->get('instruments i')->result();
        $this->db->flush_cache();


        $query = $this->_getToString();
        if($query) $query = "/$query";

        $config = array(
            'page'       => $page,
            'perPage'    => $perPage,
            'totalRows'  => $totalRows,
            'url'        => site_url('tools')."/page/[PAGE]". ($isAjax ? '':$query)
        );

        $data['pg'] = $this->tools->pagination($config);

        $data['items'] = $items;

        $this->load->model('m_group','group');

        if(!$isAjax)
        {
            if( $state )
            {
                $states = $this->group->getParents($state);
                $data['states'] = ""; $i = 0;
                foreach ($states as $sid)
                {
                    $id =  $i == 0 ? PROVINCE_ID : $states[$i-1]; $i++;
                    $data['states'] .= $this->group->creatSelect($id,NULL,'<article class="group-holder"><select class="form-control" name="state">','</select></article>',FALSE,$sid,'تمام استان ها و شهر ها');
                }
            }
            else
            {
                $data['states'] = $this->group->creatSelect(PROVINCE_ID,NULL,'<article class="group-holder"><select class="form-control" name="state">','</select></article>',FALSE,$state,'تمام استان ها');
            }

            if( $category )
            {
                $categorys = $this->group->getParents($category);
                $data['categorys'] = ""; $i = 0;
                foreach ($categorys as $sid)
                {
                    $id =  $i == 0 ? INSTRUMENT_CAT_ID : $categorys[$i-1]; $i++;
                    $data['categorys'] .= $this->group->creatSelect($id,NULL,'<article class="group-holder"><select class="form-control" name="category">','</select></article>',FALSE,$sid,'تمام گروه ها');
                }
            }
            else
            {
                $data['categorys'] = $this->group->creatSelect(INSTRUMENT_CAT_ID,NULL,'<article class="group-holder"><select class="form-control" name="category">','</select></article>',FALSE,$category,'تمام گروه ها');
            }

            if($user)
            {
                $data['user'] = $this->user->selectUserById($user,'id,displayname,avatar');
            }

        }

        $data['css'][]    = 'vendor/jquery-ui-custom/jquery-ui.min.css';
        $data['script'][] = 'vendor/jquery-ui-custom/jquery-ui.min.js';

        if($isAjax)
        {
            if(!empty($items))
            {
                foreach ($items as $item)
                    echo $this->instrument->htmlTemplate($item);
            }
            else
                echo '<div class="alert alert-warning mt-15 text-center"><h2 class="mb-20 mt-20">هیچ ابزاری یافت نشد</h2></div>';

            echo '<div class="col-xs-12 text-center ajax-pagination">' . $data['pg'] . '</div>';
        }
        else
        {
            $this->logs->addView('tools');
            $this->tools->view('instrument/v_index',$data);
        }
	}

    public function add()
    {
        $data['_title'] = "اجاره ابزار";

        //======================================//
        $this->bc->add('اجاره ابزار' ,current_url());
        //======================================//

        $this->load->model('m_group','group');

        $data['province'] = $this->group->creatSelect(PROVINCE_ID,NULL,'<select class="form-control need" name="state">');

        $data['category'] = $this->group->creatSelect(INSTRUMENT_CAT_ID,NULL,'<select class="form-control need" name="category">');

        $this->tools->view('instrument/v_add',$data);

        $this->logs->addView('add_tools');
    }

    public function edit($id=NULL,$hash=NULL)
    {
        $data['_title'] = "ویرایش";
        $data['can_delete'] = FALSE;

        //======================================//
        $this->bc->add('ویرایش ابزار' ,current_url());
        //======================================//

        $id = (int)$id;

        $row = NULL;

        if($this->user->logged)
        {
            $this->db->where('id',$id);
            if( ! $this->user->can('edit_tools') )
                $this->db->where('user_id',$this->user->user_id);

            $row = $this->db->get('instruments')->row();
        }
        elseif(trim($hash) != '')
        {
            $row = $this->db->where('id',$id)->where('hash',do_hash($hash))->get('instruments')->row();
            $data['can_delete'] = TRUE;
        }

        if($row && !empty($row))
        {
            $this->load->model('m_group','group');
            $this->load->model('m_instrument','instrument');

            if(($this->user->logged && $row->user_id === $this->user->user_id) OR $this->user->can('delete_tools'))
                $data['can_delete'] = TRUE;

            $row->meta = $this->instrument->getMeta($id);

            $state = $row->meta->state_json;

            if($this->tools->isJson($state))
            {
                $state = json_decode($state);
                $data['province'] = ""; $i = 0;
                foreach ($state as $sid)
                {
                    $id =  $i == 0 ? PROVINCE_ID : $state[$i-1]; $i++;
                    $data['province'] .= $this->group->creatSelect($id,NULL,'<div class="group-holder"><select class="form-control need" name="state">','</select></div>',TRUE,$sid);
                }
            }
            else
            {
                $data['province'] = $this->group->creatSelect(PROVINCE_ID,NULL,'<div class="group-holder"><select class="form-control need" name="state">','</select></div>');
            }

            $category = $row->meta->category_json;

            if($this->tools->isJson($category))
            {
                $category = json_decode($category);

                $data['category'] = ""; $i = 0;

                foreach ($category as $sid)
                {
                    $id =  $i == 0 ? INSTRUMENT_CAT_ID : $category[$i-1]; $i++;
                    $data['category'] .= $this->group->creatSelect($id,NULL,'<div class="group-holder"><select class="form-control need" name="category">','</select></div>',TRUE,$sid);
                }
            }
            else
            {
                $data['category'] = $this->group->creatSelect(PROVINCE_ID,NULL,'<div class="group-holder"><select class="form-control need" name="category">','</select></div>');
            }

            if($row->thumb != NULL)
            {
                $data['thumbs'][0] = $row->thumb;

                if(isset($row->meta->thumb_json) && $this->tools->isJson($row->meta->thumb_json))
                {
                    $thumbs = json_decode($row->meta->thumb_json);
                    foreach ($thumbs as $thumb)
                        $data['thumbs'][] = $thumb;
                }
            } else
                $data['thumbs'] = array();

            $data['item'] = $row;
        }
        else
        {
            //redirect('404');
            show_404();
        }
        $this->tools->view('instrument/v_edit',$data);

        $this->logs->addView('edot_tools');
    }

    public function view($id=NULL)
    {
        $ip = $this->input->ip_address();
        $user_id = isset($this->user->data->id) ? $this->user->user_id:0;

        $this->db->select("i.*");
        $this->db->select("(SELECT COUNT(*)   FROM ci_rates r    WHERE (r.table='instruments'  AND r.row_id = i.id)) AS rate_count");
        $this->db->select("(SELECT COUNT(*)   FROM ci_logs  l    WHERE (l.table='instruments'  AND l.row_id = i.id)) AS view_count");
        $this->db->select("(SELECT COUNT(*)   FROM ci_logs  l2   WHERE (l2.table='instruments' AND l2.row_id = i.id AND ip='$ip')) AS is_viewd");
        $this->db->select("(SELECT COUNT(*)   FROM ci_rates r2   WHERE (r2.table='instruments' AND r2.row_id = i.id AND (user_id ='$user_id' OR ip='$ip'))) AS is_rated");

        $id      = (int)$id;
        $row     = $this->db->where('i.id',$id)->get('instruments i',1)->row();
        $mine    = FALSE;
        $viewKey = TRUE;
        $viewMsg = NULL;

        if(empty($row))
        {
            $viewMsg = 'مطلب مورد نظر پیدا نشد. اطلاعات ورودی اشتباه است';
            $viewKey = FALSE;
            $data['_title'] = "404";
            $this->output->set_status_header('404');
        }
        elseif(($row->submitted == 0 OR $row->active == 0) && !$this->user->can('edit_instrument'))
        {
            $viewKey = FALSE;
            $viewMsg = 'اطلاعات این وسیله قابل نمایش نیست';
        }

        if($viewKey)
        {
            if(!$row->is_viewd)
                $this->logs->add('view','instruments',$row->id);

            //======================================//
            $this->bc->add('ابزارها' ,site_url('tools'));
            $this->bc->add($row->name ,current_url());
            //======================================//

            $this->load->model('m_group','group');
            $this->load->model('m_instrument','instrument');
            $this->load->model('m_comment','comment');
            $row->meta = $this->instrument->getMeta($row->id);

            if(isset($this->user->data->id) && $row->user_id == $this->user->user_id)
                $mine = TRUE;

            $row->user = $this->user->selectUserById($row->user_id,"id,displayname,username,avatar");

            if($row->thumb != NULL)
            {
                $data['thumbs'][0] = $row->thumb;

                if(isset($row->meta->thumb_json) && $this->tools->isJson($row->meta->thumb_json))
                {
                    $thumbs = json_decode($row->meta->thumb_json);
                    foreach ($thumbs as $thumb)
                        $data['thumbs'][] = $thumb;
                }
            } else
                $data['thumbs'] = array();

            $data['_title'] = $row->name;
            $data['meta_k'] = $row->name;
            $data['meta_d'] = $row->name;
        }

        $data['row']      = $row;
        $data['view_key'] = $viewKey;
        $data['view_msg'] = $viewMsg;
        $data['mine']     = $mine;

        $this->tools->view('instrument/v_view',$data);

        $this->logs->addView('view_tools');
    }

    private function _getToString()
    {
        $result = '';
        $get = $this->input->get();
        foreach ($get as $k=>$g)
        {
            if($g != '')
                $result .= "&$k=$g";
        }
        if($result)
            $result = "?" . trim($result,'&');

        return $result;
    }
}