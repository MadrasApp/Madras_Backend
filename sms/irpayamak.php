<?php
//==========================================	
	class webserviceIRPayamak{
	
	private $client_object;
	public $username;
	public $password;
	public $IsRead;
	public $From;
	public $To;
	public $Message;
	public $RecID;
	public function __construct($wsdl){
		if(!class_exists("nusoap_client"))
		require_once('nusoap/nusoap.php');
		$this->client_object = new nusoap_client($wsdl,true);
		$this->client_object->http_encoding='utf-8';
		$this->client_object->soap_defencoding = 'utf-8';
		$this->client_object->decode_utf8 = false;
		
	}
	public function GetInboxCount(){
		$authentication = array ('username' => $this->username, 'password' => $this->password);
		$result = $this->client_object->call ( 'WSgetNewMessages', array ('authentication' => $authentication) );
		return count($result);
	}
	public function GetCredit(){
		$authentication = array ('username' => $this->username, 'password' => $this->password);
		$result = $this->client_object->call ( 'WSGETBalance', array ('authentication' => $authentication, 'username' => $this->username ) );
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
		$authentication = array ('username' => $this->username, 'password' => $this->password);
		$data = array(
			'fromNum' => $this->From,
			'toNum' => $Receptions,
			'msg' => $this->Message
		);
		$result = $this->client_object->call ( 'WSdoSendSMS1', array ('authentication' => $authentication, 'message' => $data));
		return $result;
	}
	}
//==========================================	
class IRPayamak{
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
			$soapClientObj = new webserviceIRPayamak($sms_center);
			$soapClientObj->username = $sms_username;
			$soapClientObj->password = $sms_password;
		switch($mode){
		case 'check':
			$res = $soapClientObj->GetCredit();
			if(empty($res)){
			return "<strong>عدم دسترسی به سرور پیامک ایرانیان</strong>";
			}
			return (int)$res;
		break;
		case 'inbox':
			$soapClientObj->IsRead = false;
			$res = $soapClientObj->GetInboxCount();
			return (int)$res; 
		break;
		case 'send':
			$soapClientObj->From = $sms_number;
			$soapClientObj->To = $sms_NO;
			$soapClientObj->Message = $message;
			return $soapClientObj->SendSMS(); 
		break;
		}
		}
		catch (SoapFault $sf)
		{
			return "<strong>عدم دسترسی به سرور پیامک ایرانیان</strong>";
		}
	}
	}
//==========================================	
?>