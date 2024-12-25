<?php
//==========================================	
	class PayaSMS{
	function LoadSmsPanel($mode,$sms_username,$sms_password,$sms_number,$sms_center,$sms_NO=NULL,$message=NULL){
		$Udh="";
		$options = array();
		if(!strlen($sms_username) || !strlen($sms_password) || !strlen($sms_number))
		{
			return "<strong>تنظیمات پیامک صحیح نیست</strong>";
		}
		switch($mode){
		case 'check':
			$url = "$sms_center/API/GetCredit.ashx?username=$sms_username&password=$sms_password";
			$res = self::PayaSMScall($url);//
			if(empty($res)){
			return "<strong>عدم دسترسی به سرور پیامک پایا اس ام اس</strong>";
			}
			return $res;
		break;
		case 'inbox':
			return "این امکان را ندارد";
		break;
		case 'send':
			$url = "$sms_center/API/SendSms.ashx?".
				"username=" .$sms_username . "&password=" .$sms_password. 
				"&from=" . $sms_number . "&To=" . implode(",",$sms_NO) .
				"&Text=" . urlencode($message)  ;
			$res = self::PayaSMScall($url);//
			return $res;
		break;
		}
	}
//==========================================	
	function PayaSMScall($url){
		return @file_get_contents($url);
	}
	}
//==========================================	
	?>