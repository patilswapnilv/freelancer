<?php

/**
 * Main admin setting class.
 *
 * @package    	Freelancer/Admin/Settings
 * @since      	0.2.0
 * @author     	Pulido Pereira Nuno Ricardo <pereira@nunoapps.com>
 * @link       	http://nunoapps.com/plugins/freelancer
 * @license     GPL-2.0+
 */

/**
 * Settings class.
 *
 * @since 	0.2.0
 * @access 	public
 * @return 	void
 */
class Freelancer_Admin_Settings {

	private static $instance;

	public $settings_page = '';

	public static function instance() {

		if ( ! self::$instance )
			self::$instance = new self;

		return self::$instance;
	}

	public function __construct() {

		add_action( 'admin_menu', array( $this, 'admin_menu' ) );

	}

	protected static function add_separator( $position ) {
		global $menu;
		
		$index = 0;
		foreach( $menu as $offset => $section ) {
			if ( substr( $section[2], 0, 9 ) == 'separator' ) {
				$index++;
			}
				
		
			if ( $offset >= $position ) {
				$menu[$position] = array( '','read',"separator{$index}", '', 'wp-menu-separator' );
				break;
			}
		}

		ksort( $menu );
	}

	public function admin_menu() {

		$this->add_separator( 81 );

		$this->settings_page = add_menu_page(
			__( 'Freelancer - Settings', 'freelancer' ),
			__( 'Freelancer', 'freelancer' ),
			apply_filters( 'freelancer_settings_capability', 'manage_options' ),
			'freelancer-settings',
			array( $this, 'settings_page' ),
			false,
			82
		);

		$this->add_separator( 89 );

		if ( ! empty( $this->settings_page ) ) {

			/* Register the plugin settings. */
			add_action( 'admin_init', array( $this, 'register_settings' ) );

			/* Add media for the settings page. */
			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
			add_action( "admin_head-{$this->settings_page}", array( $this, 'print_scripts' ) );

			/* Load the meta boxes. */
			add_action( 'add_meta_boxes_freelancer', array( $this, 'add_meta_boxes' ) );
		}

	}

	public function register_settings() {

		register_setting( 'freelancer-settings', 'freelancer_settings', array( $this, 'validate_settings' ) );

	}

	public function add_meta_boxes() {

		/* Add the 'About' meta box. */
		add_meta_box( 'freelancer-about', _x( 'About', 'meta box', 'freelancer' ), array( $this, 'meta_box_about' ), 'freelancer-settings', 'side', 'high' );

		/* Add the 'Donate' meta box. */
		add_meta_box( 'freelancer-donate', _x( 'Like this plugin?', 'meta box', 'freelancer' ), array( $this, 'meta_box_donate' ), 'freelancer-settings', 'side', 'default' );

		/* Add the 'Support' meta box. */
		add_meta_box( 'freelancer-support', _x( 'Support', 'meta box', 'freelancer' ), array( $this, 'meta_box_support' ), 'freelancer-settings', 'side', 'low' );

		/* Add the 'Invoice Settings' meta box. */
		add_meta_box( 'freelancer-invoice', _x( 'Invoice Settings', 'meta box', 'freelancer' ), array( $this, 'meta_box_invoice' ), 'freelancer-settings', 'normal', 'high' );

		/* Add the 'Gateway Paypal' meta box */
		add_meta_box( 'freelancer-gateway-paypal', _x( 'Gateway Paypal Settings', 'meta box', 'freelancer' ), array( $this, 'meta_box_gateway_paypal' ), 'freelancer-settings', 'normal', 'default' );
	}

	public function validate_settings( $settings ) {

		$settings['invoice_counter']				= intval( $settings['invoice_counter'] );

		$settings['invoice_currency_symbol'] 		= strip_tags( $settings['invoice_currency_symbol'] );
		$settings['invoice_prefix'] 				= strip_tags( $settings['invoice_prefix'] );
		
		$settings['invoice_paypal_currency_code'] 	= strip_tags( $settings['invoice_paypal_currency_code'] );
		$settings['invoice_paypal_business_email'] 	= strip_tags( $settings['invoice_paypal_business_email'] );

		/* Kill evil scripts. */
		if ( ! current_user_can( 'unfiltered_html' ) ) {

			$settings['invoice_currency_symbol'] 		= stripslashes( wp_filter_post_kses( addslashes( $settings['invoice_currency_symbol'] ) ) );
			$settings['invoice_prefix'] 				= stripslashes( wp_filter_post_kses( addslashes( $settings['invoice_prefix'] ) ) );

			$settings['invoice_paypal_currency_code'] 	= stripslashes( wp_filter_post_kses( addslashes( $settings['invoice_paypal_currency_code'] ) ) );
			$settings['invoice_paypal_business_email'] 	= stripslashes( wp_filter_post_kses( addslashes( $settings['invoice_paypal_business_email'] ) ) );
		
		}			

		/* Return the validated/sanitized settings. */
		return $settings;

	}

