<?php
//==========================================	
	class MarkazWeb{
	function LoadSmsPanel($mode,$Username,$Password,$fromNum,$sms_center,$toNum=NULL,$Content=NULL){
		//ob_clean();
		$options = array();
		if(!strlen($Username) || !strlen($Password) || !strlen($fromNum))
		{
			return " <strong>تنظیمات پیامک صحیح نیست</strong> ";
		}
		//error_reporting(0);
		try
		{
			$soapClientObj = new soapclient($sms_center,array('trace'=> 1, 'exceptions' => true));
			switch($mode){
			case 'check':
				$result = $soapClientObj->GetCredit($Username,$Password);
				if(!strlen($result)){
					return "<strong>عدم دسترسی به سرور مرکز وب</strong> ";
				}
				$_SESSION["DataControl"]["MarkazWebcheck"] = 1;
				return ((int)$result);
			break;
			case 'send':
				$Type = 0;
				$toNum = array($toNum);
				$result = $soapClientObj->SendSMS($fromNum, $toNum, $Content, $Type, $Username, $Password);
				if(is_array($result) && count($result)==2){
					$result = array_pop($result);
				} else {
					$result = array_pop($result);
					$result = array(1,$result);
				}
				return $result; 
			break;
			case 'getdelivery':
				$soapClientObj->recId = $toNum;
				return $soapClientObj->GetStatus($Username,$Password,$toNum);
			break;
			}
		}
		catch (SoapFault $sf)
		{
			return "<strong>عدم دسترسی به سرور مرکز وب</strong> ";
		}
	}
	}
//==========================================	
?>