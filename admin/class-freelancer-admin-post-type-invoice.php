<?php

/**
 * Admin post type invoice.
 *
 * @package    	Freelancer/Admin/PostType/Invoice
 * @since      	0.2.5
 * @author 		Pulido Pereira Nuno Ricardo <pereira@nunoapps.com>
 * @link  		http://nunoapps.com/plugins/freelancer
 * @license     GPL-2.0+
 */


class Freelancer_Admin_Post_Type_Invoice {

	private static $instance;

	public static function instance() {

		if ( ! self::$instance )
			self::$instance = new self;

		return self::$instance;

	}

	/**
 	 * Construct.
 	 *
 	 * @since 	0.2.0
 	 * @access 	public
 	 * @return 	void
 	 */
 	public function __construct() {

 		new Freelancer_Admin_Post_Type( $this, 'invoice' );

 	}

 	public function load_post_php() {

 		$this->load_post_meta_boxes();
 	
 	}

 	public function load_post_new_php() {

 		$this->load_post_meta_boxes();
 	
 	}

 	/**
 	 * Add meta boxes to post page.
 	 *
 	 * @since 	0.2.1
 	 * @access 	public
 	 * @return 	void
 	 */
 	public function add_meta_boxes() {

 		// Invoice ID Meta Box
 		add_meta_box( 
			'freelancer-invoice-id', 
			__( 'Invoice ID', 'freelancer' ), 
			array( $this, 'invoice_id_meta_box' ), 
			'invoice',
			'side', 
			'high'
		);

		// Invoice Clients Meta Box
		add_meta_box( 
			'freelancer-invoice-client', 
			__( 'Invoice Client', 'freelancer' ), 
			array( $this, 'invoice_client_meta_box' ), 
			'invoice',
			'side', 
			'core'
		);

		// Invoice Status Meta Box
		add_meta_box( 
			'freelancer-invoice-status', 
			__( 'Invoice Status', 'freelancer' ), 
			array( $this, 'invoice_status_meta_box' ), 
			'invoice',
			'side', 
			'core'
		);

		// Invoice Items Meta Box
		add_meta_box( 
			'freelancer-invoice-items', 
			__( 'Invoice Items', 'freelancer' ), 
			array( $this, 'invoice_items_meta_box' ), 
			'invoice',
			'normal', 
			'core'
		);

		/* Remove default tags box. */
		remove_meta_box( 'tagsdiv-invoice_status', 'invoice', 'side' );

 	}
	
	/**
	 * Style adjustments for the manage invoice screen.
	 *
	 * @since  	0.2.0
	 * @access 	public
	 * @return 	void
	 */
	public function admin_head( ) { ?>

		<style type="text/css">
			.edit-php .wp-list-table td.thumbnail.column-invoice_total,
			.edit-php .wp-list-table th.manage-column.column-invoice_total { 
				text-align: center; 
				width: 120px; 
			}

			.edit-php .wp-list-table td.thumbnail.column-invoice_id,
			.edit-php .wp-list-table th.manage-column.column-invoice_id { 
				text-align: center; 
				width: 50px; 
			}
		</style>
	
	<?php }

	/**
     * Filter on the 'request' hook.
     *
     * @since  	0.2.0
     * @access 	public
     * @param  	array  $vars
     * @return 	array
     */
	public function request( $vars ) {

		/* Invoice Client. */
		if ( isset( $vars['orderby'] ) && 'invoice_client' === $vars['orderby'] ) {

			$vars = array_merge(
				$vars,
				array(
					'orderby'  => 'meta_value_num',
					'meta_key' => 'freelancer_invoice_client'
				)
			);
		}

		/* Invoice Total. */
		if ( isset( $vars['orderby'] ) && 'invoice_total' === $vars['orderby'] ) {

			$vars = array_merge(
				$vars,
				array(
					'orderby'  => 'meta_value_num',
					'meta_key' => 'freelancer_invoice_total'
				)
			);
		}

		/* Invoice ID. */
		if ( isset( $vars['orderby'] ) && 'invoice_id' === $vars['orderby'] ) {

			$vars = array_merge(
				$vars,
				array(
					'orderby'  => 'meta_value_num',
					'meta_key' => 'freelancer_invoice_id'
				)
			);
		}

		/* Invoice Client Filter. */
		if ( isset( $_GET['invoice_client'] ) && $_GET['invoice_client'] ) {

			$vars = array_merge(
				$vars,
				array(
					'meta_key'   	=> 'freelancer_invoice_client',
					'meta_value'	=> intval( $_GET['invoice_client'] )
				)
			);

		}

		return $vars;

	}

