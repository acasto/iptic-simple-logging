<?php

/**
 * Fired during plugin activation
 *
 * @link       http://iptic.com
 * @since      0.1.0
 *
 */

namespace Iptic\SL;

defined( 'ABSPATH' ) or exit;

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      0.1.0
 * @author     Adam Casto <adam@iptic.com>
 */
class Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    0.1.0
	 */
	public static function run(): void {
		// some basic security stuff
		if ( ! current_user_can( 'activate_plugins' ) ) {
			return;
		}
		$plugin = $_REQUEST['plugin'] ?? '';
		check_admin_referer( "activate-plugin_$plugin" );
		
		// create the log table
		LogDB::activate();

	}
	

}
