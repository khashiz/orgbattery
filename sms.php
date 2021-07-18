<?php
/*
ini_set("soap.wsdl_cache_enabled", "0");
try {
    $client = new SoapClient('http://api.payamak-panel.com/post/send.asmx?wsdl', array('encoding'=>'UTF-8'));
    $parameters['username'] = "steelarvin";
    $parameters['password'] = "1248";
    $parameters['from'] = "30007290000733";
    $parameters['to'] = array("09223815503");
    $parameters['text'] ="تست پیامک";
    $parameters['isflash'] = true;
    $parameters['udh'] = "";
    $parameters['recId'] = array(0);
    $parameters['status'] = 0x0;
    echo $client->GetCredit(array("username"=>"wsdemo","password"=>"wsdemo"))->GetCreditResult;
    echo $client->SendSms($parameters)->SendSmsResult;
}
catch (SoapFault $ex) {
    echo $ex->faultstring;
}
*/
?>

<?php
// Send SMS
$sms_client = new SoapClient('http://api.payamak-panel.com/post/Send.asmx?wsdl', array('encoding'=>'UTF-8'));
$parameters['username'] = "steelarvin";
$parameters['password'] = "1248";
$parameters['to'] = $_SESSION["m_cellphone"];
$parameters['text'] = array($_SESSION["m_fullname"], $_SESSION["m_fnumber"], $_SESSION["m_quantity"]);
$parameters['bodyId'] = 40282;
$res =  $sms_client->SendByBaseNumber($parameters);
return $res;
?>