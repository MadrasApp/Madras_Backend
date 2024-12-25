<?php
/**
 * Created by Talkabi.
 * User: nikan
 * Date: 6/23/2016
 * Time: 11:03 AM
 */

defined('BASEPATH') OR exit('No direct script access allowed');

class Group extends CI_Controller {

    function __construct(){

        parent::__construct();
        $this->load->model('m_user','user');

        if( ! $this->user->check_login() )
        {
            redirect('admin/login');
        }
        else
        {
            $this->user->checkAccess('read_group');
        }
    }

    public function index()
    {
        $data = $this->settings->data;
        $data['_title'] = ' | Groups ';

        $data['query'] = " WHERE `parent`=0 ";

        if( $this->user->can('edit_group') )
        {
            $data['options'][] = array('name'=>'ویرایش','icon'=>'pencil','href'=>'admin/group/editgroup/[FLD]');
        }

        $data['tableName'] = 'group';

        $data['query'] .= " ORDER BY `id` DESC";

        $this->_view('group/v_table',$data);
    }

    public function editgroup($id=NULL)
    {
        if(!$id OR !$this->user->can('edit_group'))
        {
            show_404();return;
        }

        $data = $this->settings->data;
        $data['_title'] = ' | Groups | edit';

        $data['group'] = $this->db->where('id',$id)->where('parent',0)->get('group')->row();

        if( $data['group'] )
        {
            $this->load->model('m_group','group');
            $data['items'] = $this->group->get($id);
			
            $html = '<li data-id="[ID]" data-position="[POSITION]" data-name="[NAME]" data-parent="[PARENT]">
                        <div class="li"><span class="id">[ID]</span><input type="text" class="name" value="[NAME]"></div>
                        [CHILDREN]
                    </li>';
			
            $data['list'] = $this->group->creatList($html);
        }
        $this->_view('group/v_edit',$data);
    }

    public function _view($view,$data)
    {
        $this->load->view('admin/v_header',$data);
        $this->load->view('admin/v_sidebar');
        $this->load->view('admin/'.$view);
        $this->load->view('admin/v_footer');
    }
}