	/**
	 * Render invoice clients dropdown for filters.
	 *
	 * @since 	0.2.1
	 * @access 	public
	 * @return 	void
	 */
	public function restrict_manage_posts() {

		/* Render invoice clients dropdown for filters. */
		$invoice_client = isset( $_GET['invoice_client'] ) ? esc_attr( $_GET['invoice_client'] ) : '';

		// prepare arguments
		$client_args  = array(
			// search only for Authors role
			'role' => 'Subscriber',
			
			// order results by user_email
			'orderby' => 'user_email'
		);

		// Create the WP_User_Query object
		$client_query = new WP_User_Query( $client_args );
		
		// Get the results
		$clients = $client_query->get_results();

		if ( ! empty( $clients ) ) {

			echo '<select name="invoice_client" class="postform">';

			echo '<option value="" ' . selected( '', $invoice_client, false ) . ' >' . __( 'View all invoice clients', 'freelancer' ) . '</option>';

			foreach ( $clients as $client )
				printf( '<option value="%s"%s>%s</option>', esc_attr( $client->ID ), selected( $client->ID, $invoice_client, false ), esc_html( $client->user_email ) );

			echo '</select>';
		}

		/* USE INVOICE STATUS TAGS FOR THIS */
		$tag   = isset( $_GET['invoice_status'] ) ? esc_attr( $_GET['invoice_status'] ) : '';
		$terms = get_terms( 'invoice_status' );

		if ( ! empty( $terms ) ) {
			echo '<select name="invoice_status" class="postform">';

			echo '<option value="" ' . selected( '', $tag, false ) . ' >' . __( 'View all invoice status', 'freelancer' ) . '</option>';

			foreach ( $terms as $term )
				printf( '<option value="%s"%s>%s (%s)</option>', esc_attr( $term->slug ), selected( $term->slug, $tag, false ), esc_html( $term->name ), esc_html( $term->count ) );

			echo '</select>';
		}

	}

	/**
	 * Save post boxes at runtime.
	 *
	 * @since 	0.2.0
	 * @access 	public
	 * @param 	integer
	 * @param 	object
	 * @return 	void
	 */
	public function save_post( $post_id, $post ) {

		/* Get new post values. */
		$post_meta = $_POST['freelancer_post_meta'];

		/* Validate Invoice Items Array. */
		if ( $post_meta['invoice_total'] != '0.00' ) {

			$invoice_items_old = get_post_meta( $post_id, 'invoice-items', true );
			$invoice_items_new = array();
	 
			$invoice_items_names 	= $post_meta['invoice_items']['name'];
			$invoice_items_qtys 	= $post_meta['invoice_items']['qty'];
			$invoice_items_prices	= $post_meta['invoice_items']['price'];
	 
			$invoice_items_count = count( $invoice_items_names );
	 
			for ( $i = 0; $i < $invoice_items_count; $i++ ) {

				if ( $invoice_items_names[$i] != '' ) {

					$invoice_items_new[$i]['name'] 	= stripslashes( strip_tags( $invoice_items_names[$i] ) );
					$invoice_items_new[$i]['qty'] 	= intval( $invoice_items_qtys[$i] );
					$invoice_items_new[$i]['price'] = number_format( $invoice_items_prices[$i], 2 );
				
				}
			}

			$post_meta['invoice_items'] = $invoice_items_new;

		} else {

			/* Reset invoice_items array to clean list. */
			$post_meta['invoice_items'] = '';

		}

		/* Get the meta value of the custom field key. */
		$meta = array(

			'freelancer_invoice_id'		=> $post_meta['invoice_id'],
			'freelancer_invoice_client'	=> $post_meta['invoice_client'],
			'freelancer_invoice_items'	=> $post_meta['invoice_items'],
			'freelancer_invoice_total'	=> number_format( $post_meta['invoice_total'], 2 )
		
		);
		
		/* Runtime saving post metas. */
		foreach ( $meta as $meta_key => $new_meta_value ) {

			/* Get the meta value of the custom field key. */
			$meta_value = get_post_meta( $post_id, $meta_key, true );

			/* If a new meta value was added and there was no previous value, add it. */
			if ( $new_meta_value && '' == $meta_value )
				add_post_meta( $post_id, $meta_key, $new_meta_value, true );

			/* If the new meta value does not match the old value, update it. */
			elseif ( $new_meta_value && $new_meta_value != $meta_value )
				update_post_meta( $post_id, $meta_key, $new_meta_value );

			/* If there is no new meta value but an old value exists, delete it. */
			elseif ( '' == $new_meta_value && $meta_value )
				delete_post_meta( $post_id, $meta_key, $meta_value );

		}

	}

