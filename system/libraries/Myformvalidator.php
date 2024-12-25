<?php defined('BASEPATH') OR exit('No direct script access allowed');
//Alireza Balvardi
//include_once("Form_validation.php");
class CI_MyFormValidator extends CI_Form_validation {
	public function __construct($rules = array())
    {
		// Pass the $rules to the parent constructor.
        parent::__construct($rules);
		$this->_config_rules = $rules;
        // $this->CI is assigned in the parent constructor, no need to do it here.
    }
	public function valid_mobile($mobile){
		
		if( substr($mobile,0,3) == '+98' )
		{
			if( strlen($mobile) != 13 )
				throw new Exception( "شماره موبایل با پیشوند +98 باید 13 رقم باشد" , 1);
		}
		elseif( substr($mobile,0,1) == '0' )
		{
			if( strlen($mobile) != 11 )
				throw new Exception( "شماره موبایل بدون پیشوند +98 باید 11 رقم باشد" , 1);
			
			$mobile = '+98' . substr($mobile,1,11);				
		}
		else
			throw new Exception( "شماره موبایل معتبر نیست" , 2);
		
		return $mobile;
	}
	public function check_mobile($mobile){
		
		if( substr($mobile,0,3) == '+98' )
		{
			if( strlen($mobile) != 13 )
				throw new Exception( "شماره موبایل با پیشوند +98 باید 13 رقم باشد" , 1);
		}
		elseif( substr($mobile,0,1) == '0' )
		{
			if( strlen($mobile) != 11 )
				throw new Exception( "شماره موبایل بدون پیشوند +98 باید 11 رقم باشد" , 1);
			
			$mobile = '+98' . substr($mobile,1,11);				
		}
		else
			throw new Exception( "شماره موبایل معتبر نیست" , 2);
		
		return $mobile;
	}
	public function valid_mac($mac){
		$this->LogMe(array("_valid_mac"=>$mac));
		return $mac;//Alireza Balvardi
		if(strlen(str_replace(array(':','-','.'),'',$mac)) !== 12)
			return FALSE;
		
		if (
			preg_match('/^([a-fA-F0-9]{2}:){5}[a-fA-F0-9]{2}$/', $mac) OR
			preg_match('/^([a-fA-F0-9]{2}\-){5}[a-fA-F0-9]{2}$/', $mac) OR
			preg_match('/^[a-fA-F0-9]{12}$/', $mac) OR
			preg_match('/^([a-fA-F0-9]{4}\.){2}[a-fA-F0-9]{4}$/', $mac)
		){
			$mac = $this->_normalize_mac($mac);
			if(strlen($mac)===17) return $mac;
		} 
		
		return FALSE;
	}
}

?>