<?php

/**
 * Fired during plugin deactivation
 *
 * @link       http://iptic.com
 * @since      0.1.0
 *
 */

namespace Iptic\SL;

defined( 'ABSPATH' ) or exit;

/**
 * Fired during plugin deactivation.
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 *
 * @since      0.1.0
 * @author     Adam Casto <adam@iptic.com>
 */
class Deactivator {

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
		check_admin_referer( "deactivate-plugin_$plugin" );
		
		flush_rewrite_rules();
	}

}
