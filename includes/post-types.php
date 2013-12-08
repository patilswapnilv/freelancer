<?php

/**
 * Post types functions for the plugin.
 *
 * @package 	Freelancer
 * @subpackage 	Includes
 * @since      	0.0.1
 * @author     	Pulido Pereira Nuno Ricardo <pereira@nunoapps.com>
 * @copyright  	Copyright (c) 2007 - 2013, Pulido Pereira Nuno Ricardo
 * @link       	http://nunoapps.com/plugins/freelancer
 * @license    	http://www.gnu.org/licenses/gpl-2.0.html
 */

/* Register custom post types on the 'init' hook. */
add_action( 'init', 'freelancer_register_post_types' );

/* Filter post updated messages for custom post types. */
add_filter( 'post_updated_messages', 'freelancer_post_updated_messages' );

/* Filter the "enter title here" text. */
add_filter( 'enter_title_here', 'freelancer_enter_title_here', 10, 2 );

/**
 * Register post types.
 *
 * @since 	0.2.0
 * @access 	public
 * @return 	void
 */
function freelancer_register_post_types() {

	$settings = freelancer_get_settings();

	$labels = array(
		'name'					=> _x( 'Invoices', 'Post Type General Name', 'freelancer' ),
		'singular_name'			=> _x( 'Invoice', 'Post Type Singular Name', 'freelancer' ),
		'menu_name'				=> __( 'Invoices', 'freelancer' ),
		'parent_item_colon'		=> __( 'Parent Invoice:', 'freelancer' ),
		'all_items'				=> __( 'All Invoices', 'freelancer' ),
		'view_item'				=> __( 'View Invoice', 'freelancer' ),
		'add_new_item'			=> __( 'Add New Invoice', 'freelancer' ),
		'add_new'				=> __( 'New Invoice', 'freelancer' ),
		'edit_item'				=> __( 'Edit Invoice', 'freelancer' ),
		'update_item'			=> __( 'Update Invoice', 'freelancer' ),
		'search_items'			=> __( 'Search invoices', 'freelancer' ),
		'not_found'				=> __( 'No invoices found', 'freelancer' ),
		'not_found_in_trash'	=> __( 'No invoices found in Trash', 'freelancer' ),
	);

	$capabilities = array(
		// meta caps (don't assign these to roles)
		'edit_post'					=> 'edit_invoice',
		'read_post'					=> 'read_invoice',
		'delete_post'				=> 'delete_invoice',

		// primitive/meta caps
		'create_posts'				=> 'create_invoices',

		// primitive caps used outside of map_meta_cap()
		'edit_posts'				=> 'edit_invoices',
		'edit_others_posts'			=> 'manage_invoices',
		'publish_posts'				=> 'manage_invoices',
		'read_private_posts'		=> 'read',

		// primitive caps used inside of map_meta_cap()
		'read'						=> 'read',
		'delete_posts'				=> 'manage_invoices',
		'delete_private_posts'		=> 'manage_invoices',
		'delete_published_posts'	=> 'manage_invoices',
		'delete_others_posts'		=> 'manage_invoices',
		'edit_private_posts'		=> 'edit_invoices',
		'edit_published_posts'		=> 'edit_invoices'
	);

	$args = array(
		'label'					=> __( 'invoice', 'freelancer' ),
		'description'			=> __( 'Invoices page', 'freelancer' ),
		'labels'				=> $labels,
		'supports'				=> array( 'title', 'editor', 'author', 'custom-fields' ),
		'hierarchical'			=> false,
		'public'				=> true,
		'show_ui'				=> true,
		'show_in_menu'			=> true,
		'show_in_nav_menus'		=> true,
		'show_in_admin_bar'		=> true,
		'menu_position'			=> 83,
		'menu_icon'				=> true,
		'can_export'			=> true,
		'has_archive'			=> false,
		'exclude_from_search'	=> true,
		'publicly_queryable'	=> true,
		'map_meta_cap'			=> true,
		'capabilities'			=> $capabilities
	);

	register_post_type( 'invoice', $args );

}

/**
 * Edit "enter title here" message for post types.
 *
 * @since 	0.2.0
 * @access 	public
 * @param 	string 	$title
 * @param 	object 	$post
 * @return 	string
 */
function freelancer_enter_title_here( $title, $post ) {

	if ( 'invoice' === $post->post_type ) {
		return __( 'Enter invoice title here', 'freelancer' );
	}

	return $title;

}

/**
 * Update messages for post types.
 *
 * @since 	0.2.0
 * @access 	public
 * @param 	array 	$messages
 * @return 	array
 */
function freelancer_post_updated_messages( $messages ) {

	global $post, $post_ID;

	$messages['invoice'] = array(
		0 => '', // Unused. Messages start at index 1.
		1 => sprintf( __( 'Invoice updated. <a href="%s">View invoice</a>', 'freelancer' ), esc_url( get_permalink( $post_ID ) ) ),
		2 => __( 'Custom field updated.', 'freelancer' ),
		3 => __( 'Custom field deleted.', 'freelancer' ),
		4 => __( 'Invoice updated.', 'freelancer' ),
		/* translators: %s: date and time of the revision */
		5 => isset( $_GET['revision'] ) ? sprintf( __( 'Invoice restored to revision from %s', 'freelancer' ), wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
		6 => sprintf( __( 'Invoice published. <a href="%s">View invoice</a>', 'freelancer' ), esc_url( get_permalink( $post_ID ) ) ),
		7 => __( 'Invoice saved.', 'freelancer' ),
		8 => sprintf( __( 'Invoice submitted. <a target="_blank" href="%s">Preview invoice</a>', 'freelancer' ), esc_url( add_query_arg( 'preview', 'true', get_permalink( $post_ID ) ) ) ),
		9 => sprintf( __( 'Invoice scheduled for: <strong>%1$s</strong>. <a target="_blank" href="%2$s">Preview invoice</a>', 'freelancer' ),
		// translators: Publish box date format, see http://php.net/date
		date_i18n( __( 'M j, Y @ G:i' ), strtotime( $post->post_date ) ), esc_url( get_permalink( $post_ID ) ) ),
		10 => sprintf( __( 'Invoice draft updated. <a target="_blank" href="%s">Preview invoice</a>', 'freelancer' ), esc_url( add_query_arg( 'preview', 'true', get_permalink( $post_ID ) ) ) ),
	);

	return $messages;

}