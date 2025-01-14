<?php defined('BASEPATH') OR exit('No direct script access allowed');

class inc{
	
	public $Settings;
    public $Project; 
    public $dbData; 
    public $MySQL;
	public $SMS;
	private $ShowError = false;
	private $CI;
	 	
	function __construct(){
		
		$this->SMS['Username']      = 'SMS_USER';
		$this->SMS['Password']      = 'SMS_PASS';
		$this->SMS['Number']        = 'SMS_NUM';
		$this->SMS['Host']          = 'SMS_HOST';
				
		$this->Project['sitename']  = 'P_SITENAME';
		$this->Project['licence']   = 'P_LICENCE';	
		$this->Project['team']      = 'P_TEAM';
        $this->Project['author']    = 'P_AUTHOR';
		$this->Project['version']   = 'P_VERSION';
		
		$this->CI =& get_instance();
				
    }
	
	function SendSMS($data){
		
		$errorCode = 
		array(
			0 => 'بدون خطا',
			1 => 'نام کاربری و رمز عبور نامعتبر است',
			2 => 'شماره فرستنده نا معتبر است',
			3 => 'شماره همراه نامعتبر است',
			4 => 'اعتبار حساب کافی نمی باشد',
			5 => 'خطا در ارتباط با سرور',
			6 => 'پیام نامعتبر است',
			7 => 'متن پیام بیش از حد طولانی است',
			8 => 'خطا در برقراری ارتباط با سوئیچ مخابرات',
			9 => 'پیام دریافتی معتبر نمی باشد',
			10 => 'شناسه پیام نامعتبر است.',
			11 => 'تعداد پارامترها صحیح نمی باشد.',
			12 => 'مقدار UDH نامعتبر است.',
			13 => 'شماره گیرنده امکان دریافت پیامک تبلیغاتی ندارد.',
		);			
		
		
		$nusoap = "api/sms/nusoap/nusoap.php";
		
		if(file_exists($nusoap))
		require($nusoap);
		else
		require("../".$nusoap);
		
		$client = new nusoap_client($this -> SMS['Host'], 'wsdl');
		$client->decodeUTF8(false);
		
		$Nums = array();
		$Mails = array();
		$SMS_Msgs = array();
		$Mail_Msgs = array();
		
		if(count($data)==1){
			
			$Num = $data[0]['tel'];
			$Mail = $data[0]['mail'];
			$SMS_Msg  = str_replace("[BR]","\n",$data[0]['msg']);
			$Mail_Msg = $data[0]['msg'];
			
			
			$res = $client->call('send', array(
				'username'	=> $this -> SMS['Username'],
				'password'	=> $this -> SMS['Password'],
				'to'		=> $Num,
				'from'		=> $this -> SMS['Number'],
				'message'	=> $SMS_Msg
			));
			
			//$res = array('status'=>13,'status_message'=>'msg','identifier'=>'');
			
			if (is_array($res) && isset($res['status']) && $res['status'] === 0) {
				
				$Result['Result'] = true;
				$Result['Description'] = "پیغام با موفقیت ارسال شد";

				
			} elseif (is_array($res)) {
				
				$Result['Result'] = false;
				
				$Result['Status'] = $res['status'];
				
				$Result['Description'] = $res['status_message'];
				
				

			} else {
				
				$Result['Result'] = false;
				$Result['Status'] = '';
				$Result['Description'] = $client->getError();
				
			}
										
			//$this->SendMail($Mail,$Mail_Msg);
			
			return $Result;
		
		}else{
			
			foreach($data as $Key=>$Value){
				
				$Nums[$Key]      = $Value['tel'];
				$Mails[$Key]     = $Value['mail'];
				$SMS_Msgs[$Key]  = str_replace("[BR]","\n",$Value['msg']);
				$Mail_Msgs[$Key] = $Value['msg'];
			}
		
		}
		
		$res = $client->call('bulkSend', array(
			'username'	=> $this -> SMS['Username'], 
			'password'	=> $this -> SMS['Password'], 
			'to'		=> $Nums,
			'from'		=> array($this -> SMS['Number']),
			'message'	=> $SMS_Msgs
		));
		
		/*
		foreach($Mails as $k=>$mail)
		$this->SendMail($mail,$Mail_Msgs[$k]);
		*/
		$Result['Result'] = true;	
	
		$Result['Description'] = "پیغام با موفقیت ارسال شد";
		
		
		return $Result;
		
	}
	
	function Get_SMS_Info(){
		
		require_once('../api/sms/nusoap/nusoap.php'); 
		
		$client = new nusoap_client($this -> SMS['Host'], 'wsdl');
		$client->decodeUTF8(false);
		
		$res = $client->call('accountInfo', array(
			'username'	=> $this->SMS['Username'],
			'password'	=> $this->SMS['Password']
			));
			
		return $res;
	}

	function truncate($string,$max, $rep = '...') {
	
		$words = preg_split("/[\s]+/", $string);
	
		$newstring = '';
	
		$numwords = 0;
	
		foreach ($words as $word) {
	
			if ((mb_strlen($newstring,'utf8') + 1 + mb_strlen($word,'utf8')) < $max) {
	
				$newstring .= ' '.$word;
				++$numwords;
	
			}else
			break;
		}
	
		if ($numwords < count($words)) {
	
			$newstring .= $rep;
	
		}
	
		return $newstring;
	
	}



//---------------------------------------------	هر سه رقم ممیز بزند



