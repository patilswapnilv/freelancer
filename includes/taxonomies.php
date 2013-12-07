<?php

/**
 * File for registering custom taxonomies.
 *
 * @package    	Freelancer/Includes/Taxonomies
 * @since      	0.2.0
 * @author     	Pulido Pereira Nuno Ricardo <pereira@nunoapps.com>
 * @link       	http://nunoapps.com/plugins/freelancer
 * @license   	GPL-2.0+
 */

/* Register taxonomies on the 'init' hook. */
add_action( 'init', 'freelancer_register_taxonomies' );

/**
 * Register taxonomies for the plugin.
 *
 * @since  0.2.0
 * @access public
 * @return void.
 */
function freelancer_register_taxonomies()  {

	$labels = array(
		'name'							=> _x( 'Invoice Status', 'Taxonomy General Name', 'freelancer' ),
		'singular_name'					=> _x( 'Invoice Status', 'Taxonomy Singular Name', 'freelancer' ),
		'menu_name'						=> __( 'Invoice Statuses', 'freelancer' ),
		'all_items'						=> __( 'All Invoice Statuses', 'freelancer' ),
		'parent_item'					=> __( 'Parent Invoice Status', 'freelancer' ),
		'parent_item_colon'				=> __( 'Parent Invoice Status:', 'freelancer' ),
		'new_item_name'					=> __( 'New Invoice Status', 'freelancer' ),
		'add_new_item'					=> __( 'Add New Invoice Status', 'freelancer' ),
		'edit_item'						=> __( 'Edit Invoice Status', 'freelancer' ),
		'update_item'					=> __( 'Update Invoice Status', 'freelancer' ),
		'separate_items_with_commas'	=> __( 'Separate invoice statuses with commas', 'freelancer' ),
		'search_items'					=> __( 'Search invoice statuses', 'freelancer' ),
		'add_or_remove_items'			=> __( 'Add or remove invoice status', 'freelancer' ),
		'choose_from_most_used'			=> __( 'Choose from the most used invoice status', 'freelancer' ),
	);

	$capabilities = array(
		'manage_terms'	=> 'manage_invoices',
		'edit_terms'	=> 'manage_invoices',
		'delete_terms'	=> 'manage_invoices',
		'assign_terms'	=> 'edit_invoices',
	);

	$args = array(
		'labels'				=> $labels,
		'hierarchical'			=> false,
		'public'				=> true,
		'show_ui'				=> true,
		'show_admin_column'		=> true,
		'show_in_nav_menus'		=> false,
		'show_tagcloud'			=> false,
		'capabilities'			=> $capabilities,
	);
	
	register_taxonomy( 'invoice_status', 'invoice', $args );

}