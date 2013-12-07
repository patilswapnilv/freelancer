<?php

/**
 * Settings functions.
 *
 * @package    	Freelancer/Includes/Settings
 * @since      	0.2.0
 * @author     	Pulido Pereira Nuno Ricardo <pereira@nunoapps.com>
 * @link       	http://nunoapps.com/plugins/freelancer
 * @license  	GPL-2.0+
 */

/**
 * Get all options from settings.
 *
 * @since 	0.2.4
 * @access 	public
 * @return 	array 	Settings array.
 */
function freelancer_get_settings() {

	return get_option( 'freelancer_settings' );

}

/**
 * Get single option from settings.
 *
 * @since 	0.2.4
 * @access 	public
 * @return 	string|null Option value.
 */
function freelancer_get_setting( $key ) {

	/* Get all settings. */
	$settings = freelancer_get_settings();

	/* Validate setting key exists. */
	return ( isset( $settings[$key] ) ) ? $settings[$key] : null;

}

/**
 * Set single option into settings.
 *
 * @since 	0.2.4
 * @access 	public
 * @return 	void
 */
function freelancer_set_setting( $key, $value ) {

	$settings = freelancer_get_settings();

	if ( array_key_exists( $key, $settings ) ) {

		/* Update settings option if key exists into settings array. */
		$settings[$key] = $value;
		update_option( 'freelancer_settings', $settings );

	}

}

/**
 * Set settings array.
 *
 * @since 	0.2.4
 * @access 	public
 * @return 	void
 */
function freelancer_set_settings( $settings ) {
	update_option( 'freelancer_settings', $settings );
}