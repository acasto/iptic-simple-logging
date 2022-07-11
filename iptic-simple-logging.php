<?php

use Iptic\SL\Activator;

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              http://iptic.com
 * @since             0.1.0
 * @package           IpticSL
 *
 * @wordpress-plugin
 * Plugin Name:       Iptic Simple Logging
 * Plugin URI:        http://iptic.com/plugins
 * Description:       A plugin to enable simple logging to the database
 * Version:           0.1.0
 * Author:            Adam Casto
 * Author URI:        http://iptic.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       ipticsl
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

// include and instantiate the autoloader for our classes
require_once( 'classes/Autoload.php' );
new Iptic\SL\Autoload();

// register the activation/deactivation functions
register_activation_hook( __FILE__, '\Iptic\SL\Activator::run' );
register_deactivation_hook( __FILE__, '\Iptic\SL\Deactivator::run' );
register_uninstall_hook( __FILE__, '\Iptic\SL\Uninstall::run' );