	function price($price){
		$price = trim(strip_tags($price));
		$p=strlen($price);
	
		if($p<4){
			$num=substr($price,-3);
		}
	
		if($p==4){
	
			$num3=substr($price,-3);
		
			$num2=substr($price,0,1);
		
			$num=$num2.",".$num3;
	
	
		}
	
		if($p==5){
	
			$num3=substr($price,-3);
		
			$num2=substr($price,0,2);
		
			$num=$num2.",".$num3;
	
		}
	
		if($p==6){
	
			$num3=substr($price,-3);
		
			$num2=substr($price,0,3);
		
			$num=$num2.",".$num3;
		
		}
	
		if($p==7){
	
			$num3=substr($price,-3);
		
			$num2=substr($price,1,-3);
		
			$num1=substr($price,0,1);
		
			$num=$num1.",".$num2.",".$num3;
	
		}
	
		if($p==8){
	
			$num3=substr($price,-3);
		
			$num2=substr($price,2,-3);
		
			$num1=substr($price,0,2);
		
			$num=$num1.",".$num2.",".$num3;
	
		}
	
		if($p==9){
	
			$num3=substr($price,-3);
		
			$num2=substr($price,3,-3);
		
			$num1=substr($price,0,3);
		
			$num=$num1.",".$num2.",".$num3;
	
		}
	
		return $num;
	}

