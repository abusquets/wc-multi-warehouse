<?php

// Publish warehouses via API
add_action( 'rest_api_init', function () {
  register_rest_route( 'multi-warehouse/v1', '/warehouse', array(
    'methods' => 'GET',
    'callback' => 'warehouses_get_all'
  ) );
} );


// Get all warehouses
function warehouses_get_all( $data ) {
  global $wpdb;
  $table_name = "{$wpdb->prefix}wc_warehouse";
  $sql = "SELECT * FROM $table_name ORDER BY public ASC, sort ASC";
  $rows = $wpdb->get_results($sql);
  return $rows;
}