	/**
	 * Filters the columns on the "invoices" screen.
	 *
	 * @since  	0.2.0
	 * @access 	public
	 * @param  	array  	$post_columns
	 * @return 	array
	 *
	 * @todo 	Update  
	 */
	public function manage_edit_columns( $post_columns ) {

		$screen     = get_current_screen();
		$post_type  = $screen->post_type;
		$columns    = array();
		$taxonomies = array();

		/* Adds the checkbox column. */
		$columns['cb'] = '<input type="checkbox" />';

		/* Add custom columns and overwrite the 'title' column. */
		$columns['title']			= $post_columns['title'];
		$columns['invoice_client']	= __( 'Invoice Client', 'freelancer' );
		
		/* Get taxonomies that should appear in the manage posts table. */
		$taxonomies = get_object_taxonomies( $post_type, 'objects' );
		$taxonomies = wp_filter_object_list( $taxonomies, array( 'show_admin_column' => true ), 'and', 'name' );

		/* Allow devs to filter the taxonomy columns. */
		$taxonomies = apply_filters( "manage_taxonomies_for_{$post_type}_columns", $taxonomies, $post_type );
		$taxonomies = array_filter( $taxonomies, 'taxonomy_exists' );

		/* Loop through each taxonomy and add it as a column. */
		foreach ( $taxonomies as $taxonomy )
			$columns[ 'taxonomy-' . $taxonomy ] = get_taxonomy( $taxonomy )->labels->name;

		/* Add the comments column. */
		$post_status = !empty( $_REQUEST['post_status'] ) ? $_REQUEST['post_status'] : 'all';
		
		if ( post_type_supports( $post_type, 'comments' ) && ! in_array( $post_status, array( 'pending', 'draft', 'future' ) ) )
			$columns['comments'] = '<span class="vers"><div title="' . esc_attr__( 'Comments', 'freelancer' ) . '" class="comment-grey-bubble"></div></span>';

		/* Add after invoice status. */
		$columns['invoice_total']	= __( 'Invoice Total', 'freelancer' );
		$columns['invoice_id']		= __( 'ID', 'freelancer' );

		/* Return the columns. */
		return $columns;

	}

	/**
	 * Adds the 'invoice' columns to the array of sortable columns.
	 *
	 * @since  	0.2.0
	 * @access 	public
	 * @param  	array   $columns
	 * @return 	array
	 */
	public function manage_edit_sortable_columns( $columns ) {

		$columns['invoice_client']	= 'invoice_client';
		$columns['invoice_total']	= 'invoice_total';
		$columns['invoice_id'] 		= 'invoice_id';

		return $columns;

	}

