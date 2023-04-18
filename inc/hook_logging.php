<?php

if ( ! defined( 'ABSPATH' ) ) {
	die(); // silence is golden
}

use Iptic\SL\Log as LOG;

/** This file is just for setting up callbacks for logging on various WP hooks */

/** log user logins */
add_action( 'wp_login', static function ( string $user_login, WP_User $user ) {
	LOG::log(
		"User successfully logged in",
		array(
			'user' => $user_login,
			'data' => 'login'
		)
	);
}, 10, 2 );

/** log user logouts */
add_action( 'wp_logout', static function ( int $uid ) {
	LOG::log(
		
		"User successfully logged out",
		array(
			'user' => get_user_by( 'ID', $uid )->user_login,
			'data' => 'logout'
		)
	);
}, 10, 1 );

/** log user registration  */
add_action( 'user_register', static function ( int $uid ) {
	LOG::log(
		"User " . get_user_by( 'ID', $uid )->user_login . " successfully registered",
		array(
			'user' => wp_get_current_user()->user_login,
			'data' => 'register-user'
		)
	);
}, 10, 1 );

/** log user deletion */
add_action( 'deleted_user', static function ( int $ui, $reasssign, WP_User $user ) {
	LOG::log(
		"User " . $user->user_login . " successfully deleted",
		array(
			'user' => wp_get_current_user()->user_login,
			'data' => 'delete-user'
		)
	);
}, 10, 3 );

/** log user password change */
add_action( 'after_password_reset', static function ( WP_User $user ) {
	LOG::log(
		"Password successfully reset",
		array(
			'user' => $user->user_login,
			'data' => 'pw-reset'
		)
	);
}, 10, 1 );

/** log when user email changes */
add_action( 'profile_update', static function ( int $uid, WP_User $old_user_data ) {
	LOG::log(
		"User " . get_user_by( 'ID', $uid )->user_login . " successfully updated",
		array(
			'user' => wp_get_current_user()->user_login,
			'data' => $uid . ' changed from ' . $old_user_data->user_email . ' to ' . get_user_by( 'ID', $uid )->user_email
		)
	);
}, 10, 2 );

// log failed login attempts
add_action( 'wp_login_failed', static function ( $username ) {
	LOG::log(
		"Failed login attempt",
		array(
			'user' => $username,
			'data' => 'login-failed'
		)
	);
});

// log changes to user roles or permissions
add_action( 'set_user_role', static function ( $user_id, $role, $old_roles ) {
	LOG::log(
		"User role or permissions changed",
		array(
			'user' => wp_get_current_user()->user_login,
			'data' => $user_id . ' changed from ' . var_export($old_roles, true) . ' to ' . $role
		)
	);
}, 10, 3 );

// log creation or deletion of posts or pages
add_action( 'transition_post_status', static function ( $new_status, $old_status, $post ) {
	if ( $new_status !== $old_status ) {
		$action = $new_status === 'publish' ? 'created' : 'deleted';
		LOG::log(
			"Post or page $action",
			array(
				'user' => wp_get_current_user()->user_login,
				'data' => $post->ID . ' changed from ' . $old_status . ' to ' . $new_status
			)
		);
	}
}, 10, 3 );

// log updates to plugins
add_action( 'upgrader_process_complete', static function ( $upgrader_object, $options ) {
	if ( $options['type'] === 'plugin' || $options['type'] === 'theme' ) {
		if ( $options['type'] === 'plugin' ) {
			$plugin_name = $options['plugin'];
		} else {
			$plugin_name = $options['theme'];
		}
		LOG::log(
			"Plugin or theme updated",
			array(
				'user' => wp_get_current_user()->user_login,
				'data' => $options['type'] . ' update ' . var_export($plugin_name, true)
			)
		);
	}
}, 10, 2 );

// log changes to site settings or configurations
add_action( 'updated_option', static function ( $option_name, $old_value, $new_value ) {
	// filter out options that should not be logged
	$ignored_options = array(
		'_site_transient_update_plugins',
		'_transient_doing_cron'
	);
	if ( in_array( $option_name, $ignored_options, true ) ) {
		return;
	}
	
	LOG::log(
		"Site setting or configuration changed",
		array(
			'user' => wp_get_current_user()->user_login,
			'data' => $option_name . ' option updated from ' . var_export($old_value, true) . ' to ' . var_export($new_value, true)
		)
	);
}, 10, 3 );