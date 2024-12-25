<?php
//==========================================	
	class webserviceUSMS{
    private $Username = "";
    private $Password = "";
	private $client   = null;

	public function __construct($wsdl,$sms_username,$sms_password){
		if(!class_exists("nusoap_client"))
		require_once('nusoap/nusoap.php');
		
		$this->Username = $sms_username;
		$this->Password = $sms_password;
		$this->client = new nusoap_client($wsdl);
        $this->client->soap_defencoding = 'UTF-8';
        $this->client->decode_utf8 = true;

	}
	
    public function SendSMS($Message, $SenderNumber, $Receptors, $type = 'normal')
	{
		if(is_array($Receptors))
		{
			$i = sizeOf($Receptors);
			
			while($i--)
			{
				$Receptors[$i] =  self::CorrectNumber($Receptors[$i]);
			}
		}
		else
		{
			$Receptors = array(self::CorrectNumber($Receptors));
		}

		$params = array(
			'Username' => $this->Username,
			'Password' => $this->Password,
			'RecipientNumbers' => $Receptors,
			'SenderNumber'=> $SenderNumber,
			'Message' => $Message,
			'Type' => $type
		);

        $response = $this->call("SendSMS", $params);

		return $response;
    }
	
    public function GetStatus($BatchID, $UniqueIDs)
	{
		$params = array(
			'Username' => $this->Username,
			'Password' => $this->Password,
			'BatchID' => $BatchID,
			'UniqueIDs'=> $UniqueIDs
		);

        $response = $this->call("GetStatus", $params);

		return $response;
    }
	
    public function GetMaxReceptors()
	{
		$response = $this->call("GetMaxReceptors", array());
			
		return $response;
    }
	
    public function GetCredit()
	{
		$response = $this->call("GetCredit", array('Username' => $this->Username, 'Password' => $this->Password));
			
		return $response;
    }

    private function call($method, $params)
	{
        $result = $this->client->call($method, $params);

			if($this->client->fault || ((bool)$this->client->getError()))
			{
				return array('error' => true, 'fault' => true, 'message' => $this->client->getError());
			}

        return $result;
    }
	public function GetInboxCount(){
		$args = array('parameters' => array(
			'UserName' => $this->UserName,
			'Password' => $this->Password,
			'IsRead' => false
		));
		$result = $this->client->call('GetInboxCount', $args);
		return $result;
	}
	public static function CorrectNumber(&$uNumber)
	{
		$uNumber = Trim($uNumber);
		$ret = &$uNumber;
		
		if (substr($uNumber,0, 3) == '%2B')
		{ 
			$ret = substr($uNumber, 3);
			$uNumber = $ret;
		}
		
		if (substr($uNumber,0, 3) == '%2b')
		{ 
			$ret = substr($uNumber, 3);
			$uNumber = $ret;
		}
		
		if (substr($uNumber,0, 4) == '0098')
		{ 
			$ret = substr($uNumber, 4);
			$uNumber = $ret;
		}
		
		if (substr($uNumber,0, 3) == '098')
		{ 
			$ret = substr($uNumber, 3);
			$uNumber = $ret;
		}
		
		
		if (substr($uNumber,0, 3) == '+98')
		{ 
			$ret = substr($uNumber, 3);
			$uNumber = $ret;
		}
		
		if (substr($uNumber,0, 2) == '98')
		{ 
			$ret = substr($uNumber, 2);
			$uNumber = $ret;
		}
		
		if(substr($uNumber,0, 1) == '0')
		{ 
			$ret = substr($uNumber, 1);
			$uNumber = $ret;
		}  
		   
		return '+98' . $ret;
	}
	}
//==========================================
class USMS{
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
			$soapClientObj = new webserviceUSMS($sms_center,$sms_username,$sms_password);
		switch($mode){
		case 'check':
			$res = $soapClientObj->GetCredit();
			if(empty($res)){
			return "<strong>عدم دسترسی به سرور پیامک U-SMS</strong>";
			}
			return (int)$res;
		break;
		case 'inbox':
			$soapClientObj->IsRead = false;
			$res = $soapClientObj->GetInboxCount();
			return (int)$res;
		break;
		case 'send':
			$res = $soapClientObj->SendSMS($message, $sms_number, (array)$sms_NO); 
			return array(1,$res);
		break;
		}
		}
		catch (SoapFault $sf)
		{
			return "<strong>عدم دسترسی به سرور پیامک U-SMS</strong>";
		}
	}
	}
//==========================================	
?>