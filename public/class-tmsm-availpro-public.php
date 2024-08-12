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
class Tmsm_Availpro_Public
{

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
	public function __construct($plugin_name, $version)
	{

		$this->plugin_name = $plugin_name;
		$this->version     = $version;
	}

	/**
	 * Get locale
	 */
	private function get_locale()
	{
		return (function_exists('pll_current_language') ? pll_current_language() : substr(get_locale(), 0, 2));
	}


	/**
	 * Get option
	 * @param string $option_name
	 *
	 * @return null
	 */
	private function get_option($option_name)
	{

		$options = get_option($this->plugin_name . '-options');

		if (empty($options[$option_name])) {
			return null;
		}
		return $options[$option_name];
	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles()
	{

		wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/tmsm-availpro-public.css', array(), $this->version, 'all');

		// Styling vars
		$tmsm_availpro_calendar_selectedcolor 	= get_theme_mod('tmsm_availpro_calendar_selectedcolor', '#333333');
		$tmsm_availpro_calendar_rangecolor 	= get_theme_mod('tmsm_availpro_calendar_rangecolor', '#808080');
		$tmsm_availpro_calendar_bestpricecolor 	= get_theme_mod('tmsm_availpro_calendar_bestpricecolor', '#0f9d58');

		// Define css var
		$css 			= '';

		if (!empty($tmsm_availpro_calendar_rangecolor) && $tmsm_availpro_calendar_rangecolor !== '#808080') {
			$css .= '
			#tmsm-availpro-calendar .table-calendarprices tbody .day.selected .cell {background: ' . $tmsm_availpro_calendar_rangecolor . ';}
			#tmsm-availpro-calendar .table-calendarprices tbody .day.selected-hover .cell { background: ' . $tmsm_availpro_calendar_rangecolor . '; }
			';
		}

		if (!empty($tmsm_availpro_calendar_selectedcolor) && $tmsm_availpro_calendar_selectedcolor !== '#333333') {
			$css .= '
			#tmsm-availpro-calendar .table-calendarprices tbody .day.selected-begin .cell,
			#tmsm-availpro-calendar .table-calendarprices tbody .day.selected-end .cell {
			background: ' . $tmsm_availpro_calendar_selectedcolor . ';
			}';
		}

		if (!empty($tmsm_availpro_calendar_rangecolor) && $tmsm_availpro_calendar_rangecolor !== '#808080') {
			$css .= '
			#tmsm-availpro-calendar .table-calendarprices tbody .day.selected-begin .price,
			#tmsm-availpro-calendar .table-calendarprices tbody .day.selected-end .price {
			color:' . $tmsm_availpro_calendar_rangecolor . ';
			}';
		}

