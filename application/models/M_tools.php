<?php defined('BASEPATH') OR exit('No direct script access allowed');

class M_tools extends CI_Model {
	
	public $setting = NULL;
	public $data = NULL;
	
	function __construct()
	{
		parent::__construct();
		$this->setting = $this->settings->data;		
	}

	public function isJson($string)
	{
		json_decode($string);
		return (json_last_error() == JSON_ERROR_NONE);		
	}
	
	public function jsonDecode($string,$mode = 1)
	{
		if( $mode == 1)
		{
			return json_decode(str_replace('&quot;', '"', $string), true);				
		}
		else
		{
			return json_decode($string);
		}
	}
		
	public function postTypeProp($type = 'post', $prop = NULL)
	{
		if( $type && $prop && isset($POST_TYPES) && isset($POST_TYPES[$type]) )
		{
			switch ($prop)
			{
				case 's_name': 
				case 'g_name': 
				case 'icon':
				return $POST_TYPES[$type][$prop];
				break;
				case '': 
				break;
			}
		}
		return NULL;
	}

	public function Date($date,$html=TRUE,$short=FALSE){
		
		$setting = $this->settings->data;
		date_default_timezone_set($setting['time_zone']);
		
		$datestr = strtotime($date);
		$d = date("d",$datestr);
		$m = date("m",$datestr);
		$Y = date("Y",$datestr);
		
		$datestr_n = strtotime("now");
		$d_n = date("d",$datestr_n);
		$m_n = date("m",$datestr_n);
		$Y_n = date("Y",$datestr_n);
		
		$D = $d_n-$d;
		$date = date("Y-m-d H:i:s",$datestr);
		
		if(($D==0 || $D == 1) && ($m_n == $m && $Y_n == $Y))
		{
			$return = ($D? ' دیروز - ' : ' امروز - ' ).jdate($setting['time_format'],$datestr,"","","en");
		}
		elseif($D > 0 && $D < 7 && $m_n == $m && $Y_n == $Y)
		{
			$return = jdate('l',$datestr).' - ' .jdate($setting['time_format'],$datestr,"","","en");
		}
		elseif($m_n == $m && $Y_n == $Y && $D==-1)
		{
			$return = ' فردا '.jdate($setting['time_format'],$datestr,"","","en");
		}
		else $return = jdate($setting['date_format'].' - '.$setting['time_format'],$datestr,"","","en");

        if($Y == $Y_n)
        {
            $return = str_replace(jdate('Y',$datestr,"","","en"),'' ,$return);
            $return = str_replace(jdate('y',$datestr,"","","en"),'' ,$return);
        }

        if($short == TRUE && ($Y != $Y_n OR ($Y == $Y_n && $m != $m_n)))
        {
            $return = jdate($setting['date_format'],$datestr,"","","en");
        }

		$title = jdate($setting['date_format'].' - '.$setting['time_format'],$datestr,"","","en");
		
		$datestr = $datestr - date('Z');
		
		if( $html )
		return  '<span class="relative-date" datestr="'.( $datestr ). 
				'" date="'.$return.'" title="'.$title.'" >'.$return.'</span>';
		
		else return array(
			'datestr' => $datestr,
			'date'    =>$return,
			'full'    =>$title
		);
	}	
	/*==========================================================
						  .:: Category ::.
	==========================================================*/
	public function setCategoryList($type='post')
	{		
		$data = $this->db ->where('type',$type)->order_by('position')->get('category')->result();
		
		if( $data )
		{
			foreach( $data as $key=>$value ){
				$this->data['category'][$type][$value->id] = 
					array(
						'name'=>$value->name,
						'description'=>$value->description,
						'pic'=>$value->pic,
						'icon'=>@$value->icon,
						'parent'=>$value->parent,
					);
			}			
		}
		else 
		{
			$this->data['category'][$type] = NULL;
		}
	}
	
	public function getCategoryChildren($type='post',$parent=0)
	{		
		if( ! isset( $this->data['category'][$type] ) )
		$this->setCategoryList($type);
		
		$result = NULL;
		
		if( $this->data['category'][$type]  )
		foreach( $this->data['category'][$type] as $key=>$value ){
			if($value['parent'] == $parent)
			$result[$key] = $value;
		}
		
		return $result;		
	}
		
	public function getCategoryIcon($type,$id)
	{
		$icon = NULL;			
		if( isset($this->data['category'][$type][$id]) )
		{
			$cList = $this->data['category'][$type][$id];
			$icon = $cList['icon'] ?:
			( $cList['parent'] != 0 ? $this->getCategoryIcon($type,$cList['parent']):'' );			
		}
		if( $icon ) $icon = "fa fa-$icon";
		return $icon;	
	}
	
