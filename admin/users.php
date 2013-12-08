<?php

/**
 * Admin user functions for the plugin.
 *
 * @package 	Freelancer
 * @subpackage 	Admin
 * @since      	0.0.1
 * @author     	Pulido Pereira Nuno Ricardo <pereira@nunoapps.com>
 * @copyright  	Copyright (c) 2007 - 2013, Pulido Pereira Nuno Ricardo
 * @link       	http://nunoapps.com/plugins/freelancer
 * @license    	http://www.gnu.org/licenses/gpl-2.0.html
 */

add_action( 'admin_init', 'freelancer_users_setup' );

function freelancer_users_setup() {

	/* Load and save of user meta on user editing screen. */
	add_action( 'show_user_profile', 'freelancer_user_profile_fields' );
	add_action( 'edit_user_profile', 'freelancer_user_profile_fields' );
	add_action( 'personal_options_update', 'freelancer_save_user_profile_fields' );
	add_action( 'edit_user_profile_update', 'freelancer_save_user_profile_fields' );

	/* Add validation for user meta fields. */
	add_action( 'user_profile_update_errors', 'freelancer_validate_user_profile_fields', 10, 3 );

}

function freelancer_user_profile_fields( $user ) {

	/* Get plugin user meta. */
	$user_meta = freelancer_get_user_metas( $user->ID ); ?>

	<h3><?php _e( 'Billing / Invoicing Info', 'freelancer' ); ?></h3>

	<table class="form-table">

		<tr>
			<th><label for="freelancer_user_meta-business_name"><?php _e( 'Business Name', 'freelancer' ); ?> <span class="description"><?php _e( '(required)', 'freelancer' ); ?></span></label></th>
			<td>
				<input type="text" name="freelancer_user_meta[business_name]" id="freelancer_user_meta-business_name" value="<?php echo esc_attr( $user_meta['business_name'] ); ?>" class="regular-text" /><br />
				<span class="description"><?php _e( 'Enter your business name.', 'freelancer' ); ?></span>
			</td>
		</tr>

		<tr>
			<th><label for="freelancer_user_meta-business_address"><?php _e( 'Business Address', 'freelancer' ); ?> <span class="description"><?php _e( '(required)', 'freelancer' ); ?></span></label></th>
			<td>
				<input type="text" name="freelancer_user_meta[business_address]" id="freelancer_user_meta-business_address" value="<?php echo esc_attr( $user_meta['business_address'] ); ?>" class="large-text" /><br />
				<span class="description"><?php _e( 'Enter your business address.', 'freelancer' ); ?></span>
			</td>
		</tr>

		<tr>
			<th><label for="freelancer_user_meta-business_city"><?php _e( 'Business City', 'freelancer' ); ?> <span class="description"><?php _e( '(required)', 'freelancer' ); ?></span></label></th>
			<td>
				<input type="text" name="freelancer_user_meta[business_city]" id="freelancer_user_meta-business_city" value="<?php echo esc_attr( $user_meta['business_city'] ); ?>" class="regular-text" /><br />
				<span class="description"><?php _e( 'Enter your business city.', 'freelancer' ); ?></span>
			</td>
		</tr>

	</table>
	<?php

}

function freelancer_save_user_profile_fields( $user_id ) {

	if ( ! current_user_can( 'edit_user', $user_id ) )
		return false;

	/* Update user metas */
	freelancer_set_user_metas( $user_id, $_POST['freelancer_user_meta'] );

}

function freelancer_validate_user_profile_fields( $errors, $update, $user ) {

	/* User meta from $_POST. */
	$user_meta = $_POST['freelancer_user_meta'];

	/* Get default user metas. */
	$default_user_metas = freelancer_get_default_user_metas();

	/* Set empty new array. */
	$new_user_meta = array();

	/* Validation array. */
	foreach ( $default_user_metas as $key => $value )
		$new_user_meta[$key] = strip_tags( $user_meta[$key] );

	/* Update exists user. */
	if ( $update ) {

		foreach ( $new_user_meta as $key => $value ) {

			if ( ! $value )
				$errors->add( 'freelancer_user_meta_error_' . $key, sprintf(  __('<strong>ERROR:</strong> The field "%s" is required.', 'freelancer' ), ucwords( str_replace( '_', ' ', $key ) ) ) );

		}

	}

	/* Update new user. */
	else {

		foreach ( $new_user_meta as $key => $value ) {

			if ( ! $value )
				$errors->add( 'freelancer_new_user_meta_error_' . $key, sprintf( __('<strong>ERROR:</strong> The field "%s" is required.', 'freelancer' ), ucwords( str_replace( '_', ' ', $key ) ) ) );

		}

	}

}