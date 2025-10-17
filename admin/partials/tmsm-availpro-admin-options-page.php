<?php
/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @since      1.0.0
 *
 * @package    Tmsm_Availpro
 * @subpackage Tmsm_Availpro/admin/partials
 */
?>
<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>

<p><?php echo '<a href="' . admin_url( 'customize.php') . '">'.__('Customize colors', 'tmsm-availpro').'</a>' ?></p>

<form method="post" action="options.php"><?php
	settings_fields( $this->plugin_name . '-options' );
	do_settings_sections( $this->plugin_name );
	submit_button( __( 'Save options', 'tmsm-availpro' ));

	do_action( 'tmsmavailpro_cronaction' );
	?></form>