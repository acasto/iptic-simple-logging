<?php

namespace Iptic\SL;

/**
 *  Class to abstract the logging functions from the database functions
 */

class Log {
	
	/**
	 * Wrapper method for Iptic Simple Logging class to log a message
	 *
	 * @param $message
	 * @param array $args
	 *
	 * @return void
	 */
	public static function log( $message, array $args  ): void {
		$args['user']     = $args['user'] ?? wp_get_current_user()->user_login;
		$args['facility'] = $args['facility'] ?? null;
		$args['level']    = $args['level'] ?? null;
		$args['data']     = $args['data'] ?? null;
		LogDB::log( $message, $args );
	}
	
	/**
	 * Wrapper method for Iptic Simple Logging's log method
	 *
	 * @param array $args
	 * @param string $output
	 *
	 * @return  array
	 * @since   0.1.0
	 */
	public static function get_logs( array $args, string $output = 'OBJECT' ): array {
		return LogDB::get_logs( $args, $output );
	}
	
}