	function generateRandomString($length = 10) {
	
		return substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, $length);
	}

	function NoCache(){

		header("Expires: Mon, 26 Jul 1997 05:00:00 GMT"); // date in the past

		header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT"); // always modified

		header("Cache-Control: no-store, no-cache, must-revalidate"); // HTTP/1.1 

		header("Cache-Control: post-check=0, pre-check=0", false); 

		header("Cache-Control: private");

		header("Pragma: no-cache"); // HTTP/1.0 
 
	}
	
	function HASH($string){
		
		$s = '_ex_salt_hash';
		return sha1(md5($string.$s));
		
	}
	
	function GetFieldById($tableName,$Id,$Name='name'){}
	
		
	function byteToSize($bytes) {
		$sizes = array ('Bytes', 'KB', 'MB', 'GB', 'TB');
		if ($bytes == 0) return '0 Bytes';
		$i = floor(log($bytes) / log(1024));
		return round($bytes / pow(1024, $i),2).' '.$sizes[$i];
	}
	
	function createTable($cols,$sqlQuery,$tableAttr,$tableName,$perPage=50,$SumField = NULL){//Alireza Balvardi
				
		?><table <?php echo  $tableAttr ?> >
		  <thead>
		  </thead>
		  <tbody>
		  <tr>
			<?php
			foreach($cols as $header => $v){
				
				$thClassName = "";
				$thValign = "";
				unset($get_params);
				$get_params = array();
				
				if(isset($_GET) && !empty($_GET)){
					
					$get_params = $_GET;
					if(isset($get_params[$tableName.'_order']) && $get_params[$tableName.'_order'] == $v['field_name']){
						if(isset($get_params[$tableName.'_sort']) && $get_params[$tableName.'_sort'] =='ASC')
						$get_params[$tableName.'_sort'] = 'DESC';
						else
						$get_params[$tableName.'_sort'] = 'ASC';
						if(isset($v['link'])){
							$thClassName = "sorted-by-this ".$get_params[$tableName.'_sort'];
							$thValign = "top";
						}
					}else{
						if(@$v['field_name']=='date')
						$get_params[$tableName.'_sort'] = 'DESC';
						else
						$get_params[$tableName.'_sort'] = 'ASC';	
						$get_params[$tableName.'_order'] = $v['field_name'];
					}

				}elseif(isset($v['field_name'])){
					
					$get_params[$tableName.'_order'] = $v['field_name'];
					if(@$v['field_name']=='date')
					$get_params[$tableName.'_sort'] = 'DESC';
					else
					$get_params[$tableName.'_sort'] = 'ASC';					
				}
				
				$get_params[$tableName.'_page']=1;
				
				if(isset($v['field_name']))
				$new_get = $this->arrayToGetParameters($get_params); 

				$style = "";
				if(isset($v['th-attr']))
					$style = $v['th-attr'];//Alireza Balvardi
				
				if (isset($v['link'])&&$v['link'])
				echo "<th class='$thClassName' valign='$thValign' $style><a href='$new_get'>$header</a></th>";//Alireza Balvardi
				else
				echo "<th class='$thClassName' $style>$header</th>";//Alireza Balvardi
				
			}
			?>
		  </tr>
          
		<?php
		
		if( FALSE === strpos($sqlQuery,"SELECT") )
		$sqlQuery = "SELECT * FROM `ci_$tableName` $sqlQuery";
		
		$query = $this->CI->db->query($sqlQuery);

		$totalRows = $query->num_rows();
		
		$page = $this->CI->input->get($tableName.'_page');
		if($page != '' and is_numeric($page)){

			$min = $page == 1 ? 0 :$perPage*($page-1); 
			
		}else{
		   $min = 0;
		   $page = 1;
		}
		
		if($perPage)
		$sqlQuery .= " limit $perPage offset $min ";
		
		$query = $this->CI->db->query($sqlQuery);
		
		$sqlResult = $query->result_array();
		
		if($totalRows <= 0 OR $query->num_rows() < 1 ){
			
		?>
        <tr>
        	<td colspan="<?php echo  count($cols)?>" align="center">
				<div style="padding:15px">
					<img src="<?php echo  base_url()?>/style/images/warning.png" width="15" style="vertical-align:middle;margin:0 15px" />
					هیچ داده ای جهت نمایش وجود ندارد 
				</div>
            </td>
        </tr>
		<?php	
			
		}else{
			foreach($sqlResult as $key => $field){
			?>
			<tr class="data">
			<?php
					
				foreach($cols as $k =>$v){
					
					$col = isset($field[$v['field_name']]) ? $field[$v['field_name']] : '';
					
					if(isset($v['max']))
					$col = $this -> truncate($col,$v['max']);
					
					if(isset($v['function']))
					{
						$col = $v['function']($col,$field,$perPage*($page-1)+$key);//Alireza Balvardi
					}				
					
					if(isset($v['type'])){
						
						switch ($v['type']){
							
							case 'price': $col = $this->price($col); break;
							
							case 'filesize': $col = $this->ByteToSize($col); break;
	
							case 'date':
								if($col != ''){
									if(is_numeric($col))
									$col = $this->print_date(date("Y-m-d H:i:s",$col),TRUE,0);
									else
									$col = $this->print_date($col);
								}
							
							break;
							
							case 'strtime': if($col != '') $col = $this->print_date(date("Y-m-d H:i:s",$col)); break;
							
							case 'bool': $col = $col == 1 ? '<font color="#009900">&#10004;</font>':'<font color="#CA0000">&#10008;</font>'; break;
							
							case 'register': $col = $col=='done'?'<font color="#009900">&radic;</font>':'معلق'; break;	
	
							case 'user-avatar': $col = file_exists($col) ? $col : $this->CI->settings->data['default_user_avatar']; break;
							
							case 'author': $col = $this->CI->db->get_field('displayname','users',$col); break;	
							
							case 'post-thumb': $col = file_exists($col) ? $col : $this->CI->settings->data['default_post_thumb']; break;
							
							case 'user-level':
								
								$col = $this->CI->user->getLevelName('',$col);
								
								$col = '<div style="max-width:200px;" class="wb">'. $col .'</div>';
								
							break;
							
							case 'op':
								$span = '<span class="table-op cu">';
								$span .= '<i class="fa fa-ellipsis-v fa-lg cu"></i>';
									$span .= '<ul class="list-group">';
										foreach( $v['items'] as $op )
										{
											$onclick = isset($op['click']) ? 'onClick="'.$op['click'].'"':'';
											$span .= '<li '.$onclick.' class="cu list-item">';
											
												isset($op['href']) && 
												$span .= '<a href="'.$op['href'].'">';
											
												$span .= '<span>';
												$span .= '<i class="fa fa-'.$op['icon'].'"></i>';
												$span .= $op['name'].'</span>';
												
												isset($op['href']) && $span .= '</a>';
												
											$span .= '</li>';
										}
									$span .= '</ul>';
								$span .= '</span>';
								
								$v['html'] = $span;
							break;
						}
					}
					
					if(isset($v['html']))
					$col = str_replace('[FLD]',$col,$v['html']);
	
					if( isset($field['id']) && isset($v['count']) ){
						$col = str_replace('[COUNT]',$field[$v['count']] ,$col);
					}
	
					if( isset($field['id']) )
					$col = str_replace('[ID]',$field['id'] ,$col);
	
					if(isset($v['html']) && isset($v['name']))
					$col = str_replace('[NAME]',$v['name'],$col);
	
					if( isset($field['count']) ){
						$col = str_replace('[COUNT]',$field['count'] ,$col);
					}
					
					$attr = "";
					if(isset($v['td-attr']))
						$attr = str_replace('[ID]',$field[$v['field_name']] ,$v['td-attr']);//Alireza Balvardi
					
				?><td <?php echo   $attr ?>><?php echo   $col ?></td><?php
				
				}
			?></tr><?php		   
			}
		}
		?>
		</tbody>
		  <tfoot id="tfoot">
		  <?php if($SumField){?>
		  <?php
		  $Sum = 0;
		  foreach($sqlResult as $key => $field){
		  	foreach($SumField as $k => $v){
				$Sum+= $field[$v['field_name']];
			}
		  }
		  ?>
          <tr>
		  <?php foreach($SumField as $k => $v){ ?>
		  	<td align="center" <?php echo $v['td-attr'];?>>
				<?php echo $k;?>
			</td>
			<td>
				<?php echo $this->price($Sum);?>
			</td>
		  <?php } ?>
		  </tr>
		  <?php } ?>
          <tr>
            <th colspan="<?php echo  count($cols)?>" align="center" valign="middle" dir="ltr">
            <?php $this->pagination($page,$totalRows,$perPage,$tableName.'_page'); ?>
            </th>
          </tr>
		</tfoot>
        </table><?php
			
	}	
	
	function arrayToGetParameters($array){
		$i = 0;
		$new_get = "?";
		foreach($array as $p => $val){
			$i++;
			$new_get .= urlencode($p).'='.urlencode($val).($i!=count($array)?'&':'');
			
		}
		return current_url(). $new_get;
	}	
	
	function pagination($page,$totalRows,$perPage=50,$parameterName='page'){
		if(!$perPage)
			return;
		$allPages = ceil($totalRows/$perPage); 
		
		$get_params = array();
		
		if(isset($_GET) && !empty($_GET))
		$get_params = $_GET;
		
		if(isset($get_params[$parameterName])){
			unset($get_params[$parameterName]);	
		
		}
		
		if($allPages == 0 ){
			
			return;
			//echo '<span title="صفحه 0 از 0 "> صفحه 0 از 0 </span>';
			
		}
		
		if($allPages == 1){
			
			echo '<span title="صفحه 1 از 1 "> صفحه 1 از 1 </span>';
			return;
		}
		
		
		if($allPages>1)
		echo '<span style="float:left" title="صفحه '.$page.' از '.$allPages.' ">
				<span class="PageNum">'.$page.'</span>/<span class="PageNum">'.$allPages.'</span>
			  </span>';
		
		if($page>1){
			$get_params[$parameterName] = 1;
			echo '<span page="1" class="PageNum"  title="صفحه اول">
			        <a href="'.($this->arrayToGetParameters($get_params)).'">First</a>
				  </span> ';
			$get_params[$parameterName] = $page-1;
			if($page>2)
			echo '<span page="'.($page-1).'" class="PageNum" title="صفحه قبل">
					<a href="'.($this->arrayToGetParameters($get_params)).'" >&laquo;</a>
				  </span>';
		}
		
		for($j=($page-5);$j<($page+5);$j++)
		if($j<=$allPages && $j>0){
			
			$get_params[$parameterName] = $j;
			echo '<span page="'.$j.'" class="PageNum '.($j==$page? 'this-page' :'').'"  title="صفحه '.$j.'">';
			if($j!=$page)
			echo '<a href="'.($this->arrayToGetParameters($get_params)).'" >'.$j.'</a>';
			else
			echo $j; 
			echo '</span>';
			
		}
		if($page<$allPages){
			$get_params[$parameterName] = $page+1;
			if($page+1!=$allPages)
			echo '<span page="'.($page+1).'" class="PageNum" title="صفحه بعد">
			        <a href="'.($this->arrayToGetParameters($get_params)).'">&raquo;</a>
				  </span>';
			$get_params[$parameterName] = $allPages;		
			echo '<span page="'.$allPages.'" class="PageNum" >
					<a href="'.($this->arrayToGetParameters($get_params)).'" title="صفحه '.$allPages.'">Last</a>
				  </span>';		
		} 		
	}
	
	function pagination2($page,$totalRows,$perPage=50,$op=0){
		$allPages = ceil($totalRows/$perPage); 
		
		$return = "";
		
		if($allPages == 0 ){return;}
		
		if($allPages == 1){
			
			$return .= '<span data-title="صفحه 1 از 1 "> صفحه 1 از 1 </span>';
			return $return;
		}
		
		
		if($allPages>1)
		$return .= '<span style="float:left" data-title="صفحه '.$page.' از '.$allPages.' ">
				<span class="PageNum">'.$page.'</span>/<span class="PageNum">'.$allPages.'</span>
			  </span>';
		
		if($page>1){
			$return .= '<span page="1" class="PageNum '.$op.'"  data-title="صفحه اول">ابتدا</span> ';
			if($page>2)
			$return .= '<span page="'.($page-1).'" class="PageNum '.$op.'" data-title="صفحه قبل">&laquo;</span>';
		}
		
		for($j=($page-5);$j<($page+5);$j++)
		if($j<=$allPages && $j>0){
			
			$return .= '<span page="'.$j.'" class="PageNum '.($j==$page? 'this-page' : $op).'" data-title="صفحه '.$j.'">';
			$return .= $j;
			$return .= '</span>';
			
		}
		if($page<$allPages){
			if($page+1!=$allPages)
			$return .= '<span page="'.($page+1).'" class="PageNum '.$op.'" data-title="صفحه بعد">&raquo;</span>';		
			$return .= '<span page="'.$allPages.'" class="PageNum '.$op.'" >انتها</span>';		
		} 
		return $return;		
	}
	
	function print_date($date,$html=TRUE,$type=0){
		
		$CI = $this->CI;
		$setting = $CI->settings->data;
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
		
		if(($D==0 || $D == 1) && ($m_n == $m && $Y_n == $Y)){
			if($type)
			$return = ($D? ' دیروز - ' : ' امروز - ' ).jdate($setting['time_format'],$datestr,"","","en");
			else
			$return = jdate($setting['date_format'],$datestr,"","","en");
		}elseif($D > 0 && $D < 7 && $m_n == $m && $Y_n == $Y){
			if($type)
			$return = jdate('l',$datestr).' - ' .jdate($setting['time_format'],$datestr,"","","en");
			else
			$return = jdate($setting['date_format'],$datestr,"","","en");
		}elseif($m_n == $m && $Y_n == $Y && $D==-1){
			if($type)
			$return = ' فردا '.jdate($setting['time_format'],$datestr,"","","en");
			else
			$return = jdate($setting['date_format'],$datestr,"","","en");
		}else{
			if($type)
			$return = jdate($setting['date_format'].' - '.$setting['time_format'],$datestr,"","","en");
			else
			$return = jdate($setting['date_format'],$datestr,"","","en");
		}
		if($type)
		$title = jdate($setting['date_format'].' - '.$setting['time_format'],$datestr,"","","en").' | '.$date;
		else
		$title = jdate($setting['date_format'],$datestr,"","","en");
		
		
		if( $html )
		return 
		'<span class="relative-date" datestr="'.( $datestr - date('Z') ). '" date="'.$return.'" title="'.$title.'" >'.$return.'</span>';
		
		else
		return array(
			'datestr' => $datestr - date('Z'),
			'date'    =>$return,
			'full'    =>$title
		);
	}
		
				
	function DigitToPersainLetters($money){ 
	   
		$one = array(
			'صفر',
			'یک',
			'دو',
			'سه',
			'چهار',
			'پنج',
			'شش',
			'هفت',
			'هشت',
			'نه');
		$ten = array(
			'',
			'ده',
			'بیست',
			'سی',
			'چهل',
			'پنجاه',
			'شصت',
			'هفتاد',
			'هشتاد',
			'نود',
		);
		$hundred = array(
			'',
			'یکصد',
			'دویست',
			'سیصد',
			'چهارصد',
			'پانصد',
			'ششصد',
			'هفتصد',
			'هشتصد',
			'نهصد',
		);
		$categories = array(
			'',
			'هزار',
			'میلیون',
			'میلیارد',
		);
		$exceptions = array(
			'',
			'یازده',
			'دوازده',
			'سیزده',
			'چهارده',
			'پانزده',
			'شانزده',
			'هفده',
			'هجده',
			'نوزده',
		);
		
		if(strlen($money) > count($categories)){
			throw new Exception('number is longger!');
		}
		
		$letters_separator = ' و ';
		$money = (string)(int)$money;
		$money_len = strlen($money);
		$persian_letters = '';
		
		for($i=$money_len-1; $i>=0; $i-=3){
			$i_one = (int)isset($money[$i]) ? $money[$i] : -1;
			$i_ten = (int)isset($money[$i-1]) ? $money[$i-1] : -1;
			$i_hundred = (int)isset($money[$i-2]) ? $money[$i-2] : -1;
			
			$isset_one = false;
			$isset_ten = false;
			$isset_hundred = false;
			
			$letters = '';
			
			// zero
			if($i_one == 0 && $i_ten < 0 && $i_hundred < 0){
				$letters = $one[$i_one];
			}
			
			// one
			if(($i >= 0) && $i_one > 0 && $i_ten != 1 && isset($one[$i_one])){
				$letters = $one[$i_one];
				$isset_one = true;
			}
			
			// ten
			if(($i-1 >= 0) && $i_ten > 0 && isset($ten[$i_ten])){
				if($isset_one){
					$letters = $letters_separator.$letters;
				}
				
				if($i_one == 0){
					$letters = $ten[$i_ten];
				}            
				elseif($i_ten == 1 && $i_one > 0){
					$letters = $exceptions[$i_one];
				}
				else{
					$letters = $ten[$i_ten].$letters;
				}
				
				$isset_ten = true;
			}
			
			// hundred
			if(($i-2 >= 0) && $i_hundred > 0 && isset($hundred[$i_hundred])){
				if($isset_ten || $isset_one){
					$letters = $letters_separator.$letters;
				}
				
				$letters = $hundred[$i_hundred].$letters;
				$isset_hundred = true;
			}
			
			if($i_one < 1 && $i_ten < 1 && $i_hundred < 1){
				$letters = '';
			}
			else{
				$letters .= ' '.$categories[($money_len-$i-1)/3];
			}
			
			if(!empty($letters) && $i >= 3){
				$letters = $letters_separator.$letters;
			}
			
			$persian_letters = $letters.$persian_letters;
		}
		
		return $persian_letters;
	}
	
	function GetArchive($dateArray){
		
		function DateInArray($array,$b,$e){
			$i = 0;
			foreach($array as $strTime)
			if($strTime>=$b and $strTime<=$e)
			$i++;
			return $i;	
		}
		
		$now = strtotime('now');
		$today = strtotime('today');
		$todayInWeek = jdate("N",$today,'','','en')-1;
		$todayInMonth = jdate("d",$today,'','','en');
		$thisMonth = jdate("m",$today,'','','en');
		$thisYear = jdate("y",$today,'','','en');
		
		$BeginWeek = $todayInWeek==0?$today:strtotime('today -'.($todayInWeek+1).' days'); 
		$beginMonth = $todayInMonth==1?$today:strtotime('today -'.($todayInMonth-1).' days');
		$EndWeek = $BeginWeek+(7*24*3600);
		$EndMonth = $beginMonth+(jdate('t',$today,'','','en')*24*3600);
		
		
		$return = "";
		$return .='
		<ul class="archive-ul">
		   <li><span date="all">همه</span></li>';
		
		 if(DateInArray($dateArray,$BeginWeek,$EndWeek)){
			 
		 $return .='
		 <li class="this-week"><span date="'.$BeginWeek.','.$EndWeek.'">این هفته</span>
			<ul>';
			
			for($i=0;$i<=$todayInWeek+1;$i++){
				
				if($i==$todayInWeek+1){
					$dName = 'امروز';
				}else
				switch ($i){
					case 0: $dName = 'شنبه'; break;case 1: $dName = 'یکشنبه'; break;
					case 2: $dName = 'دوشنبه'; break;case 3: $dName = 'سه شنبه'; break;
					case 4: $dName = 'چهارشنبه'; break;case 5: $dName = 'پنجشنبه'; break;
					case 5: $dName = 'جمعه'; break;
				}
				
				$dateBegin = strtotime("today -".($todayInWeek-$i+1)." days");
				$dateEnd = strtotime("today -".($todayInWeek-$i)." days");
				
				if(DateInArray($dateArray,$dateBegin,$dateEnd)>0)
				$return .='
				<li class="sub">
					<span date="'.$dateBegin.','.$dateEnd.'">'.$dName.'</span>
				  </li>';
				
			}           
			$return .='       
			</ul>
		  </li>';
		 }
		  
		if(DateInArray($dateArray,$BeginWeek-7*24*3600,$BeginWeek)>0)
		$return .=' 
		<li class="prev-week"><span date="'.($BeginWeek-7*24*3600) .','.$BeginWeek.'">هفته پیش</span></li>';
		
		if(DateInArray($dateArray,$beginMonth,$EndMonth)>0)
		$return .='
		<li class="this-month"><span date="'.$beginMonth.','.$EndMonth.'"> این ماه </span></li>';
		
		
		
		
		for($i=$thisMonth-1;$i>0;$i--){
			
			$EndMonth = $beginMonth;
			$daysOfMonth = jdate('t',$beginMonth-100,'','','en');
			$m = jdate('m',$beginMonth-100,'','','en');
			$beginMonth  -= $daysOfMonth*24*3600;
			
			if($m==6)$beginMonth-=3600;
			elseif($m==1)$beginMonth+=3600;
			
			$nameMonth = jdate('F',$beginMonth);
			
			if(DateInArray($dateArray,$beginMonth,$EndMonth))					  
			$return .= '
			<li class="this-year-month"><span date="'.$beginMonth.','.$EndMonth.'">'.$nameMonth.'</span></li>';
		
		}
	
		$BeginYear = $beginMonth;
			
		for($j=0;$j<20;$j++){
			
			$EndYear = $BeginYear;
			
			$yearDays = jdate('z',$BeginYear-100,'','','en');
			
			$BeginYear -= ($yearDays+1)*24*3600;
			
			if(DateInArray($dateArray,$BeginYear,$EndYear)){
			
				$return .= '
				<li class="ar-year"><span date="'.$BeginYear.','.$EndYear.'">'.jdate('y',$BeginYear).'</span>
				  <ul style="display:none">';
				
					$beginMonth = $BeginYear;
					for($z=1;$z<=12;$z++){
						
						$daysOfMonth = jdate('t',$beginMonth,'','','en');
						$EndMonth = $beginMonth + $daysOfMonth*24*3600;
						
						if($z==1)$EndMonth-=3600;
						elseif($z==6)$EndMonth+=3600;
						
						$nameMonth = jdate('F',$beginMonth);
						
						if(DateInArray($dateArray,$beginMonth,$EndMonth))			  
						$return .= '
						<li class="sub"><span date="'.$beginMonth.','.$EndMonth.'">'.$nameMonth.'</span></li>';
						
						$beginMonth = $EndMonth ;
					}			
				$return .= '
				</ul>';
			}
		}
					  
		$return .= '
		  </li>
		</ul>'; 
		return $return;	
	}

	
	function jalaliToGregorian($jy,$jm,$jd,$mod=''){
		$d4=($jy+1)%4;
		$doyj=($jm<7)?(($jm-1)*31)+$jd:(($jm-7)*30)+$jd+186;
		$d33=(int)((($jy-55)%132)*.0305);
		$a=($d33!=3 and $d4<=$d33)?287:286;
		$b=(($d33==1 or $d33==2) and ($d33==$d4 or $d4==1))?78:(($d33==3 and $d4==0)?80:79);
		if((int)(($jy-19)/63)==20){$a--;$b++;}
		if($doyj<=$a)
		{
			$gy=$jy+621; $gd=$doyj+$b;
		}else
		{
			$gy=$jy+622; $gd=$doyj-$a;
		}
		foreach(array(0,31,($gy%4==0)?29:28,31,30,31,30,31,31,30,31,30,31) as $gm=>$v){
			if($gd<=$v)break;
			$gd-=$v;
		}
		return($mod=='')?array($gy,$gm,$gd):$gy.$mod.$gm.$mod.$gd;
	} 
	
	function getDateIntervals(){
		
		$dateIntervals = array();
		
		$now = strtotime('now');
		$today = strtotime('today');
		$todayInWeek = jdate("N",$today,'','','en')-1;
		$todayInMonth = jdate("d",$today,'','','en');
		$thisMonth = jdate("m",$today,'','','en');
		$thisYear = jdate("y",$today,'','','en');
		
		$BeginWeek = $todayInWeek==0?$today:strtotime('today -'.($todayInWeek+1).' days'); 
		$beginMonth = $todayInMonth==1?$today:strtotime('today -'.($todayInMonth-1).' days');
		$EndWeek = $BeginWeek+(7*24*3600);
		$EndMonth = $beginMonth+(jdate('t',$today,'','','en')*24*3600);

		$dateIntervals['this_week']['se'] = array('name'=>'این هفته','begin'=>$BeginWeek,'end'=>$EndWeek);
			
		for($i=0;$i<=$todayInWeek+1;$i++){
			
			if($i==$todayInWeek+1){
				$dName = 'امروز';
			}else
			switch ($i){
				case 0: $dName = 'شنبه'; break;case 1: $dName = 'یکشنبه'; break;
				case 2: $dName = 'دوشنبه'; break;case 3: $dName = 'سه شنبه'; break;
				case 4: $dName = 'چهارشنبه'; break;case 5: $dName = 'پنجشنبه'; break;
				case 5: $dName = 'جمعه'; break;
			}
			
			$dateBegin = strtotime("today -".($todayInWeek-$i+1)." days");
			$dateEnd = strtotime("today -".($todayInWeek-$i)." days");
			
			$dateIntervals['this_week']['su'][] = array('name'=>$dName,'begin'=>$dateBegin,'end'=>$dateEnd); 
		}           
		
		$dateIntervals['last_week']['se'] = array('name'=>'هفته پیش','begin'=>$BeginWeek-7*24*3600,'end'=>$BeginWeek); 
		$dateIntervals['this_month']['se'] = array('name'=>'این ماه','begin'=>$beginMonth,'end'=>$EndMonth); 
		
		for($i=$thisMonth-1;$i>0;$i--){
			
			$EndMonth = $beginMonth;
			$daysOfMonth = jdate('t',$beginMonth-100,'','','en');
			$m = jdate('m',$beginMonth-100,'','','en');
			$beginMonth  -= $daysOfMonth*24*3600;
			
			if($m==6)$beginMonth-=3600;
			elseif($m==1)$beginMonth+=3600;
			
			$nameMonth = jdate('F',$beginMonth);
			$dateIntervals['this_year'][] = array('name'=>$nameMonth,'begin'=>$beginMonth,'end'=>$EndMonth); 
		}
	
		$BeginYear = $beginMonth;
			
		for($j=0;$j<5;$j++){
			
			$EndYear = $BeginYear;
			
			$yearDays = jdate('z',$BeginYear-100,'','','en');
			
			$BeginYear -= ($yearDays+1)*24*3600;
			
			$yearName = jdate('y',$BeginYear);
			
			$dateIntervals[$yearName]['se'] = array('name'=>$yearName ,'begin'=>$BeginYear,'end'=>$EndYear); 
				
			$beginMonth = $BeginYear;
			for($z=1;$z<=12;$z++){
				
				$daysOfMonth = jdate('t',$beginMonth,'','','en');
				$EndMonth = $beginMonth + $daysOfMonth*24*3600;
				
				if($z==1)$EndMonth-=3600;
				elseif($z==6)$EndMonth+=3600;
				
				$nameMonth = jdate('F',$beginMonth);
										
				$dateIntervals[$yearName]['su'][] =
				array('name'=>$nameMonth,'begin'=>$beginMonth,'end'=>$EndMonth); 
				
				$beginMonth = $EndMonth ;
			}			
		}
		return $dateIntervals;	
	}
	
}//======= end inc