	public function getCategoryLink($type,$id)
	{
		/*$link = array();			
		if( isset($this->data['category'][$type]) )
		{
			$cList = $this->data['category'][$type];
			$link[] = $cList[$id]['name'];
			while( $id = $cList[$id]['parent'] ){
				$link[] = $cList[$id]['name'];
			}
		}
		
		$link = site_url( "category/".implode('/',array_reverse($link)) );
		*/
		
		if( isset($this->data['category'][$type]) )
		{
			$cList = $this->data['category'][$type];
			$name = $cList[$id]['name'];
		}
		$link = site_url( "category/$id")."/".STU($name);
		return $link;	
	}	
		
	public function categoryList($type='post',$parent=0,$start='<ul>',$end='</ul>',$html=NULL)
	{
		$data = $this->getCategoryChildren($type,$parent);

        if( ! $html )
		    $html = '<li><a href="[LINK]"><i class="[ICON]"></i>[NAME]</a> [CHILDS]</li>';


		if($data)
		{
			foreach($data as $id=>$value)
			{
				$icon   = $this->getCategoryIcon($type,$id);
				$href   = $this->getCategoryLink($type,$id);
				$childs = $this->getCategoryChildren($type,$id);

                $this->categoryList($type,$id,$start,$end,$html);

				?>
				<li>
					<?php if($childs): ?>
                    <span class="has-child"></span>
					<?php endif ?>
					<a href="<?php echo $href ?>">
						<i class="<?php echo $icon ?>"></i>
						<span><?php echo $value['name'] ?></span> 
					</a>
                    <?php if($childs): ?>
					<ul>
						<li></li>
						<?php	?>
					</ul>
                    <?php endif ?>
				</li>
				<?php
			}
		}
	}


     
	
	/*===============================================
	
	===============================================*/	
	public function generateRandomString($length = 10)
	{
		$str = "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
		return substr(str_shuffle($str), 0, $length);
	}
	
