<?php

/**
 * Availpro web service
 *
 * @since      1.0.0
 *
 * @package    Tmsm_Availpro
 * @subpackage Tmsm_Availpro/includes
 */

class Tmsm_Availpro_Webservice {

	/**
	 * Webservice Namespace
	 *
	 * @access 	const
	 * @since 	1.0.0
	 * @var 	string
	 */
	// const WSNAMESPACE = 'http://ws.availpro.com/schemas/planning/2012A';

	/**
	 * Webservice URL
	 *
	 * @access 	private
	 * @since 	1.0.0
	 * @var 	string
	 */
	// const URL = 'https://ws.availpro.com/Planning/2012A/PlanningService.asmx?WSDL';
	//TODO json
	const URL = 'https://ws.availpro.com/planning/2018A/Planning/';

	/**
	 * Webservice Oauth identifiers
	 *
	 * @access 	private
	 * @since 	1.0.0
	 * @var 	array
	 */
	// private $oauth_identifiers = [];

	// /**
	//  * Constructor
	//  */
	// public function __construct() {

	// 	$options = get_option('tmsm-availpro-options', false);

	// 	$this->set_oauth_identifiers();

	// }

	// /**
	//  * Set oauth identifiers
	//  */
	// private function set_oauth_identifiers(){
	// 	$options = get_option('tmsm-availpro-options', false);
	// 	$this->oauth_identifiers = [
	// 		'consumerKey'    => $options['consumerkey'],
	// 		'consumerSecret' => $options['consumersecret'],
	// 		'accessToken'    => $options['accesstoken'],
	// 		'accessSecret'   => $options['tokensecret'],
	// 	];
	// }
//TODO json
	/**
	 * Get Layout
	 *
	 * @return string
	 */
	private function get_layout(){
		$layout = array(
			'levels' => array(
				[
					'name' => 'ArticleRate',
					'properties'=> [
						'Availability',
						'Status',
						'MinimumStayThrough',
						'TotalPrice',
					]
				]
			)
		);

		return $layout;

	}
	// private function get_layout(){
	// 	return '<level name="ArticleRate"><property name="Status" /><property name="Price" /><property name="Availability" /><property name="MinimumStayThrough" /></level>';

	// }
	/**
	 * Get Filters
	 *
	 * @param null $rateids
	 *
	 * @return string
	 */
	private function get_filters($rateids = null){
		$options = get_option('tmsm-availpro-options', false);

		$option_rateids = $rateids;
		$option_ratecode = null;
		$option_roomids = $options['roomids'];
		$option_groupid = $options['groupid'];
		$option_hotelid = $options['hotelid'];

		// rates
		$option_rateids_array = [];
		if(!empty($option_rateids) ){
			$option_rateids_array = explode(',', $option_rateids);
			foreach($option_rateids_array as &$item){
				$item = trim($item);
			}
		}
		//TODO json
		$filters_rateids = [];
		// $filters_rateids = '';
		if(!empty($option_rateids_array) && is_array($option_rateids_array) && count($option_rateids_array) > 0){
			// $filters_rateids = '<rates default="Excluded">';
			// foreach($option_rateids_array as $item){
			// 	$filters_rateids .= '<exception id="'.$item.'"/>';
			// }
			// $filters_rateids .= '</rates>';
			//TODO json
			$filters_rateids['rates'] =[ 				
					'default'=>'Excluded'
			];
			foreach($option_rateids_array as $item) {
				$filters_rateids['rates']['exceptions'] = array($item);
			}

		}
		
		//rooms
		$option_roomids_array = [];
		if(!empty($option_roomids) ){
			$option_roomids_array = explode(',', $option_roomids);
			foreach($option_roomids_array as &$item){
				$item = trim($item);
			}
		}
		$filters_roomids = '';
		if(!empty($option_roomids_array) && is_array($option_roomids_array) && count($option_roomids_array) > 0){
			// $filters_roomids = '<rooms default="Excluded">';
			// foreach($option_roomids_array as $item){
			// 	$filters_roomids .= '<exception id="'.$item.'"/>';
			// }
			// $filters_roomids .= '</rooms>';
			//TODO json
			$filters_roomids = array( 
				'rooms' => array( 
					'defaults' => 'Excluded',
				)
				);
			foreach($option_roomids_array as $item) {
				$filters_rateids['rooms']['exceptions'] = $item;
			}
		}

		// ratecode
		$filters_ratecode='';
		if(!empty($option_ratecode)){
			// $filters_ratecode = 'referenceRateCode="'.$option_ratecode.'"';
			$filters_ratecode = $option_ratecode;
		}

		// @TODO include $filters_roomids but it doesn't give any result with it
		// @TODO not hardcode OTABAR
		//referenceRateCode="BARPROM"
		// $filters = '
		// 			<ratePlans><ratePlan groupId="'.$option_groupid.'" '.$filters_ratecode.'><hotels default="Excluded"><exception id="'.$option_hotelid.'" /></hotels></ratePlan></ratePlans>'.
	    //             $filters_rateids.
	    //             //$filters_roomids.
	    //             '<currencies default="Excluded"><exception currency="EUR"/></currencies>'.
	    //             '<status><include status="Available" /><include status="NotAvailable" /></status>'.
		// '';
		//TODO json
		$filters = 
			array( 
			'ratePlans'=> array([ 
				 'groupId' => $option_groupid,
				 'hotels' => array( 
					'exceptions'=> array($option_hotelid) ,
					'default'=> 'Excluded'
				 ),
				 'referenceRateCode' => $filters_ratecode
				]),
				 'rates' => $filters_rateids['rates'],
				 'currencies' => array( 
					'default'=>'Excluded',
					'exceptions'=> array( 
						'EUR'
					)
			),
			'status'=>	array( 
				'Available',
				'NotAvailable',
				"OnRequest"
			)			  
		);

		// if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
		// 	error_log('filters:');
		// 	error_log(print_r($filters, true));
		// }

		return $filters;

	}

