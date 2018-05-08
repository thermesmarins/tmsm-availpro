<?php

// Get the latest version of oauth-php at http://code.google.com/p/oauth-php/

include_once "library/OAuthStore.php";
include_once "library/OAuthRequester.php";

class OAuthBasicRequest extends OAuthRequester 
{
  private $consumerKey;
  private $consumerSecret;
  private $accessToken;
  private $accessSecret;
  private $signatureMethod;
  
  function __construct ($consumerKey, $consumerSecret, $accessToken, $accessSecret, $signatureMethod='HMAC-SHA1', $request, $method = null, $params = null, $body = null, $files = null)
  {
    parent::__construct($request, $method, $params, $body, $files);
    
    $this->consumerKey = $consumerKey;
    $this->consumerSecret = $consumerSecret;
    $this->accessToken = $accessToken;
    $this->accessSecret = $accessSecret;
    $this->signatureMethod = $signatureMethod;
  }
  
  function sign ( $usr_id = 0, $secrets = null, $name = '', $token_type = null)
  {
    $url = $this->getRequestUrl();
    
    $this->setParam('oauth_signature_method', $this->signatureMethod);
    $this->setParam('oauth_signature', '');
    $this->setParam('oauth_nonce', uniqid(''));
    $this->setParam('oauth_timestamp', time());
    $this->setParam('oauth_token', $this->accessToken);
    $this->setParam('oauth_consumer_key',	$this->consumerKey);
    $this->setParam('oauth_version', '1.0');
    
    $body = $this->getBody();
    
    $signature = $this->calculateSignature($this->consumerSecret, $this->accessSecret, $token_type);
    $this->setParam('oauth_signature',	$signature, true);
    
    $this->usr_id = $usr_id;
  }  
}

class SoapOAuthWrapper
{
  static function Invoke($url, $namespace, $method, $soapParameters, $oauthOptions)
  {
    $body = '<?xml version="1.0" encoding="utf-8"?><soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/"><soap:Body>';
    $body = $body . '<' . $method . ' xmlns="' . $namespace . '">';

    foreach ($soapParameters as $name => $value)
      $body = $body . '<' . $name . '>' . $value . '</' . $name . '>';

    $body = $body . '</' . $method . '>';
    $body = $body . '</soap:Body></soap:Envelope>';
	  //echo '<pre>';
	  //echo ((($body)));
	  //echo '</pre>';
    $request = new OAuthBasicRequest(
    $oauthOptions['consumerKey'],
    $oauthOptions['consumerSecret'],
    $oauthOptions['accessToken'],
    $oauthOptions['accessSecret'],
    'HMAC-SHA1',
    $url,
    'POST',
    null,
    $body);



    $curloptions=array(CURLOPT_HTTPHEADER => array('Content-Type: text/xml; charset=utf-8', 'SOAPAction: "' . $namespace . '/' . $method . '"'));
    $result = $request->doRequest(1, $curloptions);

    return $result['body'];
  }
}

$options = array('consumer_key' => '', 'consumer_secret' => '');
OAuthStore::instance("2Leg", $options);

?>
