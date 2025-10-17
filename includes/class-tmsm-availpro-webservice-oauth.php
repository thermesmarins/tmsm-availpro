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
    $oauth_identifiers = $this->oauth_identifiers;
    $authHeader = self::getOauth1Header($url, $method, $oauth_identifiers);
    return $authHeader;
}
}