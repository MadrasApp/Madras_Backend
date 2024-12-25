<?php
//==========================================	
	class webserviceSMSHome{
	
	private $client;
	public $uUsername;
	public $uPassword;
	public $IsRead;
	public $uNumber;
	public $uCellphones;
	public $uMessage;
	public $RecID;
	public $uFarsi = "True";
	public function __construct($wsdl){
		if(!class_exists("nusoap_client"))
		require_once('nusoap/nusoap.php');
		$this->client = new nusoap_client($wsdl,true);
		$this->client->http_encoding='utf-8';
		$this->client->soap_defencoding = 'utf-8';
		$this->client->decode_utf8 = false;
		
	}
	public function GetInboxCount(){
		return "عدم پشتیبانی سرور پیامک";
		$args = array(
			'uUsername' => $this->uUsername,
			'uPassword' => $this->uPassword,
			'IsRead' => $this->IsRead
		);
		$result = $this->client->GetInboxCount($args);
		return $result;
	}
	public function GetCredit(){
		$args = array(
			'uUsername' => $this->uUsername,
			'uPassword' => $this->uPassword
		);
		$Xresult = $this->client->call("getInfo",$args);
		if(is_array($Xresult) && isset($Xresult["getInfoResult"])){
			$X = explode(";",$Xresult["getInfoResult"]);
			$result = $X[1]." ".$X[0];
			return $result;
		}
		else
		{
			return "عدم دسترسی به سرور پیامک";
		}
		return $result;
	}
	public function GetDelivery(){
		$RecIDs = $this->RecID;
		if(is_array($RecIDs)){
			$RecIDs = implode(";", $RecIDs);
		}
		$args = array(
			'RecID' => $RecIDs
		);
		$result = $this->client->GetDelivery($args);
		return $result;
	}
	public function SendSMS(){
		$Receptions = $this->uCellphones;
		if(is_array($Receptions)){
			$Receptions = implode(";", $Receptions);
		}
		$args = array(
			'uUsername' => $this->uUsername,
			'uPassword' => $this->uPassword,
			'uNumber' => $this->uNumber,
			'uCellphones' => $Receptions,
			'uMessage' => $this->uMessage,
			'uFarsi' => $this->uFarsi
		);
		$Xresult = $this->client->call("doSendSMS",$args);
		if(is_array($Xresult) && isset($Xresult["doSendSMSResult"])){
			$X = explode(".",$Xresult["doSendSMSResult"]);
			$result = $X[1];
		}
		else
		{
			return "عدم دسترسی به سرور پیامک";
		}
		return $result;
	}
	}
//==========================================	
	class SMSHome{
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
			$soapClientObj = new webserviceSMSHome($sms_center);
			$soapClientObj->uUsername = $sms_username;
			$soapClientObj->uPassword = $sms_password;
		switch($mode){
		case 'check':
			$res = $soapClientObj->GetCredit();
			if(empty($res)){
			return "<strong>عدم دسترسی به سرور پیامک 2</strong>";
			}
			return $res;
		break;
		case 'inbox':
			$soapClientObj->IsRead = false;
			$res = $soapClientObj->GetInboxCount();
			return $res; 
		break;
		case 'send':
			$soapClientObj->uNumber = $sms_number;
			$soapClientObj->uCellphones = $sms_NO;
			$soapClientObj->uMessage = $message;
			return $soapClientObj->SendSMS(); 
		return 1;
		break;
		}
		}
		catch (SoapFault $sf)
		{
			return "<strong>عدم دسترسی به سرور پیامک 2</strong>";
		}
	}
	}
//==========================================	
?>