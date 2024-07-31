<?php 
/**
 * Oauth signature for tmsm availpro json
 */
class Tmsm_Availpro_Webservice_Oauth {

private $oauth_identifiers = [];

public function __construct()
{
    $options = get_option('tmsm-availpro-options', false);

	$this->set_oauth_identifiers();
}

/**
 * Set oauth identifiers
 */
private function set_oauth_identifiers(){
    $options = get_option('tmsm-availpro-options', false);
    $this->oauth_identifiers = [
        'consumerKey'    => $options['consumerkey'],
        'consumerSecret' => $options['consumersecret'],
        'accessToken'    => $options['accesstoken'],
        'accessSecret'   => $options['tokensecret'],
    ];
}


 public static function buildBaseString($baseURI, $method, $params) {
    $r = array();
    
    ksort($params);
    foreach ($params as $key => $value) {
       
        if (is_array($value)) {
            $value = implode(',', $value);
        }
        
        $r[] = rawurlencode($key) . '=' . rawurlencode($value);
    }
    
    return $method . "&" . rawurlencode($baseURI) . '&' . rawurlencode(implode('&', $r));
}
 
public static function buildAuthorizationHeader($oauth) {
    $r = 'Authorization: OAuth ';
    $values = array();
    foreach ($oauth as $key => $value) {
        $values[] = "$key=\"" . rawurlencode($value) . "\"";
    }
    $r .= implode(',', $values);
    return $r;
}
 
// public function getOauth1Header($url, $method, $consumerKey, $consumerSecret, $accessToken, $accessSecret, $jsonBody) {
public static function getOauth1Header($url, $method, $oauth_identifiers) {
    $oauth = array(
        'oauth_consumer_key' => $oauth_identifiers['consumerKey'],
        'oauth_token' => $oauth_identifiers['accessToken'],
        'oauth_signature_method' => 'HMAC-SHA1',
        'oauth_timestamp' => time(),
        'oauth_nonce' => uniqid(''),
        'oauth_version' => '1.0'
    );
 
    $base_info = self::buildBaseString($url, $method, $oauth);
    $composite_key = rawurlencode($oauth_identifiers['consumerSecret']) . '&' . rawurlencode($oauth_identifiers['accessSecret']);
    $oauth_signature = base64_encode(hash_hmac('sha1', $base_info, $composite_key, true));
    $oauth['oauth_signature'] = $oauth_signature;
 
    return self::buildAuthorizationHeader($oauth);
}
public function getSignature($url, $method) {
    // $method = "POST";

    $oauth_identifiers = $this->oauth_identifiers;
    $authHeader = self::getOauth1Header($url, $method, $oauth_identifiers);
    return $authHeader;
}
// Préparation des paramètres
// $url = "https://ws.availpro.com/planning/2018A/Planning/GetDailyPlanning";
// $method = "POST";
// $consumerKey = "ThermesMarinsWS";
// $consumerSecret = "1ZVqDEbCWGgKpKj9QmuwLw";
// $accessToken = "1B6L5D7BH7E8GG6VFLUJRS54V";
// $accessSecret = "V8gny863J6J4oXPP7pzLSA";
// $jsonBody = '{"beginDate": "2024-12-12","endDate": "2024-12-20","filter": {"ratePlans": [{"hotels": {"exceptions": ["6286"],"default": "Excluded"},"groupId": "5692","referenceRateCode": ""}],"status": ["Available","NotAvailable"]},"layout": {"levels": [{"name": "ArticleRate","properties": ["Availability","Status","Price","TotalPrice","CrossedOutPrice","MaximumPrice","MinimumPrice","DiscountPrice","DiscountNightCount","PublicOffer","MixedRates","IncludedArticles","Occupancies","OriginalPrice","Taxes","MaximumStayOnDeparture","MaximumStayThrough","MaximumStayOnArrival","MinimumStayOnDeparture","MinimumStayThrough","MinimumStayOnArrival","ClosedOnDeparture","ClosedOnArrival"]}]},"impersonation": {"hotelId": "6286","groupId": "5692"}}';
// récupère le fichier js pour que ce soit plus facile pour les variables dont j'ai besoin
// $jsonBody = file_get_contents('../public/assets/app.js', false, null,11 );
// echo print_r($jsonBody, true);
// Transforme le fichier js en objet php
// $body = json_decode($jsonBody);
// $authHeader = getOauth1Header($url, $method, $consumerKey, $consumerSecret, $accessToken, $accessSecret, $jsonBody);
// $authHeader = getOauth1Header($url, $method, $this->oauth_identifiers);
 
// Préparation de la requete
// $options = array(
//     'http' => array(
//         'header'  => $authHeader . "\r\n" .
//                      "Content-Type: application/json\r\n",
//         'method'  => $method,
//         'content' => $jsonBody,
//     ),
// );
// $context  = stream_context_create($options);
 
// $result = file_get_contents($url, false, $context);
 
// if ($result === FALSE) {
//     echo "Error: " . error_get_last()['message'] . "\n";
// } else {
//     echo '<pre>'; 
//     echo print_r(json_decode($result), true);
//     echo '</pre>';
// }
// die;
// $this->render('d-edge');

}