	/**
	 * Redesign admin settings page for WordPress 3.8 P6 design responsive.
	 *
	 * @since 	0.2.3
	 * @access 	public
	 * @return 	void
	 */
	public function settings_page() {

		$plugin_data = get_plugin_data( FREELANCER_DIR . 'freelancer.php' );

		do_action( 'add_meta_boxes', 'freelancer-settings', $plugin_data );
		do_action( 'add_meta_boxes_freelancer', 'freelancer-settings', $plugin_data );

		?>
		<div class="wrap">

			<?php screen_icon(); ?>

			<h2><?php _e( 'Employer Settings', 'freelancer' ); ?></h2>

			<?php settings_errors(); ?>

			<form method="post" action="options.php">

				<div id="poststuff">

				

					<?php settings_fields( 'freelancer-settings' ); ?>
					<?php wp_nonce_field( 'closedpostboxes', 'closedpostboxesnonce', false ); ?>
					<?php wp_nonce_field( 'meta-box-order', 'meta-box-order-nonce', false ); ?>

					<div id="post-body" class="metabox-holder columns-2">

						<div id="post-body-content">
							<?php do_meta_boxes( 'freelancer-settings', 'normal', $plugin_data ); ?>
						</div><!-- .post-body-content -->

						<div id="postbox-container-1" class="postbox-container">
							<?php do_meta_boxes( 'freelancer-settings', 'side', $plugin_data ); ?>
						</div><!-- #post-container-1 .postbox-container -->

						

					</div><!-- #post-body -->

					<br class="clear" />

					<?php submit_button( esc_attr__( 'Update Settings', 'freelancer' ) ); ?>

				</div><!-- #poststuff -->

			</form>

		</div><!-- .wrap --><?php

	}

	public function enqueue_scripts( $hook_suffix ) {

		if ( $hook_suffix == $this->settings_page ) {
		
			wp_enqueue_script( 'common' );
			wp_enqueue_script( 'wp-lists' );
			wp_enqueue_script( 'postbox' );

			wp_enqueue_style( 'freelancer-admin', FREELANCER_URI . 'assets/css/admin.css', false, FREELANCER_VERSION, 'screen' );
		
		}

	}

	public function print_scripts() { ?>
		<script type="text/javascript">
			jQuery(document).ready( 
				function() {
					jQuery( '.if-js-closed' ).removeClass( 'if-js-closed' ).addClass( 'closed' );
					postboxes.add_postbox_toggles( 'freelancer-settings' );
				}
			);
		</script>
	<?php }

	public function meta_box_about( $object, $box ) { ?>

		<p>
			<strong><?php _e( 'Version:', 'freelancer' ); ?></strong> 
			<?php echo $object['Version']; ?>
		</p>
		
		<p>
			<strong><?php _e( 'Description:', 'freelancer' ); ?></strong> 
			<?php echo $object['Description']; ?>
		</p>

	<?php }

	public function meta_box_donate( $object, $box ) { ?>

		<div>
			<p><?php _e( "Here's how you can give back:", 'freelancer' ); ?></p>

			<ul>
				<li><a href="http://wordpress.org/extend/plugins/freelancer" title="<?php esc_attr_e( 'Freelancer on the WordPress plugin repository', 'freelancer' ); ?>"><?php _e( 'Give the plugin a good rating.', 'freelancer' ); ?></a></li>
			</ul>
		</div>

	<?php }

	public function meta_box_support( $object, $box ) { ?>
	
		<p>
			<?php printf( __( 'Support for this plugin is provided via the our site at %s. If you need any help using it, please ask your support questions there.', 'freelancer' ), '<a href="http://nunoapps.com/" title="' . esc_attr__( 'NunoApps Support', 'freelancer' ) . '">' . __( 'NunoApps', 'freelancer' ) . '</a>' ); ?>
		</p>

	<?php }