class dateChart{
	
	public $data;
	
	function __construct(){
		
		echo '<script>
function setChartSize(){$("ul.chart li").each(function(t,f){var e=$(f).closest("ul.chart").offset().left,i=$(f).offset().left;$(f).find(".before").width(i-e)})}
$(document).ready(function(){$(window).resize(setChartSize),setChartSize()});
</script>';
	}
	
	public function setData($data,$title,$height = 300)
	{
		$dates = array();
		foreach( $data as $k=>$v )
		{
			$date = $v['date'];
			if( !is_numeric( $date ) )
			$date = strtotime($date);
			
			$dates[] = $date;
		}
		sort($dates);
		$this->data['dates'] = $dates;
		$this->data['min'] = $this->data['dates'][0];
		$this->data['max'] = $this->data['dates'][count($data)-1];
		$this->data['title'] = $title;
		$this->data['height'] = $height;
	}
	
	public function analyze()
	{		
		$defrence = $this->data['max'] - $this->data['min'];
		$defrence = ceil($defrence/3600);
		$day = 24;$month = $day*30;$year = $day*365;
		
		$d_min = $this->data['min'];
		$d_max = $this->data['max'];		
		$step = 10*60;
		$min = $this->getBeginEnd($d_min,'hour');
		$max = $this->getBeginEnd($d_max,'hour','end');
		$col_format = "m-d H:i";
		$info_format1 = "m/d (H:i - ";
		$info_format2 = "H:i)";	

		if( $defrence > $day && $defrence <= $month )
		{
			$step = 3*60*60;
			$min = $this->getBeginEnd($d_min,'day');
			$max = $this->getBeginEnd($d_max,'day','end');
			$col_format = "H:i m-d";			
		}
		elseif( $defrence > $month && $defrence <= $month*3 )
		{
			$step = 24*3600;
			$min = $this->getBeginEnd($d_min,'day');
			$max = $this->getBeginEnd($d_max,'day','end');
			$col_format = "H:i m-d";
			$info_format1 = "m/d - ";
			$info_format2 = "m/d";	
		}		
		elseif( $defrence > $month*3 && $defrence <= $year )
		{
			$step = 2*24*3600;
			$min = $this->getBeginEnd($d_min,'day');
			$max =  $this->getBeginEnd($d_max,'day','end');
			$col_format = "y-m-d H:i";
			$info_format1 = "y/m/d - ";
			$info_format2 = "y/m/d";							
		}
		elseif( $defrence > $year )
		{
			$step = 30*24*60*60;
			$min = $this->getBeginEnd($d_min,'month');
			$max =  $this->getBeginEnd($d_max,'month','end');
			$col_format = "y-m-d H:i";
			$info_format1 = "y/m/d - ";
			$info_format2 = "y/m/d";					
		}
		
		$this->data['step'] = $step;
		
		for($c = $min; $c <= $max; $c+=$step){
			
			$H = substr(jdate($col_format,$c,'','','en'),0,8);
			
			if( ! isset($this->data['cols'][$H]) && $this->getCount($c,$c+$step))
			{
				$info = jdate($info_format1,$c,'','','en').jdate($info_format2,$c+$step,'','','en');
				$this->data['cols'][$H] = array($c,$this->getCount($c,$c+$step),$info);
			}
		}		

	}	

