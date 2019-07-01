<?php

/*
Plugin Name: almanac
Plugin URI:http://wordpress.org/plugins/almanac
Description: TO DOw
Author: Ivan Naluzhnyi / Mikael PAUL / Theo LEGAGNEUR
Version: 0.1.0
Author URI: http://almanac.com
*/

if (!defined("WPE_url")) { define("WPE_url", WP_PLUGIN_URL.'/wordpress-events'); } 

if (!defined("WPE_dir")) { define("WPE_dir", WP_PLUGIN_DIR.'/wordpress-events'); } 



include_once('includes/almanac-setup.class.php');

// include('includes/class-wordpress-events-widget.php');
// register_activation_hook( __FILE__, 'flush_rewrite_rules' );

