<?php

/**
 * Admin meta box settings functions for the plugin.
 *
 * @package 	Freelancer
 * @subpackage 	Admin
 * @since      	0.0.1
 * @author     	Pulido Pereira Nuno Ricardo <pereira@nunoapps.com>
 * @copyright  	Copyright (c) 2007 - 2013, Pulido Pereira Nuno Ricardo
 * @link       	http://nunoapps.com/plugins/freelancer
 * @license    	http://www.gnu.org/licenses/gpl-2.0.html
 */

/* Add the meta boxes for the settings page on the 'add_meta_boxes' hook. */
add_action( 'add_meta_boxes', 'freelancer_settings_page_create_meta_boxes' );

/**
 * Adds the meta boxes to the plugin settings page.
 *
 * @since 0.0.1
 */
function freelancer_settings_page_create_meta_boxes() {

	global $freelancer;

	/* Add the 'About' meta box. */
	add_meta_box( 'freelancer-about', _x( 'About', 'meta box', 'freelancer' ), 'freelancer_meta_box_display_about', $freelancer->settings_page, 'side', 'high' );

	/* Add the 'Donate' meta box. */
	add_meta_box( 'freelancer-donate', _x( 'Like this plugin?', 'meta box', 'freelancer' ), 'freelancer_meta_box_display_donate', $freelancer->settings_page, 'side', 'default' );

	/* Add the 'Support' meta box. */
	add_meta_box( 'freelancer-support', _x( 'Support', 'meta box', 'freelancer' ), 'freelancer_meta_box_display_support', $freelancer->settings_page, 'side', 'low' );

	/* Add the 'Invoice Settings' meta box. */
	add_meta_box( 'freelancer-invoice', _x( 'Invoice Settings', 'meta box', 'freelancer' ), 'freelancer_meta_box_display_invoice', $freelancer->settings_page, 'normal', 'high' );

	/* Add the 'Gateway Paypal' meta box */
	add_meta_box( 'freelancer-gateway-paypal', _x( 'Gateway Paypal Settings', 'meta box', 'freelancer' ), 'freelancer_meta_box_display_gateway_paypal', $freelancer->settings_page, 'normal', 'default' );

}

function freelancer_meta_box_display_about( $object, $box ) {

	$plugin_data = get_plugin_data( FREELANCER_DIR . 'freelancer.php' ); ?>

	<p>
		<strong><?php _e( 'Version:', 'members' ); ?></strong> <?php echo $plugin_data['Version']; ?>
	</p>
	<p>
		<strong><?php _e( 'Description:', 'members' ); ?></strong>
	</p>
	<p>
		<?php echo $plugin_data['Description']; ?>
	</p>

<?php }

function freelancer_meta_box_display_donate( $object, $box ) { ?>

	<p><?php _e( "Here's how you can give back:", 'freelancer' ); ?></p>

	<ul>
		<li><a href="http://wordpress.org/extend/plugins/freelancer" title="<?php esc_attr_e( 'Freelancer on the WordPress plugin repository', 'freelancer' ); ?>"><?php _e( 'Give the plugin a good rating.', 'freelancer' ); ?></a></li>
	</ul>

<?php }

function freelancer_meta_box_display_support( $object, $box ) { ?>

	<p>
		<?php printf( __( 'Support for this plugin is provided via the our site at %s. If you need any help using it, please ask your support questions there.', 'freelancer' ), '<a href="http://nunoapps.com/" title="' . esc_attr__( 'NunoApps Support', 'freelancer' ) . '">' . __( 'NunoApps', 'freelancer' ) . '</a>' ); ?>
	</p>

<?php }

function freelancer_meta_box_display_invoice( $object, $box ) {

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

function freelancer_meta_box_display_gateway_paypal( $object, $box ) {

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