	/**
	 * Get Data from Availpro API call
	 *
	 * @param string $month (YYYY-MM)
	 *
	 * @return string
	 */
	public function get_data($month){

		$timezone = new DateTimeZone( "Europe/Paris" );

		$month_firstday = DateTime::createFromFormat('Y-m-d', $month.'-01', $timezone);
		$month_firstday->modify('first day of this month');
		$month_lastday = clone $month_firstday;
		$month_lastday->modify('last day of this month');
		//$month_lastday->modify('first day of this month')->modify('+6 days');

		if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
			error_log('get_data');
			error_log('firstday:'.$month_firstday->format('Y-m-d'));
			error_log('lastday:'.$month_lastday->format('Y-m-d'));
		}

		// if(!class_exists('SoapOAuthWrapper')){
		// 	return 'SoapOAuthWrapper doesn\'t exist';
		// }

		$options = get_option('tmsm-availpro-options', false);
		$rateids = $options['accommodationrateids'];

		$soap_parameters = array(
			'groupId'   => $options['groupid'],
			'hotelId'   => $options['hotelid'],
			'beginDate' => "2024-08-01",
			'endDate'   => "2024-08-31",
			// 'beginDate' => $month_firstday->format('Y-m-d'),
			// 'endDate'   => $month_lastday->format('Y-m-d'),
			'layout'    => $this->get_layout(),
			// 'filter'    => json_encode($this->get_filters($rateids)),
			'filter'    => $this->get_filters($rateids),
		);
		try {
			$method='POST';
			$signature = new Tmsm_Availpro_Webservice_Oauth();
			$oauth_signature = $signature->getSignature(self::URL.'GetDailyPlanning', $method);
			$options = array(
					'headers'  => $oauth_signature . "\r\n" .
								 "Content-Type: application/json\r\n",
					'body' => json_encode($soap_parameters),
			);
			$response = wp_remote_post( self::URL.'GetDailyPlanning', $options );
				//TODO traiter les différentes erreurs de retour 200 300 400
				return json_decode($response['body'], true);
			if ($response === FALSE) {
				echo "Error: " . error_get_last()['message'] . "\n";
			} else {
				error_log('TMSM_SIGNATURE');
				error_log( print_r(json_decode($oauth_signature), true));
				
			}
			die;
		} catch ( OAuthException2 $e ) {
			return $e;
		}
	}

	/**
	 * Convert XML results in array
	 *
	 * @param string $xml
	 *
	 * @return array
	 */
	static public function convert_to_array($xml){

		$domObject = new DOMDocument();
		$domObject->loadXML($xml);

		$domXPATH = new DOMXPath($domObject);
		$results = $domXPATH->query("//soap:Body/*");

		$array = [];
		foreach($results as $result)
		{
			$array = json_decode(json_encode(simplexml_load_string($result->ownerDocument->saveXML($result))), true);
		}
		return $array;
	}



	/**
	 * Get Stay Planning from Availpro API call
	 *
	 * @param string $arrivaldate (YYYY-MM-DD)
	 * @param int $nights
	 * @param string $rateids
	 *
	 * @return string
	 */
	public function get_stayplanning($arrivaldate, $nights, $rateids){

		$timezone = new DateTimeZone( "Europe/Paris" );
		$arrivaldate = DateTime::createFromFormat('Y-m-d', $arrivaldate, $timezone);
		$options = get_option('tmsm-availpro-options', false);
		$soap_parameters = array(
			'arrivalDate' => $arrivaldate->format('Y-m-d'),
			'nightCount'   => $nights,
			'layout' => array( 
				'levels' => array( [
					'name'=> "ArticleRate",
					'properties'=> array (
						"Availability",
						"Status",
						"TotalPrice",
						"MinimumStayThrough"
					)]
				)
			),
			'filter'    => $this->get_filters($rateids),
		);

		if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
			// error_log('soap_parameters:');
			// error_log(var_export($soap_parameters,true));
		}
		try {
			$method='POST';
			$signature = new Tmsm_Availpro_Webservice_Oauth();
			$oauth_signature = $signature->getSignature(self::URL.'GetStayPlanning', $method);
			$options = array(
					'headers'  => $oauth_signature . "\r\n" .
								 "Content-Type: application/json\r\n",
					'body' => json_encode($soap_parameters),
			);
			$response = wp_remote_post( self::URL.'GetStayPlanning', $options );
			//TODO gérer une réponse erreur
			if($response['response']['code'] === 200 ){
				return json_decode($response['body'], true);
			}
		} catch ( OAuthException2 $e ) {
			return $e;
		}

	}

}