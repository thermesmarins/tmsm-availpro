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
	 * Webservice URL
	 *
	 * @access 	private
	 * @since 	1.0.0
	 * @var 	string
	 */
	const URL = 'https://ws.availpro.com/planning/2018A/Planning/';

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
		if(!empty($option_rateids_array) && is_array($option_rateids_array) && count($option_rateids_array) > 0){
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
			$filters_ratecode = $option_ratecode;
		}
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
		$month_lastday->modify('last day of this month')->modify('+1 day');

		if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
			error_log('get_data');
			error_log('firstday:'.$month_firstday->format('Y-m-d'));
			error_log('lastday:'.$month_lastday->format('Y-m-d'));
		}

		$options = get_option('tmsm-availpro-options', false);
		$rateids = $options['accommodationrateids'];

		$soap_parameters = array(
			'groupId'   => $options['groupid'],
			'hotelId'   => $options['hotelid'],
			'beginDate' => $month_firstday->format('Y-m-d'),
			'endDate'   => $month_lastday->format('Y-m-d'),
			'layout'    => $this->get_layout(),
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
			if($response['response']['code'] == 200)
			{
				return json_decode($response['body'], true);
			} else 
			{
				echo $response['response']['message'];
			}

		} catch ( OAuthException2 $e ) {
			return $e;
		}
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
			if($response['response']['code'] === 200 ){
				return json_decode($response['body'], true);
			}
			else 
			{
				echo $response['response']['message'];
			}
		} catch ( OAuthException2 $e ) {
			return $e;
		}

	}

}