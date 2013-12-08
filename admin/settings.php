<?php

/**
 * Admin settings functions for the plugin.
 *
 * @package 	Freelancer
 * @subpackage 	Admin
 * @since      	0.0.1
 * @author     	Pulido Pereira Nuno Ricardo <pereira@nunoapps.com>
 * @copyright  	Copyright (c) 2007 - 2013, Pulido Pereira Nuno Ricardo
 * @link       	http://nunoapps.com/plugins/freelancer
 * @license    	http://www.gnu.org/licenses/gpl-2.0.html
 */

function freelancer_add_menu_separator( $position ) {

	global $menu;
	
	$index = 0;
	
	foreach( $menu as $offset => $section ) {
		
		if ( 'separator' === substr( $section[2], 0, 9 ) ) {
			$index++;
		}
			
	
		if ( $offset >= $position ) {

			$menu[ $position ] = array( '','read', "separator{$index}", '', 'wp-menu-separator' );
			break;
		
		}

	}

	ksort( $menu );

}

add_action( 'admin_menu', 'freelancer_settings_page_setup' );

/**
 * Setup the settings page and menu.
 *
 * @access public
 * @since 0.0.1
 * @return void
 */
function freelancer_settings_page_setup() {

	global $freelancer;

	/* If not settings are avariable, load defaults. */
	if ( false === get_option( 'freelancer_settings' ) )
		add_option( 'freelancer_settings', freelancer_get_default_settings(), '', 'yes' );

	/* Register plugin settings. */
	add_action( 'admin_init', 'freelancer_register_settings' );

	/* Add menu separator top. */
	freelancer_add_menu_separator( 81 );

	/* Add Freelancer settings page. */
	$freelancer->settings_page = add_menu_page(
		esc_attr__( 'Freelancer - Settings', 'freelancer' ),
		esc_attr__( 'Freelancer', 'freelancer' ),
		apply_filters( 'freelancer_settings_capability', 'manage_options' ),
		'freelancer-settings',
		'freelancer_settings_page',
		false,
		82
	);

	/* Add menu separator in foot. */
	freelancer_add_menu_separator( 89 );

	/* Add media for the settings page. */
	add_action( 'admin_enqueue_scripts', 'freelancer_settings_page_media' );
	add_action( "admin_head-{$GLOBALS['freelancer']->settings_page}", 'freelancer_settings_page_scripts' );

	/* Load the meta boxes. */
	add_action( "load-{$GLOBALS['freelancer']->settings_page}", 'freelancer_settings_page_load_meta_boxes' );

	/* Create a hook for adding meta boxes. */
	add_action( "load-{$GLOBALS['freelancer']->settings_page}", 'freelancer_settings_page_add_meta_boxes' );

}

/**
 * Register the settings plugin with Wordpress.
 *
 * @access public
 * @since 0.0.1
 * @return void
 */
function freelancer_register_settings() {

	register_setting( 'freelancer_settings', 'freelancer_settings', 'freelancer_validate_settings' );

}

/**
 * Execute action 'add_meta_boxes' with plugin data. 
 *
 * @access public
 * @since 0.0.1
 * @return void
 */
function freelancer_settings_page_add_meta_boxes() {

	global $freelancer;

	/* Get plugin meta data. */
	$plugin_data = get_plugin_data( FREELANCER_DIR . 'freelancer.php' );

	/* Add plugin data to settings meta box. */
	do_action( 'add_meta_boxes', $freelancer->settings_page, $plugin_data );
}

/**
 * Loads the plugin settings page meta boxes.
 *
 * @access public
 * @since 0.0.1
 * @return void
 */
function freelancer_settings_page_load_meta_boxes() {

	require_once( FREELANCER_DIR . 'admin/meta-box-plugin-settings.php' );

}

/**
 * Function for validating the settings input from the plugin settings page.
 *
 * @access public
 * @since 0.0.1
 * @param array
 * @return array
 */
