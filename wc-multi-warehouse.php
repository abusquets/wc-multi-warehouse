<?php
/*
Plugin Name: WooCommerce Multi Warehouse
Plugin URI: https://github.com/abusquets/wc-multi-warehouse
Description: WooCommerce Multi Warehouse plugin
Author: Alexandre Busquets Triola
Version: 1.0.5

Copyright: © 2017 Alexandre Busquets Triola (email: abusquets@gmail.com)

*/

function log_log($str, $log_file_path='log.log'){
    if (is_array($str)) $str = print_r($str);
    error_log(date('d-m-Y, H:i:s') . ": " . $str . "\n", 3, $log_file_path);
}


function sample_admin_notice__success() {
    if ('yes' !== WC_Admin_Settings::get_option('woocommerce_manage_stock')){
        ?>
        <div class="notice notice-error">
            <p><?php _e('ERROR: WooCommerce manage stock is disabled. Please enable it.'); ?></p>
        </div>
        <?php
    }
}
add_action( 'admin_notices', 'sample_admin_notice__success' );


// function to create the DB tables
function wc_multi_warehouse_install() {
    global $wpdb;
    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

    $charset_collate = $wpdb->get_charset_collate();
    $sql = "
            CREATE TABLE `{$wpdb->prefix}wc_warehouse` (
              `id` int(4) UNSIGNED NOT NULL AUTO_INCREMENT,
              `code` varchar(50) NOT NULL,
              `name` varchar(255) NOT NULL DEFAULT '',
              `email` varchar(255) NOT NULL DEFAULT '',
              `public` char(1) NOT NULL DEFAULT '1',
              `sort` int(4) NOT NULL DEFAULT '0',
              PRIMARY KEY (id),
              UNIQUE KEY code_unique (code)
            ) $charset_collate;
    ";
    dbDelta($sql);

}
register_activation_hook(__FILE__, 'wc_multi_warehouse_install');


# FIXME
function wc_multi_warehouse_uninstall() {
    global $wpdb;

    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

    $sql = "DROP TABLE {$wpdb->prefix}wc_warehouse;";
    dbDelta($sql);

    remove_menu_page('wc_multi_warehouse_warehouses_list');
    remove_submenu_page(null, 'wc_multi_warehouse_warehouses_update' );

}
register_uninstall_hook(__FILE__, 'wc_multi_warehouse_uninstall');


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
require_once('stock_extra_fields.php');
require_once('category_extra_fields.php');
require_once('api.php');
require_once('order_stock_functions.php');
require_once('email.php');
