<?php

//==========================================
class webserviceKaveNegar
{

    public $API_SEND = '%s/v1/%s/%s/%s.json';
    public $API_INFO = '%s/v1/%s/account/info.json';
    public $API_UNREAD = '%s/v1/%s/sms/unreads.json';
    private $client_object;
    public $API_KEY;
    public $IsRead;
    public $From;
    public $To;
    public $Message;
    public $RecID;
    public $type = 1;

    public function __construct($URL, $AK)
    {
        $this->API_KEY = $AK;
        $this->API_SEND = sprintf($this->API_SEND, $URL, $AK, "verify", "lookup");
        $this->API_INFO = sprintf($this->API_INFO, $URL, $AK);
        $this->API_UNREAD = sprintf($this->API_UNREAD, $URL, $AK);
    }
    public static function Pre($data, $die = 1)
    {
        echo "<pre>";
        print_r($data);
        echo "</pre>";
        if ($die) {
            die();
        }
    }

    public static function doPost($url, $method, $data)
    {
        $headers = array(
            'Accept: application/json',
            'Content-Type: application/x-www-form-urlencoded',
        );
        //url-ify the data for the POST
        $fields_string = [];
        foreach ($data as $key => $value) {
            $fields_string[]= $key . '=' . $value . '&';
        }
        $fields_string = implode('&',$fields_string);
        $handle = curl_init();
        curl_setopt($handle, CURLOPT_URL, $url);
        curl_setopt($handle, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($handle, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($handle, CURLOPT_SSL_VERIFYPEER, false);


        switch ($method) {
            case 'POST':
                curl_setopt($handle, CURLOPT_POST, true);
                curl_setopt($handle, CURLOPT_POSTFIELDS, $fields_string);
                break;
            case 'PUT':
                curl_setopt($handle, CURLOPT_CUSTOMREQUEST, 'PUT');
                curl_setopt($handle, CURLOPT_POSTFIELDS, $data);
                break;
            case 'DELETE':
                curl_setopt($handle, CURLOPT_CUSTOMREQUEST, 'DELETE');
                break;
        }
        $response = curl_exec($handle);
        //$code = curl_getinfo($handle, CURLINFO_HTTP_CODE);
        return $response;

    }

    public function GetInboxCount()
    {
        $result = self::doPost($this->API_UNREAD, "POST", array(
            "sender" => $this->From,
            "isread" => $this->IsRead
        ));
        $result = json_decode($result);
        if (is_object($result)) {
            if (isset($result->entries)) {
                if (is_array($result->entries)) {
                    return count($result->entries);
                }
            }
        }
        return "عدم دسترسی به سرور پیامک";
    }

    public function GetCredit()
    {
        $result = self::doPost($this->API_INFO, "POST", array());
        $result = json_decode($result);
        if (is_object($result)) {
            if (isset($result->entries)) {
                if (isset($result->entries->remaincredit)) {
                    return $result->entries->remaincredit;
                }
            } else {
                return $result->return->message;
            }
        }
        return "عدم دسترسی به سرور پیامک";
    }

    public function SendSMS()
    {
        $Receptions = $this->To;
        if (is_array($Receptions)) {
            $Receptions = implode(",", $Receptions);
        }
        $this->Message = strip_tags($this->Message);
        $this->smsTemplate = $this->smsTemplate?:'madras';

        $sent_code_auth = (int)filter_var($this->Message, FILTER_SANITIZE_NUMBER_INT);

        $option = array(
            "token" => $sent_code_auth,
            "receptor" => $Receptions,
            "template" => $this->smsTemplate
        );
        $result = self::doPost($this->API_SEND, "POST", $option);

        $result = json_decode($result);
        if (is_object($result)) {
            if (isset($result->entries)) {
                if (isset($result->entries[0])) {
                    return array($result->entries[0]->status, $result->entries[0]->messageid);
                }
            }
            if (isset($result->return)) {
                if (isset($result->return->message)) {
                    return $result->return->message;
                }
            }
        }
        return "عدم دسترسی به سرور پیامک";
    }
}

//==========================================
class KaveNegar
{
    function LoadSmsPanel($mode, $sms_username, $sms_password, $sms_number, $sms_center, $sms_NO = NULL, $message = NULL,$smsTemplate= NULL)
    {
        $Udh = "";
        $options = array();
        if (!strlen($sms_username) || !strlen($sms_center) || !strlen($sms_number)) {
            return "<strong>تنظیمات پیامک صحیح نیست</strong>";
        }
        //error_reporting(0);
        try {
            $soapClientObj = new webserviceKaveNegar($sms_center, $sms_username);
            $soapClientObj->API_KEY = $sms_username;
            $res = "";
            switch ($mode) {
                case 'check':
                    $res = $soapClientObj->GetCredit();
                    if (empty($res)) {
                        return "<strong>عدم دسترسی به سرور پیامک 3</strong>";
                    }
                    break;
                case 'inbox':
                    $soapClientObj->IsRead = 0;
                    $res = $soapClientObj->GetInboxCount();
                    break;
                case 'send':
                    $soapClientObj->From = $sms_number;
                    $soapClientObj->To = implode(',', $sms_NO);
                    $soapClientObj->Message = $message;
                    $soapClientObj->type = 1;
                    $soapClientObj->smsTemplate = $smsTemplate;
                    $res = $soapClientObj->SendSMS();
                    break;
            }
            return $res;
        } catch (Exception $e) {
            return "<strong>عدم دسترسی به سرور پیامک 3</strong>";
        }
    }
}

//==========================================
?>