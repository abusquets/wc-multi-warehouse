<?php
/*
Plugin Name: WooCommerce Multi Warehouse
Plugin URI: https://github.com/abusquets/wc-multi-warehouse
Description: WooCommerce Multi Warehouse plugin
Author: Alexandre Busquets Triola
Version: 1.0.1

Copyright: © 2017 Alexandre Busquets Triola (email : abusquets@gmail.com)

*/

function log_log($str, $log_file_path='log.log'){
    error_log(date('d-m-Y, H:i:s') . ": " . $str . "\n", 3, $log_file_path);
}

// function to create the DB tables
function wc_multi_warehouse_install() {
    global $wpdb;

    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

    $charset_collate = $wpdb->get_charset_collate();
    $sql = "
        CREATE TABLE {$wpdb->prefix}wc_warehouse (
            `id` int(4) unsigned NOT NULL AUTO_INCREMENT,
            `code` varchar(255) NOT NULL,
            `name` varchar(255) NOT NULL DEFAULT '',
            `email` varchar(255) NOT NULL DEFAULT '',
            `public` char(1) NOT NULL DEFAULT '1',
            `sort` int(4) NOT NULL DEFAULT 0,
            PRIMARY KEY  (code),
            UNIQUE KEY session_id (id)
          ) $charset_collate;
            ";

    dbDelta($sql);
}

// run the install scripts upon plugin activation
register_activation_hook(__FILE__, 'wc_multi_warehouse_install');

if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
}

add_action( 'all', 'log_hook_calls' );
function log_hook_calls() {
    $f = current_filter();
    if (strpos($f, 'woocommerce') !== false)
        log_log($f, '/tmp/log-hook-calls.log');
}

//menu items
add_action('admin_menu','wc_multi_warehouse_warehouses_modifymenu');
function wc_multi_warehouse_warehouses_modifymenu() {

	//this is the main item for the menu
	add_menu_page('Warehouses', //page title
	'Warehouses', //menu title
	'manage_options', //capabilities
	'wc_multi_warehouse_warehouses_list', //menu slug
	'wc_multi_warehouse_warehouses_list' //function
	);

	//this is a submenu
	add_submenu_page('wc_multi_warehouse_warehouses_list', //parent slug
	'Add New Warehouse', //page title
	'Add New', //menu title
	'manage_options', //capability
	'wc_multi_warehouse_warehouses_create', //menu slug
	'wc_multi_warehouse_warehouses_create'); //function

	//this submenu is HIDDEN, however, we need to add it anyways
	add_submenu_page(null, //parent slug
	'Update Warehouse', //page title
	'Update', //menu title
	'manage_options', //capability
	'wc_multi_warehouse_warehouses_update', //menu slug
	'wc_multi_warehouse_warehouses_update'); //function
}

require_once('crud.php');
require_once('extra_fields.php');
require_once('api.php');