	public function getCount($min,$max)
	{
		$counter = 0;

		foreach( $this->data['dates'] as $k=>$date )		
		{
			if( $date >= $min && $date < $max )
			$counter++;
		}
		$this->data['count-list'][] = $counter;
		return $counter;
	}
	
	public function percentage($count)
	{
		if( $count == 0 ) return $count; 
		$total = max($this->data['count-list']);
		return round( $count*10000/($total + $total/10) )/100;
	}
		
	public function leftSide($mode='default')
	{
		$total = max($this->data['count-list']);
		$rows = $total;
		$row_step = ceil($total/10);		
		
		switch($mode){
			case'row':
			for($c = 0; $c <= $rows; $c += $row_step)
			{
				$this->data['left']['rows'][] = $c;
				$percent =  ceil( $c*10000/($rows+$total/10) )/100;
				if($percent)
				echo "<li class=\"chat-row\" style=\"bottom:$percent%\"></li>";
			}
			break;
			default:
			echo "<ul class=\"chart-left-side\">";
			for($c = 0; $c <= $rows; $c += $row_step)
			{
				$this->data['left']['rows'][] = $c;
				$percent =  ceil( $c*10000/($rows+$total/10) )/100;
				echo "<li style=\"bottom:$percent%\">$c</li>";
			}
			echo "</ul>";
			break;
		}
	}
	