function freelancer_validate_settings( $input ) {

	$settings['invoice_counter']				= intval( $input['invoice_counter'] );

	$settings['invoice_currency_symbol'] 		= strip_tags( $input['invoice_currency_symbol'] );
	$settings['invoice_prefix'] 				= strip_tags( $input['invoice_prefix'] );
	
	$settings['invoice_paypal_currency_code'] 	= strip_tags( $input['invoice_paypal_currency_code'] );
	$settings['invoice_paypal_business_email'] 	= strip_tags( $input['invoice_paypal_business_email'] );

	/* Kill evil scripts. */
	if ( ! current_user_can( 'unfiltered_html' ) ) {

		$settings['invoice_currency_symbol'] 		= stripslashes( wp_filter_post_kses( addslashes( $input['invoice_currency_symbol'] ) ) );
		$settings['invoice_prefix'] 				= stripslashes( wp_filter_post_kses( addslashes( $input['invoice_prefix'] ) ) );

		$settings['invoice_paypal_currency_code'] 	= stripslashes( wp_filter_post_kses( addslashes( $input['invoice_paypal_currency_code'] ) ) );
		$settings['invoice_paypal_business_email'] 	= stripslashes( wp_filter_post_kses( addslashes( $input['invoice_paypal_business_email'] ) ) );
	
	}

	return $settings;

}

/**
 * Displays the HTML and meta boxes for the plugin settings page.
 *
 * @access public
 * @since 0.0.1
 * @return void
 */
function freelancer_settings_page() {

	global $freelancer; ?>

	<div class="wrap">

		<?php screen_icon(); ?>

		<h2><?php _e( 'Freelancer Settings', 'freelancer' ); ?></h2>

		<?php settings_errors(); ?>

		<form method="post" action="options.php">

			<div id="poststuff">

				<?php settings_fields( 'freelancer_settings' ); ?>
				<?php wp_nonce_field( 'closedpostboxes', 'closedpostboxesnonce', false ); ?>
				<?php wp_nonce_field( 'meta-box-order', 'meta-box-order-nonce', false ); ?>

				<div id="post-body" class="metabox-holder columns-2">

					<div id="post-body-content">
						<?php do_meta_boxes( $freelancer->settings_page, 'normal', null ); ?>
					</div><!-- .post-body-content -->

					<div id="postbox-container-1" class="postbox-container">
						<?php do_meta_boxes( $freelancer->settings_page, 'side', null ); ?>
					</div><!-- #post-container-1 .postbox-container -->


				</div><!-- #post-body -->

				<br class="clear" />

				<?php submit_button( esc_attr__( 'Update Settings', 'freelancer' ) ); ?>

			</div><!-- #poststuff -->

		</form>

	</div><!-- .wrap -->
<?php }

/**
 * Loads needed JavaScript files for handling the meta boxes on the settings page.
 *
 * @access public
 * @since 0.0.1
 * @param string $hook_suffix The hook for the current page in the admin.
 * @return void
 */
function freelancer_settings_page_media( $hook_suffix ) {

	global $freelancer;
	
	if ( isset( $freelancer->settings_page ) && $hook_suffix == $freelancer->settings_page ) {

		wp_enqueue_script( 'common' );
		wp_enqueue_script( 'wp-lists' );
		wp_enqueue_script( 'postbox' );

		wp_enqueue_style( 'freelancer-admin-settings', FREELANCER_URI . 'assets/css/admin-settings.css', false, FREELANCER_VERSION, 'screen' );
	
	}
}

/**
 * Loads JavaScript for handling the open/closed state of each meta box.
 *
 * @access public
 * @since 0.0.1
 * @return void
 */
function freelancer_settings_page_scripts() {

	global $freelancer; ?>

	<script type="text/javascript">
		//<![CDATA[
		jQuery(document).ready( function($) {
			$('.if-js-closed').removeClass('if-js-closed').addClass('closed');
			postboxes.add_postbox_toggles( '<?php echo $freelancer->settings_page; ?>' );
		});
		//]]>
	</script>

<?php }
