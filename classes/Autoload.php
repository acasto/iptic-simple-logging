<?php
/**
 * A simple autoloader
 *
 * @link       http://iptic.com
 * @since      0.1.0
 *
 */

namespace Iptic\SL;


/**
 * A simple autoloader class
 *
 * @since      0.1.0
 * @author     Adam Casto <adam@iptic.com>
 */
class Autoload {

	/**
	 * Registers our autoloader
	 */
	public function __construct() {
		// register this autoloader
		if ( PHP_VERSION_ID >= 50300 ) {
			spl_autoload_register(array($this, 'run'), true, false);
		} else {
			spl_autoload_register(array($this, 'run'));
		}
	}
	
	/**
	 * The autoloader function
	 *
	 * @param  string $class The name of the class that needs to be loaded
	 *
	 *@since 0.1.0
	 */
	private function run( string $class ): void {

		// remove leading slashes
	    $class = ltrim( $class, '\\' );

	    // return if not in our current namespace
	    if( strpos( $class, __NAMESPACE__ ) !== 0 ) {
	        return;
	    }

	    // remove the namespace since we are in the namespace
	    $class = str_replace( __NAMESPACE__, '', $class );

	    // build the path to the file to include
	    //$path = dirname(__FILE__) . str_replace('\\', DIRECTORY_SEPARATOR, $class) . '.php';
	    $path = __DIR__ . str_replace('\\', DIRECTORY_SEPARATOR, $class) . '.php';

	    // include the file if it exists
    	if ( file_exists($path) ) {
        	include($path);
    	}

	}

}
