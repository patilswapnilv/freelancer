<?php

/**
 * Main admin class.
 *
 * @package    	Freelancer/Admin
 * @since      	0.2.0
 * @author     	Pulido Pereira Nuno Ricardo <pereira@nunoapps.com>
 * @link  		http://nunoapps.com/plugins/freelancer
 * @license     GPL-2.0+
 */

/**
 * Main admin class.
 *
 * @since 	0.2.0
 * @access 	public
 * @return 	void
 */
class Freelancer_Admin {
	
	/**
	 * Instance object.
	 *
	 * @since 	0.2.0
	 * @access 	private
	 * @var 	object
	 */
	private static $instance;

	/**
	 * Instance method.
	 *
	 * @since 	0.2.0
	 * @access 	public
	 * @return 	object
	 */
	public static function instance() {

		if ( ! self::$instance )
			self::$instance = new self;

		return self::$instance;

	}

	/**
	 * Initialize class.
	 *
	 * @since 	0.2.0
	 * @access 	public
	 * @return 	void
	 */
	public function __construct() {

		/* Load and save of user meta on user editing screen. */
		add_action( 'show_user_profile', 		array( $this, 'user_profile_fields' ) );
		add_action( 'edit_user_profile', 		array( $this, 'user_profile_fields' ) );
		add_action( 'personal_options_update', 	array( $this, 'save_user_profile_fields' ) );
		add_action( 'edit_user_profile_update', array( $this, 'save_user_profile_fields' ) );

		/* Add validation for user meta fields. */
		add_action( 'user_profile_update_errors', array( $this, 'validate_user_profile_fields' ), 10, 3 );

	}

	/**
	 * Show custom user meta fields on user screen.
	 *
	 * @since 	0.2.3
	 * @access 	public
	 * @param 	object 	User object
	 * @return 	void
	 */
	public function user_profile_fields( $user ) {

		/* Get plugin user meta. */
		$user_meta = freelancer_get_user_metas( $user->ID );

		?>
		<h3><?php _e( 'Billing / Invoicing Info', 'freelancer' ); ?></h3>

		<table class="form-table">

			<tr>
				<th><label for="freelancer_user_meta-business_name"><?php _e( 'Business Name', 'freelancer' ); ?> <span class="description"><?php _e( '(required)', 'freelancer' ); ?></span></label></th>
				<td>
					<input type="text" name="freelancer_user_meta[business_name]" id="freelancer_user_meta-business-name" value="<?php echo esc_attr( $user_meta['business_name'] ); ?>" class="regular-text" /><br />
					<span class="description"><?php _e( 'Enter your business name.', 'freelancer' ); ?></span>
				</td>
			</tr>

			<tr>
				<th><label for="freelancer_user_meta-business_address"><?php _e( 'Business Address', 'freelancer' ); ?> <span class="description"><?php _e( '(required)', 'freelancer' ); ?></span></label></th>
				<td>
					<input type="text" name="freelancer_user_meta_user_meta[business_address]" id="freelancer_user_meta-business-address" value="<?php echo esc_attr( $user_meta['business_address'] ); ?>" class="large-text" /><br />
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

	/**
	 * Save custom user meta on admin user screen.
	 *
	 * @since 	0.2.3
	 * @access 	public
	 * @param 	int 	User ID
	 * @return 	void
	 */
	public function save_user_profile_fields( $user_id ) {

		if ( ! current_user_can( 'edit_user', $user_id ) )
			return false;

		/* Update user metas */
		freelancer_set_user_metas( $user_id, $_POST['freelancer_user_meta'] );

	}

	/**
	 * Add validation for custom user meta fields for add 'required' feature.
	 *
	 * @since 	0.2.3
	 * @access 	public
	 * @param 	object 	Error
	 * @param 	bool
	 * @param 	object 	User
	 */
	public function validate_user_profile_fields( $errors, $update, $user ) {

		/* User meta from $_POST. */
		$user_meta = $_POST['freelancer_user_meta'];

		/* Get default user metas. */
		$default_user_metas = freelancer_get_default_user_metas();

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

}

Freelancer_Admin::instance();