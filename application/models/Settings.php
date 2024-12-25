<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Settings extends CI_Model {

	public $data;

	public function __construct()
	{
		parent::__construct();
		
		$result = $this->db->select('name,value')->get('settings',200)->result();;
		
		$settings_array;
		foreach($result as $key=>$row)
		{
			$settings_array[$row->name] = $row->value;
		}
	
		$this->data = $settings_array;
		
		date_default_timezone_set($this->data['time_zone']);
		
		$this->data['tz_offset'] = date('Z');
	}
	
	public function index()
	{
		return $this->data;
	}
		
	public function get()
	{
		return $this->data;
	}	
	
	function Date($date,$format = 'html'){
		
		$setting = $this->data;
		date_default_timezone_set($setting['time_zone']);
		
		$datestr = is_numeric($date)? $date : strtotime($date);
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
			
			
			$return = ($D? ' دیروز - ' : ' امروز - ' ).jdate($setting['time_format'],$datestr,"","","en");
		}elseif($D > 0 && $D < 7 && $m_n == $m && $Y_n == $Y){
			
			
			$return = jdate('l',$datestr).' - ' .jdate($setting['time_format'],$datestr,"","","en");
			
			
		}elseif($m_n == $m && $Y_n == $Y && $D==-1){
			
			
			$return = ' فردا '.jdate($setting['time_format'],$datestr,"","","en");
			
			
		}else
		$return = jdate($setting['date_format'].' - '.$setting['time_format'],$datestr,"","","en");
		
		$title = jdate($setting['date_format'].' - '.$setting['time_format'],$datestr,"","","en").' | '.$date;
		
		if( $format == 'html' )
		return '<span class="relative-date" datestr="'.( $datestr - date('Z') ). '" date="'.$return.'" title="'.$title.'" >'.$return.'</span>';
		
		if( $format == 'array' )
		return array($datestr - date('Z'),$return,$title);
	}	
	
}
?>