	/**
	 * Add output for custom columns on the "invoice" screen.
	 *
	 * @since  	0.2.0
	 * @access 	public
	 * @param  	string  $column
	 * @param  	int 	$post_id
	 * @return 	void
	 */
	public function manage_posts_custom_column( $column, $post_id ) {

		switch( $column ) {

			case 'invoice_client':

				$invoice_client_id = get_post_meta( $post_id, 'freelancer_invoice_client', true );

				if ( $invoice_client_id ) {

					$client = get_userdata( $invoice_client_id );

					$client_business_name = freelancer_get_user_meta( 'business_name', $client->ID );

					$invoice_client = '<a href="edit.php?post_type=invoice&invoice_client=' . $client->ID . '">' . $client->user_email . '</a><br /><span class="description">' . $client_business_name . '</span>';

				} else {

					$invoice_client = false;

				}

				echo ! empty( $invoice_client ) ? $invoice_client : '&mdash;';

			break;

			case 'invoice_total':

				$invoice_total = get_post_meta( $post_id, 'freelancer_invoice_total', true );

				echo ! empty( $invoice_total ) ? freelancer_get_setting( 'invoice_currency_symbol' ) . $invoice_total : '&mdash;';

			break;

			case 'invoice_id' :

				$invoice_id = get_post_meta( $post_id, 'freelancer_invoice_id', true );

				echo ! empty( $invoice_id ) ? $invoice_id : '&mdash;';

			break;

			/* Just break out of the switch statement for everything else. */
			default :
			break;
		}

	}

	// -------------------------------------------------------------------

	/**
 	 * Load post meta boxes at runtime.
 	 *
 	 * @since 	0.2.1
 	 * @access 	public
 	 * @return 	void
 	 */
 	public function load_post_meta_boxes() {

 		/* Register scripts in post box page. */
		add_action( 'admin_enqueue_scripts', array( $this, 'invoice_post_box_print_scripts' ) );

 	}

