<?php

/*
Plugin Name: WP Fusion - WooCommerce Lifetime Value
Description: Allows syncing a customer's lifetime value from WooCommerce to a custom field in your CRM
Plugin URI: https://wpfusion.com/
Version: 1.0
Author: Very Good Plugins
Author URI: https://verygoodplugins.com/
*/

function wpf_woo_ltv_field( $meta_fields ) {

	$meta_fields['lifetime_value'] = array(
		'label' => 'Lifetime Value',
		'group' => 'woocommerce',
	);

	return $meta_fields;

}

add_filter( 'wpf_meta_fields', 'wpf_woo_ltv_field' );


function wpf_woo_ltv_calculate( $order_data, $order ) {

	$order_data['lifetime_value'] = 0;

	$customer_orders = get_posts( array(
		'posts_per_page' => -1,
		'post_type'      => 'shop_order',
		'post_status'    => wc_get_is_paid_statuses(),
		'meta_key'       => '_billing_email',
		'meta_value'     => $order_data['billing_email'],
		'orderby'        => 'ID',
		'order'          => 'DESC'
	));

	if ( ! empty( $customer_orders ) ) {

		foreach ( $customer_orders as $order_id ) {

			$order = wc_get_order( $order_id );

			$order_total = $order->get_total();

			$order_data['lifetime_value'] += floatval( $order_total );

		}

	}

	return $order_data;

}

add_filter( 'wpf_woocommerce_customer_data', 'wpf_woo_ltv_calculate', 10, 2 );