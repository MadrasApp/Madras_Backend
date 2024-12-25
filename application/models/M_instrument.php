<?php defined('BASEPATH') OR exit('No direct script access allowed');

if(!session_id()) session_start();

class M_instrument extends CI_Model {

	public $data;
    public $setting = NULL;

	function __construct(){
		
		parent::__construct();
        $this->setting = $this->settings->data;
	}

    public function add($data,$meta=NULL)
    {
        $me              = isset($this->user->data->id) ? $this->user->data->id:NULL;
        $for_sale        = $data['for_sale'] == 1?1:0;
        $data['price_1'] = (isset($data['price_1']) && $data['price_1'] != '') ? $data['price_1']:NULL;
        $data['price_2'] = (isset($data['price_2']) && $data['price_2'] != '') ? $data['price_2']:NULL;
        $data['price_3'] = (isset($data['price_3']) && $data['price_3'] != '') ? $data['price_3']:NULL;

        $data = array(
            'user_id'     => $me,
            'name'        => $data['name'],
            'description' => $data['description'],
            'thumb'       => isset($data['thumb']) && trim($data['thumb']) != '' ? $data['thumb']:NULL,
            'price'       => $data['price'],
            'price_1'     => $for_sale ? NULL:$data['price_1'],
            'price_2'     => $for_sale ? NULL:$data['price_2'],
            'price_3'     => $for_sale ? NULL:$data['price_3'],
            'min_day'     => $for_sale ? NULL:$data['min_day'],
            'tel'         => $data['tel'],
            'for_sale'    => $for_sale,
            'submitted'   => $this->setting['auto_submit_instrument'],
            'active'      => isset($data['active']) ? $data['active']:1,
            'date'        => date('Y-m-d H:i:s')
        );

        if(!$this->db->insert('instruments',$data))
            return FALSE;

        $id = $this->db->insert_id();

        if($meta)
            $this->addMeta($meta,$id);

        return $id;
    }

    public function update($id,$data){
        return $this->db->where('id',$id)->update('instruments',$data);
    }

    /**********************************
                 Meta
    ********************************/

    public function addMeta($data,$id=NULL)
    {
        if(!$id OR !is_array($data)) return FALSE;

        foreach( $data as $key=>$value  )
        {
			if( is_array($value) )
			{
				foreach( $value as $v )
				$this->db->insert('instrument_meta' , array(
					'instrument_id' => $id,
					'meta_name'     => $key,
					'meta_value'    => $v
				));
				$value = $this->MakeJSON($value);
				$key .= '_json';
			}
            $this->db->insert('instrument_meta' , array(
                'instrument_id' => $id,
                'meta_name'     => $key,
                'meta_value'    => $value
            ));
        }
        return TRUE;
    }

    public function updateMeta($data,$id=NULL)
    {
        if(!$id OR !is_array($data)) return FALSE;

        foreach( $data as $key=>$value  )
        {
            if( is_array($value) )
            {
                $this->db->where(array(
                    'instrument_id' => $id,
                    'meta_name'     => $key.'_json',
                ))->delete('instrument_meta');
            }
            $this->db->where(array(
                'instrument_id'    => $id,
                'meta_name'        => $key,
            ))->delete('instrument_meta');
        }
        return $this->addMeta($data,$id);
    }

    public function getMeta($id=NULL)
    {
        if(!$id) return NULL;

        $meta = new stdClass();

        $data = $this->db->where('instrument_id',$id)->get('instrument_meta')->result();

        foreach( $data as $key=>$value )
        {
			if( isset($meta->{$value->meta_name}) )
			{
				if( ! is_array($meta->{$value->meta_name}) )
					$meta->{$value->meta_name} = array($meta->{$value->meta_name});
				array_push($meta->{$value->meta_name},$value->meta_value);
			}
			else
				$meta->{$value->meta_name} = $value->meta_value;
        }
        return $meta;
    }

    public function deleteMeta($id=NULL,$metaKey=NULL)
    {
        if(!$id) return NULL;

        $this->db->where('instrument_id',$id);

        if($metaKey != NULL)
            $this->db->where('meta_name',$metaKey);

        return $this->db->delete('instrument_meta');
    }

	public function meta($meta=NULL,$metaName=NULL)
	{
		if( ! $meta OR ! $metaName ) return NULL;
		
		if( isset( $meta->$metaName ) && $meta->$metaName != '' )
		return $meta->$metaName;
		
		return NULL;
	}

