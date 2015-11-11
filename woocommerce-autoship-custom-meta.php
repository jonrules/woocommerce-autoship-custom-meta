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
	global $wpdb;
	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
	// Create tables
	$create_sql =
			"CREATE TABLE {$wpdb->prefix}wc_autoship_schedule_custom_meta (
			schedule_id BIGINT(20) UNSIGNED NOT NULL,
			meta_key VARCHAR(255) NOT NULL,
			meta_value VARCHAR(255) NOT NULL,
			created_time DATETIME NOT NULL,
			modified_time DATETIME NOT NULL,
			PRIMARY KEY  (schedule_id,meta_key)
			);";
	dbDelta( $create_sql );
}
register_activation_hook( __FILE__, 'wc_autoship_custom_meta_install' );

function wc_autoship_custom_meta_deactivate() {

}
register_deactivation_hook( __FILE__, 'wc_autoship_custom_meta_deactivate' );

function wc_autoship_custom_meta_uninstall() {

}
register_uninstall_hook( __FILE__, 'wc_autoship_custom_meta_uninstall' );

function wc_autoship_custom_meta_scripts() {
	wp_enqueue_script( 'wc-autoship-custom-meta-autoship-schedule', plugin_dir_url( __FILE__ ) . 'js/autoship-schedule.js', array( 'autoship-schedule' ) );
}
add_action( 'wp_enqueue_scripts', 'wc_autoship_custom_meta_scripts' );

