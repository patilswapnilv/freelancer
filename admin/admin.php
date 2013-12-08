<?php

/**
 * Admin functions for the plugin.
 *
 * @package 	Freelancer
 * @subpackage 	Admin
 * @since      	0.0.1
 * @author     	Pulido Pereira Nuno Ricardo <pereira@nunoapps.com>
 * @copyright  	Copyright (c) 2007 - 2013, Pulido Pereira Nuno Ricardo
 * @link       	http://nunoapps.com/plugins/freelancer
 * @license    	http://www.gnu.org/licenses/gpl-2.0.html
 */

add_action( 'admin_init', 'freelancer_admin_setup' );

function freelancer_admin_setup() {

	/* Include all custom post types views. */
	require_once( FREELANCER_DIR . 'admin/post-types/invoice.php' );

	/* Add and save methods for all meta boxes. */
	add_action( 'add_meta_boxes', 'freelancer_add_meta_boxes' );
	add_action( 'save_post', 'freelancer_save_post', 10, 2 );

	/* Load admin head scripts and styles. */
	add_action( 'admin_head', 'freelancer_admin_head' );

	/* Load on edit post the post types. */
	add_action( 'load-edit.php', 'freelancer_load_edit_post' );

}

function freelancer_load_edit_post() {

	/* Validate orderby and filters. */
	add_filter( 'request', 'freelancer_request' );
		
	/* Add dropdown filters. */
	add_action( 'restrict_manage_posts', 'freelancer_restrict_manage_posts' );

	/* Get current screen to switch custom post types hook. */
	$screen = get_current_screen();

	switch ( $screen->post_type ) {

		case 'invoice':

			/* Modify the columns on the "invoices" screen. */
			add_filter( 'manage_edit-invoice_columns', 'freelancer_invoice_manage_edit_columns' );
			add_action( 'manage_invoice_posts_custom_column', 'freelancer_invoice_manage_posts_custom_column', 10, 2 );
			add_filter( 'manage_edit-invoice_sortable_columns', 'freelancer_invoice_manage_edit_sortable_columns' );

		break;

		default:
		break;

	}

}

function freelancer_restrict_manage_posts() {

	$screen = get_current_screen();

	switch ( $screen->post_type ) {

		case 'invoice':
			return freelancer_invoice_restrict_manage_posts();
		break;

		default:
		break;
	}
}

function freelancer_request( $vars ) {

	/* Default ordering alphabetically. */
	if ( ! isset( $vars['order'] ) && ! isset( $vars['orderby'] ) ) {

		$vars = array_merge(
			$vars,
			array(
				'order'   => 'ASC',
				'orderby' => 'title'
			)
		);

	}

	switch ( $vars['post_type'] ) {

		case 'invoice':
			
			$vars = freelancer_invoice_request( $vars );

		break;
		
		default:
		break;
	}

	return $vars;

}

function freelancer_admin_head() {

	$screen = get_current_screen();

	switch ( $screen->post_type ) {

		case 'invoice':
			freelancer_invoice_admin_head();
		break;

		default:
		break;

	}

}

/**
 * Add all meta boxes on all custom post types.
 *
 * @access public
 * @since 0.0.1
 * @param string
 * @return void
 */
function freelancer_add_meta_boxes( $post_type ) {

	switch ( $post_type ) {

		case 'invoice':
			return freelancer_invoice_add_meta_boxes( $post_type );
		break;
	}

}

function freelancer_save_post( $post_id, $post ) {

	/* Verify the nonce. */
	if ( ! isset( $_POST[ FREELANCER_BASE . '_nonce' ] ) || ! wp_verify_nonce( $_POST[ FREELANCER_BASE . '_nonce'], FREELANCER_BASE ) )
		return;

	/* Get the post type object. */
	$post_type = get_post_type_object( $post->post_type );

	/* Check if the current user has permission to edit the post. */
	if ( ! current_user_can( $post_type->cap->edit_post, $post_id ) )
		return $post_id;

	/* Don't save if the post is only a revision. */
	if ( 'revision' == $post->post_type )
		return;

	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
		return;

	/* Swith save function with custom post type. */
	switch ( $post->post_type ) {

		case 'invoice':
			return freelancer_invoice_save_post( $post_id, $post );
		break;

		default:
		break;
	}

}





