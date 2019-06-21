<?php

/*
Plugin Name: almanac
Plugin URI:http://wordpress.org/plugins/almanac
Description: TO DO
Author: Ivan Naluzhnyi / Mikael PAUL / Theo LEGAGNEUR
Version: 0.1.0
Author URI: http://almanac.com
*/


require_once( dirname(__FILE__) . '/includes/the-events-calendar.class.php' );

TribeEvents::instance();

// register_activation_hook( __FILE__, 'flush_rewrite_rules' );

if ( is_admin() && ( !defined( 'DOING_AJAX' ) || !DOING_AJAX ) ) {
	// register_deactivation_hook( __FILE__, array( 'TribeEvents', 'resetActivationMessage' ) );
}