	/**
	 * Add Invoice items meta box.
	 *
	 * @since 	0.2.1
	 * @access 	public
	 * @param 	object
	 * @param 	string
	 * @return 	void
	 */
	public function invoice_items_meta_box( $object, $box ) {

		$settings = freelancer_get_settings();

		$invoice_items = get_post_meta( get_the_ID(), 'freelancer_invoice_items', true );
		$invoice_total = get_post_meta( get_the_ID(), 'freelancer_invoice_total', true );

		?>
		<script type="text/javascript">
			jQuery(document).ready(function($) {
				/* No submit from Invoice Items
				$('.metabox_submit').click(function(e) {
					e.preventDefault();
					$('#publish').click();
				});
				*/
				$('#add-row').on('click', function() {
					var row = $('.empty-row.screen-reader-text').clone(true);
					row.removeClass('empty-row screen-reader-text');
					row.addClass('invoice-items');
					row.insertBefore('#invoice-items tbody>tr:last');

					// Update currency for new line.
					freelancer_recalc_totals();

					return false;
				});
				$('.remove-row').on('click', function() {
					$(this).parents('tr').remove();

					// Update when remove one line.
					freelancer_recalc_totals();

					return false;
				});
			 
				$('#invoice-items tbody').sortable({
					opacity: 0.6,
					revert: true,
					cursor: 'move',
					handle: '.sort'
				});

				$('.row_name, .row_qty, .row_price').live('blur', function() {
					freelancer_recalc_totals();
				});

				// Run recalc totals on startup.
				freelancer_recalc_totals();
			});

			function freelancer_recalc_totals() {
				var total = 0;

				jQuery( '.invoice-items' ).each( function( i ) {
					var row_price = parseFloat( jQuery( ".row_price", this ).val() );
					row_price = row_price < 0 || isNaN( row_price ) ? 0.00 : row_price;

					var row_qty = parseFloat( jQuery( ".row_qty", this ).val() );
					row_qty = row_qty < 0 || isNaN( row_qty ) ? 0 : row_qty;

					// Update fields with valid data
					if ( ! isNaN( row_price ) ) {
						jQuery( ".row_price", this ).val( row_price.toFixed(2) );
					}
  
					if ( ! isNaN( row_qty ) ) {
						jQuery( ".row_qty", this ).val( row_qty );
					}

					row_price 	= ! isNaN( row_price ) ? row_price : 0.00;
					row_qty 	= ! isNaN( row_qty ) ? row_qty : 0;
  
					if ( row_price > 0 && row_qty > 0 ) {
						var row_total = ( row_price * row_qty );
					}

					if ( ! row_total ) {
						row_total = 0;
					}

					total += row_total;

					jQuery( ".row_total", this ).html( row_total );
					jQuery( ".row_total", this ).formatCurrency({
						roundToDecimalPlace: 2,
						useHtml: true,
						symbol: "<?php echo $settings['invoice_currency_symbol']; ?>"
					});
				});

				jQuery( '#invoice-total-div' ).html( total );
				jQuery( '#invoice-total-div' ).formatCurrency({
					roundToDecimalPlace: 2,
					useHtml: true,
					symbol: "<?php echo $settings['invoice_currency_symbol']; ?>"
				});
				jQuery( '#invoice-total' ).val( total );
			}
		</script>

		<table id="invoice-items" width="100%">
			<thead>
				<tr>
					<th width="10%"></th>
					<th width="70%"><?php _e( 'Name', 'freelancer' ); ?></th>
					<th width="5%"><?php _e( 'Qty', 'freelancer' ); ?></th>
					<th width="5%"><?php _e( 'Price', 'freelancer' ); ?></th>
					<th width="10%"><?php _e( 'Total', 'freelancer' ); ?></th>
				</tr>
			</thead>
			<tbody>
			<?php
 
				if ( $invoice_items ) :
 
					foreach ( $invoice_items as $field ) :
						?>
							<tr class="invoice-items">
								<td>
									<a class="button button-small button-primary sort">||</a>
									<a class="button remove-row">-</a>
								</td>
								<td><input type="text" class="row_name large-text" name="freelancer_post_meta[invoice_items][name][]" value="<?php if($field['name'] != '') echo esc_attr( $field['name'] ); ?>" /></td>
								<td><input type="text" class="row_qty small-text" name="freelancer_post_meta[invoice_items][qty][]" value="<?php if ($field['qty'] != '') echo esc_attr( $field['qty'] ); ?>" /></td>
								<td><input type="text" class="row_price small-text" name="freelancer_post_meta[invoice_items][price][]" value="<?php if ($field['price'] != '') echo esc_attr( $field['price'] ); ?>" /></td>
								<td><div class="row_total" style="font-weight:bold; text-align:center;">0.00</div></td>
							</tr>
						<?php
					endforeach;
				else :
					// show a blank one
					?>
						<tr class="invoice-items">
							<td>
								<a class="button button-small button-primary sort">||</a>
								<a class="button button-small remove-row">-</a>
							</td>
							<td><input type="text" class="row_name large-text" name="freelancer_post_meta[invoice_items][name][]" /></td>
							<td><input type="text" class="row_qty small-text" name="freelancer_post_meta[invoice_items][qty][]" /></td>
							<td><input type="text" class="row_price small-text" name="freelancer_post_meta[invoice_items][price][]" /></td>
							<td><div class="row_total" style="font-weight:bold; text-align:center;">0.00</div></td>
						</tr>
				<?php endif; ?>
 
				<!-- empty hidden one for jQuery -->
				<tr class="empty-row screen-reader-text">
					<td>
						<a class="button button-small button-primary sort">||</a>
						<a class="button button-small remove-row">-</a>
					</td>
					<td><input type="text" class="row_name large-text" name="freelancer_post_meta[invoice_items][name][]" /></td>
						<td><input type="text" class="row_qty small-text" name="freelancer_post_meta[invoice_items][qty][]" /></td>
					<td><input type="text" class="row_price small-text" name="freelancer_post_meta[invoice_items][price][]" /></td>
					<td><div class="row_total" style="font-weight:bold; text-align:center;">0.00</div></td>
				</tr>
			</tbody>
		</table>
 
		<p>
			<a id="add-row" class="button" href="#"><?php _e( 'Add new item', 'freelancer' ); ?></a>
			<!--<input type="submit" class="button button-primary metabox_submit" value="Save" />-->
		</p>
		<hr />
		<div class="textright"><h2><?php _e( 'Invoice Total:', 'freelancer' ); ?> <span style="font-weight:bold;" id="invoice-total-div"></span></h2></div>
		<input name="freelancer_post_meta[invoice_total]" id="invoice-total" type="hidden" />
		
	<?php }

