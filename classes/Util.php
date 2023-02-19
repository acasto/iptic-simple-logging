<?php

namespace Iptic\SL;

/**
 * Offers some utility functions to support the static methods throughout the plugin. Many of these fall more under
 * "settings" than what would normally be considered "utility" functions, but should suffice for now.
 */
class Util {
	
	/**
	 * Some constants that define various plugin settings
	 */
	private static string $_plugin_name = 'ipticsl';
	private static string $_plugin_slug = 'iptic-simple-logging';
	private static array $_columns = array(
		'user',
		'facility',
		'level',
		'message',
		'data',
	);
	
	/**
	 * Returns the plugin name
	 *
	 * @since  0.1.0
	 * @return string
	 */
	public static function plugin_name(): string {
		return self::$_plugin_name;
	}
	
	/**
	 * Returns the plugin slug
	 *
	 * @since  0.1.0
	 * @return string
	 */
	public static function plugin_slug(): string {
		return self::$_plugin_slug;
	}

	/**
	 * Returns the name of the log table
	 *
	 * @since   0.1.0
	 * @return  string
	 */
	public static function table(): string {
		global $wpdb;
		$tablename = self::plugin_name() . '_log';
		return $wpdb->prefix . $tablename;
	}

	/**
	 * Returns an array of columns for the log table
	 *
	 * @since 0.1.0
	 * @return array
	 */
	public static function columns(): array {
		return self::$_columns;
	}
	
}