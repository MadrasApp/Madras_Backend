<?php
//==========================================	
	class SMSGhasedak{
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
			$url = "$sms_center/ghasedakwebservice/smsghasedak.asmx?wsdl";
			$client = new SoapClient($url);
			$options['userName'] = $sms_username;
			$options['password'] = $sms_password;
		switch($mode){
		case 'check':
			$status = $client->checkCredit($options)->checkCreditResult;
			if(empty($status)){
			return "<strong>عدم دسترسی به سرور پیامک قاصدک</strong>";
			}
			return (int)$status;
		break;
		case 'inbox':
			$status = 0;
			//return (int)$status; 
		break;
		case 'send':
			$options['from'] = $sms_number;
			$options['receivers'] = $sms_NO;
			$options['message'] = $message;
			$status = $client->sendSmsLessThan20($options);
			$options['flash'] = false;
			$options['recId'] = array(0);
			$options['status'] = 0x0;
		return array(1,$status);
		break;
		}
		}
		catch (SoapFault $sf)
		{
			return "<strong>عدم دسترسی به سرور پیامک قاصدک</strong>";
		}
	}
	}
//==========================================	
	?>