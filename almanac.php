<?php

/*
Plugin Name: almanac
Plugin URI:http://wordpress.org/plugins/almanac
Description: TO DOw
Author: Ivan Naluzhnyi / Mikael PAUL / Theo LEGAGNEUR
Version: 0.1.0
Author URI: http://almanac.com
*/

?>
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.5.1/dist/leaflet.css" integrity="sha512-xwE/Az9zrjBIphAcBb3F6JVqxf46+CDLwfLMHloNu6KEQCAWi6HcDUbeOfBIptF7tcCzusKFjFw2yuvEpDL9wQ==" crossorigin="" />
<?php

if (!defined("WPE_url")) {
    define("WPE_url", WP_PLUGIN_URL . '/wordpress-events');
}

if (!defined("WPE_dir")) {
    define("WPE_dir", WP_PLUGIN_DIR . '/wordpress-events');
}



include_once('includes/almanac-setup.class.php');

// include('includes/almanac-widget.class.php');
// register_activation_hook( __FILE__, 'flush_rewrite_rules' );