    public function htmlTemplate($item , $class="col-xs-12 col-sm-6 col-md-4 col-lg-3")
    {
        if(empty($item)) return NULL;

        $html = "";

        $thumb  = (isset($item->thumb) && $item->thumb != '' && file_exists($item->thumb)) ? $item->thumb:$this->setting['default_ins_image'];
        $thumbs = [];
        $rated  = $item->is_rated ? 'on':'';

        if(isset($item->thumb_json) && $this->tools->isJson($item->thumb_json))
            $thumbs = json_decode($item->thumb_json);

        $item->for_sale = 0; // show all tools as rent only until we need to change this

        $html .= '<div class="'.$class.'">';
        $html .= '  <a href="'.(site_url('tools/view/'.$item->id)."/".STU($item->name)).'" title="'.(html_escape($item->name)).'" target="_blank">';
        $html .= '        <div class="tools-item '.($item->for_sale == 1 ? 'for-sale':'for-rent').'" data-forsale="'.$item->for_sale.'">';
        $html .= '            <div class="item-sale-rent"></div>'; //title="'.($item->for_sale == 0 ? 'اجاره ای':'فروشی').'" data-placement="left"
        $html .= '            <div class="item-thumb" data-bg="'.(thumb($thumb,300)).'">';

        if(!empty($thumbs))
        {
            $html .= '            <div class="thumbs-xs">';
            foreach ($thumbs as $thumb)
                $html .= '              <img src="'.thumb($thumb,150).'" data-img="'.(thumb($thumb,300)).'" alt="'.html_escape($item->name).'">';
            $html .= '            </div>';
        }

        $price = ($item->price_1 != '' && is_numeric($item->price_1)) ? number_format($item->price_1):$item->price_1;

        $html .= '            </div>';
        $html .= '            <div class="item-icons clearfix">';
        $html .= '                <span class="item-view" title="'.($item->view_count ?'بازدید':'بدون بازدید').'"><i class="fa fa-play-circle-o ml-10"></i>'.($item->view_count ? :'').'</span>';
        $html .= '                <span class="item-rate toggle-rate '.$rated.'" title="پسندها" data-toggle=\'{"table":"instruments","row":'.$item->id.'}\'>';
        $html .= '                    <i class="fa fa-star"></i>';
        $html .= '                    <span>'.($item->rate_count ? :'' ).'</span>';
        $html .= '                </span>';
        $html .= '            </div>';
        $html .= '            <div class="item-details clearfix">';
        $html .= '                <span class="item-date ellipsis">'.$this->tools->Date($item->date,TRUE,TRUE).'</span>';
        $html .= '                <span class="item-price ellipsis">'.$price.'</span>';
        $html .= '            </div>';
        $html .= '            <div class="item-state clearfix ellipsis">';
        $html .= '                <span>'.$item->province.'</span><i class="fa fa-angle-double-left ml-5 mr-5"></i><span>'.$item->state.'</span>';
        $html .= '            </div>';
        $html .= '            <h2 class="item-name ellipsis">'.$item->name.'</h2>';
        $html .= '        </div>';
        $html .= '    </a>';
        $html .= '</div>';

        return $html;
    }
    
    /**===========================================*/

    /**
     * @param int $id
     * @return bool
     */
    public function delete($id=0)
    {
        $id = (int)$id;
        if(!$id) return FALSE;

        $row = $this->db->where('id',$id)->get('instruments')->row();

        if(empty($row)) return FALSE;

        $meta = $this->getMeta($id);

        if( ! $this->db->where('id',$id)->delete('instruments') )
            return FALSE;

        $this->load->model('admin/m_media','media');

        $this->media->deleteFile($row->thumb);

        if(isset($meta->thumb_json) && $this->tools->isJson($meta->thumb_json))
        {
            $thumbs = json_decode($meta->thumb_json);
            foreach ($thumbs as $thumb)
                $this->media->deleteFile($thumb);
        }

        $this->db->where('table','instruments')->where('row_id',$id)->delete('comments');
        $this->db->where('table','instruments')->where('row_id',$id)->delete('rates');
        $this->db->where('table','instruments')->where('row_id',$id)->delete('logs');

        return TRUE;
    }
	private function MakeJSON($data){
		return json_encode($data,JSON_UNESCAPED_UNICODE | JSON_HEX_QUOT | JSON_HEX_TAG);
	}//Alireza Balvardi
}