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
		$plugin = isset( $_REQUEST['plugin'] ) ? $_REQUEST['plugin'] : '';
		check_admin_referer( "activate-plugin_{$plugin}" );
		
		flush_rewrite_rules();
		self::create_table();
		add_option( 'ipticsl_db_version', '0.1.0' );
	}
	
	private static function create_table(): void {
		global $wpdb;
		$table_name = $wpdb->prefix . 'ipticsl_log';
		$charset_collate = $wpdb->get_charset_collate();
		$sql = "CREATE TABLE $table_name (
			id mediumint(9) NOT NULL AUTO_INCREMENT,
			time datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
			message text NOT NULL,
			PRIMARY KEY  (id)
		) $charset_collate;";
		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		dbDelta( $sql );
	}

}
