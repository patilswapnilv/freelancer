<?php

/**
 * Main Post Types class.
 *
 * @package    Freelancer/Admin/PostType
 * @since      0.2.0
 * @author     Pulido Pereira Nuno Ricardo <pereira@nunoapps.com>
 * @link       http://nunoapps.com/plugins/freelancer
 * @license    GPL-2.0+
 */

class Freelancer_Admin_Post_Type {

	public $post_type;
	public $post_type_name;

	public $screen;

     public function __construct( $custom_post_type_object = null, $custom_post_type_name = '' ) {

 		/* Register required variables. */
 		$this->post_type = $custom_post_type_object;
 		$this->post_type_name = $custom_post_type_name;

 		/* Load current screen. */
		add_action( 'load-post.php', array( $this, '_load_current_screen' ) );
		add_action( 'load-post-new.php', array( $this, '_load_current_screen' ) );
		add_action( 'load-edit.php', array( $this, '_load_current_screen' ) );

 		/* Add and save methods for all meta boxes. */
 		add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ) );
		add_action( 'save_post', array( $this, 'save_post' ), 10, 2 );


		/* Load post meta boxes on the post editing screen. */
		add_action( 'load-post.php', array( $this, 'load_post_php' ) );
		add_action( 'load-post-new.php', array( $this, 'load_post_new_php' ) );

		/* Load on edit post the post types. */
		add_action( 'load-edit.php', array( $this, 'load_edit_php' ) );

		/* Modify the columns on the "invoices" screen. */
		add_filter( 'manage_edit-' . $this->post_type_name . '_columns', array( $this, 'manage_edit_columns' ) );
		add_filter( 'manage_edit-' . $this->post_type_name . '_sortable_columns', array( $this, 'manage_edit_sortable_columns' ) );
		add_action( 'manage_' . $this->post_type_name . '_posts_custom_column', array( $this, 'manage_posts_custom_column' ), 10, 2 );

 	}

 	/**
 	 * Save in variable the current screen when load a page.
 	 *
 	 * @since 	0.3.0
 	 * @access 	public
 	 * @return 	object
 	 */
 	public function _load_current_screen() {

 		$this->screen = get_current_screen();
 	
 	}

 	/**
     * Filter on the 'load-edit.php' hook.
     *
     * @since  	0.3.0
     * @access 	public
     * @return 	void
     */
 	public function load_edit_php() {

 		/* Validate the current post type. */
 		if ( ! empty( $this->screen->post_type ) && $this->post_type_name === $this->screen->post_type ) {
 			
 			/* Validate orderby and filters. */
			add_filter( 'request', array( $this, 'request' ) );
			
			/* Add dropdown filters. */
			add_action( 'restrict_manage_posts', array( $this, 'restrict_manage_posts' ) );
			
			/* Add styles and scripts on admin area. */
			add_action( 'admin_head', array( $this, 'admin_head' ) );

 		}

 		/* Validate exists method on extended class. */
 		if ( method_exists( $this->post_type, 'load_post_php' ) )
 			return $this->post_type->load_post_php();
 	}

 	/**
     * Filter on the 'load-post.php' hook.
     *
     * @since  	0.3.0
     * @access 	public
     * @return 	void
     */
 	public function load_post_php() {

 		/* Validate exists method on extended class. */
 		if ( ! method_exists( $this->post_type, 'load_post_php' ) )
 			return;

 		/* Validate the current post type. */
 		if ( ! empty( $this->screen->post_type ) && $this->post_type_name === $this->screen->post_type )
			return $this->post_type->load_post_php();

 	}

 	/**
     * Filter on the 'load-post-new.php' hook.
     *
     * @since  	0.3.0
     * @access 	public
     * @return 	void
     */
 	public function load_post_new_php() {

 		/* Validate exists method on extended class. */
 		if ( ! method_exists( $this->post_type, 'load_post_new_php' ) )
 			return;

 		/* Validate the current post type. */
 		if ( ! empty( $this->screen->post_type ) && $this->post_type_name === $this->screen->post_type )
			return $this->post_type->load_post_new_php();

 	}

 	/**
     * Filter on the 'add_meta_boxes' hook.
     *
     * @since  	0.3.0
     * @access 	public
     * @return 	void
     */
 	public function add_meta_boxes() {

 		/* Validate exists method on extended class. */
 		if ( ! method_exists( $this->post_type, 'add_meta_boxes' ) )
 			return;

 		return $this->post_type->add_meta_boxes();
 	
 	}

 	/**
     * Filter on the 'save_post' hook.
     *
     * @since  	0.3.0
     * @access 	public
     * @param  	integer 	$vars
     * @param 	object 		$post
     * @return 	void
     */
 	public function save_post( $post_id, $post ) {

 		/* Validate exists method on extended class. */
 		if ( ! method_exists( $this->post_type, 'save_post' ) )
 			return;

          //wp_die( '<pre>' . print_r( $_POST, true ) . '</pre>' );

 		/* Verify the current post type with post custom post type. */
 		if ( $this->post_type_name != $post->post_type )
 			return;

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

 		return $this->post_type->save_post( $post_id, $post );

 	}

 	/**
     * Filter on the 'request' hook.
     *
     * @since  	0.3.0
     * @access 	public
     * @param  	array  $vars
     * @return 	array
     */
 	public function request( $vars ) {

 		/* Validate exists method on extended class. */
 		if ( ! method_exists( $this->post_type, 'request' ) )
 			return $vars;

 		/* Validate only for current post type. */
		if ( $this->post_type_name != $vars['post_type'] )
			return $vars;

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

		return $this->post_type->request( $vars );

 	}

 	/**
     * Filter on the 'restrict_manage_posts' hook.
     *
     * @since  	0.3.0
     * @access 	public
     * @return 	void
     */
 	public function restrict_manage_posts() {

 		/* Validate exists method on extended class. */
 		if ( ! method_exists( $this->post_type, 'restrict_manage_posts' ) )
 			return;

 		return $this->post_type->restrict_manage_posts();

 	}

 	/**
     * Filter on the 'admin_head' hook.
     *
     * @since  	0.3.0
     * @access 	public
     * @return 	void
     */
 	public function admin_head() {

 		/* Validate exists method on extended class. */
 		if ( ! method_exists( $this->post_type, 'admin_head' ) )
 			return;

 		return $this->post_type->admin_head();

 	}

     /**
     * Filter on the 'manage_edit_columns' hook.
     *
     * @since       0.3.0
     * @access      public
     * @param       array
     * @return      void
     */
 	public function manage_edit_columns( $columns ) {

 		/* Validate exists method on extended class. */
 		if ( ! method_exists( $this->post_type, 'manage_edit_columns' ) )
 			return $columns;

 		return $this->post_type->manage_edit_columns( $columns );

 	}

     /**
     * Filter on the 'manage_edit_sortable_columns' hook.
     *
     * @since       0.3.0
     * @access      public
     * @param       array
     * @return      void
     */
 	public function manage_edit_sortable_columns( $columns ) {

 		/* Validate exists method on extended class. */
 		if ( ! method_exists( $this->post_type, 'manage_edit_sortable_columns' ) )
 			return $columns;

 		return $this->post_type->manage_edit_sortable_columns( $columns );

 	}

     /**
     * Filter on the 'manage_posts_custom_column' hook.
     *
     * @since       0.3.0
     * @access      public
     * @param       string
     * @param       int
     * @return      void
     */
 	public function manage_posts_custom_column( $column, $post_id ) {

 		/* Validate exists method on extended class. */
 		if ( ! method_exists( $this->post_type, 'manage_posts_custom_column' ) )
 			return;

 		return $this->post_type->manage_posts_custom_column( $column, $post_id );
 	}

 }