	public function bottom()
	{
		$data = $this->data['cols'];
		$total = count($data);
		$counter = 0;
		$step = floor($total/10);
		
		if( $step == 0 ) $step = 1;

		echo "<ul class=\"chart-bottom\">";
			foreach( $data as $key => $array )
			{
				if( ($counter%$step) == 0 OR $counter == 0 )
				{
					$percent =  ceil( $counter*10000/($total+$total/40) )/100;
					echo '<li style="left:'.$percent.'%">'.$key.'</li>';
				}
				$counter++;
			}
		echo "</ul>";

	}	
	
			
	public function render()
	{
		$data = $this->data['cols'];
		$total = count($data);
		$counter = 0;
		
		$h = $this->data['height'];
		
		echo '<div style="height:'.($h+100).'px">';
		echo '<div class="chart-div">';
		echo '<div class="chart-title">
				<i class="fa fa-bar-chart"></i>
				<span>'.$this->data['title'].'</span>	
			  </div>';
		$this->leftSide();
		echo '<ul class="chart" style="height:'.$h.'px"> ';
			foreach( $data as $key => $array )
			{
				$percent = $this->percentage($array[1]);
				$left = ceil( $counter*10000/($total+$total/40) )/100;
				if($array[1])
				echo '
				<li style="left:'.$left.'%">
				    <div class="col-details">
						<span style=color:orange>'. $array[2].'</span> &nbsp;
						<span style=color:green>'.$array[1].'</span>
					</div>
					<i class="'.($array[1]?'data':'').'" style="height:'.$percent.'%">
					<span class="before"></span>
					</i>
				</li>';
				
				$counter++;
			}
		$this->leftSide('row');
		echo "</ul>";
		$this->bottom();
		echo '</div>';
		echo '</div>';
	}
	
