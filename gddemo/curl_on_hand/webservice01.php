<?php
$data = sprintf('<?xml version="1.0" encoding="utf-8"?>
<soap:Envelope 
xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" 
xmlns:xsd="http://www.w3.org/2001/XMLSchema" 
xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
  <soap:Body>
    <getWeatherbyCityName xmlns="http://WebXml.com.cn/">
      <theCityName>%s</theCityName>
    </getWeatherbyCityName>
  </soap:Body>
</soap:Envelope>','北京');
$curlobj = curl_init();	
curl_setopt($curlobj, CURLOPT_URL, "http://www.webxml.com.cn/WebServices/WeatherWebService.asmx");  
curl_setopt($curlobj, CURLOPT_POST, 1);  
curl_setopt($curlobj, CURLOPT_HEADER, 0); 
curl_setopt($curlobj, CURLOPT_RETURNTRANSFER, 1);  
curl_setopt($curlobj, CURLOPT_POSTFIELDS, $data);  
curl_setopt($curlobj, CURLOPT_HTTPHEADER, array("Content-Type: application/soap+xml; charset=utf-8", 
	"Content-length: ".strlen($data),
	"SOAPAction:\"http://WebXml.com.cn/getWeatherbyCityName\"")); 
$rtn = curl_exec($curlobj);   
if(!curl_errno($curlobj)){
	$info = curl_getinfo($curlobj); 
	print_r($info);
	echo "RETURN: " . $rtn;  
} else {
  echo 'Curl error: ' . curl_error($curlobj);
}
curl_close($curlobj);
?>