<?php

/*
Plugin Name: WC Autoship Custom Meta
Plugin URI: https://wooautoship.com
Description: Add custom meta fields to WC Autoship
Version: 1.0
Author: Patterns In the Cloud
Author URI: http://patternsinthecloud.com
License: Single-site
*/

function wc_autoship_custom_meta_shipping_install() {

}
register_activation_hook( __FILE__, 'wc_autoship_custom_meta_shipping_install' );

function wc_autoship_custom_meta_shipping_deactivate() {

}
register_deactivation_hook( __FILE__, 'wc_autoship_custom_meta_shipping_deactivate' );

function wc_autoship_custom_meta_shipping_uninstall() {

}
register_uninstall_hook( __FILE__, 'wc_autoship_custom_meta_shipping_uninstall' );