	public function encryptString($text , $key = NULL )
	{
		if( $key === NULL )
		{
			$str = "¥;GÇlPá®«hæá“¦žnÃ‚å×ODA86m&Â™|ôM£ïÜã]õ}¯=\¢Õë²w’Û³&vl>Ö+çk¾SÃG^ÓÚcäA>ŽÕÅ–™ÜØ]öû";
			$key = substr(str_shuffle($str), 0, 8);;
		}
		srand((double) microtime() * 1000000); 
		$iv = mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_BLOWFISH, MCRYPT_MODE_CFB), MCRYPT_RAND);
		$cipher = mcrypt_encrypt(MCRYPT_BLOWFISH, $key, $text, MCRYPT_MODE_CFB, $iv);
		
		return $iv . $cipher . $key ;
	}
	
	public function decryptString($text , $key = NULL)
	{
		if( $key === NULL )
		{
			$key = substr($text,-8);
			$text = substr($text,0,-8);
		}
		$iv = substr($text, 0, mcrypt_get_iv_size(MCRYPT_BLOWFISH, MCRYPT_MODE_CFB));
		$cipher = substr($text, mcrypt_get_iv_size(MCRYPT_BLOWFISH, MCRYPT_MODE_CFB));
	
		return mcrypt_decrypt(MCRYPT_BLOWFISH, $key, $cipher, MCRYPT_MODE_CFB, $iv);
	}	
	
	public function outS($status = 0 , $msg = "All done !" , $data = NULL)
	{
		$result = array(
			'done'   => TRUE ,
			'status' => $status ,
			'msg'    => $msg
		);
		
		if( is_array($data) ) $result = array_merge($result,$data);
		
		$this   ->output
				->set_content_type('application/json')
				->set_output($this->MakeJSON($result));			
	}
	
	public function outE($e , $data = NULL)
	{
		$result = array(
			'done'   => FALSE,
			'status' => $e->getCode(),
			'msg'    => $e->getMessage()
		);
		if( is_array($data) ) $result = array_merge($result,$data);
		
		$this   ->output
				->set_content_type('application/json')
				->set_output($this->MakeJSON($result));							
	}
	
	public function sub($str,$len,$suff = "...")
	{
		if( mb_strlen($str) <= $len ) $suff = "";
		
		return mb_substr($str,0,$len)."$suff";
	}
		
	/******************************/
	public function tableQuery($tableName,$where = "",$do="id",$ds="desc")
	{
		$query = $where; $querySortAr = NULL;
		
		if(strpos($do,','))
		{
			$querySortAr = explode(',',$do);
		}	
		
		$order = $this->input->get($tableName.'_order');
		
		if( ! $order OR ! $this->db->field_exists($order,$tableName) )
		$order = $do;
		
		$sort = $this->input->get($tableName.'_sort');
		if( $sort ) $sort = strtolower($sort); 
		if( $sort != 'asc' && $sort !='desc' ) $sort = $ds;		
		
		if( $querySortAr && ! $this->input->get($tableName.'_sort')  )
		{
			$query .= " order by  ";
			foreach($querySortAr as $k=>$q )
			{
				list( $order , $sort ) = explode(' ',trim($q));
				$query .= " `$order` $sort , ";
			}
			$query = rtrim(trim($query),',');			
		}
		else
		{
			$query .= " order by  `$order` $sort";
		}
		return $query;
	}
	
	public function toggleField($table = NULL,$field = NULL ,$id = NULL,$tg="0|1")
	{
		if( $table && $field && $id )
		{
			$id = intval($id);
			if(
				$this->db->table_exists($table) && 
				$this->db->field_exists($field,$table) &&
				$this->db->where('id',$id)->count_all_results($table) > 0
			){
				$tg = explode('|',$tg);
				if(isset($tg[0],$tg[1]))
				{
					$fld = $this->db->get_field($field,$table,$id);
					if( $fld != -1 )
					{
						$fld = $fld == $tg[1] ? $tg[0]:$tg[1];
						if( $this->db->where('id',$id)->update($table,array($field=>$fld)) )
						return TRUE;
					}
				}
			}
		}
		return FALSE; 
	}

	public function out($response=NULL)
	{
		if( ! $response ) return NULL;
		 
		$accept = @$_SERVER['HTTP_ACCEPT'];
		if( !empty( $accept ) )
		{
			$accept = explode(',',$accept);
			if( isset( $accept[0] ) && $accept[0] == 'application/json' )
			$response = $this->MakeJSON($response);
		}
		return $response;
	}
	
	public function searchStringCompile($str=NULL,$s=NULL,$range=40)
	{
		if( ! $str OR ! $s ) return NULL;
		
		$result = "";
		
		$str = stripHTMLtags($str);
		$str = preg_replace("/\&(.*?)\;/i", ' ',$str);
		$str = preg_replace('!\s+!', ' ', $str);
		$str = str_replace(array("\n","\r"), '.',$str);
		
		$sents = preg_split("/[.!?;،؟]/u",$str);
		
		$i = 0;
		foreach( $sents as $se )
		{
			$se = trim($se);
            $pos = mb_strpos($se,$s,0,'UTF-8');
			
			if( $pos === FALSE ) continue;
			
			if( $i > 2 ) break;

			$begin = $pos < $range ? 0 : $pos - $range; 
			$len   = $pos + $range;
			
			if( $begin != 0 ) 
			while( mb_substr($se,$begin,1,'UTF-8') != ' ' && $begin < $len ) $begin++;
			
			while( mb_substr($se,($len-1),1,'UTF-8') != ' ' && $len > 0 ) $len--;
			
			$add = trim(mb_substr($se,$begin,$len,'UTF-8'));
			
			$sLen = mb_strlen($s,'UTF-8');
			if( $pos - $sLen > $range ) $add = "...$add";
			
			if( mb_strlen($add,'UTF-8') < mb_strlen($se,'UTF-8') ) $add .= "...";
			
			$result .= " $add <br>";
			$i++;
		}
		
		$result = preg_replace("/($s)/i", "<span class='match'>$1</span>",$result);
		return $result;
	}

    public function sendEmail($to,$subject,$msg,$from = "")
    {
        $msg =
            /** @lang text */
            '<div style="max-width:600px;margin:60px auto;padding:10px;direction:rtl;font-family:Tahoma;background-color:#23333F;color:#ccc;">
				<img style="width:100px;float:right" alt="{site-title}" src="{site-url}{logo-src}">
				<h3 style="float:right">{site-title} ::: '.$subject.'</h3>
				<div style="clear:both"></div>
				<hr> 
				<div style="padding:10px 0 30px 0;color:#ccc;">'.$msg.'</div>
				
			</div>';
			

        $replace = array(
            '{site-title}'       => $this->setting['title'],
            '{logo-src}'         => $this->setting['site_logo'],
            '{site-url}'         => base_url(),
            '{site_about}'       => trim($this->setting['site_about']) != '' ? '<hr>'.$this->setting['site_about']:'',
            '<hr>'               => '<div style="margin:15px 0;border-bottom:dashed 2px #ddd;height:1px"></div>',
            '<hr small>'         => '<div style="margin:10px 0;height:1px"></div>',
        );

        $msg = str_replace(array_keys($replace),array_values($replace),$msg);

        if( $to == 'admin' )
            $to = $this->setting['site_email'];

        if( ! $from ) $from = "afarineshweb@gmail.com";

        if($this->setting['mail_type'] == 'smtp')
        {
            $config['protocol']  = 'smtp';
            $config['smtp_host'] = $this->setting['smtp_server'];
            $config['smtp_port'] = $this->setting['smtp_port'];
            $config['smtp_user'] = $this->setting['smtp_user'];
            $config['smtp_pass'] = $this->setting['smtp_pass'];
        }
        $config['mailtype']  = 'html';

        $this->load->library('email', $config);
        $this->email->from($from, $this->setting['title']);
        $this->email->to($to);
        $this->email->subject($subject);
        $this->email->message($msg);

        return $this->email->send();
    }

    /**
     * @param array $config
     * @return null|string
     */
    public function pagination($config = [])
	{
		$default = array('page'=>1,'totalRows'=>0,'perPage'=>10,'url'=>'','num'=>2 );
		
		$config  = array_merge($default,$config);

        extract($config);
		//foreach($config as $K=>$V) $$K = $V;
		
		function p($p,$url)
		{
			return str_replace('[PAGE]',$p,$url);
		}
		
		$allPages = ceil($totalRows/$perPage); 
		
		if($allPages == 0 OR $allPages == 1) return NULL;
		
		$html = "<ul class=pagination>";

		$min = $page-$num; $max = $page+$num;
		
		if( $min < 1 ) $max += abs($min)+1;
		elseif( $max > $allPages ) $min -= $max-$allPages;
		
		
		if( $page > 1 )
		{
			if( $min > 1 )
			$html .= '<li data-page="1"><a href="'. p(1,$url) .'"><i class="fa fa-backward"></i></a></li> ';
		}
		
		for( $c = $min; $c <= $max; $c++ )
		if( $c <= $allPages && $c > 0 )
		{
			$html .= '<li data-page="'.$c.'" '.( $c == $page ? 'class="active"' : '' ).'>';
			$html .= '<a href="'.( $c != $page ? p($c,$url) : "#" ).'" >'.$c.'</a>';
			$html .= '</li>';
		}
		
		if( $page < $allPages && $max < $allPages)
		{
			$html .= 
			'<li data-page="'.$allPages.'">
				<a href="'.p($allPages,$url).'"><i class="fa fa-forward"></i></a>
			 </li>';		
		} 
		
		$html .= "</ul>";
		return $html; 		
	}

    /**
     * @param $case
     * @param string $additional
     * @return string
     */
    public function buildQuery($case, $additional="")
	{
		$query = "";$table_prefix = "ci_";
		$ip = $this->input->ip_address();
		$userid = isset($this->user->data->id) ? $this->user->user_id : NULL;
		
		switch($case)
		{
			case 'posts':
			$user_id = 	$userid ? : $ip;
			$query =
                /** @lang text */
                "SELECT *
                ,(SELECT avatar   FROM +users    WHERE id = p.author)                       AS author_avatar
                ,(SELECT username   FROM +users    WHERE id = p.author)                     AS author_name
                ,(SELECT COUNT(*) FROM +rates    WHERE (`table`='posts' AND row_id = p.id)) AS rate_count
                ,(SELECT COUNT(*) FROM +comments WHERE (`table`='posts' AND row_id = p.id)) AS comment_count
                ,(SELECT COUNT(*) FROM +logs     WHERE (`table`='posts' AND row_id = p.id)) AS view_count
                ,(SELECT COUNT(*) FROM +rates    WHERE (`table`='posts' AND row_id = p.id AND 
											(user_id = '$user_id' OR ip = '$user_id' ))) AS is_rated	
			FROM +posts p	 	 
			$additional";			
			
			break;	
			/*******************************************************************************/		
			case 'view-post':			
			$user_id = 	$userid ? : $ip;		
			$query =
                /** @lang text */
                "SELECT p.*
                ,(SELECT avatar   FROM +users WHERE id=p.author)                            AS author_avatar
                ,(SELECT username FROM +users WHERE id=p.author)                            AS author_name
                ,(SELECT COUNT(*) FROM +rates    WHERE (`table`='posts' AND row_id = p.id)) AS rate_count
                ,(SELECT COUNT(*) FROM +comments WHERE (`table`='posts' AND row_id = p.id)) AS comment_count
                ,(SELECT COUNT(*) FROM +logs     WHERE (`table`='posts' AND row_id = p.id)) AS view_count
                ,(SELECT COUNT(*) FROM +rates    WHERE (`table`='posts' AND row_id = p.id AND
                                                (user_id = '$user_id' OR ip = '$user_id' ))) AS is_rated
                ,(SELECT COUNT(*) FROM +logs     WHERE (`table`='posts' AND row_id = p.id AND ip = '$ip' )) AS is_viewd
                FROM +posts p	 
			$additional";			
			
			break;
			/*******************************************************************************/
			case 'search':
						
			$user_id = 	$userid ? : $ip;
			$filter = array('id','title','content','thumb','author','date_modified');
			
			$query = 
			"SELECT ".implode(',',$filter)."
			,(SELECT avatar   FROM +users    WHERE id = p.author)                       AS author_avatar
			,(SELECT COUNT(*) FROM +rates    WHERE (`table`='posts' AND row_id = p.id)) AS rate_count
			,(SELECT COUNT(*) FROM +comments WHERE (`table`='posts' AND row_id = p.id)) AS comment_count
			,(SELECT COUNT(*) FROM +logs     WHERE (`table`='posts' AND row_id = p.id)) AS view_count
			,(SELECT COUNT(*) FROM +rates    WHERE (`table`='posts' AND row_id = p.id AND 
											(user_id = '$user_id' OR ip = '$user_id' ))) AS is_rated									
			FROM +posts p	 	 
			$additional";			
			
			break;			
			/*******************************************************************************/
			case 'post-comments':
			$user_id = 	$userid ? : $ip;
			$query =
                /** @lang text */
                "SELECT c.id , c.user_id, c.name, c.date, c.text
                ,(SELECT avatar   FROM +users    WHERE id = c.user_id)                         AS user_avatar
                ,(SELECT username FROM +users    WHERE id = c.user_id)                         AS user_username
                ,(SELECT COUNT(*) FROM +rates    WHERE (`table`='comments' AND row_id = c.id)) AS rate_count
                ,(SELECT COUNT(*) FROM +rates    WHERE (`table`='comments' AND row_id = c.id AND (user_id = '$user_id' OR ip = '$ip'))) AS is_rated
                FROM +comments c	 
			$additional";
			break;
            /*******************************************************************************/
            case 'user-reviews':
                $user_id = 	$userid ? : $ip;
                $query =
                    /** @lang text */
                    "SELECT c.id , c.user_id, c.name, c.text, c.date, r.rating
                    ,(SELECT avatar   FROM +users    WHERE id = c.user_id)                         AS user_avatar
                    ,(SELECT username FROM +users    WHERE id = c.user_id)                         AS user_username
                    ,(SELECT COUNT(*) FROM +rates    WHERE (`table`='comments' AND row_id = c.id)) AS rate_count
                    ,(SELECT COUNT(*) FROM +rates    WHERE (`table`='comments' AND row_id = c.id AND (user_id = '$user_id' OR ip = '$ip'))) AS is_rated
                    FROM +comments c	
                    LEFT JOIN +comment_rate cr ON (cr.comment_id=c.id)
                    LEFT JOIN +rates r         ON (r.id=cr.rate_id)
			$additional";
            break;
            /*******************************************************************************/
            case 'user-missions':
                $query =
                    /** @lang text */
                    "SELECT m.*, f.displayname as from_name, f.username as from_username, f.avatar as from_avatar
                               , t.displayname as to_name,   t.username as to_username,   t.avatar as to_avatar
                    FROM +missions m	
                    LEFT JOIN +users f ON (m.from=f.id)
                    LEFT JOIN +users t ON (m.to=t.id)
			$additional";
                break;
            /*******************************************************************************/
			case 'top-rating':	
			
			$filter = array('id','title');
			
			$query = 
			"SELECT ".implode(',',$filter)."
			,(SELECT COUNT(*) FROM +rates    WHERE (`table`='posts' AND row_id = p.id)) AS rate_count
			FROM +posts p	 	 
			$additional";			
			
			break;	
			/*******************************************************************************/
			case 'most-viewed':	
			
			$filter = array('id','title');
			
			$query = 
			"SELECT ".implode(',',$filter)."
			,(SELECT COUNT(*) FROM +logs WHERE (`table`='posts' AND row_id = p.id)) AS view_count
			FROM +posts p	 	 
			$additional";			
			
			break;				
			/*******************************************************************************/
			case 'hot-topics':	
			
			$filter = array('id','title');

			$query = 
			"SELECT ".implode(',',$filter)."
			,(SELECT COUNT(*) FROM +comments WHERE (`table`='posts' AND submitted=1 AND row_id = p.id)) AS comment_count
			FROM +posts p
			$additional";		
			
			break;	
			/*******************************************************************************/
			case 'category-like':
			
			$cId = $additional;	
			$query = "(category LIKE '$cId,%' OR category LIKE '%,$cId,%' OR category LIKE '%,$cId' OR category = '$cId')";		
			break;
			
			/*******************************************************************************/
			case 'category-page':
						
			$query =
                /** @lang text */
                "SELECT * 
                ,(
                    SELECT COUNT(id) FROM +posts WHERE published=1 AND (
                        category LIKE CONCAT('' , c.id , ',%')   OR
                        category LIKE CONCAT('%,' , c.id , ',%') OR 
                        category LIKE CONCAT('%,' , c.id , '')   OR 
                        category = c.id
                    )
                 ) as post_count
                FROM +category c
                $additional";
			break;
            /*******************************************************************************/
            case 'users':
                $filter = array('u.id','u.username','u.displayname','u.avatar');

                $query =
                    "SELECT ".implode(',',$filter)."
                    ,(
                        SELECT COUNT(id) FROM +posts WHERE published=1 AND (
                            category LIKE CONCAT('' , c.id , ',%') OR
                            category LIKE CONCAT('%,' , c.id , ',%') OR 
                            category LIKE CONCAT('%,' , c.id , '') OR 
                            category = c.id
                        )
                     ) as post_count
                    FROM +users u
                    $additional";
                break;

		}
		$query = str_replace('+',$table_prefix,$query);
		return $query;
	}
	
	
	public function getSidebarPosts($case='top-rating',$post_type='post')
	{
		$result = NULL;
		
		$last_m = date("Y-m-d H:i:s",strtotime('-1 month'));
		
		switch($case)
		{
			case 'top-rating':
				$additional = 
				"WHERE type='$post_type' AND published=1 AND date_modified > '$last_m'
				ORDER BY rate_count DESC , date_modified DESC LIMIT 6";	
				$query = $this->buildQuery($case,$additional);			
			break;
			/*****************/
			case 'most-viewed':
				$additional = 
				"WHERE type='$post_type' AND published=1 AND date_modified > '$last_m' 
				ORDER BY view_count DESC , date_modified DESC LIMIT 6";	
				$query = $this->buildQuery($case,$additional);			
			break;
			/*****************/
			case 'post-picturs':
				$query =
                    /** @lang text */
                    "SELECT id,title,thumb FROM ci_posts
				WHERE type='$post_type' AND published=1 AND thumb IS NOT NULL AND date_modified > '$last_m' 
				ORDER BY position DESC, date_modified DESC LIMIT 6";
			break;			
			/*****************/
			case 'hot-topics':
				$additional = 
				"WHERE type='$post_type' AND published=1 AND date_modified > '$last_m'
				HAVING comment_count > 2
				ORDER BY comment_count DESC , date_modified DESC LIMIT 6";	
				$query = $this->buildQuery($case,$additional);			
			break;	
			/*****************/
			case 'side-category':
				$query =
                    /** @lang text */
                    "SELECT id,name,icon FROM ci_category WHERE type='$post_type' ORDER BY parent ASC,position ASC";
			break;
            /*****************/
            case 'page-list':
                $query =
                    /** @lang text */
                    "SELECT id,title,icon FROM ci_posts WHERE type='$post_type' AND published=1 AND title !='' ORDER BY position ASC,date_modified DESC";
                break;
			default:
				return NULL;
			break;						
		}
		$result = $this->db->query($query)->result();
		
		return $result;					
	}

    public function createSearch($fields=NULL)
    {
        if( ! $fields ) return NULL;

        $form = '<form action="" method="GET" class="clearfix form-inline filter-form">';
        foreach($fields as $k=>$field)
        {
            $form .= '<div class="form-group">';
            switch($field['type'])
            {
                case 'text':
                case 'email':
                case 'tel':
                    $type = $field['type'];
                    $value = $this->input->get($k);
					if($value == NULL && strpos($k,'.') !== FALSE)
						$value = $this->input->get(str_replace('.','_',$k));
					
                    $form .= "<p class='text-center'>{$field['name']}</p>";
                    $form .= "<input type='{$type}' class='form-control' value='{$value}' name='{$k}'>";
                break;
                case 'select' :
                    if( isset($field['options']) && is_array($field['options']) )
                    {
                        $form .= "<p class='text-center'>{$field['name']}</p>";
                        $form .= "<select class='form-control' name='{$k}'>";

                        $get = $this->input->get($k);
						if($get == NULL && strpos($k,'.') !== FALSE)
							$get = $this->input->get(str_replace('.','_',$k));
					
                        foreach($field['options'] as $opk=>$opv)
                        {
                            $selected = ($get !== NULL && $get == $opk) ? 'selected':'';
                            $form .= "<option value='{$opk}' {$selected}>{$opv}</option>";
                        }
                        $form .= "</select>";
                    }
                break;
                case 'dropdown' :
                    if( isset($field['options']) && is_array($field['options']) )
                    {
                        $form .= "<p class='text-center'>{$field['name']}</p>";
                        $form .= "<select class='form-control' name='{$k}'>";

                        $get = $this->input->get($k);
						if($get == NULL && strpos($k,'.') !== FALSE)
							$get = $this->input->get(str_replace('.','_',$k));
					
                        foreach($field['options'] as $opk=>$opv)
                        {
                            if(is_array($opv)){
								$form .= '<optgroup label="'.$opk."\">\n";
								foreach ($opv as $opgk=>$opgv)
								{
									$selected = ($get !== NULL && $get == $opgk) ? 'selected':'';
									$form .= "<option value='{$opgk}' {$selected}>{$opgv}</option>";
								}
								$form .= "</optgroup>\n";
							} else {
								$selected = ($get !== NULL && $get == $opk) ? 'selected':'';
								$form .= "<option value='{$opk}' {$selected}>{$opv}</option>";
							}
                        }
                        $form .= "</select>";
                    }
                break;
                case 'date-from-to':
                    $from = $this->input->get('date-from');
                    $to = $this->input->get('date-to');

                    $fromDf = $from ? $from:jdate('Y-m-d',strtotime("-1 month"),'','','en');

                    $form .= "<p class='text-center'>از تاریخ</p>";
                    $form .= "<input type='text' class='form-control dateInput-0111 dateFormat' value='{$from}' name='date-from'>";
                    $form .= "<script>$('.dateInput-0111').datepicker({dateFormat: 'yy-mm-dd',defaultDate:'{$fromDf}'});</script>";
                    $form .= '</div>';

                    /************************************/
                    $toDf = $to ? $to:jdate('Y-m-d',strtotime("now"),'','','en');
                   
                    $form .= '<div class="form-group">';
                    $form .= "<p class='text-center'>تا تاریخ</p>";
                    $form .= "<input type='text' class='form-control dateInput-0112 dateFormat' value='{$to}' name='date-to'>";
                    $form .= "<script>$('.dateInput-0112').datepicker({dateFormat: 'yy-mm-dd',defaultDate:'{$toDf}'});</script>";
                break;

            }
            $form .= '</div>';
        }
        $form .= '<div class="form-group">';
        $form .= '<p>&nbsp</p>';
        $form .= '<button type="submit" class="btn btn-info"><span>فیلتر</span> <i class="fa fa-angle-double-left"></i></button>';
        $form .= '</div>';
        $form .= '<hr/>';
        $form .= '</form>';
        return $form;
    }

    public function createSearchQuery($fields=NULL,$table=NULL)
    {
        if( ! $fields ) return NULL;

        $query = "";
        foreach($fields as $k=>$field)
        {
            $type = $field['type'];
            switch($type)
            {
                case 'text':
                case 'email':
                case 'tel':
                    $value = $this->input->get($k);
					if($value == NULL && strpos($k,'.') !== FALSE)
						$value = $this->input->get(str_replace('.','_',$k));
					
                    if($value != '' /*&& $this->db->table_exists($table) && $this->db->field_exists($k,$table)*/)
                    {
                        $value = $this->db->escape_like_str($value);
                        $k = str_replace('.','`.`' ,$k);
                        $query .= " AND `{$k}` LIKE '%{$value}%' ";
                    }
                break;
                case 'dropdown':
                case 'select':
                    $value = $this->input->get($k);
					
					if($value == NULL && strpos($k,'.') !== FALSE)
						$value = $this->input->get(str_replace('.','_',$k));
					
                    if($value != '' /*&& $this->db->table_exists($table) && $this->db->field_exists($k,$table)*/)
                    {
                        $fieldInfo = $this->field_data($table,$k);

                        if(isset($fieldInfo->type) && $fieldInfo->type == 'int')
                        {
                            $value = intval($value);
                        }
                        else
                        {
                            $value = $this->db->escape($value);
                        }
                        $k = str_replace('.','`.`' ,$k);
                        $query .= " AND `{$k}` = {$value} ";
                    }
                break;
                case 'date-from-to':
                    $from = $this->input->get('date-from');
                    $to = $this->input->get('date-to');

                    if( ! $from && ! $to )
                    break;

                    $from = explode('-',$from);
                    $to = explode('-',$to);

                    if(count($from)==3 && count($to)==3)
                    {
                        $from = jalali_to_gregorian($from[0],$from[1],$from[2],'-').' 00:00:00';
                        $to = jalali_to_gregorian($to[0],$to[1],$to[2],'-').' 00:00:00';
                    }
                    else
                    {
                        $from = date("Y-m-d H:i:s",strtotime("-1 month"));
                        $to = date("Y-m-d H:i:s");
                    }

					if(isset($field['field_type']) && $field['field_type'] == 'int')
					{
						$from = strtotime($from);
						$to   = strtotime($to); 
					}
					
                    $k = str_replace('.','`.`' ,$k);
                    $query .= " AND `{$k}` >= '{$from}' AND  `{$k}` <= '{$to}'  ";
                    break;
            }
        }
        if(str_replace(' ','',$query) == '()') $query = "";
        return $query;
    }

    public function field_data($table=NULL,$field=NULL)
    {
        if( ! $table ) return NULL;

        $fields = $this->db->field_data($table);
        $fieldsArr = array();
        foreach ($fields as $dbfield)
        {
            if( $field && $dbfield->name == $field) return $dbfield;
            $fieldsArr[$dbfield->name] = $dbfield;
        }
        if($field) return NULL;
        return $fieldsArr;
    }

    public function newCaptcha($config=NULL)
    {
        if( ! $config )
            $config = array(
                'img_path'      => './_captcha/',
                'img_url'       => base_url().'_captcha/',
                'font_path'     => BASEPATH.'/fonts/Gordon-Heights.ttf',
                'img_width'     => 130,
                'img_height'    => 50,
                'expiration'    => 7200,
                'word_length'   => 5,
                'font_size'     => 20,
                'img_id'        => 'captcha-img',
                'pool'          => '123456789abcdefghijkmnpqrstuvwxyz',/*ABCDEFGHIJKLMNOPQRSTUVWXYZ*/
                'colors'        => array(
                    'background' => array(132, 132, 132),
                    'border' => array(255, 255, 255),
                    'text' => array(255, 255, 255),
                    'grid' => array(160, 160, 160)
                )
            );
        $this->load->helper('captcha');
        $cap = create_captcha($config);
        $ip = $this->input->ip_address();
        $this->db->where('ip_address', $ip)->delete('captcha');
        $data = array(
            'captcha_time'  => $cap['time'],
            'ip_address'    => $ip,
            'word'          => strtolower($cap['word'])
        );
        $query = $this->db->insert_string('captcha', $data);
        $this->db->query($query);
        return $cap;
    }

    public  function destroyCaptcha()
    {
        $ip = $this->input->ip_address();
        $this->db->where('ip_address', $ip)->delete('captcha');
    }

    public function checkCaptcha($captcha = NULL)
    {
        if( ! $captcha ) $captcha = $this->input->post('captcha');

        $captcha = strtolower($captcha);

        $expiration = time() - 7200;
        $this->db->where('captcha_time < ', $expiration)->delete('captcha');

        $this->db->where('word',$captcha)
                 ->where('ip_address',$this->input->ip_address())
                 ->where('captcha_time > ',$expiration);

        if( $row = $this->db->get('captcha')->row() )
        {
            //$this->db->where('captcha_id',$row->captcha_id)->delete('captcha');
            return TRUE;
        }
        return FALSE;
    }

    function view($view,$data=null)
    {
        $data['config'] = $this->setting;
        
        $this->load->view('client/v_header',$data);

        //$this->load->view('client/v_navbar',$data);
		if( ! is_array($view) ) $view = array($view);
		
		foreach( $view as $v )
        $this->load->view('client/'.$v);

        $this->load->view('client/v_footer');
    }

    public function deleteRow($table=NULL,$id=NULL,$field='id')
    {
        if( ! $this->db->table_exists($table) ) return FALSE;

        if($table != 'users' && $table != 'instruments')
        {
            if( ! $this->db->where($field,$id)->delete($table)  ) return FALSE;
        }


        switch($table)
        {
            case 'users' :
                return $this->user->deleteAccount($id);
            break;
            case 'instruments' :
                $this->load->model('m_instrument');
                return $this->m_instrument->delete($id);
            break;
            case 'posts' :
                $this->db->where('row_id',$id)->where('table',$table)->delete('comments');
                $this->db->where('row_id',$id)->where('table',$table)->delete('rates');
                $this->db->where('row_id',$id)->where('table',$table)->delete('logs');
            break;
        }
        return TRUE;
    }
    
    public $msg = NULL;
    public function msg($id=NULL)
    {
        $id = (int) $id;
        if( ! $id ) return NULL;

        if( $this->msg == NULL)
        {
            $msgs = $this->db->select('id,value')->get('system_messages')->result();
            foreach( $msgs as $msg )
                $this->msg[$msg->id] = $msg->value;
        }
        if( isset($this->msg[$id]) )
            return $this->msg[$id];

        return NULL;
    }

    public function siteName($url=NULL)
    {
        $url = str_replace('http://','',$url);
        $url = str_replace('https://','',$url);

        return $url;
    }


    public function siteUrl($url=NULL)
    {
        if( strpos($url,'http://') === FALSE && strpos($url,'https://') === FALSE )
            $url = 'http://' . $url;

        return $url;
    }

    function isValidUrl($url)
    {
        return (filter_var($url, FILTER_VALIDATE_URL) !== FALSE);
    }

    function period($from=NULL,$to=NULL)
    {
        /*$yf = jdate('Y',$from,NULL,NULL,'en');
        $mf = jdate('m',$from,NULL,NULL,'en');
        $df = jdate('d',$from,NULL,NULL,'en');

        $yt = jdate('Y',$to,NULL,NULL,'en');
        $mt = jdate('m',$to,NULL,NULL,'en');
        $dt = jdate('d',$to,NULL,NULL,'en');

        $years = $yt - $yf;
        $month = $mt - $mf;*/

        $def = $to - $from;

        if( 0 <= $def && $def < 31*24*3600 )
        {
            $period =  ceil($def/(24*3600)) . " روز ";
        }
        elseif( 31*24*3600 <= $def && $def < 365*24*3600 )
        {
            $month =  floor($def/(30*24*3600));
            $def  -= $month*30*24*3600;
            $days  = ceil($def/(24*3600));

            $period = $month  . " ماه ";

            if( $days >= 10 )
                $period .=  " و ".$days ." روز ";
            if( $days > 27 )
                $period = ($month+1)  . " ماه ";
            if( $month+1 == 12 )
                $period = " یک سال ";
        }
        else
        {
            $years = floor($def/(365*24*3600));
            $def  -= $years*365*24*3600;
            $month = floor($def/(30*24*3600));

            $period = $years . " سال ";

            if( $month > 0 )
                $period .=  " و ".$month ." ماه ";

            if( $month > 11 )
                $period =  ($years+1)  . " سال ";
        }

        return $period;
    }
	private function MakeJSON($data){
		return json_encode($data,JSON_UNESCAPED_UNICODE | JSON_HEX_QUOT | JSON_HEX_TAG);
	}//Alireza Balvardi
}
?>