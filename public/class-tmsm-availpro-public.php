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
	 * Engine URL
	 *
	 * @since 		1.0.0
	 */
	const ENGINE_URL = 'https://www.secure-hotel-booking.com/';

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

	}

	/**
	 * Get locale
	 */
	private function get_locale() {
		return (function_exists('pll_current_language') ? pll_current_language() : substr(get_locale(),0, 2));
	}


	/**
	 * Get option
	 * @param string $option_name
	 *
	 * @return null
	 */
	private function get_option($option_name){

		$options = get_option($this->plugin_name . '-options');

		if(empty($options[$option_name])){
			return null;
		}
		return $options[$option_name];
	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/tmsm-availpro-public.css', array(), $this->version, 'all' );

		// Styling vars
		$tmsm_availpro_calendar_selectedcolor 	= get_theme_mod( 'tmsm_availpro_calendar_selectedcolor', '#333333' );
		$tmsm_availpro_calendar_rangecolor 	= get_theme_mod( 'tmsm_availpro_calendar_rangecolor', '#808080' );
		$tmsm_availpro_calendar_bestpricecolor 	= get_theme_mod( 'tmsm_availpro_calendar_bestpricecolor', '#0f9d58' );

		// Define css var
		$css 			= '';

		if ( ! empty( $tmsm_availpro_calendar_rangecolor ) && $tmsm_availpro_calendar_rangecolor!=='#808080') {
			$css .= '
			#tmsm-availpro-calendar .table-calendarprices tbody .day.selected .cell {background: '.$tmsm_availpro_calendar_rangecolor.';}
			#tmsm-availpro-calendar .table-calendarprices tbody .day.selected-hover .cell { background: '.$tmsm_availpro_calendar_rangecolor.'; }
			';
		}

		if ( ! empty( $tmsm_availpro_calendar_selectedcolor ) && $tmsm_availpro_calendar_selectedcolor!=='#333333') {
			$css .= '
			#tmsm-availpro-calendar .table-calendarprices tbody .day.selected-begin .cell,
			#tmsm-availpro-calendar .table-calendarprices tbody .day.selected-end .cell {
			background: '.$tmsm_availpro_calendar_selectedcolor.';
			}';
		}

		if ( ! empty( $tmsm_availpro_calendar_rangecolor ) && $tmsm_availpro_calendar_rangecolor!=='#808080') {
			$css .= '
			#tmsm-availpro-calendar .table-calendarprices tbody .day.selected-begin .price,
			#tmsm-availpro-calendar .table-calendarprices tbody .day.selected-end .price {
			color:'.$tmsm_availpro_calendar_rangecolor.';
			}';
		}

		if ( ! empty( $tmsm_availpro_calendar_selectedcolor ) && $tmsm_availpro_calendar_selectedcolor!=='#333333') {
			$css .= '
			#tmsm-availpro-calendar .table-calendarprices tbody .day.mouseover .cell {
			background: '.$tmsm_availpro_calendar_selectedcolor.';
			}';
		}

		if ( ! empty( $tmsm_availpro_calendar_rangecolor ) && $tmsm_availpro_calendar_rangecolor!=='#808080') {
			$css .= '
			#tmsm-availpro-calendar .table-calendarprices tbody .day.mouseover .price {
			color: '.$tmsm_availpro_calendar_rangecolor.';
			}';
		}

		if ( ! empty( $tmsm_availpro_calendar_bestpricecolor ) && $tmsm_availpro_calendar_bestpricecolor !== '#0f9d58') {
			$css .= '
			#tmsm-availpro-calendar .table-calendarprices tbody .day:not(.selected):not(.past):not(.mouseover) .cell[data-lowestprice=\'1\'] .price {
			color: '.$tmsm_availpro_calendar_bestpricecolor.';
			}
			#tmsm-availpro-form .tmsm-availpro-form-legend .legend-item.legend-item-lowestprice:before {
			background: '.$tmsm_availpro_calendar_bestpricecolor.';
			}
			';

		}

		// Return CSS
		if ( ! empty( $css ) ) {
			$css = '/* Availpro CSS */'. $css;
		}

		wp_add_inline_style( $this->plugin_name, $css );

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		// Scripts
		wp_enqueue_script( 'moment', plugin_dir_url( dirname(__FILE__) ) . 'vendor/moment/min/moment.min.js', array( 'jquery' ), $this->version, true );
		if ( function_exists( 'PLL' ) && $language = PLL()->model->get_language( get_locale() ) && pll_current_language() !== 'en')
		{
			wp_enqueue_script( 'moment-'.pll_current_language(), plugin_dir_url( dirname(__FILE__) ) . 'vendor/moment/locale/'.pll_current_language().'.js', array( 'jquery' ), $this->version, true );
		}

		wp_enqueue_script( 'clndr', plugin_dir_url( dirname(__FILE__) ) . 'vendor/clndr/clndr.min.js', array( 'jquery', 'moment', 'underscore' ), $this->version, true );

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/tmsm-availpro-public.js', array( 'jquery', 'wp-util' ), $this->version, true );


		// Params
		$params = [
			'ajax_url' => admin_url( 'admin-ajax.php' ),
			'locale' => $this->get_locale(),
			'security' => wp_create_nonce( 'security' ),
			'i18n' => [
				'button_continue' => __( 'Book now', 'tmsm-availpro' ),
				'fromprice' => _x( 'From', 'price', 'tmsm-availpro' ),
			],
			'options' => [
				'currency' => $this->options['currency'],
			],
			'data' => $this->get_options_bestprice(),
		];

		wp_localize_script( $this->plugin_name, 'tmsm_availpro_params', $params);
	}

	/**
	 * Register the shortcodes
	 *
	 * @since    1.0.0
	 */
	public function register_shortcodes() {
		add_shortcode( 'tmsm-availpro-calendar', array( $this, 'calendar_shortcode') );
	}

	/**
	 * Calendar shortcode
	 *
	 * @since    1.0.0
	 */
	public function calendar_shortcode($atts) {
		$atts = shortcode_atts( array(
			'option' => '',
		), $atts, 'tmsm-availpro-calendar' );

		$output = $this->calendar_template();
		$output .= $this->form_template();

		$output = '<div id="tmsm-availpro-container">'.$output.'</div>';
		return $output;
	}

	/**
	 * Legend template
	 *
	 * @return string
	 */
	private function legend_template(){
		$output = '
		        <div class="tmsm-availpro-form-legend">
                <p class="legend-item legend-item-notavailable">'.__('Not available','tmsm-availpro').'</p>
                <p class="legend-item legend-item-available">'.__('Available','tmsm-availpro').'</p>
                <p class="legend-item legend-item-lowestprice">'.__('Lowest price','tmsm-availpro').'</p>
                <p class="legend-item legend-item-lastrooms">'.__('Last rooms','tmsm-availpro').'</p>
                <p class="legend-item legend-item-minstay">'.__('Minimum stay','tmsm-availpro').'</p>
	        </div>';
		return $output;
	}	
		
	/**
	 * Form template
	 *
	 * @return string
	 */
	private function form_template(){

		$today = new Datetime();
		$tomorrow = (new DateTime())->modify('+1 day');

		$output = '
		<form target="_blank" action="'.self::ENGINE_URL.$this->get_option('engine').'" method="get" id="tmsm-availpro-form">
		
		<input type="hidden" name="language" value="'.$this->get_locale().'">
		<input type="hidden" name="arrivalDate" value="'.$today->format('Y-m-d').'" id="tmsm-availpro-form-arrivaldate">
		<input type="hidden" name="nights" value="1" id="tmsm-availpro-form-nights">
		<input type="hidden" name="checkinDate" value="'.$today->format('Y-m-d').'" id="tmsm-availpro-form-checkindate">
		<input type="hidden" name="checkoutDate" value="'.$tomorrow->format('Y-m-d').'" id="tmsm-availpro-form-checkoutdate">
		<input type="hidden" name="selectedAdultCount" value="2">
		<input type="hidden" name="selectedChildCount" value="0">
		<input type="hidden" name="guestCountSelector" value="ReadOnly">
		<input type="hidden" name="rate" value="">
		<input type="hidden" name="roomid" value="">
		<input type="hidden" name="showSearch" value="true">
		
        <div class="tmsm-availpro-form-fields">

			'.(!empty($this->get_option('intro')) ? '<p id="tmsm-availpro-form-intro">'.html_entity_decode( $this->get_option('intro')).'</p>' : '' ).'
			
			<p id="tmsm-availpro-form-dates-container">
				' . _x( 'From', 'date selection',  'tmsm-availpro' ) . ' <span id="tmsm-availpro-form-checkindateinfo"  ></span> ' . _x( 'to', 'date selection', 'tmsm-availpro' ) . ' <span id="tmsm-availpro-form-checkoutdateinfo" ></span>
			</p>
            <p id="tmsm-availpro-form-nights-message" data-value="0">'.__('Number of nights:','tmsm-availpro').' <span id="tmsm-availpro-form-nights-number"></span></p>
            <p id="tmsm-availpro-form-minstay-message" data-value="0">'.__('Minimum stay:','tmsm-availpro').' <span id="tmsm-availpro-form-minstay-number"></span></p>
			';

		/*$output.='

			<p>
				<label for="tmsm-availpro-form-adults" id="tmsm-availpro-form-adults-label">'.__( 'Number of adults:', 'tmsm-availpro' ).'</label>
				<select name="selectedAdultCount" id="tmsm-availpro-form-adults">
				<option value="2">'.__( 'Number of adults', 'tmsm-availpro' ).'</option>';


				for ( $adults = 1; $adults <= 6; $adults ++ ) {
					$output .= '<option value="' . $adults . '">';
					$output .= sprintf( _n( '%s adult', '%s adults', $adults, 'tmsm-availpro' ), number_format_i18n( $adults ) );
					$output .= '</option>';
				}

		$output.='

				</select>
			</p>';
		*/

		$theme = wp_get_theme();
		$buttonclass = ( 'StormBringer' == $theme->name || 'stormbringer' == $theme->template  ? 'btn btn-primary': '');

        $output.='  
            <p id="tmsm-availpro-calculateprice-results">
                
                <span id="tmsm-availpro-calculatetotal-totalprice" style="display: none">
                <span id="tmsm-availpro-calculatetotal-totalprice-label">' . __( 'Total price:', 'tmsm-availpro' ) . ' </span><span id="tmsm-availpro-calculatetotal-totalprice-value"></span>
                </span>
                <span id="tmsm-availpro-calculatetotal-errors" style="display: none"></span>
                <i class="fa fa-spinner fa-spin" aria-hidden="true" id="tmsm-availpro-calculatetotal-loading" style="display: none"></i>
			</p>
            <p>
            <button type="submit" id="tmsm-availpro-form-submit" class="'.$buttonclass.'">' .(!empty($this->get_option('bookbuttonlabel')) ? html_entity_decode($this->get_option('bookbuttonlabel')) : __( 'Book now', 'tmsm-availpro' ) ). '</button>
            </p>
            '.(!empty($this->get_option('outro')) ? '<p id="tmsm-availpro-form-outro">'.html_entity_decode($this->get_option('outro')).'</p>' : '' ).'
            </div>

            </form>
            <form action="" method="post" id="tmsm-availpro-calculatetotal">
			'.wp_nonce_field( 'tmsm-availpro-calculatetotal-nonce-action', 'tmsm-availpro-calculatetotal-nonce', true, false ).'
	
	        
			</form>
		';//<button type="submit" id="tmsm-availpro-calculatetotal-submit">Submit</button>
		return $output;
	}

	/**
	 * Display calendar template
	 *
	 * @return string
	 */
	private function calendar_template(){
		$output = '
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
                </th>
                <th class="clndr-control-button clndr-control-button-next">
                    <span class="clndr-next-button">&rarr;</span>
                </th>
            </tr>
            <tr class="header-days">

                <% for(var i = 0; i < daysOfTheWeek.length; i++) { %>
<th class="header-day">
                    <span class=""><%= moment().weekday(i).format(\'dd\').charAt(0) %></span>
                    
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
                    <div class="cell" data-price="<%= event.Price %>" data-status="<%= event.Status %>"  data-lowestprice="<%= event.LowestPrice %>" data-availability="<%= event.Availability %>" data-minstay="<%= event.MinimumStayThrough %>">
                        <span class="day-number"><%= days[d].day %></span>
                        <span class="minstay">⇾</span>
                        <p class="price" data-test="<%= event.Test %>"><%= event.PriceWithCurrency %></p>
                    </div>
                    <% }) %>

                    <% } else { %>

                    <div class="cell">
                        <span class="day-number"><%= days[d].day %></span>
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
';

		$output = '<div id="tmsm-availpro-calendar-container">'.$output.$this->legend_template().'</div>';
		return $output;
	}


	/**
	 * Get options for all bestprices months
	 *
	 * @return array
	 */
	private function get_options_bestprice(){

		$data = [];

		// Browse 12 next months
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
		//$lastmonthchecked = '2018-06';
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
			$lastmonthchecked_object = DateTime::createFromFormat( 'Y-m-d', $lastmonthchecked.'-01' );

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


							foreach($dailyplanning_bestprice_entity as $date => $attributes){
								//if($date == '2018-07-05'){
									error_log('*roomid: '.$entity['@attributes']['roomId']);
									error_log('*Date: '.$date);
									error_log('*Price: '.@$attributes['Price']);
									error_log('*Status: '.@$attributes['Status']);
									if(@$attributes['Status'] !=='NotAvailable' && !empty($attributes['Price'])){
										error_log('*Consider Price');
										if(!empty($dailyplanning_bestprice[$date]['Price'])){
											error_log('*Current Best Price: '.$dailyplanning_bestprice[$date]['Price']);
											if(
												$attributes['Price'] < $dailyplanning_bestprice[$date]['Price']
											){
												error_log('*Price Inferior to Current Best Price: '.$attributes['Price']);
												$dailyplanning_bestprice[$date]['Price'] = $attributes['Price'];
												error_log('*New Best Price: '.$dailyplanning_bestprice[$date]['Price']);
											}
										}
										else{
											@$dailyplanning_bestprice[$date]['Price'] = $attributes['Price'];
											@$dailyplanning_bestprice[$date] = $dailyplanning_bestprice_entity[$date];
											error_log('*Setting  Best Price: '.$dailyplanning_bestprice[$date]['Price']);
										}
									}
								//}

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


	/**
	 * Ajax calculate total price
	 *
	 * @since    1.0.0
	 */
	public static function ajax_calculate_totalprice() {

		$security = sanitize_text_field( $_POST['security'] );
		$date_begin = sanitize_text_field( $_POST['date_begin'] );
		$date_end = sanitize_text_field( $_POST['date_end'] );
		$nights = sanitize_text_field( $_POST['nights'] );

		$errors = array(); // Array to hold validation errors
		$data   = array(); // Array to pass back data

		if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
			error_log('ajax_calculate_totalprice');
		}

		// Check security
		if ( empty( $security ) || ! wp_verify_nonce( $security, 'tmsm-availpro-calculatetotal-nonce-action' ) ) {
			$errors[] = __('The request is not valid', 'tmsm-availpro');
			if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
				error_log('Ajax security not OK');
			}
			wp_die( -1 );
		}
		else{
			if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
				error_log('Ajax security OK');
			}
		}
		check_ajax_referer( 'tmsm-availpro-calculatetotal-nonce-action', 'security' );

		// Check date begin
		if(empty($date_begin)){
			$errors[] = __('Date is empty', 'tmsm-availpro');
			if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
				error_log('Date is empty');
			}
		}
		// Check nights number
		if(empty($nights)){
			$errors[] = __('Nights number are empty', 'tmsm-availpro');
			if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
				error_log('Nights number is empty');
			}
		}



		// Calculate price
		$webservice = new Tmsm_Availpro_Webservice();
		$response   = $webservice->get_stayplanning( $date_begin, $nights);
		$data       = $webservice::convert_to_array( $response );
		if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
			error_log('response:');
			error_log($response);
		}

		// Init data var
		$dailyplanning_bestprice = [];
		if ( ! empty( $data ) ) {
			if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
				error_log( 'Data responsee' );
			}

			if ( isset( $data['response']['success'] ) ) {
				if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
					error_log( 'data success' );
				}
				if ( isset( $data['response']['stayPlanning'] ) ) {

					if ( isset( $data['response']['stayPlanning']['ratePlan']['hotel'] )
					     && is_array( $data['response']['stayPlanning']['ratePlan']['hotel'] ) ) {

						foreach ( $data['response']['stayPlanning']['ratePlan']['hotel']['entity'] as $entity ) {
							if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
								error_log( 'Entity: roomId=' . $entity['@attributes']['roomId'] . ' rateId=' . $entity['@attributes']['rateId'] );
							}

							$dailyplanning_bestprice_entity = [];

							foreach ( $entity['property'] as $property ) {

								$propertyname = $property['@attributes']['name'];

								@$dailyplanning_bestprice_entity[$propertyname] = $property['@attributes']['value'];

							}

							ksort($dailyplanning_bestprice_entity);
							if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
								error_log('dailyplanning_bestprice_entity:');
								error_log(var_export($dailyplanning_bestprice_entity, true));
							}

							// Merge data
							if(empty($dailyplanning_bestprice) && @$dailyplanning_bestprice_entity['Status'] !== 'NotAvailable'){
								$dailyplanning_bestprice = $dailyplanning_bestprice_entity;
							}
							else{
								if(!empty($dailyplanning_bestprice_entity['Price']) && !empty($dailyplanning_bestprice['Price']) ){
									// New Price is less than merged data
									if(
										$dailyplanning_bestprice_entity['Price'] < $dailyplanning_bestprice['Price']
										&& @$dailyplanning_bestprice_entity['Status'] !== 'NotAvailable'
									){
										$dailyplanning_bestprice = $dailyplanning_bestprice_entity;
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

		$totalprice = null;
		if ( ! empty( $dailyplanning_bestprice ) && @$dailyplanning_bestprice['Status'] !== 'NotAvailable' ) {
			$totalprice = $dailyplanning_bestprice['Price'];
		} else {
			$errors[] = __( 'No availability', 'tmsm-availpro' );
		}
		$data = [
			'totalprice' => money_format( '%.2n', $totalprice ),
		];

		// Return a response
		if( ! empty($errors) ) {
			$data['success'] = false;
			$data['errors']  = $errors;
		}
		else {
			$data['success'] = true;
			$data['data'] = $data;
		}
		wp_send_json($data);
		wp_die();

    }

}
