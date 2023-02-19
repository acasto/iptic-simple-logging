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
class Uninstall {

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
		check_admin_referer( 'bulk-plugins' );
		
		// Important: Check if the file is the one
		// that was registered during the uninstall hook.
		if ( __FILE__ !== WP_UNINSTALL_PLUGIN ) {
			return;
		}
		
		// TODO: test this on a remote server since we can't delete locally
		global $wpdb;
		
		// removes the table we created during installation
		$table_name = Util::table();
		$wpdb->query( "DROP TABLE IF EXISTS $table_name" );
		
		// removes the option we created during installation
		delete_option( 'ipticsl_db_version' );

	}
	
}