		if (!empty($tmsm_availpro_calendar_selectedcolor) && $tmsm_availpro_calendar_selectedcolor !== '#333333') {
			$css .= '
			#tmsm-availpro-calendar .table-calendarprices tbody .day.mouseover .cell {
			background: ' . $tmsm_availpro_calendar_selectedcolor . ';
			}';
		}

		if (!empty($tmsm_availpro_calendar_rangecolor) && $tmsm_availpro_calendar_rangecolor !== '#808080') {
			$css .= '
			#tmsm-availpro-calendar .table-calendarprices tbody .day.mouseover .price {
			color: ' . $tmsm_availpro_calendar_rangecolor . ';
			}';
		}

		if (!empty($tmsm_availpro_calendar_bestpricecolor) && $tmsm_availpro_calendar_bestpricecolor !== '#0f9d58') {
			$css .= '
			#tmsm-availpro-calendar .table-calendarprices tbody .day:not(.selected):not(.past):not(.mouseover) .cell[data-lowestprice=\'1\'] .price {
			color: ' . $tmsm_availpro_calendar_bestpricecolor . ';
			}
			#tmsm-availpro-form .tmsm-availpro-form-legend .legend-item.legend-item-lowestprice:before {
			background: ' . $tmsm_availpro_calendar_bestpricecolor . ';
			}
			';
		}

		// Return CSS
		if (!empty($css)) {
			$css = '/* Availpro CSS */' . $css;
		}

		wp_add_inline_style($this->plugin_name, $css);
	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts()
	{

		// Scripts
		wp_dequeue_script('moment');
		wp_deregister_script('moment');
		wp_enqueue_script('moment', plugin_dir_url(dirname(__FILE__)) . 'vendor/moment/min/moment.min.js', array('jquery'), $this->version, true);
		if (function_exists('PLL') && $language = PLL()->model->get_language(get_locale()) && pll_current_language() !== 'en') {
			$moment_locale = pll_current_language();
			if (pll_current_language() === 'zh') {
				$moment_locale = 'zh-cn';
			}

			wp_enqueue_script('moment-' . $moment_locale, plugin_dir_url(dirname(__FILE__)) . 'vendor/moment/locale/' . $moment_locale . '.js', array('jquery'), $this->version, true);
		}

		wp_enqueue_script('clndr', plugin_dir_url(dirname(__FILE__)) . 'vendor/clndr/clndr.min.js', array('jquery', 'moment', 'underscore'), $this->version, true);

		wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . 'js/tmsm-availpro-public' . (!WP_DEBUG ? '.min' : '') . '.js', array('jquery', 'wp-util'), $this->version, true);


		// Params
		$params = [
			'ajax_url' => admin_url('admin-ajax.php'),
			'locale'   => $this->get_locale(),
			'security' => wp_create_nonce('security'),
			'i18n'     => [
				'fromprice'          => _x('From', 'price', 'tmsm-availpro'),
				'yearbestpricelabel' => $this->get_option('yearbestpricelabel'),
				'otacomparelabel' => $this->get_option('otacomparelabel'),
				'selecteddatepricelabel' => $this->get_option('selecteddatepricelabel'),
			],
			'options'  => [
				'currency' => $this->get_option('currency'),
			],
			'data'     => $this->get_options_bestprice(),
		];

		wp_localize_script($this->plugin_name, 'tmsm_availpro_params', $params);
	}

	/**
	 * Register the shortcodes
	 *
	 * @since    1.0.0
	 */
	public function register_shortcodes()
	{
		add_shortcode('tmsm-availpro-calendar', array($this, 'calendar_shortcode'));
		add_shortcode('tmsm-availpro-bestprice-year', array($this, 'bestpriceyear_shortcode'));
	}


	/**
	 * Send an email to admin if the scheduled cron is not defined
	 */
	public function check_cron_schedule_exists()
	{

		if (!wp_next_scheduled('tmsmavailpro_cronaction')) {

			$email = wp_mail(
				get_option('admin_email'),
				wp_specialchars_decode(sprintf(__('TMSM Availpro cron is not scheduled on %s', 'tmsm-availpro'), get_option('blogname'))),
				wp_specialchars_decode(sprintf(__('TMSM Availpro cron is not scheduled on %s', 'tmsm-availpro'), get_option('blogname')))
			);
		}
	}

	/**
	 * Calendar shortcode
	 *
	 * @since    1.0.0
	 */
	public function calendar_shortcode($atts)
	{
		$atts = shortcode_atts(array(
			'option' => '',
		), $atts, 'tmsm-availpro-calendar');

		$output = $this->calendar_template();
		$output .= $this->form_template();

		$output = '<div id="tmsm-availpro-container">' . $output . '</div>';
		return $output;
	}

	/**
	 * Best Price Year shortcode
	 *
	 * @since    1.0.7
	 */
	public function bestpriceyear_shortcode($atts)
	{
		$atts = shortcode_atts(array(
			'roomid' => '',
			'rateid' => '',
		), $atts, 'tmsm-availpro-bestprice-year');

		if (defined('WP_DEBUG') && WP_DEBUG) {
			error_log(print_r($atts, true));
		}

		$price = null;
		$output = null;
		$date = null;
		$bestprice_year_requested = null;

		$bestprice_year = get_option('tmsm-availpro-bestprice-year', false);
error_log('BestPrice shortcode');
error_log(print_r($bestprice_year, true));
		if (!empty($atts['roomid']) && !empty($bestprice_year['Room' . $atts['roomid']])) {
			$bestprice_year_requested = $bestprice_year['Room' . $atts['roomid']];
		} elseif (!empty($atts['rateid']) && !empty($bestprice_year['Rate' . $atts['rateid']])) {
			$bestprice_year_requested = $bestprice_year['Rate' . $atts['rateid']];
		} else {
			$bestprice_year_requested = (!empty($bestprice_year['Overall']) ? $bestprice_year['Overall'] : null);
		}

		if (!empty($bestprice_year_requested) && !empty($bestprice_year_requested['totalPrice'])) {
			$price = sanitize_text_field($bestprice_year_requested['totalPrice']);
			if (!empty($bestprice_year_requested['Date'])) {
				$date = sanitize_text_field($bestprice_year_requested['Date']);
			}
		}
		if (!empty($price)) {
			$output = '<span class="tmsm-availpro-bestprice-year" data-date="' . $date . '" data-price="' . $price . '" data-roomid="' . (!empty($atts['roomid']) ? esc_attr__($atts['roomid']) : '') . '" data-rateid="' . (!empty($atts['rateid']) ? esc_attr__($atts['rateid']) : '') . '"></span>';
		}
		return $output;
	}

	/**
	 * Legend template
	 *
	 * @return string
	 */
	private function legend_template()
	{
		$output = '
		        <div class="tmsm-availpro-form-legend">
                <p class="legend-item legend-item-notavailable">' . __('Not available', 'tmsm-availpro') . '</p>
                <p class="legend-item legend-item-available">' . __('Available', 'tmsm-availpro') . '</p>
                <p class="legend-item legend-item-lowestprice">' . __('Lowest price', 'tmsm-availpro') . '</p>
                <p class="legend-item legend-item-lastrooms">' . __('Last rooms', 'tmsm-availpro') . '</p>
                <p class="legend-item legend-item-minstay">' . __('Minimum stay', 'tmsm-availpro') . '</p>
	        </div>';
		return $output;
	}

	/**
	 * Form template
	 *
	 * @return string
	 */
	private function form_template()
	{

		$today = new Datetime();
		$tomorrow = (new DateTime())->modify('+1 day');

		$output = '
		<form target="_blank" action="' . self::ENGINE_URL . $this->get_option('engine') . '" method="get" id="tmsm-availpro-form">
		
		<input type="hidden" name="language" value="' . $this->get_locale() . '">
		<input type="hidden" name="arrivalDate" value="" id="tmsm-availpro-form-arrivaldate">
		<input type="hidden" name="nights" value="1" id="tmsm-availpro-form-nights">
		<input type="hidden" name="checkinDate" value="" id="tmsm-availpro-form-checkindate">
		<input type="hidden" name="checkoutDate" value="" id="tmsm-availpro-form-checkoutdate">
		<input type="hidden" name="selectedAdultCount" value="2">
		<input type="hidden" name="selectedChildCount" value="0">
		<input type="hidden" name="guestCountSelector" value="ReadOnly">
		<input type="hidden" name="rate" value="">
		<input type="hidden" name="roomid" value="">
		<input type="hidden" name="showSearch" value="true">
		
        <div class="tmsm-availpro-form-fields">

			' . (!empty($this->get_option('intro')) ? '<p id="tmsm-availpro-form-intro">' . html_entity_decode($this->get_option('intro')) . '</p>' : '') . '
			
			<p id="tmsm-availpro-form-dates-container" style="display: none">
				' . _x('From', 'date selection',  'tmsm-availpro') . ' <span id="tmsm-availpro-form-checkindateinfo"></span> ' . _x('to', 'date selection', 'tmsm-availpro') . ' <span id="tmsm-availpro-form-checkoutdateinfo"></span>
			</p>
            <p id="tmsm-availpro-form-nights-message" data-value="0">' . __('Number of nights:', 'tmsm-availpro') . ' <span id="tmsm-availpro-form-nights-number"></span></p>
            <p id="tmsm-availpro-form-minstay-message" data-value="0">' . __('Minimum stay:', 'tmsm-availpro') . ' <span id="tmsm-availpro-form-minstay-number"></span></p>
			';

		$theme = wp_get_theme();
		$buttonclass = '';
		if ('StormBringer' == $theme->get('Name') || 'stormbringer' == $theme->get('Template')) {
			$buttonclass = 'btn btn-primary';
		}
		if ('OceanWP' == $theme->get('Name') || 'oceanwp' == $theme->get('Template')) {
			$buttonclass = 'button';
		}

		/**
		 *             <a href="'.self::ENGINE_URL.$this->get_option('engine').'" id="tmsm-availpro-form-submit" class="'.$buttonclass.'">' .(!empty($this->get_option('bookbuttonlabel')) ? html_entity_decode($this->get_option('bookbuttonlabel')) : __( 'Book now', 'tmsm-availpro' ) ). '</a>
		 */

		$output .= '  
            <p id="tmsm-availpro-calculatetotal-results">
                <span id="tmsm-availpro-calculatetotal-totalprice" style="display: none"></span>
                <span id="tmsm-availpro-calculatetotal-errors" style="display: none"></span>
                <i class="fa fa-spinner fa-spin" aria-hidden="true" id="tmsm-availpro-calculatetotal-loading" style="display: none"></i>
			</p>
            <p>
            <button type="submit" id="tmsm-availpro-form-submit" class="' . $buttonclass . '">' . (!empty($this->get_option('bookbuttonlabel')) ? html_entity_decode($this->get_option('bookbuttonlabel')) : __('Book now', 'tmsm-availpro')) . '</button>
            </p>
            <p id="tmsm-availpro-calculatetotal-ota" style="display: none"></p>
            ' . (!empty($this->get_option('outro')) ? '<div id="tmsm-availpro-form-outro">' . html_entity_decode($this->get_option('outro')) . '</div>' : '') . '
            </div>
            </form>
            <form action="" method="post" id="tmsm-availpro-calculatetotal">
			' . wp_nonce_field('tmsm-availpro-calculatetotal-nonce-action', 'tmsm-availpro-calculatetotal-nonce', true, false) . '        
			</form>
		'; //<button type="submit" id="tmsm-availpro-calculatetotal-submit">Submit</button>
		return $output;
	}

	/**
	 * Display calendar template
	 *
	 * @return string
	 */
	private function calendar_template()
	{
		$output = '
<div id="tmsm-availpro-calendar">
</div>
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
                    <span class="hide"><%= moment().weekday(i).format(\'dd\').charAt(0) %></span>
                    <span class=""><%= daysOfTheWeek[i] %></span>
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
                    <div class="cell" data-price="<%= event.totalPrice %>" data-status="<%= event.Status %>"  data-lowestprice="<%= event.LowestPrice %>" data-availability="<%= event.Availability %>" data-minstay="<%= event.MinimumStayThrough %>">
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
';

		$output = '<div id="tmsm-availpro-calendar-container">' . $output . $this->legend_template() . '</div>';
		return $output;
	}

	/**
	 * Get options for all bestprices months
	 *
	 * @return array
	 * @throws Exception
	 */
	private function get_options_bestprice()
	{

		$data = [];

		// Browse 12 next months
		$date = new Datetime();
		$date->modify('-1 month');
		$i = 0;
		while ($i <= 12) {
			$date->modify('+1 month');
			$month_data = get_option('tmsm-availpro-bestprice-' . $date->format('Y-m'), false);
			if (!empty($month_data)) {
				$data[$date->format('Y-m')] = $month_data;
			}
			$i++;
		}

		return $data;
	}

	/**
	 * Check Availpro Prices
	 */
	public function checkprices()
	{

		if (defined('WP_DEBUG') && WP_DEBUG) {
			error_log('Function checkprices()');
		}

		$lastmonthchecked = get_option('tmsm-availpro-lastmonthchecked', false);
		if (defined('WP_DEBUG') && WP_DEBUG) {
			error_log('Last month checked: ' . $lastmonthchecked);
		}

		// Check if the last checked value was created
		if ($lastmonthchecked === false) {
			if (defined('WP_DEBUG') && WP_DEBUG) {
				error_log('Check not initiated yet');
			}
			// Initialize value
			$monthtocheck = date('Y-m');
		} else {
			$lastmonthchecked_object = DateTime::createFromFormat('Y-m-d', $lastmonthchecked . '-01');

			$lastmonthchecked_object->modify('+1 month');
			$lastmonthchecked_limit = new Datetime();
			$lastmonthchecked_limit->modify('+1 year');

			if ($lastmonthchecked_object->getTimestamp() >= $lastmonthchecked_limit->getTimestamp()) {
				if (defined('WP_DEBUG') && WP_DEBUG) {
					error_log('Limit month passed');
				}
				$monthtocheck = date('Y-m');
			} else {
				$monthtocheck = $lastmonthchecked_object->format('Y-m');
			}
		}

		if (defined('WP_DEBUG') && WP_DEBUG) {
			error_log('Month to check: ' . $monthtocheck);
		}

		// Empty year price
		$bestprice_year = get_option('tmsm-availpro-bestprice-year', false);
		if (!empty($bestprice_year) && is_array($bestprice_year)) {
			foreach ($bestprice_year as $bestprice_year_item_key => $bestprice_year_item_value) {
				if (!empty($bestprice_year_item_value) && isset($bestprice_year_item_value['Date'])) {
					if (defined('WP_DEBUG') && WP_DEBUG) {
						error_log('key $bestprice_year: ' . $bestprice_year_item_key);
						error_log('current $bestprice_year[Date]: ' . $bestprice_year_item_value['Date']);
					}

					// unset value if we are checking again the prices of the month
					if (strpos($bestprice_year_item_value['Date'], $monthtocheck) !== false) {
						unset($bestprice_year[$bestprice_year_item_key]);
						if (defined('WP_DEBUG') && WP_DEBUG) {
							error_log('current $bestprice_year[Date] is in month to check');
						}
					}

					// unset value if the value date is passed
					$bestprice_year_item_object = DateTime::createFromFormat('Y-m-d', $bestprice_year_item_value['Date']);
					if ($bestprice_year_item_object->getTimestamp() < time()) {
						unset($bestprice_year[$bestprice_year_item_key]);
						if (defined('WP_DEBUG') && WP_DEBUG) {
							error_log('current $bestprice_year[Date] is passed');
						}
					}
				}
			}
		}

		// Update last check value
		$result = update_option('tmsm-availpro-lastmonthchecked', $monthtocheck, true);
		if (defined('WP_DEBUG') && WP_DEBUG) {
			error_log('Result saving new month: ' . $result);
		}

		// API call
		$webservice = new Tmsm_Availpro_Webservice();
		$response   = $webservice->get_data($monthtocheck);
		$data = $response;

		if (defined('WP_DEBUG') && WP_DEBUG) {
			// error_log( 'webservice response as array:' );
			// error_log( print_r( $data, true ) );
		}

		// Init data var
		$dailyplanning_bestprice = [];
		$dailyplanning_bestprice_year = null;

		$interval = new DateInterval('P1D');

		if (!empty($data)) {
			if (defined('WP_DEBUG') && WP_DEBUG) {
				error_log('Data responsee');
			}
			if (isset($data)) {
				if (defined('WP_DEBUG') && WP_DEBUG) {
					error_log('data success');
				}

				if (isset($data['ratePlans'])) {
					if (
						isset($data['ratePlans'][0]['hotels'])
						&& is_array($data['ratePlans'][0]['hotels'])
					) {

						// TODO boucle for pour les 2 tableaux !!!
						foreach ($data['ratePlans'][0]['hotels'][0]['entities'] as $entity) {
							if (defined('WP_DEBUG') && WP_DEBUG) {
								error_log('******************Entity: roomId=' . $entity['roomId'] . ' rateId=' . $entity['rateId']);
								error_log( print_r($entity,true));

							}

							$properties = $entity;

							if (defined('WP_DEBUG') && WP_DEBUG) {
								// error_log( '******properties before:');
								// error_log( print_r($properties,true));
							}
							//  TODO voir si nécessaire ?? 
							if (!isset($properties[0])) {
								if (defined('WP_DEBUG') && WP_DEBUG) {
									error_log('properties not multiple');
								}
								$tmp = $properties;
								unset($properties);
								$properties[0] = $tmp;
							}

							if (defined('WP_DEBUG') && WP_DEBUG) {
								// error_log( '******properties after:');
								// error_log( print_r($properties,true));
							}
							$dailyplanning_bestprice_entity = [];
							foreach ($properties as $property) {
								if (defined('WP_DEBUG') && WP_DEBUG) {
									error_log( '***property:');
									error_log( print_r($property,true));
								}

								if (!empty($property)) {
									foreach ($property as $key => $period) {

										$propertyname = $key;
										$attributes = (isset($period[$key]) ? $period[$key] : $period);

										if (is_array($attributes)) {
											foreach ($attributes as $attribute) {
												if (!empty($attribute['start']) && ! empty($attribute['end']) && !empty($attribute['value'])){
												// error_log(print_r($attribute, true));
												error_log($propertyname . ': beginDate=' . $attribute['start'] . ' endDate=' . $attribute['end'] . ' value=' . $attribute['value']);}


												$begindate = Datetime::createFromFormat('Y-m-d', $attribute['start']);
												$enddate   = Datetime::createFromFormat('Y-m-d', $attribute['end']);
												$value   = $attribute['value'];

												$daterange = new DatePeriod($begindate, $interval, $enddate);

												/* @var $date Datetime */
												foreach ($daterange as $date) {
													//error_log( 'date: ' . $date->format( 'Y-m-d' ) );
													if (empty($dailyplanning_bestprice_entity[$date->format('Y-m-d')])) {
														$dailyplanning_bestprice_entity[$date->format('Y-m-d')] = array();
													}
													$dailyplanning_bestprice_entity[$date->format('Y-m-d')][$propertyname] = $value;
												}
												
											}
										}
									}
								}
							}


							//}

							ksort($dailyplanning_bestprice_entity);
							if (defined('WP_DEBUG') && WP_DEBUG) {
								// error_log('dailyplanning_bestprice_entity:');
								// error_log(print_r($dailyplanning_bestprice_entity, true));
							}


							foreach ($dailyplanning_bestprice_entity as $date => $attributes) {
								// if($date == '2018-07-05'){
								if(defined('WP_DEBUG') && WP_DEBUG){
								error_log('*roomid: '.$entity['roomId']);
								error_log('*Date: '.$date);
								error_log('*Price: '.@$attributes['totalPrice']);
								error_log('*Status: '.@$attributes['status']);
								}
								if (@$attributes['status'] !== 'NotAvailable' && !empty($attributes['totalPrice'])) {

									$attributes['Date'] = $date;

									// Check year price overall

									// Init overall year price
									if (empty($dailyplanning_bestprice_year['Overall']) && empty($dailyplanning_bestprice_year['Overall']['totalPrice'])) {
										if (defined('WP_DEBUG') && WP_DEBUG) {
											error_log('Overall is empty');
										}
										$dailyplanning_bestprice_year['Overall'] = $attributes;
									}

									error_log('attributes dailyplanning_best_price_year');
											// error_log(print_r($attributes, true));
											// error_log('old price:' . $dailyplanning_bestprice_year['Overall']['totalPrice']);
									
									// Compare existing overall year totalPrice
									if (
										!empty($dailyplanning_bestprice_year['Overall']) && !empty($dailyplanning_bestprice_year['Overall']['totalPrice']) &&
										@$dailyplanning_bestprice_year['Overall']['totalPrice'] > $attributes['totalPrice']
									)
									 {
										if (defined('WP_DEBUG') && WP_DEBUG) {
											// error_log('New price: inferior');
											// error_log(print_r($attributes, true));
											// error_log('old price:' . $dailyplanning_bestprice_year['Overall']['Price']);
										}
										$dailyplanning_bestprice_year['Overall'] = $attributes;
									}
									
									// Check if price date has passed
									/*if(!empty($dailyplanning_bestprice_year['Overall']) && !empty($dailyplanning_bestprice_year['Overall']['Date']) &&
									   @$dailyplanning_bestprice_year['Overall']['Date'] < date('Y-m-d')
									){
										if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
											error_log('New price: date passed');
											error_log(print_r($attributes, true));
											error_log('old date:'.$dailyplanning_bestprice_year['Overall']['Date']);
										}
										$dailyplanning_bestprice_year['Overall'] = $attributes;
									}*/


									// Check year price Room

									// Init overall year price Room
									if (empty($dailyplanning_bestprice_year['Room' . $entity['roomId']]) && empty($dailyplanning_bestprice_year['Room' . $entity['roomId']]['totalPrice'])) {
										$dailyplanning_bestprice_year['Room' . $entity['roomId']] = $attributes;
										error_log('dailyplanning_bestprice_year');
										error_log(print_r($dailyplanning_bestprice_year, true));
									}
									// Compare existing overall year price Room
									if (!empty($dailyplanning_bestprice_year['Room' . $entity['roomId']]) && !empty($dailyplanning_bestprice_year['Room' . $entity['roomId']]['totalPrice']) && $dailyplanning_bestprice_year['Room' . $entity['roomId']]['totalPrice'] > $attributes['totalPrice']) {
										$dailyplanning_bestprice_year['Room' . $entity['roomId']] = $attributes;
										error_log('dailyplanning_bestprice_year[\'Room\' . $entity[\'roomId\']]');
										error_log(print_r($dailyplanning_bestprice_year['Room' . $entity['roomId']], true));
									}								
									// Check year price Rate

									// Init overall year price Rate
									if (empty($dailyplanning_bestprice_year['Rate' . $entity['rateId']]) && empty($dailyplanning_bestprice_year['Rate' . $entity['rateId']]['Price'])) {
										$dailyplanning_bestprice_year['Rate' . $entity['rateId']] = $attributes;
										error_log('dailyplanning_bestprice_year[\'Rate\' . $entity[\'rateId\']]');
										error_log(print_r($dailyplanning_bestprice_year['Rate' . $entity['rateId']], true));
									}

								
									// Compare existing overall year price Rate
									if (!empty($dailyplanning_bestprice_year['Rate' . $entity['rateId']]) && !empty($dailyplanning_bestprice_year['Rate' . $entity['rateId']]['totalPrice']) && $dailyplanning_bestprice_year['Rate' . $entity['rateId']]['totalPrice'] > $attributes['totalPrice']) {
										$dailyplanning_bestprice_year['Rate' . $entity['rateId']] = $attributes;
										error_log('dailyplanning_bestprice_year[\'Rate\' . $entity[\'rateId\']]');
										error_log(print_r($dailyplanning_bestprice_year['Rate' . $entity['rateId']], true));
									}

									// Check month price
									if (!empty($dailyplanning_bestprice[$date]['totalPrice'])) {
										error_log('*Current Best totalPrice: '.$dailyplanning_bestprice[$date]['totalPrice']);
										if (
											$attributes['totalPrice'] < $dailyplanning_bestprice[$date]['totalPrice']
										) {
											error_log('*Price Inferior to Current Best Price: '.$attributes['totalPrice']);
											$dailyplanning_bestprice[$date]['Price'] = $attributes['totalPrice'];
											error_log('*New Best Price: '.$dailyplanning_bestprice[$date]['totalPrice']);
										}
									} else {
										if (empty($dailyplanning_bestprice[$date])) {
											$dailyplanning_bestprice[$date] = array();
										}
										$dailyplanning_bestprice[$date]['totalPrice'] = $attributes['totalPrice'];
										$dailyplanning_bestprice[$date] = $dailyplanning_bestprice_entity[$date];
										error_log('*Setting  Best Price: '.$dailyplanning_bestprice[$date]['totalPrice']);
									}
								}
								// }

							}
						}
					}
				}
			}
		}

		if (defined('WP_DEBUG') && WP_DEBUG) {
			//error_log('dailyplanning_bestprice:');
			//error_log(print_r($dailyplanning_bestprice, true));
		}
		// Save Month to check data
		update_option('tmsm-availpro-bestprice-' . $monthtocheck, $dailyplanning_bestprice);

		// Check year best price
		$bestprice_year = get_option('tmsm-availpro-bestprice-year', false);
		if (defined('WP_DEBUG') && WP_DEBUG) {
			error_log('dailyplanning_bestprice_year:');
			error_log(print_r($dailyplanning_bestprice_year, true));
		}
		if (($bestprice_year === false || $bestprice_year === '') && !empty($dailyplanning_bestprice_year)) {
			$bestprice_year = $dailyplanning_bestprice_year;
			if (defined('WP_DEBUG') && WP_DEBUG) {
				error_log('Init bestprice_year');
			}
		} else {
			if (is_array($dailyplanning_bestprice_year)) {
				foreach ($dailyplanning_bestprice_year as $bestprice_year_item_key => $bestprice_year_item_value) {
					if (defined('WP_DEBUG') && WP_DEBUG) {
						error_log('key bestprice_year: ' . $bestprice_year_item_key);
						error_log('isset:' . isset($dailyplanning_bestprice_year[$bestprice_year_item_key]));
						error_log('price:' . (@$bestprice_year[$bestprice_year_item_key]['totalPrice'] > @$dailyplanning_bestprice_year[$bestprice_year_item_key]['totalPrice']));
						error_log('best:' . @$bestprice_year[$bestprice_year_item_key]['totalPrice']);
						error_log('current:' . @$dailyplanning_bestprice_year[$bestprice_year_item_key]['totalPrice']);
						error_log('date:' . (@$bestprice_year[$bestprice_year_item_key]['Date'] < date('Y-m-d')));
						error_log('date best:' . @$bestprice_year[$bestprice_year_item_key]['Date']);
						error_log('date current:' . date('Y-m-d'));
					}

					if (
						!isset($bestprice_year[$bestprice_year_item_key])
						||
						(
							isset($dailyplanning_bestprice_year[$bestprice_year_item_key])
							&&
							(
								@$bestprice_year[$bestprice_year_item_key]['totalPrice'] > @$dailyplanning_bestprice_year[$bestprice_year_item_key]['totalPrice']
								||
								@$bestprice_year[$bestprice_year_item_key]['Date'] < date('Y-m-d')
							)
						)

					) {
						$bestprice_year[$bestprice_year_item_key] = $dailyplanning_bestprice_year[$bestprice_year_item_key];
						if (defined('WP_DEBUG') && WP_DEBUG) {
							error_log('New bestprice_year');
							error_log(print_r($dailyplanning_bestprice_year[$bestprice_year_item_key], true));
						}
					} else {
						if (defined('WP_DEBUG') && WP_DEBUG) {
							error_log('Not best bestprice_year');
						}
					}
				}
			}
		}

		update_option('tmsm-availpro-bestprice-year', $bestprice_year);


		/*$bestprice_year = get_option( 'tmsm-availpro-bestprice-year', false );
		if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
			error_log('$dailyplanning_bestprice_year:');
			error_log(print_r($dailyplanning_bestprice_year, true));
		}

		if(($bestprice_year === false || $bestprice_year === '') && $dailyplanning_bestprice_year !== null){
			update_option('tmsm-availpro-bestprice-year', $dailyplanning_bestprice_year);
			if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
				error_log('Init bestprice_year');
				error_log(print_r($dailyplanning_bestprice_year, true));
			}
		}
		else{
			if(isset($dailyplanning_bestprice_year) && @$bestprice_year['Price'] > @$dailyplanning_bestprice_year['Price']){
				update_option('tmsm-availpro-bestprice-year', $dailyplanning_bestprice_year);
				if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
					error_log('New bestprice_year');
					error_log(print_r($dailyplanning_bestprice_year, true));
				}
			}
			else{
				error_log('Not best bestprice_year');
			}
		}
		*/

		// Delete previous month data
		$today = new Datetime();
		delete_option('tmsm-availpro-bestprice-' . $today->modify('-1 month')->format('Y-m'));
	}


	/**
	 * Ajax calculate total price
	 *
	 * @since    1.0.0
	 */
	public static function ajax_calculate_totalprice()
	{

		$security = sanitize_text_field($_POST['security']);
		$date_begin = sanitize_text_field($_POST['date_begin']);
		$date_end = sanitize_text_field($_POST['date_end']);
		$nights = sanitize_text_field($_POST['nights']);

		$errors = array(); // Array to hold validation errors
		$jsondata   = array(); // Array to pass back data

		if (defined('WP_DEBUG') && WP_DEBUG) {
			error_log('ajax_calculate_totalprice');
		}

		// Check security
		if (empty($security) || !wp_verify_nonce($security, 'tmsm-availpro-calculatetotal-nonce-action')) {
			$errors[] = __('Token security not valid', 'tmsm-availpro');
			if (defined('WP_DEBUG') && WP_DEBUG) {
				error_log('Ajax security not OK');
			}
		} else {
			if (defined('WP_DEBUG') && WP_DEBUG) {
				error_log('Ajax security OK');
			}

			check_ajax_referer('tmsm-availpro-calculatetotal-nonce-action', 'security');

			// Check date begin
			if (empty($date_begin)) {
				$errors[] = __('Date is empty', 'tmsm-availpro');
				if (defined('WP_DEBUG') && WP_DEBUG) {
					error_log('Date is empty');
				}
			}
			// Check nights number
			if (empty($nights)) {
				$errors[] = __('Nights number are empty', 'tmsm-availpro');
				if (defined('WP_DEBUG') && WP_DEBUG) {
					error_log('Nights number is empty');
				}
			}


			// All rates
			$rates = ['accommodation', 'ota'];
			$options = get_option('tmsm-availpro-options', false);
			foreach ($rates as $rate) {
				$rateids = $options[$rate . 'rateids'];

				if (!empty($rateids)) {
					// Calculate price
					$webservice = new Tmsm_Availpro_Webservice();
					$response   = $webservice->get_stayplanning($date_begin, $nights, $rateids);
					$data = $response;
					if (defined('WP_DEBUG') && WP_DEBUG) {
						// error_log('data:');
						// error_log(print_r($data, true));
						// error_log(print_r($data['ratePlans'][0]['hotels'][0]['entities'], true));
					}

					// Init data var
					$dailyplanning_bestprice = [];
					if (!empty($data)) {
						if (defined('WP_DEBUG') && WP_DEBUG) {
							error_log('Data responsee');
						}
						//TODO voir car checker directement 

						// if ( isset( $data['response']['success'] ) ) {
						// 	if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
						// 		error_log( 'data success' );
						// 	}

						if (isset($data['ratePlans'])) {
							//TODO voir une boucle for pour le ratePlans['0'] ?
							if (
								isset($data['ratePlans'][0]['hotels'])
								&& is_array($data['ratePlans'][0]['hotels'])
							) {
								// Boucle pour chaque ratePlans
								for ($i = 0; $i < count($data['ratePlans']); $i++) {
									// Boucle pour chaque ratePlans => hotels
									for ($h = $i; $h < count($data['ratePlans'][$i]['hotels']); $h++) {
										// Boucle pour chaque entitées de chaque hotel et de chaque ratePlans
										foreach ($data['ratePlans'][$i]['hotels'][$h]['entities'] as $entity) {
											if (defined('WP_DEBUG') && WP_DEBUG) {
												error_log('Entity: roomId=' . $entity['roomId'] . ' rateId=' . $entity['rateId']);
											}
											error_log('$entity');
											error_log(print_r($entity, true));
											// $properties = $entity;
											// if (!isset($properties[0])) {
											// 	if (defined('WP_DEBUG') && WP_DEBUG) {
											// 		error_log('properties not multiple');
											// 	}
											// 	$tmp = $properties;
											// 	unset($properties);
											// 	$properties[0] = $tmp;
											// }
											// error_log('$properties');
											// error_log(print_r($properties, true));
											$dailyplanning_bestprice_entity = [];
											// $dailyplanning_bestprice_entity[] = $entity;
											$dailyplanning_bestprice_entity = $entity;
											ksort($dailyplanning_bestprice_entity);
											if (defined('WP_DEBUG') && WP_DEBUG) {
												error_log('dailyplanning_bestprice_entity:');
												error_log(print_r($dailyplanning_bestprice_entity, true));
											}
											// Merge data
											if (empty($dailyplanning_bestprice) && @$dailyplanning_bestprice_entity['status'] !== 'NotAvailable') {
												error_log('Je passe ici *****');
												$dailyplanning_bestprice = $dailyplanning_bestprice_entity;
											} else {
												if (!empty($dailyplanning_bestprice_entity['totalPrice']) && !empty($dailyplanning_bestprice['totalPrice'])) {
													// New totalPrice is less than merged data
													if (
														$dailyplanning_bestprice_entity['totalPrice'] < $dailyplanning_bestprice['totalPrice']
														&& @$dailyplanning_bestprice_entity['status'] !== 'NotAvailable'
													) {
														$dailyplanning_bestprice = $dailyplanning_bestprice_entity;
													}
												}
											}
											// if (empty($dailyplanning_bestprice) && @$dailyplanning_bestprice_entity[0][0]['status'] !== 'NotAvailable') {
											// 	error_log('Je passe ici *****');
											// 	$dailyplanning_bestprice = $dailyplanning_bestprice_entity;
											// } else {
											// 	if (!empty($dailyplanning_bestprice_entity[0][0]['totalPrice']) && !empty($dailyplanning_bestprice[0][0]['totalPrice'])) {
											// 		// New totalPrice is less than merged data
											// 		if (
											// 			$dailyplanning_bestprice_entity[0][0]['totalPrice'] < $dailyplanning_bestprice[0][0]['totalPrice']
											// 			&& @$dailyplanning_bestprice_entity['status'] !== 'NotAvailable'
											// 		) {
											// 			$dailyplanning_bestprice = $dailyplanning_bestprice_entity;
											// 		}
											// 	}
											// }
										}
									}
								}
							}
						}
					}
					if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
						// // TODO commenter !
						// error_log('dailyplanning_bestprice:');
						// error_log(print_r($dailyplanning_bestprice, true));
					}

					$totalprice = null;
					$fmt = new NumberFormatter('fr_FR', NumberFormatter::CURRENCY);
					if (!empty($dailyplanning_bestprice) && @$dailyplanning_bestprice['status'] !== 'NotAvailable') {
						$totalprice = $dailyplanning_bestprice['totalPrice'];
						$jsondata['data'][$rate] = [
							'totalprice' => $fmt->formatCurrency($totalprice, 'EUR'),
						];
					} 
					// if (!empty($dailyplanning_bestprice[0][0]) && @$dailyplanning_bestprice[0][0]['status'] !== 'NotAvailable') {
					// 	$totalprice = $dailyplanning_bestprice[0][0]['totalPrice'];
					// 	$jsondata['data'][$rate] = [
					// 		'totalprice' => $fmt->formatCurrency($totalprice, 'EUR'),
					// 	];
					// } 
					else {
						if ($rate == 'accommodation') {
							$errors[] = __('No availability', 'tmsm-availpro');
						}
					}
				} else {
					if (defined('WP_DEBUG') && WP_DEBUG) {
						error_log('rateids empty:');
					}
				}
			}
		}

		// if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
		// 	//TODO commenter !
		// 	error_log('jsondata:');
		// 	error_log(print_r($jsondata, true));
		// }

		// Return a response
		if (!empty($errors)) {
			$jsondata['success'] = false;
			$jsondata['errors']  = $errors;
		} else {
			$jsondata['success'] = true;
		}

		if (defined('WP_DEBUG') && WP_DEBUG) {
			error_log('json data:');
			error_log(print_r($jsondata, true));
		}

		wp_send_json($jsondata);
		wp_die();
	}

	/**
	 * WP Rocket: Filters inline JS excluded from being combined
	 *
	 * @param array $excluded_inline
	 *
	 * @return array
	 */
	function rocket_excluded_inline_js_content(array $excluded_inline)
	{

		$excluded_inline[] = 'tmsm_availpro_params';

		return $excluded_inline;
	}
}
