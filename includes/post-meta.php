<?php

/**
 * Post meta functions for the plugin.
 *
 * @package 	Freelancer
 * @subpackage 	Includes
 * @since      	0.0.1
 * @author     	Pulido Pereira Nuno Ricardo <pereira@nunoapps.com>
 * @copyright  	Copyright (c) 2007 - 2013, Pulido Pereira Nuno Ricardo
 * @link       	http://nunoapps.com/plugins/freelancer
 * @license    	http://www.gnu.org/licenses/gpl-2.0.html
 */

function freelancer_get_default_post_metas() {

	return array(

		'invoice_id'		=> '',
		'invoice_client'	=> '',
		'invoice_items'		=> '',
		'invoice_total'		=> ''

	);

}

function freelancer_get_post_metas( $post_id ) {

	global $freelancer;

	if ( ! isset( $freelancer->post_metas ) || ! isset( $freelancer->post_metas[ $post_id ] ) ) {

		$default_post_metas = freelancer_get_default_post_metas();

		foreach ( $default_post_metas as $key => $value ) {

			$freelancer->post_metas[ $post_id ][ $key ] = get_post_meta( $post_id, 'freelancer_' . $key, true );

			update_post_meta( $post_id, 'freelancer_' . $key, $freelancer->post_metas[ $post_id ][ 'freelancer_' . $key ] );
		}
	}

	return $freelancer->post_metas[ $post_id ];
}

function freelancer_get_post_meta( $post_id, $meta_key ) {

	global $freelancer;

	if ( ! isset( $freelancer->post_metas ) || ! isset( $freelancer->post_metas[ $post_id ] ) || ! isset( $freelancer->post_metas[ $post_id ][ $meta_key ] ) )
		$freelancer->post_metas[ $post_id ][ $meta_key ] = get_post_meta( $post_id, 'freelancer_' . $meta_key, true );

	return $freelancer->post_metas[ $post_id ][ $meta_key ];
}

function freelancer_set_post_metas( $post_id, $post_metas ) {

	global $freelancer;

	//$default_post_metas = freelancer_get_default_post_metas();

	foreach ( $post_metas as $key => $value ) {

		$freelancer->post_metas[ $post_id ][ $key ] = $value;

		update_post_meta( $post_id, 'freelancer_' . $key, $value );
	}

}

function freelancer_set_post_meta( $post_id, $meta_key, $meta_value ) {

	global $freelancer;

	$freelancer->post_metas[ $post_id ][ $meta_key ] = $meta_value;

	update_post_meta( $post_id, 'freelancer_' . $meta_key, $meta_value );

}