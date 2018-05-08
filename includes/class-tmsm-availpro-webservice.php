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
	const NAMESPACE = 'http://ws.availpro.com/schemas/planning/2012A';

	/**
	 * Webservice URL
	 *
	 * @access 	private
	 * @since 	1.0.0
	 * @var 	string
	 */
	const URL = 'https://ws.availpro.com/Planning/2012A/PlanningService.asmx?WSDL';

	/**
	 * Webservice URL
	 *
	 * @access 	private
	 * @since 	1.0.0
	 * @var 	string
	 */
	const METHOD = 'GetDailyPlanning';

	/**
	 * Webservice URL
	 *
	 * @access 	private
	 * @since 	1.0.0
	 * @var 	string
	 */
	const LAYOUT = '<level name="ArticleRate"><property name="Status" /><property name="Price" /><property name="Availability" /><property name="MinimumStayThrough" /></level>';

	/**
	 * Webservice Oauth identifiers
	 *
	 * @access 	private
	 * @since 	1.0.0
	 * @var 	array
	 */
	private $oauth_identifiers = [];

	/**
	 * Webservice Oauth identifiers
	 *
	 * @access 	private
	 * @since 	1.0.0
	 * @var 	string
	 */
	private $filters = '';


	/**
	 * Constructor
	 */
	public function __construct() {

		$options = get_option('tmsm-availpro-options', false);

		$this->set_oauth_identifiers();
		$this->set_filters();


	}

	/**
	 * Set oauth identifiers
	 */
	private function set_oauth_identifiers(){
		$options = get_option('tmsm-availpro-options', false);
		$this->oauth_identifiers = [
			'consumerKey' => $options['consumerkey'],
			'consumerSecret' => $options['consumersecret'],
			'accessToken'    => $options['accesstoken'],
			'accessSecret'   => $options['tokensecret'],
		];
		error_log(var_export($this->oauth_identifiers, true));
	}

	/**
	 * Set filters
	 */
	private function set_filters(){
		$options = get_option('tmsm-availpro-options', false);

		$option_rateids = $options['rateids'];
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
		$filters_rateids = '';
		if(!empty($option_rateids_array) && is_array($option_rateids_array) && count($option_rateids_array) > 0){
			$filters_rateids = '<rates default="Excluded">';
			foreach($option_rateids_array as $item){
				$filters_rateids .= '<exception id="'.$item.'"/>';
			}
			$filters_rateids .= '</rates>';
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
			$filters_roomids = '<rooms default="Excluded">';
			foreach($option_roomids_array as $item){
				$filters_roomids .= '<exception id="'.$item.'"/>';
			}
			$filters_roomids .= '</rooms>';
		}

		$this->filters ='
					<ratePlans><ratePlan groupId="'.$option_groupid.'"><hotels default="Excluded"><exception id="'.$option_hotelid.'" /></hotels></ratePlan></ratePlans>'.
	                '<status><include status="Available" /><include status="NotAvailable" /></status>'.
	                $filters_rateids.
					$filters_roomids.
		'';

		error_log($this->filters);
	}

	/**
	 * Get Data from Availpro API call
	 */
	public function get_data(){

		try {
			$result = SoapOAuthWrapper::Invoke( $url, $namespace, $method, $soapParameters, $oauthOptions );
			echo 'body : ' . htmlentities( $result ) . '<br/>';
		} catch ( OAuthException2 $e ) {
			echo $e;
		}
	}

}