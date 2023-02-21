<?php
/**
 * Plugin Name:       Iptic Simple Logging
 * Plugin URI:        http://iptic.com/plugins
 * Description:       A plugin to enable simple logging to the database
 * Version:           0.1.0
 * Update URI:        https://iptic.com
 * Author:            Adam Casto
 * Author URI:        http://iptic.com
 * Text Domain:       iptic-simple-logging
 * Domain Path:       /languages
 * License:           GPL-3.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-3.0-standalone.html
 *
 * This plugin provides a simple logging mechanism to the database.
 *
 * @link              http://iptic.com
 * @since             0.1.0
 * @package           IpticSL
 * @wordpress-plugin
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

// include and instantiate the autoloader for our classes
require_once( 'classes/Autoload.php' );
new Iptic\SL\Autoload();

// check for updates
new Iptic\SL\CheckUpdates( 'iptic-simple-logging' );

// register the activation/deactivation functions
register_activation_hook( __FILE__, '\Iptic\SL\Activator::run' );
register_uninstall_hook( __FILE__, '\Iptic\SL\Uninstall::run' );

// stuff that needs to be run after everything else is loaded
add_action( 'wp_loaded', static function() {
	// default logging behavior can be disabled with the isl_default_logging filter
	if ( apply_filters( 'isl_default_logging', true ) ) {
		require_once( 'inc/hook_logging.php' );
	}
	// admin section can be disabled with the isl_admin filter
	if ( is_admin() && apply_filters( 'isl_admin', true ) ) {
		new Iptic\SL\Admin();
	}
	
});
