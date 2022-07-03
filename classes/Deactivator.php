<?php

/**
 * Fired during plugin deactivation
 *
 * @link       http://iptic.com
 * @since      0.1.0
 *
 */

namespace Iptic\SL;

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
		flush_rewrite_rules();
	}

}