	/**
	 * Invoice ID meta box.
	 *
	 * @since 	0.2.1
	 * @access 	public
	 * @param 	object
	 * @param 	string
	 * @return 	void
	 */
 	public function invoice_id_meta_box( $object, $box ) {

 		$settings = freelancer_get_settings();

 		$invoice_id = get_post_meta( get_the_ID(), 'freelancer_invoice_id', true );

 		if ( ! $invoice_id )
 			$invoice_id = $settings['invoice_counter'] + 1;

		?>
			<input type="hidden" name="<?php echo FREELANCER_BASE; ?>_nonce" value="<?php echo wp_create_nonce( FREELANCER_BASE ); ?>" />

			<div id="invoice-id-div"><?php echo $settings['invoice_prefix']; ?><?php echo $invoice_id; ?></div>

			<input type="hidden" name="freelancer_post_meta[invoice_id]" value="<?php echo $invoice_id; ?>" />

			<?php do_action( 'freelancer_invoice_id_meta_box', $object, $box ); ?>

		<?php

	}

	/**
	 * Invoice Client meta box.
	 *
	 * @since 	0.2.1
	 * @access 	public
	 * @param 	object
	 * @param 	string
	 * @return 	void
	 */
	public function invoice_client_meta_box( $object, $box ) {

		// Get post meta
		$invoice_client = get_post_meta( get_the_ID(), 'freelancer_invoice_client', true );

		// prepare arguments
		$client_args  = array(
			// search only for Authors role
			'role' => 'Subscriber',
			
			// order results by user_email
			'orderby' => 'user_email'
		);

		// Create the WP_User_Query object
		$client_query = new WP_User_Query( $client_args );
		
		// Get the results
		$clients = $client_query->get_results();
		
		// Check for results
		if ( ! empty( $clients ) ) {

			?>

			<select name="freelancer_post_meta[invoice_client]" id="invoice-client">
				<?php foreach( $clients as $client ) : ?>
					<option value="<?php echo $client->ID; ?>" <?php selected( $client->ID, $invoice_client ); ?>><?php echo $client->user_email; ?></option>
				<?php endforeach; ?>
			</select>
			<!-- When choose a valid client, update with client data -->
			<div id="invoice-client-data"></div>

			<?php

		} else {

			echo __( 'No clients found', 'freelancer' );

		}

		do_action( 'freelancer_invoice_client_meta_box', $object, $box );

	}

	/**
	 * Add Invoice Status Tags on meta box with select for only one choice.
	 *
	 * @since 	0.2.1
	 * @access 	public
	 * @param 	object
	 * @param 	string
	 * @return 	void
	 */
	public function invoice_status_meta_box( $object, $box ) {

		$post_invoice_status = wp_get_object_terms( get_the_ID(), 'invoice_status' );

		$invoice_statuses = get_terms( 'invoice_status', 'hide_empty=0' );

		?>

		<select name="tax_input[invoice_status]">
			
			<?php foreach ( $invoice_statuses as $invoice_status ) : ?>
				<option id="in-invoice_status_tax-<?php echo $invoice_status->term_id; ?>" value="<?php echo $invoice_status->slug; ?>" <?php selected( $post_invoice_status[0]->slug, $invoice_status->slug ); ?>><?php echo $invoice_status->name; ?></option>
			<?php endforeach; ?>

		</select>
		
	<?php }

	/**
	 * Scripts for manage invoice post box.
	 *
	 * @since 	0.2.3
	 * @access 	public
	 * @return 	void
	 */
	public function invoice_post_box_print_scripts() {

		wp_enqueue_script( 'jquery-format_currency', FREELANCER_URI . 'assets/js/jquery.formatCurrency-1.4.0.min.js', array(), FREELANCER_VERSION, true );

	}

}

Freelancer_Admin_Post_Type_Invoice::instance();