function wc_autoship_custom_meta_settings( $settings ) {
	$settings[] = array(
		'title' => __( 'Custom Meta', 'wc-autoship-custom-meta' ),
		'desc' => __( 'Add custom meta fields to autoship orders.', 'wc-autoship-custom-meta' ),
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
	include( 'templates/settings-fields.php' );
}
add_action( 'woocommerce_admin_field_wc_autoship_custom_meta_fields', 'wc_autoship_custom_meta_fields' );

function wc_autoship_custom_meta_update_options( $options ) {
	global $wpdb;
	if ( isset( $_POST['wc_autoship_custom_meta_fields'] ) ) {
		$fields = $_POST['wc_autoship_custom_meta_fields'];
		$filtered_fields = array();
		$key_args = array();
		foreach ( $fields as $field ) {
			if ( ! empty( $field['key'] ) ) {
				$field['key'] = stripslashes( $field['key'] );
				$field['default_value'] = stripslashes( $field['default_value'] );
				$filtered_fields[] = $field;
				$key_args[] = $wpdb->prepare( '%s', $field['key'] );
			}
		}
		usort( $filtered_fields, 'wc_autoship_custom_meta_key_compare' );
		update_option( 'wc_autoship_custom_meta_fields', $filtered_fields );
		$wpdb->query(
				"DELETE FROM {$wpdb->prefix}wc_autoship_schedule_custom_meta
				WHERE meta_key NOT IN(" . implode( ',', $key_args ) . ')'
		);
	}
}
add_action( 'woocommerce_update_options_wc_autoship', 'wc_autoship_custom_meta_update_options' );

function wc_autoship_custom_meta_checkout_fields() {
	$fields = get_option( 'wc_autoship_custom_meta_fields', array() );
	include( 'templates/checkout-fields.php' );
}
add_action( 'woocommerce_checkout_after_customer_details', 'wc_autoship_custom_meta_checkout_fields' );

function wc_autoship_custom_meta_schedule_fields( $schedule_id ) {
	$values = array();
	$meta_result = wc_autoship_custom_meta_get( $schedule_id );
	foreach ( $meta_result as $meta ) {
		$values[ $meta->meta_key ] = $meta->meta_value;
	}
	$fields = get_option( 'wc_autoship_custom_meta_fields', array() );
	include( 'templates/autoship-schedule-fields.php' );
}
add_action( 'wc_autoship_schedule_after_items', 'wc_autoship_custom_meta_schedule_fields' );

function wc_autoship_custom_meta_ajax_save_field() {
	global $wpdb;

	$user_id = get_current_user_id();
	$schedule_id = $_POST['schedule_id'];
	$key = stripslashes( $_POST['key'] );
	$value = stripslashes( $_POST['value'] );

	if (empty( $user_id ) ) {
		header( "HTTP/1.1 403 Unauthorized" );
		die();
	}

	if ( empty( $schedule_id ) || empty( $key ) ) {
		header( "HTTP/1.1 400 Bad Request" );
		die();
	}

	// Check schedule owner
	$customer_id = $wpdb->get_var( $wpdb->prepare(
			"SELECT customer_id
			FROM {$wpdb->prefix}wc_autoship_schedules
			WHERE id = %d",
			$schedule_id
	) );
	if ( empty( $customer_id ) ) {
		// Not found
		header( "HTTP/1.1 404 Not Found" );
		die();
	}
	if ( $user_id != $customer_id && ! user_can( $user_id, 'manage_woocommerce' ) ) {
		// Action not allowed
		header( "HTTP/1.1 403 Unauthorized" );
		die();
	}

	if ( wc_autoship_custom_meta_save( $schedule_id, $key, $value ) ) {
		header( "HTTP/1.1 200 OK" );
		die();
	}

	header( "HTTP/1.1 500 Internal Server Error" );
	die();
}
add_action( 'wp_ajax_schedules_action_save_custom_meta_field', 'wc_autoship_custom_meta_ajax_save_field' );

function wc_autoship_custom_meta_schedule_delete( $result, $schedule_id ) {
	global $wpdb;

	if ( $result === false) {
		return;
	}

	$where = array( 'schedule_id' => $schedule_id );
	$wpdb->delete( "{$wpdb->prefix}wc_autoship_schedule_custom_meta", $where );
}
add_action( 'wc_autoship_schedule_delete', 'wc_autoship_custom_meta_schedule_delete', 10, 2 );

function wc_autoship_custom_meta_order_processed( $order_id ) {
	if ( is_checkout() ) {
		if ( ! isset( $_POST['wc_autoship_custom_meta'] ) ) {
			return;
		}
		$fields = get_option( 'wc_autoship_custom_meta_fields', array() );
		foreach ( $fields as $f => $field ) {
			if ( isset( $_POST[ 'wc_autoship_custom_meta' ][ $f ] ) ) {
				add_post_meta( $order_id, $field['key'], stripslashes(  $_POST[ 'wc_autoship_custom_meta' ][ $f ] ), true );
			}
		}
	}
}
add_action( 'woocommerce_checkout_order_processed', 'wc_autoship_custom_meta_order_processed', 10, 1 );

function wc_autoship_custom_meta_autoship_order_processed( $order_id, $schedule_id ) {
	$meta_result = wc_autoship_custom_meta_get( $schedule_id );
	if ( empty( $meta_result ) ) {
		return;
	}
	$values = array();
	foreach ( $meta_result as $meta ) {
		$values[ $meta->meta_key ] = $meta->meta_value;
	}
	$fields = get_option( 'wc_autoship_custom_meta_fields', array() );
	foreach ( $fields as $field ) {
		if ( isset( $values[ $field['key'] ] ) ) {
			add_post_meta( $order_id, $field['key'], $values[ $field['key'] ], true );
		}
	}
}
add_action( 'wc_autoship_order_processed', 'wc_autoship_custom_meta_autoship_order_processed', 10, 2 );

function wc_autoship_custom_meta_checkout_create_autoship_schedule( $schedule_id ) {
	if ( ! isset( $_POST['wc_autoship_custom_meta'] ) ) {
		return;
	}
	$fields = get_option( 'wc_autoship_custom_meta_fields', array() );
	foreach ( $fields as $f => $field ) {
		if ( isset( $_POST[ 'wc_autoship_custom_meta' ][ $f ] ) ) {
			wc_autoship_custom_meta_save( $schedule_id, $field['key'], stripslashes(  $_POST[ 'wc_autoship_custom_meta' ][ $f ] ) );
		}
	}
}
add_action( 'wc_autoship_checkout_create_autoship_schedule', 'wc_autoship_custom_meta_checkout_create_autoship_schedule', 10, 1 );

function wc_autoship_custom_meta_save( $schedule_id, $key, $value ) {
	global $wpdb;

	$wpdb->show_errors( false );
	$result = false;
	$now = date('Y-m-d H:i:s');
	// Try insert
	$insert_data = array( 'schedule_id' => $schedule_id, 'meta_key' => $key, 'meta_value' => $value, 'created_time' => $now, 'modified_time' => $now );
	$insert = $wpdb->insert( "{$wpdb->prefix}wc_autoship_schedule_custom_meta", $insert_data );
	if ( $insert !== false ) {
		$result = true;
	} else {
		// Try update
		$update_data = array( 'meta_value' => $value, 'modified_time' => $now );
		$update_where = array( 'schedule_id' => $schedule_id, 'meta_key' => $key );
		$update = $wpdb->update( "{$wpdb->prefix}wc_autoship_schedule_custom_meta", $update_data, $update_where );
		if ( $update !== false ) {
			$result = true;
		}
	}
	$wpdb->show_errors( true );
	return $result;
}

function wc_autoship_custom_meta_get( $schedule_id ) {
	global $wpdb;
	$meta_result = $wpdb->get_results( $wpdb->prepare(
			"SELECT * FROM {$wpdb->prefix}wc_autoship_schedule_custom_meta WHERE schedule_id = %d",
			$schedule_id
	) );
	return $meta_result;
}

function wc_autoship_custom_meta_key_compare( $a, $b ) {
	return strcasecmp( $a['key'], $b['key'] );
}