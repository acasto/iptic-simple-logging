<?php
/**
* Class to provide update functionality for the Iptic Plugin Updates plugin
*
* @link       http://iptic.com
* @since      0.2.0
* @version    v0.4.0
*
*/

namespace Iptic\SL;

use JsonException;

/**
 * A class that provides update functionality for the Iptic Plugin Updates plugin
 *
 * Using the ContainerInterface here since we'll be passing in some settings. Could use HasAction but trying
 * to keep it simple for now so that this class can more easily be reused.
 */
class CheckUpdates {
	/**
	 * Some constants for the class
	 */
	private const DOMAIN = 'iptic.com';
	private const LICENSE_KEY = '';
	// for now, I'm just including the license here as a constant but will eventually have a way to pass it in
	// externally so that it can be stored in the database for individual licensing.
	
	/**
	 * The slug of this plugin.
	 *
	 * @since    0.2.0
	 * @access   private
	 * @var      string $name The slug of this plugin.
	 */
	private string $name;
	
	/**
	 * Initialize the class and set its properties.
	 *
	 * @since   0.2.0
	 */
	public function __construct( string $name ) {
		$this->name = $name;
		// there are a couple ways to do this but decided to go with the 'update_plugins_{$domain}' filter for now even
		// though it was only introduced in WP 5.8. If needed we could hook into 'pre_set_site_transient_update_plugins'
		// or some other filter though we would need to do some additional timestamp checking to limit checks.
		add_filter( 'update_plugins_' . self::DOMAIN, array( $this, 'update_plugins_check' ), 10, 4 );
	}
	
	/**
	 * Checks for updates to the plugin
	 *
	 * @param array|false $update
	 * @param array $plugin_data
	 * @param string $plugin_file
	 * @param array $locales
	 *
	 * @return array
	 */
	public function update_plugins_check( $update, array $plugin_data, string $plugin_file, array $locales ) {
		// return $update if 'isl_update' filter returns false
		if ( ! apply_filters( 'isl_update', true ) ) {
			return $update;
		}
		$license_key = defined('self::LICENSE_KEY') ? self::LICENSE_KEY : '';
		if ( $this->name === $plugin_data['TextDomain'] ) {
			$url = 'https://' . self::DOMAIN . '/wp-json/iptic-plugin-updates/v1/check';
			$args = array(
				'timeout' => 5,
				'body' => array(
					'plugin' => $this->name,
					'key'    => hash( 'md5', $license_key ),
				),
			);
			$remote_check = wp_remote_post( $url, $args );
			if ( is_wp_error( $remote_check ) ) {
				// Handle WP_Error object
				error_log( 'Error checking for updates: ' . $remote_check->get_error_message() );
				return $update;
			}
			try {
				$response = json_decode( wp_remote_retrieve_body( $remote_check ), true, 512, JSON_THROW_ON_ERROR );
			} catch ( JsonException $e ) {
				// Handle JSON decoding error
				error_log( 'Error decoding JSON response: ' . $e->getMessage() );
				return $update;
			}
			if ( array_key_exists( $plugin_file, $response ) ) {
				$update['name']    = $response[ $plugin_file ]['name'];
				$update['slug']    = $response[ $plugin_file ]['slug'];
				$update['version'] = $response[ $plugin_file ]['version'];
				$update['package'] = $response[ $plugin_file ]['package'];
			} else {
				// Handle invalid response
				error_log( 'Invalid response while checking for updates' );
			}
		}
		return $update;
	}
	
}