	public function getBeginEnd($date,$type = 'day' ,$action = 'begin' )
	{
		switch($type){
			case 'minute':
			$begin = $date - date("s",$date);
			$end = $begin + 60;			
			break;
			case 'hour':
			$begin = $this->getBeginEnd($date,'minute') - date("i",$date)*60;
			$end = $begin + 3600;			
			break;
			case 'day':
			$begin = $this->getBeginEnd($date,'hour') - (date('H',$date)*3600);
			$end = $begin + 24*3600;			
			break;
			case 'month':
			$begin = $this->getBeginEnd($date,'day') - (jdate('d',$date,'','','en')-1)*24*3600;
			$end = $begin + jdate('t',$date,'','','en')*24*3600;		
			break;			
			case 'year':
			$m = jdate('m',$date,'','','en');$h = $m < 7 ? 3600 : 0;
			$begin = $this->getBeginEnd($date,'day') - jdate('z',$date,'','','en')*24*3600 + $h  ;
			$end =  $this->getBeginEnd($date,'day','end') + jdate('Q',$date,'','','en')*24*3600 + $h ;		
			break;							
		}
		return $action == 'begin' ? $begin : $end;
	}	
}

class SimpleImage {
 
   var $image;
   var $image_type;
 
   function load($filename) { 
      if(!file_exists($filename) || !filesize($filename))
	  	return ;
	  $image_info = getimagesize($filename);
      $this->image_type = $image_info[2];
      if( $this->image_type == IMAGETYPE_JPEG ) {
 
         $this->image = imagecreatefromjpeg($filename);
      } elseif( $this->image_type == IMAGETYPE_GIF ) {
 
         $this->image = imagecreatefromgif($filename);
      } elseif( $this->image_type == IMAGETYPE_PNG ) {
 
         $this->image = imagecreatefrompng($filename);
      }
   }
   
