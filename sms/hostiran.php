<?php
//==========================================	
class HostIran{
	function LoadSmsPanel($mode,$sms_username,$sms_password,$sms_number,$sms_center,$sms_NO=NULL,$message=NULL){
		$options = array();
		if(!strlen($sms_username) || !strlen($sms_password) || !strlen($sms_number))
		{
			return "<strong>تنظیمات پیامک صحیح نیست</strong>";
		}
		//error_reporting(0);
		try
		{
			$url = "$sms_center/webservice/?WSDL";
			$options['login'] = $sms_username;
			$options['password'] = $sms_password;
			$client = new SoapClient($url,$options);
		switch($mode){
		case 'check':
			$status = $client->accountInfo();
			if(empty($status)){
			return "<strong>عدم دسترسی به سرور پیامک هاست ایران</strong>";
			}
			return (int)$status->credit;
		break;
		case 'inbox':
			$status = $client->accountInfo();
			return "<br /><strong>دریافتی</strong> : ".$status->received."<br /><strong>ارسالی</strong> : ".$status->sent; 
		break;
		case 'send':
			if(is_array($sms_NO))
				$sms_NO = implode(",",$sms_NO);
			$status = $client->send($sms_NO,$message);
		return $status;
		break;
		}
		}
		catch (SoapFault $sf)
		{
			return "<strong>عدم دسترسی به سرور پیامک هاست ایران</strong>";
		}
	}
	}
//==========================================	
	?>