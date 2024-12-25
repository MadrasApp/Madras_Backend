<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Proficient extends CI_Controller{
	
	function __construct(){
		parent::__construct();
	}

	public function index($page=1)
	{

        $data['_title'] = "متخصصین";


        $page = intval($page);

        $perPage = $this->settings->data['users_perpage'];

        if($page)
        {
            $min = $page == 1 ? 0 : $perPage*($page-1);
        }
        else
        {
            $min = 0;$page = 1;
        }

        $state = (int)$this->input->get('state');
        $skill = (int)$this->input->get('skill');
        $order = $this->input->get('order');

        //======================================//
        $this->bc->add('متخصصین',site_url("proficient"));

        if($state)
            $this->bc->add($this->db->get_field('name','group',$state),site_url("proficient") ."?state={$state}");

        if($skill)
            $this->bc->add($this->db->get_field('name','group',$skill),site_url("proficient") ."?skill={$skill}");

        if($page > 1)
            $this->bc->add(' صفحه ' . $page ,current_url());
        //======================================//


        $this->db->start_cache();

        if($state) $this->db->join( $this->db->dbprefix('user_meta') . ' ms',"(ms.user_id=u.id AND ms.meta_name='state' AND ms.meta_value='$state')",'inner',FALSE);
        if($skill) $this->db->join( $this->db->dbprefix('user_meta') . ' mk',"(mk.user_id=u.id AND mk.meta_name='skill' AND mk.meta_value='$skill')",'inner',FALSE);

        $this->db->where('u.active',1);
        $this->db->where('u.type','expert');

        $totalRows = $this->db->count_all_results('users u');
        $this->db->stop_cache();

        $this->db->select("u.id,u.username,u.displayname,u.avatar,u.approved,u.last_seen,u.age");
        $this->db->select("(SELECT COUNT(r.id)   FROM ci_rates r WHERE(r.table='users' AND r.row_id=u.id AND r.rating != 0) )  AS rate_count");
        $this->db->select("(SELECT SUM(r.rating) FROM ci_rates r WHERE(r.table='users' AND r.row_id=u.id AND  r.rating != 0) ) AS rating_sum");
        $this->db->select("(SELECT ROUND((rating_sum/rate_count),1)) AS rating",FALSE);
        $this->db->select("(SELECT meta_value    FROM ci_user_meta m WHERE(m.meta_name='skill_json' AND m.user_id=u.id) )      AS skill_json");
        $this->db->select("(SELECT meta_value    FROM ci_user_meta m WHERE(m.meta_name='state_json' AND m.user_id=u.id) )      AS state_json");
        $this->db->select("(SELECT COUNT(*)      FROM ci_onlines o WHERE(o.user_id=u.id) )                                     AS is_online");

        if($order)
        {
            switch ($order)
            {
                case'username':
                    $this->db->order_by('u.username','asc');
                    break;
                case'rating':
                    $this->db->order_by('rating','desc');
                    break;
                case 'name':
                    $this->db->order_by('u.displayname','asc');
                    break;
                case'lastseen':
                    $this->db->order_by('u.last_seen','desc');
                    break;
                case'online':
                    $this->db->order_by('is_online','desc');
                    break;
            }
        }
        $this->db->order_by('approved','desc');
        $this->db->order_by('rating','desc');
        $this->db->group_by('u.id');

        $this->db->limit($perPage,$min);

        $get = $this->db->get('users u');
        $this->db->flush_cache();
        $users = $get->result();

        $query = "";

        if($state) $query .= "&state=$state";
        if($skill) $query .= "&skill=$skill";
        if($order) $query .= "&order=$order";
        $query = trim($query,'&');
        if($query) $query = "/?$query";

        $config = array(
            'page'       => $page,
            'perPage'    => $perPage,
            'totalRows'  => $totalRows,
            'url'        => site_url('proficient')."/page/[PAGE]".$query
        );

        $data['pg'] = $this->tools->pagination($config);

        $data['users'] = $users;

        $this->load->model('m_group','group');

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
            $data['states'] = $this->group->creatSelect(PROVINCE_ID,NULL,'<article class="group-holder"><select class="form-control" name="state">','</select></article>',FALSE,$state,'تمام استان ها');


        if( $skill )
        {
            $skills = $this->group->getParents($skill);
            $data['skills'] = ""; $i = 0;
            foreach ($skills as $sid)
            {
                $id =  $i == 0 ? SKILL_ID : $skills[$i-1]; $i++;
                $data['skills'] .= $this->group->creatSelect($id,NULL,'<article class="group-holder"><select class="form-control" name="skill">','</select></article>',FALSE,$sid,'تمام تخصص ها');
            }
        }
        else
            $data['skills'] = $this->group->creatSelect(SKILL_ID,NULL,'<article class="group-holder"><select class="form-control" name="skill">','</select></article>',FALSE,$skill,'تمام تخصص ها');


        $ids = array();
        foreach($users as $user)
        {
            if( $this->tools->isJson($user->state_json) )
                foreach($this->tools->jsonDecode($user->state_json) as $id)
                    if( ! in_array($id,$ids) ) $ids[] = $id;

            if( $this->tools->isJson($user->skill_json) )
                foreach($this->tools->jsonDecode($user->skill_json) as $id)
                    if( ! in_array($id,$ids) ) $ids[] = $id;
        }

        $groups = array();

        if($users)
        {
            $nReaults = $this->db->where_in('id',$ids)->select('id,name')->get('group')->result();
            foreach ($nReaults as $row)
                $groups[$row->id] = $row->name;
        }

        $data['groups'] = $groups;

        $data['css'][] = 'vendor/job-manager/frontend.css';

		$this->tools->view('v_proficient',$data);

        $this->logs->addView('Proficient');
	}
}