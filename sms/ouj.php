<?php
//==========================================	
	class Ouj{
	function LoadSmsPanel($mode,$sms_username,$sms_password,$sms_number,$sms_center,$sms_NO=NULL,$message=NULL){
		$Udh="";
		$options = array();
		if(!strlen($sms_username) || !strlen($sms_password) || !strlen($sms_number))
		{
			return "<strong>تنظیمات پیامک صحیح نیست</strong>";
		}
		$options['username'] = $sms_username;
		$options['password'] = $sms_password;
		$options['from'] = $sms_number;
		$options['isflash'] = false;
		$options['udh'] = $Udh;
		$options['recId'] = array(0);
		$options['status'] = 0x0;
		if(is_array($sms_NO)){
		$options['isflash'] = 0;
		$options['to'] = $sms_NO;
		if($message)
		$options['text']= $message;
		}
		if(!class_exists("soapclient")){
			return "<strong>Class 'soapclient' not found</strong>";
			}
		try
		{
		$client = new soapclient($sms_center);
		switch($mode){
		case 'check':
		$res = $client->GetCredit($options);//
		if(empty($res)){
		return "<strong>عدم دسترسی به سرور پیامک 1</strong>";
		}
		return ($res->GetCreditResult);
		break;
		case 'inbox':
		$options["isRead"]=1;
		$res = $client->GetInboxCount($options);//
		return ($res->GetInboxCountResult);
		//send a message to a number
		break;
		case 'send':
		$result = $client->SendSms($options);
		return array($result->SendSmsResult,$result->recId->long);
		break;
		}
		}
		catch (SoapFault $sf)
		{
			return "<strong>عدم دسترسی به سرور پیامک 1</strong>";
		}
	}
	}
//==========================================	
?>