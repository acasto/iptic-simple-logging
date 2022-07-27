<?php

/**
 * Class to encapsulate functions for purchasing & receiving
 *
 */

namespace Iptic\SL;

class Log {
	
	/**
	 * Returns the name of the log table since we are using static methods
	 *
	 * @since   0.1.0
	 * @return  string
	 */
	private static function _table(): string {
		global $wpdb;
		$tablename = 'ipticsl_log';
		return $wpdb->prefix . $tablename;
	}
	
	/**
	 * Ran at plugin activation and creates the log table and saves db version to options
	 *
	 * @since   0.1.0
	 * @return  void
	 */
	public static function activate(): void {
		global $wpdb;
		
		$version = get_option( 'ipticsl_db_version', '0.1.0' );
		
		$table_name = self::_table();
		$charset_collate = $wpdb->get_charset_collate();
		$sql = "CREATE TABLE $table_name (
			id mediumint UNSIGNED NOT NULL AUTO_INCREMENT UNIQUE,
			time timestamp NOT NULL,
			facility varchar(20),
			level varchar(20),
			user varchar(60),
			message text NOT NULL,
			PRIMARY KEY  (id)
		) $charset_collate;";
		
		// use code below to do db upgrade in the future
		//if ( version_compare( $version, '2.0' ) < 0 ) {
		//$sql = "CREATE TABLE $table_name (
		//	id mediumint UNSIGNED NOT NULL AUTO_INCREMENT UNIQUE,
		//	time timestamp NOT NULL,
		//	facility varchar(20),
		//	level varchar(20),
		//	user varchar(60),
		//	message text NOT NULL,
		//	PRIMARY KEY  (id)
		//	) $charset_collate;";
		//}
		
		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		dbDelta( $sql );
		
		add_option( 'ipticsl_db_version', '0.1.0' );
	}
	
	/**
	 * Logs a message to the database
	 *
	 * @param string $message
	 * @param null $user
	 * @param null $facility
	 * @param null $level
	 *
	 * @return void
	 */
	public static function log( string $message, $user = null, $facility = null, $level = null ): void {
		global $wpdb;
		$data = array(
			'time'      => current_time( 'mysql' ),
			'user'      => $user,
			'facility'  => $facility,
			'level'     => $level,
			'message'   => $message,
		);
		$wpdb->insert( self::_table(), $data );
	}
	
	/**
	 * Returns the log as an array, can be filtered by facility and level
	 *
	 * @param null $facility
	 * @param null $level
	 * @param null $user
	 * @param string $output
	 *
	 * @return  array
	 *@since   0.1.0
	 * @noinspection PhpUnused
	 */
	public static function get_logs( $user = null, $facility = null, $level = null, string $output = 'OBJECT' ): array {
		global $wpdb;
		$table_name = self::_table();
		$where = '';
		if ( $user ) {
			$where .= " AND user = '$user'";
		}
		if ( $facility ) {
			$where .= " AND facility = '$facility'";
		}
		if ( $level ) {
			$where .= " AND level = '$level'";
		}
		$sql = "SELECT * FROM $table_name WHERE 1 $where ORDER BY time DESC";
		
		return $wpdb->get_results( $sql, $output );
	}
	
}