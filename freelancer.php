<?php

/*
 * Freelancer.
 *
 * Make your business more easy. Invoices, Tickets and more!.
 *
 * @package    	Freelancer
 * @since      	0.0.1
 * @author     	Pulido Pereira Nuno Ricardo <pereira@nunoapps.com>
 * @link       	http://nunoapps.com/plugins/freelancer
 * @license   	GPL-2.0+
 *
 * @wordpress-plugin
 * Plugin Name:       	Freelancer
 * Plugin URI:        	http://nunoapps.com/plugins/freelancer
 * Description:       	Make your business more easy. Invoices, Tickets and more!.
 * Version:           	0.0.1
 * Author:            	Pereira Pulido Nuno Ricardo
 * Author URI:        	http://namaless.com
 * Text Domain:       	freelancer
 * License:      		GPL-2.0+
 * License URI:       	http://www.gnu.org/licenses/gpl-2.0.txt
 * Domain Path:       	/languages
 * GitHub Plugin URI: 	https://github.com/namaless/freelancer
 */

/*
	Copyright YEAR  PLUGIN_AUTHOR_NAME  (email : pereira@nunoapps.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

/**
 * Freelancer Main Class.
 */
class Freelancer {

    private static $instance;

    public static function instance() {

        if ( ! self::$instance )
            self::$instance = new self;

        return self::$instance;

    }

    public function __construct() {

        add_action( 'plugins_loaded', array( $this, 'constants' ), 1 );

        add_action( 'plugins_loaded', array( $this, 'i18n' ), 2 );

        add_action( 'plugins_loaded', array( $this, 'includes' ), 3 );

        /* Register activation hook. */
        register_activation_hook( __FILE__, array( $this, 'activation' ) );

        /* Register deactivation hook. */
        register_deactivation_hook( __FILE__, array( $this, 'deactivation' ) );

    }

    public function constants() {

        /* Set the version number of the plugin. */
        define( 'FREELANCER_VERSION', '0.3.0' );

        /* Set the database version number of the plugin. */
        define( 'FREELANCER_DB_VERSION', 1 );

        /* Set constant path to the plugin directory. */
        define( 'FREELANCER_DIR', trailingslashit( plugin_dir_path( __FILE__ ) ) );

        /* Set constant path to the plugin URI. */
        define( 'FREELANCER_URI', trailingslashit( plugin_dir_url( __FILE__ ) ) );

        /* Set constant basename for the plugin. */
        define( 'FREELANCER_BASE', basename( plugin_dir_path( __FILE__ ) ) );

    }

    public function i18n() {

        load_plugin_textdomain( 'freelancer', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );

    }

    public function includes() {

        /* Include frontend files. */
        require_once( FREELANCER_DIR . 'includes/core.php' );
        require_once( FREELANCER_DIR . 'includes/settings.php' );
        require_once( FREELANCER_DIR . 'includes/post-types.php' );
        require_once( FREELANCER_DIR . 'includes/taxonomies.php' );
        require_once( FREELANCER_DIR . 'includes/user-meta.php' );
        require_once( FREELANCER_DIR . 'includes/template.php' );

        /* Include admin files. */
        if ( is_admin() ) {

            // include admin files
            require_once( FREELANCER_DIR . 'admin/class-freelancer-admin.php' );
            require_once( FREELANCER_DIR . 'admin/class-freelancer-admin-settings.php' );
            require_once( FREELANCER_DIR . 'admin/class-freelancer-admin-post-type.php' );
            require_once( FREELANCER_DIR . 'admin/class-freelancer-admin-post-type-invoice.php' );

        }

    }

    public static function activation() {

        require_once( trailingslashit( plugin_dir_path( __FILE__ ) ) . 'freelancer-install.php' );

        freelancer_install();

        $role = get_role( 'administrator' );

        if ( ! empty( $role ) ) {
            $role->add_cap( 'manage_invoices' );
            $role->add_cap( 'create_invoices' );
            $role->add_cap( 'edit_invoices' );
        }

    }

    public static function deactivation() {

        $role = get_role( 'administrator' );

        if ( ! empty( $role ) ) {
            $role->remove_cap( 'manage_invoices' );
            $role->remove_cap( 'create_invoices' );
            $role->remove_cap( 'edit_invoices' );
        }

    }

}

/* Init main class. */
Freelancer::instance();
