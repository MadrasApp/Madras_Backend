<?php
//==========================================	
	class FaraSMSPanel{
	function LoadSmsPanel($mode,$sms_username,$sms_password,$sms_number,$sms_center,$sms_NO=NULL,$message=NULL){
		$options = array();
		if(!strlen($sms_username) || !strlen($sms_center) || !strlen($sms_number))
		{
			return "<strong>تنظیمات پیامک صحیح نیست</strong>";
		}
		//error_reporting(0);
		try
		{
		$params=array(
					'UserName='.$sms_username,// نام گاربری
					'Password='.$sms_password, // رمز عبور
					'LineNumber='.$sms_number,// شماره خط
					'SendDate='.date("Y-m-d"),// تاریخ دریافت
					'SendTime='.date("H:i:s"),// زمان دریافت
					'SendClass='.'1' // نوع دریافت
		);
		switch($mode){
		case 'check':
			return "سرور پیامک این امکان را ندارد";
			$res = URLSendxxxxxxxx($sms_center,$params);
			if(empty($res)){
			return "<strong>عدم دسترسی به سرور پیامک فرا اس ام اس</strong>";
			}
			return (int)$res;
		break;
		case 'inbox':
			return "سرور پیامک این امکان را ندارد";
			return URLSendxxxxxxxx($sms_center,$params); 
		break;
		case 'send':
			$params['Recivers']	= implode(',',$sms_NO);
			$params['SMSMSG']	= $message;
			return URLSendxxxxxxxx($sms_center,$params);
		break;
		}
		}
		catch(Exception $e)
		{
			return "<strong>عدم دسترسی به سرور پیامک فرا اس ام اس</strong>";
		}
	}
	}
//==========================================	
	function URLSendxxxxxxxx($sms_center,$params){
			$ch = curl_init();
			$smsURL = $sms_center."?".implode("&",$params);
			curl_setopt($ch, CURLOPT_URL, $smsURL);
	 		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			$output = curl_exec($ch);
			$aCURLinfo = curl_getInfo( $ch );
			curl_close($ch);
			return $output;
	}
//==========================================	
?>