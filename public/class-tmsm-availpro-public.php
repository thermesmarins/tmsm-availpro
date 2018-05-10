<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://github.com/nicomollet
 * @since      1.0.0
 *
 * @package    Tmsm_Availpro
 * @subpackage Tmsm_Availpro/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Tmsm_Availpro
 * @subpackage Tmsm_Availpro/public
 * @author     Nicolas Mollet <nico.mollet@gmail.com>
 */
class Tmsm_Availpro_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string $plugin_name The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string $version The current version of this plugin.
	 */
	private $version;

	/**
	 * The plugin options.
	 *
	 * @since 		1.0.0
	 * @access 		private
	 * @var 		string 			$options    The plugin options.
	 */
	private $options;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 *
	 * @param      string $plugin_name The name of the plugin.
	 * @param      string $version     The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version     = $version;

		$this->set_options();
	}

	/**
	 * Sets the class variable $options
	 */
	private function set_options() {
		$this->options = get_option( $this->plugin_name . '-options' );
	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/tmsm-availpro-public.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		// Scripts
		wp_enqueue_script( 'moment', plugin_dir_url( dirname(__FILE__) ) . 'vendor/moment/min/moment.min.js', array( 'jquery' ), $this->version, true );
		if ( function_exists( 'PLL' ) && $language = PLL()->model->get_language( get_locale() ) )
		{
			wp_enqueue_script( 'moment-'.pll_current_language(), plugin_dir_url( dirname(__FILE__) ) . 'vendor/moment/locale/'.pll_current_language().'.js', array( 'jquery' ), $this->version, true );
		}

		wp_enqueue_script( 'clndr', plugin_dir_url( dirname(__FILE__) ) . 'vendor/clndr/clndr.min.js', array( 'jquery', 'moment', 'underscore' ), $this->version, true );

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/tmsm-availpro-public.js', array( 'jquery' ), $this->version, true );


		// Params
		$params = [
			'ajax_url' => admin_url( 'admin-ajax.php' ),
			'locale' => (function_exists('pll_current_language') ? pll_current_language() : 'en'),
			'security' => wp_create_nonce( 'security' ),
			'i18n' => [
				'button_continue' => __( 'Book now', 'tmsm-availpro' ),
			],
			'options' => [
				'currency' => $this->options['currency'],
			],
			'data' => $this->get_options_bestprice(),
		];

		wp_localize_script( $this->plugin_name, 'tmsm_availpro_params', $params);
	}


	/**
	 * Display calendar template
	 *
	 * @return string
	 */
	public function calendar_template(){
		$output = <<<EOT
<div id="tmsm-availpro-calendar">
    <script id="tmsm-availpro-calendar-template" type="text/template">

        <table class="table-calendarprices table-condensed" border="0" cellspacing="0" cellpadding="0">
            <thead>
            <tr class="clndr-controls">
                <th class="clndr-control-button clndr-control-button-previous">
                    <span class="clndr-previous-button">&larr;</span>
                </th>
                <th class="month" colspan="5">
                    <%= month %> <%= year %>

                    &nbsp;
                    <small class="calendar-error">(Aucune date disponible pour ce mois)</small>
                    <small class="calendar-loading">Chargement <span class="glyphicon glyphicon-refresh glyphicon-spin"></span></small>

                </th>
                <th class="clndr-control-button clndr-control-button-next">
                    <span class="clndr-next-button">&rarr;</span>
                </th>
            </tr>
            <tr class="header-days">

                <% for(var i = 0; i < daysOfTheWeek.length; i++) { %>
<th class="header-day">
                    <%= moment().weekday(i).format('dd').charAt(0) %><span class="rest"><%= moment().weekday(i).format('dddd').slice(1) %></span>
                </th>
                <% } %>
            </tr>
            </thead>
            <tbody>
            <% for(var i = 0; i < numberOfRows; i++){ %>
            <tr>
                <% for(var j = 0; j < 7; j++){ %>
                <% var d = j + i * 7; %>
                <td class="<%= days[d].classes %>" data-daynumber="<%= days[d].day %>">

                    <% if (days[d].events.length != 0) { %>
                    <% _.each(days[d].events, function(event) { %>
                    <div class="cell" data-price="<%= event.Price %>" data-status="<%= event.Status %>" data-availability="<%= event.Availability %>" data-minstay="<%= event.MinimumStayThrough %>">
                        <small class="day-number"><%= days[d].day %></small>
                        <p class="price"><%= event.PriceWithCurrency %></p>
                    </div>
                    <% }) %>

                    <% } else { %>

                    <div class="cell">
                        <small class="day-number"><%= days[d].day %></small>
                        <p class="price">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</p>
                    </div>
                    <% } %>
                </td>
                <% } %>
            </tr>
            <% } %>
            </tbody>
        </table>

    </script>
</div>
EOT;

		echo $output;
	}

	/**
	 * Get option
	 * @param string $option_name
	 *
	 * @return null
	 */
	private function get_option($option_name){
		if(empty($this->options[$option_name])){
			return null;
		}
		return $this->options[$option_name];
	}

	/**
	 * Get options for all bestprices months
	 *
	 * @return array
	 */
	private function get_options_bestprice(){

		$data = [];

		// Brows 12 next months
		$date = new Datetime();
		$date->modify('-1 month');
		$i=0;
		while($i<=12){
			$date->modify('+1 month');
			$month_data = get_option('tmsm-availpro-bestprice-'.$date->format('Y-m'), false);
			if(!empty($month_data)){
				$data[$date->format('Y-m')] = $month_data;
			}
			$i++;
		}

		return $data;
	}

	/**
	 * Check Availpro Prices
	 */
	public function checkprices() {

		if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
			error_log( 'Function checkprices()' );
		}

		$lastmonthchecked = get_option( 'tmsm-availpro-lastmonthchecked', false );
		if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
			error_log( 'Last month checked: ' . $lastmonthchecked );
		}

		// Check if the last checked value was created
		if ( $lastmonthchecked === false ) {
			if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
				error_log( 'Check not initiated yet' );
			}
			$monthtocheck = date( 'Y-m' );
		} else {
			$lastmonthchecked_object = DateTime::createFromFormat( 'Y-m', $lastmonthchecked );
			$lastmonthchecked_object->modify( '+1 month' );
			$lastmonthchecked_limit = new Datetime();
			$lastmonthchecked_limit->modify( '+1 year' );



			if ( $lastmonthchecked_object->getTimestamp() >= $lastmonthchecked_limit->getTimestamp() ) {
				if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
					error_log( 'Limit month passed' );
				}
				$monthtocheck = date( 'Y-m' );
			} else {
				$monthtocheck = $lastmonthchecked_object->format( 'Y-m' );
			}
		}

		if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
			error_log( 'Month to check: ' . $monthtocheck );
		}
		// Update last check value
		$result = update_option( 'tmsm-availpro-lastmonthchecked', $monthtocheck, true );
		if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
			error_log( 'Result saving new month: ' . $result );
		}

		// API call
		$webservice = new Tmsm_Availpro_Webservice();
		$response   = $webservice->get_data( $monthtocheck );
		$data       = $webservice::convert_to_array( $response );

		if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
			error_log( 'array:' );
			error_log( var_export( $data, true ) );
		}

		// Init data var
		$dailyplanning_bestprice = [];
		$interval           = new \DateInterval( 'P1D' );

		if ( ! empty( $data ) ) {
			if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
				error_log( 'Data responsee' );
			}

			if ( isset( $data['response']['success'] ) ) {
				if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
					error_log( 'data success' );
				}
				if ( isset( $data['response']['dailyPlanning'] ) ) {

					if ( isset( $data['response']['dailyPlanning']['ratePlan']['hotel'] )
					     && is_array( $data['response']['dailyPlanning']['ratePlan']['hotel'] ) ) {

						foreach ( $data['response']['dailyPlanning']['ratePlan']['hotel']['entity'] as $entity ) {
							if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
								error_log( 'Entity: roomId=' . $entity['@attributes']['roomId'] . ' rateId=' . $entity['@attributes']['rateId'] );
							}

							$dailyplanning_bestprice_entity = [];

							foreach ( $entity['property'] as $property ) {

								$propertyname = $property['@attributes']['name'];

								if ( ! empty( $property['period'] ) ) {
									foreach ( $property['period'] as $period ) {

										$attributes = ( isset( $period['@attributes'] ) ? $period['@attributes'] : $period );

										if ( ! empty( $attributes['beginDate'] ) && ! empty( $attributes['endDate'] ) && ! empty( $attributes['value'] ) ) {

											if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
												//error_log( $propertyname . ': beginDate=' . $attributes['beginDate'] . ' endDate='. $attributes['endDate'] . ' value=' . $attributes['value'] );
											}


											$begindate = Datetime::createFromFormat( 'Y-m-d', $attributes['beginDate'] );
											$enddate   = Datetime::createFromFormat( 'Y-m-d', $attributes['endDate'] );
											$value   = $attributes['value'];

											$daterange = new \DatePeriod( $begindate, $interval, $enddate->modify( '+1 day' ) );

											/* @var $date Datetime */
											foreach ( $daterange as $date ) {
												//error_log( 'date: ' . $date->format( 'Y-m-d' ) );
												@$dailyplanning_bestprice_entity[ $date->format( 'Y-m-d' )][$propertyname] = $value;
												/*switch ( $propertyname ) {
													case 'Status':
														@$dailyplanning_bestprice_entity[ $date->format( 'Y-m-d' )]['status'] = 0;
														break;
													case 'Price':
														@$dailyplanning_bestprice_entity[ $date->format( 'Y-m-d' )]['price'] = $value;
														break;
													case 'Availability':
														@$dailyplanning_bestprice_entity[ $date->format( 'Y-m-d' )]['availability'] = $value;
														break;

													case 'MinimumStay':
														@$dailyplanning_bestprice_entity[ $date->format( 'Y-m-d' )]['availability'] = $value;
														break;

													default:
														break;
												}*/
											}


										}
									}
								}
							}

							ksort($dailyplanning_bestprice_entity);
							if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
								error_log('dailyplanning_bestprice_entity:');
								error_log(var_export($dailyplanning_bestprice_entity, true));
							}
							// Merge data
							if(empty($dailyplanning_bestprice)){
								$dailyplanning_bestprice = $dailyplanning_bestprice_entity;
							}
							else{
								//@TODO
								foreach($dailyplanning_bestprice_entity as $date => $attributes){
									if(!empty($attributes['Price']) && !empty($dailyplanning_bestprice[$date]['Price'])){
										// New Price is less than merged data
										if(
											@$attributes['Status'] != 'NotAvailable' &&
											$attributes['Price'] < $dailyplanning_bestprice[$date]['Price']
										){
											$dailyplanning_bestprice[$date] = $dailyplanning_bestprice_entity[$date];
										}
									}
								}
							}
						}
					}
				}
			}
		}

		if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
			error_log('dailyplanning_bestprice:');
			error_log(var_export($dailyplanning_bestprice, true));
		}
		// Save Month to check data
		update_option('tmsm-availpro-bestprice-'.$monthtocheck, $dailyplanning_bestprice);

		// Delete previous month data
		$today = new Datetime();
		delete_option( 'tmsm-availpro-bestprice-'.$today->modify('-1 month')->format('Y-m') );
	}
}