   function save($filename, $compression=75, $permissions=null) {
	   
      $image_type = $this->image_type;
	  
      if( $image_type == IMAGETYPE_JPEG ) {
		  
        imagejpeg($this->image,$filename,$compression);
		 
      } elseif( $image_type == IMAGETYPE_GIF ) {
 
        imagegif($this->image,$filename);
		 
      } elseif( $image_type == IMAGETYPE_PNG ) {
 
        imagepng($this->image,$filename,9);
      }
	  
      if( $permissions != null) {
 
         chmod($filename,$permissions);
      }
   }
   
   function output($image_type=IMAGETYPE_JPEG) {
 
      if( $image_type == IMAGETYPE_JPEG ) {
         imagejpeg($this->image);
      } elseif( $image_type == IMAGETYPE_GIF ) {
 
         imagegif($this->image);
      } elseif( $image_type == IMAGETYPE_PNG ) {
 
         imagepng($this->image);
      }
	  
   }
   function getWidth() {
 
      return imagesx($this->image);
   }
   function getHeight() {
 
      return imagesy($this->image);
   }
   function resizeToHeight($height) {
 
      $ratio = $height / $this->getHeight();
      $width = $this->getWidth() * $ratio;
      $this->resize($width,$height);
   }
 
   function resizeToWidth($width) {
      $ratio = $width / $this->getWidth();
      $height = $this->getheight() * $ratio;
      $this->resize($width,$height);
   }
 
   function scale($scale) {
      $width = $this->getWidth() * $scale/100;
      $height = $this->getheight() * $scale/100;
      $this->resize($width,$height);
   }

    function resize($width, $height)
    {
        $new_image = imagecreatetruecolor($width, $height);
        if ($this->image_type == IMAGETYPE_GIF || $this->image_type == IMAGETYPE_PNG)
        {
            $current_transparent = imagecolortransparent($this->image);
            $palletsize = imagecolorstotal($this->image);
            if ($current_transparent != -1 && $current_transparent < $palletsize) 
            {
                $transparent_color = imagecolorsforindex($this->image, $current_transparent);
                $current_transparent = imagecolorallocate($new_image, $transparent_color['red'], $transparent_color['green'], $transparent_color['blue']);
                imagefill($new_image, 0, 0, $current_transparent);
                imagecolortransparent($new_image, $current_transparent);
            }
            elseif ($this->image_type == IMAGETYPE_PNG)
            {
                imagealphablending($new_image, false);
                $color = imagecolorallocatealpha($new_image, 0, 0, 0, 127);
                imagefill($new_image, 0, 0, $color);
                imagesavealpha($new_image, true);
            }
        }
        imagecopyresampled($new_image, $this->image, 0, 0, 0, 0, $width, $height, $this->getWidth(), $this->getHeight());
        $this->image = $new_image;
    }
   
	function crop($thumb_width=150,$thumb_height=150){
		
		$width = $this->getWidth();
		$height = $this->getHeight();
		
		$original_aspect = $width / $height;
		$thumb_aspect = $thumb_width / $thumb_height;
		
		if ( $original_aspect >= $thumb_aspect )
		{
		   $new_height = $thumb_height;
		   $new_width = $width / ($height / $thumb_height);
		}
		else
		{
		   $new_width = $thumb_width;
		   $new_height = $height / ($width / $thumb_width);
		}
		$new_image = imagecreatetruecolor( $thumb_width, $thumb_height );
		
		if( $this->image_type == IMAGETYPE_GIF || $this->image_type == IMAGETYPE_PNG ) { 
			$current_transparent = imagecolortransparent($this->image); 
			if($current_transparent != -1) { 
				$transparent_color = imagecolorsforindex($this->image, $current_transparent); 
				$current_transparent = imagecolorallocate($new_image, $transparent_color['red'], $transparent_color['green'], $transparent_color['blue']); 
				imagefill($new_image, 0, 0, $current_transparent); 
				imagecolortransparent($new_image, $current_transparent); 
			} elseif( $this->image_type == IMAGETYPE_PNG) { 
				imagealphablending($new_image, false); 
				$color = imagecolorallocatealpha($new_image, 0, 0, 0, 127); 
				imagefill($new_image, 0, 0, $color); imagesavealpha($new_image, true); 
			} 
		} 
		imagecopyresampled($new_image,
						   $this->image,
						   0 - ($new_width - $thumb_width) / 2, 
						   0 - ($new_height - $thumb_height) / 2,
						   0, 0,
						   $new_width, $new_height,
					   	   $width, $height);
		$this->image = $new_image;
	}
  
   function getType() {
	   return $this->image_type;
   } 
   
}
?>