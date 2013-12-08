<?php

/**
 * Settings functions for the plugin.
 *
 * @package 	Freelancer
 * @subpackage 	Includes
 * @since      	0.0.1
 * @author     	Pulido Pereira Nuno Ricardo <pereira@nunoapps.com>
 * @copyright  	Copyright (c) 2007 - 2013, Pulido Pereira Nuno Ricardo
 * @link       	http://nunoapps.com/plugins/freelancer
 * @license    	http://www.gnu.org/licenses/gpl-2.0.html
 */

/**
 * Set default settings.
 *
 * @access public
 * @since 0.0.1
 * @return array
 */
function freelancer_get_default_settings() {

	return array(

		'invoice_counter'			=> 0,
		'invoice_currency_symbol'	=> '€',
		'invoice_prefix'			=> 'INVOICE-',

		'invoice_paypal_currency_code'	=> 'EUR',
		'invoice_paypal_business_email'	=> ''

	);

}

/**
 * Get all options from settings.
 *
 * @since 	0.2.4
 * @access 	public
 * @return 	array 	Settings array.
 */
function freelancer_get_settings() {

	global $freelancer;

	/* Validate presence in global var. */
	if ( ! isset( $freelancer->settings ) )
		$freelancer->settings = get_option( 'freelancer_settings' );

	return $freelancer->settings;

}

/**
 * Get single option from settings.
 *
 * @since 	0.2.4
 * @access 	public
 * @return 	string|null Option value.
 */
function freelancer_get_setting( $key ) {

	global $freelancer;

	/* Get all settings. */
	if ( ! isset( $freelancer->settings ) )
		$freelancer->settings = get_option( 'freelancer_settings' );

	/* Validate setting key exists. */
	return ( isset( $freelancer->settings[ $key ] ) ) ? $freelancer->settings[ $key ] : null;

}

/**
 * Set single option into settings.
 *
 * @since 	0.2.4
 * @access 	public
 * @return 	void
 */
function freelancer_set_setting( $key, $value ) {

	global $freelancer;

	$settings = freelancer_get_settings();

	/* Validate the key into settings array. */
	if ( array_key_exists( $key, $settings ) ) {

		/* Update settings option if key exists into settings array. */
		$settings[$key] = $value;

		/* Set settings in global var. */
		$freelancer->settings = $settings;

		/* Update wordpress database. */
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

	global $freelancer;

	/* Update global var with settings. */
	$freelancer->settings = $settings;

	/* Update wordpress database with settings. */
	update_option( 'freelancer_settings', $settings );

}
