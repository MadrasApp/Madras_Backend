<?php
//==========================================	
	class webserviceBasePanel_Soap{
	
	private $client_object;
	public $username;
	public $password;
	public $isRead;
	public $from;
	public $to;
	public $Message;
	public $recId;
	public $isflash = FALSE;
	public function __construct($wsdl){
		$this->client_object = new SoapClient($wsdl);
		$this->client_object->http_encoding='utf-8';
		$this->client_object->soap_defencoding = 'utf-8';
		$this->client_object->decode_utf8 = false;
		
	}
	public function GetInboxCount(){
		$args = array(
			'username' => $this->username,
			'password' => $this->password,
			'isRead' => $this->isRead
		);
		$result = 0;
		try {
		$result = $this->client_object->GetInboxCount($args)->GetInboxCountResult;
		return $result;
		} catch (SoapFault $fault) {
			return "$fault";
		}
	}
	public function GetCredit(){
		$args = array(
			'username' => $this->username,
			'password' => $this->password
		);
		$result = 0;
		try {
			$O = $this->client_object->GetCredit($args);
			$result = $O->GetCreditResult;
			return $result;
		} catch (SoapFault $fault) {
			return "$fault";
		}
	}
	public function GetDelivery(){
		$args = array(
			'recId' => $this->recId
		);
		try {
		$result = $this->client_object->GetDelivery($args);
		$payam=array(
			0 => "منتظر دریافت مخابرات",
			1 => "ارسال شده به مخابرات",
			2 => "رسیده به گوشی",
			8 => "منقضی شده",
			9 => "بلاک شده توسط مخابرات",
			-1 => "کد ارسالی اشتباه است"
		);
		$resultfinal = $result->GetDeliveryResult;
		return array($resultfinal,$payam[$resultfinal]);
		} catch (SoapFault $fault) {
			return "$fault";
		}
	}
	public function SendSMS(){
		$Receptions = (array)$this->to;
		$args = array(
			'username' => $this->username,
			'password' => $this->password,
			'from' => $this->from,
			'to' => $Receptions,
			'text' => $this->Message,
			'isflash' => $this->isflash,
			'udh' => "",
			'recId' => array(0),
			'status' => array(0)
		);
		try {
		$result = $this->client_object->SendSms($args);
		return array($result->SendSmsResult,$result->recId->long);
		} catch (SoapFault $fault) {
			return "$fault";
		}
	}
	}
//==========================================	
	class BasePanel_Soap{
	function LoadSmsPanel($mode,$sms_username,$sms_password,$sms_number,$sms_center,$sms_NO=NULL,$message=NULL){
		$Udh="";
		$options = array();
		if(!strlen($sms_username) || !strlen($sms_password) || !strlen($sms_number))
		{
			return " <strong>تنظیمات پیامک صحیح نیست</strong> ";
		}
		//error_reporting(0);
		try
		{
			$soapClientObj = new webserviceBasePanel_Soap($sms_center);
			$soapClientObj->username = $sms_username;
			$soapClientObj->password = $sms_password;
		switch($mode){
		case 'check':
			$res = $soapClientObj->GetCredit();
			if(!strlen($res)){
			return "<strong>عدم دسترسی به سرور آنی پیام</strong> ";
			}
			$_SESSION["DataControl"]["BasePanel_Soapcheck"] = 1;
			return ((int)$res);
		break;
		case 'inbox':
			$soapClientObj->isRead = 0;
			$res = $soapClientObj->GetInboxCount();
			return ((int)$res); 
		case 'send':
			$soapClientObj->from = $sms_number;
			$soapClientObj->to = $sms_NO;
			$soapClientObj->Message = $message;
			$soapClientObj->isflash = false;
			return $soapClientObj->SendSMS(); 
		break;
		case 'getdelivery':
			$soapClientObj->recId = $sms_NO;
			return $soapClientObj->GetDelivery();
		break;
		}
		}
		catch (SoapFault $sf)
		{
			return "<strong>عدم دسترسی به سرور آنی پیام</strong> ";
		}
	}
	}
//==========================================	
?>