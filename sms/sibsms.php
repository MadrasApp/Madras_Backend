<?php
//==========================================	
class SibSMS{
	function LoadSmsPanel($mode,$sms_username,$sms_password,$sms_number,$sms_center,$sms_NO=NULL,$message=NULL){
		$Udh="";
		$options = array();
		if(!strlen($sms_username) || !strlen($sms_password) || !strlen($sms_number))
		{
			return "<strong>تنظیمات پیامک صحیح نیست</strong>";
		}
		switch($mode){
		case 'check':
			$url = "$sms_center/Credit.aspx?Username=$sms_username&Password=$sms_password";
			$res = self::SibSMScall($url);//
			if(empty($res)){
			return "<strong>عدم دسترسی به سرور پیامک 1</strong>";
			}
			return $res;
		break;
		case 'inbox':
			$url = "$sms_center/Credit.aspx?Username=$sms_username&Password=$sms_password";
			$res = self::SibSMScall($url);//
			return $res;
		break;
		case 'send':
			$url = "$sms_center/APISend.aspx?".
				"Username=" .$sms_username . "&Password=" .$sms_password. 
				"&From=" . $sms_number . "&To=" . $sms_NO .
				"&Text=" . urlencode($message)  ;
			$res = self::SibSMScall($url);//
			return $res;
		break;
		}
	}
//==========================================	
	function SibSMScall($url){
		return @file_get_contents($url);
	}
}
//==========================================	
	?>