	public function meta_box_invoice( $object, $box ) { 

		$settings = freelancer_get_settings(); ?>

		<table class="form-table">
			<tr scope="row">
				<th><label for="freelancer_settings-invoice_counter"><?php _e( 'Actual Invoice Number:', 'freelancer' ); ?></label></th>
				<td>
					<input type="text" class="small-text" name="freelancer_settings[invoice_counter]" id="freelancer_settings-invoice_counter" value="<?php echo intval( $settings['invoice_counter'] ); ?>" />
					<p class="description"><?php _e( 'The actual invoice number. All new invoice is incremental this value.', 'freelancer' ); ?></p>
				</td>	
			</tr>

			<tr scope="row">
				<th><label for="freelancer_settings-invoice_currency_symbol"><?php _e( 'Invoice Currency Symbol:', 'freelancer' ); ?></label></th>
				<td>
					<input type="text" class="small-text" name="freelancer_settings[invoice_currency_symbol]" id="freelancer_settings-invoice_currency_symbol" value="<?php echo esc_attr( $settings['invoice_currency_symbol'] ); ?>" />
					<p class="description"><?php _e( 'Choose your currency symbol for print in your invoices.', 'freelancer' ); ?></p>
				</td>
			</tr>

			<tr scope="row">
				<th><label for="freelancer_settings-invoice_prefix"><?php _e( 'Invoice Prefix:', 'freelancer' ); ?></label></th>
				<td>
					<input type="text" class="option-all" name="freelancer_settings[invoice_prefix]" id="freelancer_settings-invoice_prefix" value="<?php echo esc_attr( $settings['invoice_prefix'] ); ?>" />
					<p class="description"><?php _e( 'Choose your invoice prefix.', 'freelancer' ); ?></p>
				</td>
			</tr>
		</table>

	<?php }

	public function meta_box_gateway_paypal( $object, $box ) {

		$settings = freelancer_get_settings();

		$paypal_currency_codes = array( 'AUD', 'BRL', 'CAD', 'CZK', 'DKK', 'EUR', 'HKD', 'HUF', 'ILS', 'JPY', 'MYR', 'MXN', 'NOK', 'NZD', 'PHP', 'PLN', 'GBP', 'SGD', 'SEK', 'CHF', 'TWD', 'THB', 'TRY', 'USD' );

		?>
		<table class="form-table">
			<tr scope="row">
				<th><label for="freelancer_settings-invoice_paypal_enabled"><?php _e( 'Enable Paypal Payments:', 'freelancer' ); ?></label></th>
				<td>
					<input type="checkbox" name="freelancer_settings[invoice_paypal_enabled]" id="freelancer_settings-invoice_paypal_enabled" value="1" <?php checked( $settings['invoice_paypal_enabled'], 1 ); ?> /> <span class="description"><?php _e( 'Enable paypal gateway.', 'freelancer' ); ?></span>
				</td>
			</tr>

			<tr>
				<th><label for="freelancer_settings-invoice_paypal_currency_code"><?php _e( 'Paypal Currency Code:', 'freelancer' ); ?></label></th>
				<td>
					<select name="freelancer_settings[invoice_paypal_currency_code]">
						<?php foreach ( $paypal_currency_codes as $paypal_currency_code ) : ?>
							<option value="<?php echo $paypal_currency_code; ?>" <?php selected( $settings['invoice_paypal_currency_code'], $paypal_currency_code ); ?>><?php echo $paypal_currency_code; ?></option>
						<?php endforeach; ?>
					</select>
					<p class="description"><?php echo sprintf( __( 'Choose the paypal currency code. You can see here your code: <code><a href="%s">%s</a></code>', 'freelancer' ), 'https://cms.paypal.com/mx/cgi-bin/?cmd=_render-content&content_ID=developer/e_howto_api_nvp_currency_codes', __( 'Paypal Currency Code', 'freelancer' ) ); ?></p>
				</td>
			</tr>

			<tr>
				<th><label for="freelancer_settings-invoice_paypal_business_email"><?php _e( 'Paypal Business Email:', 'freelancer' ); ?></label></th>
				<td>
					<input type="text" class="regular-text" name="freelancer_settings[invoice_paypal_business_email]" id="freelancer_settings-invoice_paypal_business_email" value="<?php echo esc_attr( $settings['invoice_paypal_business_email'] ); ?>" />
					<p class="description"><?php _e( 'Use your business email for take online payments.', 'freelancer' ); ?></p>
				</td>
			</tr>
		</table>

	<?php }
}

Freelancer_Admin_Settings::instance();