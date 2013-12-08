<?php

/**
 * Install functions for the plugin.
 *
 * @package 	Freelancer
 * @subpackage 	Install
 * @since      	0.0.1
 * @author     	Pulido Pereira Nuno Ricardo <pereira@nunoapps.com>
 * @copyright  	Copyright (c) 2007 - 2013, Pulido Pereira Nuno Ricardo
 * @link       	http://nunoapps.com/plugins/freelancer
 * @license    	http://www.gnu.org/licenses/gpl-2.0.html
 */

/**
 * Install plugin.
 *
 * @since 	0.3.0
 * @access 	public
 * @return 	void
 */
function freelancer_install() {

	/* Add default Invoice statuses taxonomies. */
	freelancer_install_default_taxonomies();

	/* Add default options. */
	freelancer_install_default_options();
	
}

function freelancer_install_default_taxonomies() {

	/* Register taxonomies before add terms. */
	require_once( trailingslashit( plugin_dir_path( __FILE__ ) ) . 'includes/taxonomies.php' );
	freelancer_register_taxonomies();

	$taxonomies = array(
		'invoice_status' => array(
			'paid',
			'unpaid',
			'overdue',
			'draft'
		)
	);

	foreach ( $taxonomies as $taxonomy => $terms ) {
		foreach ( $terms as $term ) {
			if ( ! get_term_by( 'slug', sanitize_title( $term ), $taxonomy ) )
				wp_insert_term( $term, $taxonomy );
		}
	}

}

function freelancer_install_default_options() {

	$settings = array(
		'invoice_counter'				=> 0,
		'invoice_currency_symbol'		=> '€',
		'invoice_prefix'				=> 'INVOICE-',

		'invoice_paypal_enabled'		=> false,
		'invoice_paypal_currency_code'	=> 'EUR',
		'invoice_paypal_business_email'	=> ''
	);

	if ( ! get_option( 'freelancer_settings' ) )
		add_option( 'freelancer_settings', $settings );

}

function freelancer_install_finish() {

	/* Register Custom Post Types. */
	require_once( trailingslashit( plugin_dir_path( __FILE__ ) ) . 'includes/post-types.php' );
	freelancer_register_post_types();

	/* Register taxonomies. */
	require_once( trailingslashit( plugin_dir_path( __FILE__ ) ) . 'includes/taxonomies.php' );
	freelancer_register_taxonomies();

	/* Flush rewrite rules after register custom post types and taxonomies. */
	flush_rewrite_rules();

}