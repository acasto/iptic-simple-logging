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
			'data' => 'update-user'
		)
	);
}, 10, 2 );
