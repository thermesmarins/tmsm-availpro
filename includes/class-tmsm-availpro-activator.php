<?php

/**
 * Fired during plugin activation
 *
 * @link       https://github.com/nicomollet
 * @since      1.0.0
 *
 * @package    Tmsm_Availpro
 * @subpackage Tmsm_Availpro/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Tmsm_Availpro
 * @subpackage Tmsm_Availpro/includes
 * @author     Nicolas Mollet <nico.mollet@gmail.com>
 */
class Tmsm_Availpro_Activator {

	/**
	 * Activate
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {

		if ( ! wp_next_scheduled( 'tmsmavailpro_cronaction' ) ) {
			wp_schedule_event( time(), 'tmsm_availpro_refresh_schedule', 'tmsmavailpro_cronaction' );
		}
	}

}
