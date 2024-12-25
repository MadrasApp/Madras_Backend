<?php
error_reporting(E_ALL);
//==========================================	
	class SmsPhs_NuSoap{
	function LoadSmsPanel($mode,$sms_username,$sms_password,$sms_number,$sms_center,$sms_NO=NULL,$message=NULL){
		if(!class_exists("nusoap_client"))
			require_once('nusoap/nusoap.php');
		$Udh="";
		$domain = JUri::base();
		$domain = str_replace(array('http://','https://','www.'),'',$domain);
		$domain = "sms1.smsphs.com";
		$options = array();
		if(!strlen($sms_username) || !strlen($sms_password) || !strlen($sms_number))
		{
			$_SESSION["DataControl"]["SmsPhscheck"] = " <strong>تنظیمات پیامک صحیح نیست</strong> ";
			return 0;
		}
		try
		{
			$client=new nusoap_client($sms_center, 'wsdl');
			$err = $client->getError();
			if ($err) {
				$_SESSION["DataControl"]["SmsPhscheck"] = '<h2>Constructor error</h2><pre>' . $err . '</pre>';
				return 0;
			}
		switch($mode){
		case 'check':
			$data = array('domain' => $domain,'username' => $sms_username,'password' => $sms_password);
			$res = $client->call('getCredit', $data);
			if(empty($res)){
			$_SESSION["DataControl"]["SmsPhscheck"] = " <strong>عدم دسترسی به سرور پیامک 2</strong> ";
			return 0;
			}
			$_SESSION["DataControl"]["SmsPhscheck"] = 1;
			$res = (int)$res;
			$res = self::ErrorReport($res,$domain);
			return $res;
		break;
		case 'inbox':
			$data = array('domain' => $domain,'username' => $sms_username,'password' => $sms_password);
			$res = $client->call('getCredit', $data);
			return ((int)$res); 
		break;
		case 'send':
			$sms_NO = is_array($sms_NO)?implode(";",$sms_NO):$sms_NO;
			$data = array(
			'domain' => $domain,
			'username' => $sms_username,
			'password' => $sms_password,
			'from' => $sms_number,
			"to"=>$sms_NO,
			"text"=>$message,
			"isflash"=>"1");
			$id = $client->call('sendSMS', $data);
			if(is_numeric($id) && $id > 0){
				$id = array(1,$id);
			}  else {
				$id = self::ErrorReport($id,$domain);
			}
			return $id; 
		break;
		}
		}
		catch (SoapFault $sf)
		{
			$_SESSION["DataControl"]["SmsPhscheck"] = " <strong>عدم دسترسی به سرور پیامک 2</strong> ";
			return 0;
		}
	}
	function ErrorReport($id,$domain){
		$msg = $id;
		switch($id){
			case -1 : $msg ="خطا در ارسال";break;
			case -2 : $msg ="نام کاربري ، کلمه عبور یا نام دامنه $domain اشتباه میباشد";break;
			case -3 : $msg ="شماره فرستنده معتبر نمی باشد";break;
			case -4 : $msg ="اعتبار کافی نیست";break;
			case -5 : $msg ="پیام پس از تائید ارسال می شود";break;
			case -6 : $msg ="شماره گیرنده صحیح نمی باشد";break;
			}
		return $msg;
	}
	}
//==========================================	
?>