== Description ==
This plugin provides a simple mechanism to log events to a database table and display them both in the admin area
and via a shortcode.

== Changelog ==
Added the 'isl_default_logging' filter to be able to disable the default logging facilities of the plugin by
returning false. Example: add_filter( 'isl_default_logging', '__return_false' );

Added the 'isl_admin' filter to be able to disable the admin display for the plugin by returning false.
Example: add_filter( 'isl_admin', '__return_false' );

Added the 'ipticsl_menu_name' filter to be able to change the menu name for the plugin.
