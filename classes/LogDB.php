<?php

/**
 * Class to handle the database functions for the log class
 *
 */

namespace Iptic\SL;

class LogDB {
	
	/**
	 * Returns the plugin name from the Util class
	 *
	 * @since  0.1.0
	 * @return string
	 */
	private static function _plugin_name(): string {
		return Util::plugin_name();
	}
	
	/**
	 * Returns the name of the log table since we are using static methods
	 *
	 * @since   0.1.0
	 * @return  string
	 */
	private static function _table(): string {
		return Util::table();
	}
	
	/**
	 * Returns an array of columns for the log table
	 * This just provides a single reference for so that we don't have to define an array in every
	 * function that needs to iterate over the columns.
	 *
	 * @since 0.1.0
	 * @return array
	 */
	private static function _columns(): array {
		return Util::columns();
	}
	
	/**
	 * Ran at plugin activation and creates the log table and saves db version to options
	 *
	 * @since   0.1.0
	 * @return  void
	 */
	public static function activate(): void {
		global $wpdb;
		
		$version = get_option( self::_plugin_name() . '_db_version', '0.1.0' );
		
		$table_name = self::_table();
		$charset_collate = $wpdb->get_charset_collate();
		$sql = "CREATE TABLE $table_name (
			id mediumint UNSIGNED NOT NULL AUTO_INCREMENT UNIQUE,
			time timestamp NOT NULL,
			message text NOT NULL,
			data text,
			user varchar(60),
			facility varchar(20),
			level varchar(20),
			PRIMARY KEY  (id)
		) $charset_collate;";
		
		// use code below to do db upgrade in the future
		//if ( version_compare( $version, '2.0' ) < 0 ) {
		//$sql = "CREATE TABLE $table_name (
		//  id mediumint UNSIGNED NOT NULL AUTO_INCREMENT UNIQUE,
		//	time timestamp NOT NULL,
		//	message text NOT NULL,
		//	data tinytext,
		//	user varchar(60),
		//	facility varchar(20),
		//	level varchar(20),
		//	PRIMARY KEY  (id)
		//	) $charset_collate;";
		//}
		
		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		dbDelta( $sql );
		
		add_option( self::_plugin_name() . '_db_version', '0.1.0' );
	}
	
	/**
	 * Logs a message to the database
	 *
	 * @param string $message
	 * @param array  $args
	 *
	 * @return void
	 */
	public static function log( string $message, array $args = array() ): void {
		global $wpdb;
		$timezone = !isset($args['tz_local']);
		$sql_data = array(
			'time'      => current_time( 'mysql', $timezone ),
			'user'      => $args['user'] ?? null,
			'facility'  => $args['facility'] ?? null,
			'level'     => $args['level'] ?? null,
			'message'   => $message,
			'data'      => $args['data'] ?? null,
		);
		$wpdb->insert( self::_table(), $sql_data );
	}
	
	/**
	 * Returns the log as an array, can be filtered by facility and level
	 *
	 * @param array $args
	 * @param string $output
	 
	 * @return  array|object|null
	 * @since   0.1.0
	 * @noinspection PhpUnused
	 */
	public static function get_logs( array $args, string $output = 'OBJECT' ) {
		global $wpdb;
		$table_name = self::_table();
		
		// list of columns to check for in the where statement
		$cols = self::_columns();
		
		// figure out the comparison operator to use
		$comp = ( isset($args['comp']) && ( $args['comp'] === 'like' || $args['comp'] === 'LIKE' ) ) ? 'LIKE' : '=';
		// process any order_by args or default to id
		// note: we use id instead of time to maintain order without dealing with high precision or delays
		$order_by = ( isset($args['order_by']) && in_array( $args['order_by'], $cols, true ) ) ? $args['order_by'] : 'id';
		// set the sort order
		$sort = ( isset($args['sort']) && ( $args['sort'] === 'ASC' || $args['sort'] === 'asc' ) ) ? 'ASC' : 'DESC';
		// set a limit if needed
		$limit = ( isset($args['limit']) && is_numeric($args['limit']) ) ? "LIMIT $args[limit]" : '';
		// set an offset if needed
		$offset = ( isset($args['offset']) && is_numeric($args['offset']) ) ? "OFFSET $args[offset]" : '';
		
		// build the where statement in a format that can be used with the $wpdb->prepare() function
		$where = '';
		$where_var[] = '1';
		foreach ( $cols as $col ) {
			if ( isset( $args[$col] ) && $args[$col] !== "" ) {
				$where .= " AND $col $comp %s";
				$where_var[] = $args[$col];
			}
		}
		
		// build the query
		$sql = $wpdb->prepare("SELECT * FROM $table_name WHERE %d $where ORDER BY $order_by $sort $limit $offset", $where_var);
		
		return $wpdb->get_results( $sql, $output );
	}
	
}
