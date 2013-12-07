<?php

/**
 * Userdata for billing / invoicing feature.
 *
 * @package    	Freelancer/Includes/UserMeta
 * @since      	0.2.3
 * @author     	Pulido Pereira Nuno Ricardo <pereira@nunoapps.com>
 * @link       	http://nunoapps.com/plugins/freelancer
 * @license   	GPL-2.0+
 */

/**
 * Set user meta keys.
 *
 * @since 	0.2.4
 * @access 	public
 * @param 	integer 	User ID
 * @param 	array 		User Meta Array
 * @return 	void
 */
function freelancer_set_user_metas( $user_id, $user_metas ) {

	foreach ( $user_metas as $key => $value )
		update_user_meta( $user_id, 'freelancer_' . $key, $value );

}

/**
 * Get all default user metas.
 *
 * @since 	0.2.3
 * @access 	public
 * @return 	array
 */
function freelancer_get_default_user_metas() {

	return array(
		'business_name'		=> '',
		'business_address'	=> '',
		'business_city'		=> ''
	);

}

/**
 * Get all user metas.
 *
 * @since 	0.3.0
 * @access 	public
 * @param 	int 	User ID.
 * @return 	array 	User metas.
 */
function freelancer_get_user_metas( $user_id ) {

	$default_user_metas = freelancer_get_default_user_metas();

	foreach ( $default_user_metas as $key => $value ) {

		$user_meta[ $key ] = freelancer_get_user_meta( $key, $user_id );

	}

	return $user_meta;

}

/**
 * Get user meta by key.
 *
 * @since 	0.3.0
 * @access 	public
 * @param 	string 	User meta key.
 * @param 	int 	User ID.
 * @return 	mixed 	User meta value.
 */
function freelancer_get_user_meta( $user_meta_key, $user_id ) {

	$user_meta = get_the_author_meta( 'freelancer_' . $user_meta_key, $user_id );

	return ( $user_meta ) ? $user_meta : '';

}

/**
 * Display custom user meta in registration form.
 *
 * @since 	0.2.3
 * @access 	public
 * @return 	void
 */
function nunoapps_freelancer_user_registration_fields() {

	$default_user_meta = freelancer_get_default_user_metas();

	$user_meta = isset( $_POST['freelancer_user_meta'] ) ? $_POST['freelancer_user_meta'] : $default_user_meta;
    
	?>
	<!-- @todo: add a separator from standard wordpress fields. -->
	<br />
	<hr />
	<br />

	<h3><?php _e( 'Business Informations', 'freelancer' ); ?></h3>
	<p class="description"><?php _e( 'All business informations are required.', 'freelancer' ); ?></p>

	<br />

	<p>
		<label for="freelancer_user_meta-business_name"><?php _e( 'Business Name', 'freelancer' ) ?>
			<br />
			<input type="text" name="freelancer_user_meta[business_name]" id="freelancer_user_meta-business_name" class="input" value="<?php echo esc_attr( stripslashes( $user_meta['business_name'] ) ); ?>" size="20" />
		</label>
	</p>

	<p>
		<label for="freelancer_user_meta[business_address]"><?php _e( 'Business Address', 'freelancer' ) ?>
			<br />
			<input type="text" name="freelancer_user_meta[business_address]" id="freelancer_user_meta-business_address" class="input" value="<?php echo esc_attr( stripslashes( $user_meta['business_address'] ) ); ?>" size="20" />
		</label>
	</p>

	<p>
		<label for="freelancer_user_meta[business_city]"><?php _e( 'Business City', 'freelancer' ) ?>
			<br />
			<input type="text" name="freelancer_user_meta[business_city]" id="freelancer_user_meta-business_city" class="input" value="<?php echo esc_attr( stripslashes( $user_meta['business_city'] ) ); ?>" size="20" />
		</label>
	</p>

	<!-- @todo: add a separator from standard wordpress fields. -->
	<br />
	<hr />
	<br />

	<?php

}

/* Show custom user meta in the new user registration. */
add_action( 'register_form', 'freelancer_user_registration_fields' );

/**
 * Validation for new user with custom user meta.
 *
 * @since 	0.2.3
 * @access 	public
 * @param 	object 	Any errors that have been processed up to this point
 * @param 	string 	The sanitized username as entered by the user
 * @param 	string 	The email as entered by the user
 * @return 	object
 */
function freelancer_validate_user_registration_fields( $errors, $sanitized_user_login, $user_email ) {

	$user_meta = $_POST['freelancer_user_meta'];

	foreach ( $user_meta as $key => $value ) {

		if ( empty( $value ) )
			$errors->add( 'freelancer_new_user_meta_error_' . $key, sprintf( __('<strong>ERROR:</strong> %s is required.', 'freelancer' ), ucwords( str_replace( '_', ' ', $key ) ) ) );
	}

    return $errors;

}
/* Add validation for new user registration with custom user meta. */
add_filter( 'registration_errors', 'freelancer_validate_user_registration_fields', 10, 3 );

/**
 * Save custom user meta on admin user screen.
 *
 * @since 	0.2.3
 * @access 	public
 * @param 	int 	User ID
 * @return 	void
 */
function freelancer_save_user_registration_fields( $user_id ) {

	foreach ( $_POST['freelancer_user_meta'] as $key => $value )
		update_user_meta( $user_id, 'freelancer_' . $key, $value );

}

add_action( 'register_post', 'freelancer_save_user_registration_fields' );

