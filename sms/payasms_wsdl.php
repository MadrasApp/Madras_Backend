<?php
//==========================================	
	class PayaSMS_WSDL{
	function LoadSmsPanel($mode,$sms_username,$sms_password,$sms_number,$sms_center,$sms_NO=NULL,$message=NULL){
		$Udh="";
		$options = array();
		if(!strlen($sms_username) || !strlen($sms_password) || !strlen($sms_number))
		{
			return "<strong>تنظیمات پیامک صحیح نیست</strong>";
		}
		//error_reporting(0);
		try
		{
			$url = "$sms_center/API/Send.asmx?wsdl";
			$client = new SoapClient($url);
			$options['username'] = $sms_username;
			$options['password'] = $sms_password;
		switch($mode){
		case 'check':
			$status = $client->Credit($options)->CreditResult;
			if(empty($status)){
			return "<strong>عدم دسترسی به سرور پیامک پایا اس ام اس</strong>";
			}
			return (int)$status;
		break;
		case 'inbox':
			$options['IsRead'] = false;
			$status = $client->InboxCount($options)->InboxCount();
			return (int)$status;
		break;
		case 'send':
			$options['from'] = $sms_number;
			$options['flash'] = false;
			$options['recId'] = array(0);
			$options['status'] = 0x0;
			$options['to'] = $sms_NO;
			$options['text'] = $message;
			$result = $client->SendSms($options);
			return array($result->SendSmsResult,$result->recId->long);
		break;
		}
		}
		catch (SoapFault $sf)
		{
			return "<strong>عدم دسترسی به سرور پیامک پایا اس ام اس</strong>";
		}
	}
	}
//==========================================	
	?>