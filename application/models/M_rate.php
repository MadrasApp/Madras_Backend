<?php defined('BASEPATH') OR exit('No direct script access allowed');

class M_rate extends CI_Model {
	
	function __construct()
	{
		parent::__construct();
	}
	
	public function rateIt($table=NULL,$row_id=NULL,$rating = 0)
	{
		if( $table && $row_id )
		{
			if( $this->db->table_exists($table) && $this->db->where('id',$row_id)->count_all_results($table) > 0 )
			{
				if( ! $this->isRated($table,$row_id) )
				{
					$user = isset($this->user->user->data->id) ? $this->user->user_id: $this->input->ip_address();
					$data = array(
						'user_id'  => $user   ,
						'table'    => $table  ,
						'row_id'   => $row_id ,
                        'rating'   => $rating
					);					
					if( $this->db->insert('rates',$data) )
					return TRUE;
				}
			}
		}
		return FALSE;		
	}
	
	public function toggleRate($table=NULL,$row_id=NULL)
	{
		if( $table && $row_id )
		{
			if( $this->db->table_exists($table) && $this->db->where('id',$row_id)->count_all_results($table) > 0 )
			{
				if( $this->isOwn($table,$row_id) )
				    return "شما نمی توانید به خودتان رای دهید";
				
				$ip = $this->input->ip_address();
				$user = $this->user->user->data !== NULL ? $this->user->user_id:$ip;
				$data = array(
					'user_id' => $user,
					'ip'      => $ip,
					'table'   => $table,
					'row_id'  => $row_id
				);					
				if( ! $this->isRated($table,$row_id) )
				{
					$data['id'] = $this->db->last_id('rates');
					if( $this->db->insert('rates',$data) )
					return TRUE;
				}
				else
				{
					$data1 = array(
						'user_id' => $user,
						'table'   => $table,
						'row_id'  => $row_id
					);
					$data2 = array(
						'ip'      => $ip,
						'table'   => $table,
						'row_id'  => $row_id
					);						
					if($this->db->where($data1)->delete('rates') && $this->db->where($data2)->delete('rates'))
					return TRUE;
				}
			}
		}
		return FALSE;		
	}	
	
	public function isRated($table=NULL,$row_id=NULL)
	{
		if( $table && $row_id )
		{
			$ip = $this->input->ip_address();
			$user = $this->user->user->data !== NULL ? $this->user->user_id:$ip;
			$data1 = array(
				'user_id' => $user,
				'table'   => $table,
				'row_id'  => $row_id
			);
			$data2 = array(
				'ip'      => $ip,
				'table'   => $table,
				'row_id'  => $row_id
			);			
			if( 
				$this->db->where($data1)->count_all_results('rates') > 0
				OR 
				$this->db->where($data2)->count_all_results('rates') > 0
			)
			return TRUE;
		}
		return FALSE;		
	}	

	public function rateCount($table=NULL,$row_id=NULL)
	{
		if( $table && $row_id )
		{
			$data = array(
				'table'=>$table,
				'row_id'=>$row_id
			);
			return $this->db->where($data)->count_all_results('rates');
		}
		return FALSE;		
	}
	
	public function isOwn($table,$id)
	{	
		if( $table == 'users' )
		{
			if( @$this->user->user_id == $id )
			return TRUE;
		}
		elseif($table == 'posts')
		{
			$author = $this->db->get_field('author',$table,$id);
			if( $author == @$this->user->user_id )
			return TRUE;
		}
		elseif($table == 'comments')
		{
			$user = $this->db->get_field('user_id',$table,$id);
			$ip   = $this->db->get_field('ip',$table,$id);
			if( $user == @$this->user->user_id OR $ip == $this->input->ip_address() )
			return TRUE;
		}
		return FALSE;		
	}
    /*=========================
            Rating x/y
    =========================*/
    public function setRating($table=NULL,$row_id=NULL,$rating=0)
    {
        if( $table && $row_id )
        {
            if( $this->db->table_exists($table) && $this->db->where('id',$row_id)->count_all_results($table) > 0 )
            {
                if( $this->isOwn($table,$row_id) )
                    return "شما نمی توانید به خودتان رای دهید";

                $ip = $this->input->ip_address();
                $user = $this->user->user->data !== NULL ? $this->user->user_id:$ip;

                $rating = (int) $rating;

                if( $rating > 5 ) $rating = 5;
                if( $rating < 1 ) $rating = 1;

                $data = array(
                    'user_id' => $user   ,
                    'ip'      => $ip     ,
                    'table'   => $table  ,
                    'row_id'  => $row_id ,
                    'rating'  => $rating
                );

                $data1 = array(
                    'user_id' => $user,
                    'table'   => $table,
                    'row_id'  => $row_id
                );
                $data2 = array(
                    'ip'      => $ip,
                    'table'   => $table,
                    'row_id'  => $row_id
                );
                $this->db->where($data1)->delete('rates');
                //$this->db->where($data2)->delete('rates');

                //$data['id'] = $this->db->last_id('rates');
                if( $this->db->insert('rates',$data) )
                    return TRUE;
            }
        }
        return 'اطلاعات ارسالی صحیح نمی باشد';
    }

    public  function ratingCount($table=NULL,$row_id=NULL)
    {
        $data = array(
            'table'   => $table  ,
            'row_id'  => $row_id ,
            'rating !='  => 0
        );
        return $this->db->where($data)->count_all_results('rates');
    }

    public  function ratingAvg($table=NULL,$row_id=NULL,$mode='int')
    {
        $data = array(
            'table'      => $table  ,
            'row_id'     => $row_id ,
            'rating !='  => 0
        );

        $sum = $this->db->where($data)->select_sum("rating")->get('rates')->row()->rating;
        $total = $this->db->where($data)->count_all_results('rates');

        if( $sum === NULL OR $total === 0 )
            $rating = 0;
        else
            $rating = $sum/$total;

        if($mode == 'int') return $rating;

        return array('rating'=>$rating,'sum'=>$sum,'total'=>$total);
    }

    public function ratingHtml($table=NULL,$row_id=NULL,$html=NULL,$data=NULL)
    {

        if( ! $data ) $data = $this->ratingAvg($table,$row_id,'array');

        $R     = $data['rating'];
        $total = $data['total'];

        if( ! $html )
        $html = '<div class="rating-stars" title="{INFO}">{STARS}</div>';

        /*$stars = "";
        for($i=1;$i<=5;$i++)
        {
            $cls = $i <= $R ? 'star':'star-o';
            if( $i-1 < $R && $R < $i ) $cls = 'star-half-o';
            $stars .=  '<i class="fa fa-'.$cls.'"></i>' ."\n";
        }*/
        $stars = $this->ratingStars($R);

        $rating = round($R*10)/10;

        $info = "{$rating} از 5 - {$total} رای";

        $html = str_replace('{STARS}',$stars ,$html);
        $html = str_replace('{INFO}',$info ,$html);
        $html = str_replace('{TOTAL}',$total ,$html);
        $html = str_replace('{RATING}',$rating ,$html);

        return $html;
    }

    public function ratingStars($R=0)
    {
        $stars = "";
        for($i=1;$i<=5;$i++)
        {
            $cls = $i <= $R ? 'star':'star-o';
            if( $i-1 < $R && $R < $i ) $cls = 'star-half-o';
            $stars .=  '<i class="fa fa-'.$cls.'"></i>' ."\n";
        }
        return $stars;
    }
}