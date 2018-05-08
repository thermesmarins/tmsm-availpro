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
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

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

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/tmsm-availpro-public.js', array( 'jquery' ), $this->version, false );

	}

	/**
	 * Check Availpro Prices
	 */
	public function checkprices(){

		if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
			error_log('Function checkprices()');
		}

		$lastmonthchecked = get_option('tmsm-availpro-lastmonthchecked', false);
		if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
			error_log('Last month checked: '.$lastmonthchecked);
		}

		// Check if the last checked value was created
		if($lastmonthchecked === false){
			if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
				error_log('Check not initiated yet');
			}
			$monthtocheck = date('Y-m');
		}
		else{
			$lastmonthchecked_object = DateTime::createFromFormat('Y-m', $lastmonthchecked);
			$lastmonthchecked_object->modify('+1 month');
			$lastmonthchecked_limit = new Datetime();
			$lastmonthchecked_limit->modify('+1 year');

			if($lastmonthchecked_object->getTimestamp() >= $lastmonthchecked_limit->getTimestamp()){
				if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
					error_log('Limit month passed');
				}
				$monthtocheck = date('Y-m');
			}
			else{
				$monthtocheck = $lastmonthchecked_object->format('Y-m');
			}
		}

		if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
			error_log('Month to check: '.$monthtocheck);
		}
		// Update last check value
		$result = update_option( 'tmsm-availpro-lastmonthchecked', $monthtocheck, true);
		if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
			error_log('Result saving new month: '.$result);
		}

		$webservice = new Tmsm_Availpro_Webservice();
		$month_planning = $webservice->get_data($monthtocheck);

		if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
			error_log($month_planning);

		}

	}


}
