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

function wc_autoship_custom_meta_install() {

}
register_activation_hook( __FILE__, 'wc_autoship_custom_meta_install' );

function wc_autoship_custom_meta_deactivate() {

}
register_deactivation_hook( __FILE__, 'wc_autoship_custom_meta_deactivate' );

function wc_autoship_custom_meta_uninstall() {

}
register_uninstall_hook( __FILE__, 'wc_autoship_custom_meta_uninstall' );

function wc_autoship_custom_meta_settings( $settings ) {
	$settings[] = array(
		'title' => __( 'Custom Meta', 'wc-autoship-custom-meta' ),
		'desc' => __( 'Add custom meta fields to autoship orders', 'wc-autoship-custom-meta' ),
		'desc_tip' => false,
		'type' => 'title',
		'id' => 'wc_autoship_product_page_title'
	);
	$settings[] = array(
		'name' => __( 'Custom Meta Fields', 'wc-autoship-custom-meta' ),
		'desc' => __( 'The meta fields to show for autoship orders', 'wc-autoship-custom-meta' ),
		'desc_tip' => true,
		'type' => 'wc_autoship_custom_meta_fields',
		'id' => 'wc_autoship_custom_meta_fields_tmp'
	);
	$settings[] = array(
		'type' => 'sectionend',
		'id' => 'wc_autoship_custom_meta_sectionend'
	);
	return $settings;
}
add_filter( 'wc_autoship_settings', 'wc_autoship_custom_meta_settings', 10, 1 );

function wc_autoship_custom_meta_fields( $value ) {
	$fields = get_option( 'wc_autoship_custom_meta_fields', array() );
	include( 'templates/admin/wc-autoship-custom-meta-fields.php' );
}
add_action( 'woocommerce_admin_field_wc_autoship_custom_meta_fields', 'wc_autoship_custom_meta_fields' );

function wc_autoship_custom_meta_update_options( $options ) {
	if ( isset( $_POST['wc_autoship_custom_meta_fields'] ) ) {
		$fields = $_POST['wc_autoship_custom_meta_fields'];
		$filtered_fields = array();
		foreach ( $fields as $field ) {
			if ( ! empty( $field['key'] ) ) {
				$filtered_fields[] = $field;
			}
		}
		usort( $filtered_fields, 'wc_autoship_custom_meta_key_compare' );
		update_option( 'wc_autoship_custom_meta_fields', $filtered_fields );
	}
}
add_action( 'woocommerce_update_options_wc_autoship', 'wc_autoship_custom_meta_update_options' );

function wc_autoship_custom_meta_key_compare( $a, $b ) {
	return strcasecmp( $a['key'], $b['key'] );
}