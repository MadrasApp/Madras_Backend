<?php
//==========================================	
	class webserviceBasePanel{
	
	private $client_object;
	public $UserName;
	public $Password;
	public $IsRead;
	public $From;
	public $To;
	public $Message;
	public $RecID;
	public $IsFlash = FALSE;
	public function __construct($wsdl){
		if(!class_exists("nusoap_client"))
		require_once('nusoap/nusoap.php');
		$this->client_object = new nusoap_client($wsdl,true);
		$this->client_object->http_encoding='utf-8';
		$this->client_object->soap_defencoding = 'utf-8';
		$this->client_object->decode_utf8 = false;
		
	}
	public function GetInboxCount(){
		$args = array('parameters' => array(
			'UserName' => $this->UserName,
			'Password' => $this->Password,
			'IsRead' => $this->IsRead
		));
		$result = $this->client_object->call('GetInboxCount', $args);
		return $result;
	}
	public function GetCredit(){
		$args = array('parameters' => array(
			'UserName' => $this->UserName,
			'Password' => $this->Password
		));
		$result = $this->client_object->call('GetCredit', $args);
		return $result;
	}
	public function GetDelivery(){
		$RecIDs = $this->RecID;
		if(is_array($RecIDs)){
			$RecIDs = implode(";", $RecIDs);
		}
		$args = array('parameters' => array(
			'RecID' => $RecIDs
		));
		$result = $this->client_object->call('GetDelivery', $args);
		return $result;
	}
	public function SendSMS(){
		$Receptions = $this->To;
		if(is_array($Receptions)){
			$Receptions = implode(";", $Receptions);
		}
		$args = array('parameters' => array(
			'UserName' => $this->UserName,
			'Password' => $this->Password,
			'From' => $this->From,
			'To' => $Receptions,
			'Message' => $this->Message,
			'IsFlash' => $this->IsFlash
		));
		$result = $this->client_object->call('SendSms', $args);
		return $result;
	}
	}
//==========================================	
	class BasePanel_NuSoap{
	function LoadSmsPanel($mode,$sms_username,$sms_password,$sms_number,$sms_center,$sms_NO=NULL,$message=NULL){
		$Udh="";
		$options = array();
		if(!strlen($sms_username) || !strlen($sms_password) || !strlen($sms_number))
		{
			$_SESSION["DataControl"]["BasePanelcheck"] = " <strong>تنظیمات پیامک صحیح نیست</strong> ";
			return 0;
		}
		//error_reporting(0);
		try
		{
			$soapClientObj = new webserviceBasePanel($sms_center);
			$soapClientObj->UserName = $sms_username;
			$soapClientObj->Password = $sms_password;
		switch($mode){
		case 'check':
			$res = $soapClientObj->GetCredit();
			if(empty($res)){
			$_SESSION["DataControl"]["BasePanelcheck"] = " <strong>عدم دسترسی به سرور پیامک 2</strong> ";
			return 0;
			}
			$_SESSION["DataControl"]["BasePanelcheck"] = 1;
			return ((int)$res);
		break;
		case 'inbox':
			$soapClientObj->IsRead = false;
			$res = $soapClientObj->GetInboxCount();
			return ((int)$res); 
		break;
		case 'send':
			$soapClientObj->From = $sms_number;
			$soapClientObj->To = $sms_NO;
			$soapClientObj->Message = $message;
			$soapClientObj->IsFlash = false;
			return $soapClientObj->SendSMS(); 
		break;
		}
		}
		catch (SoapFault $sf)
		{
			$_SESSION["DataControl"]["BasePanelcheck"] = " <strong>عدم دسترسی به سرور پیامک 2</strong> ";
			return 0;
		}
	}
	}
//==========================================	
?>