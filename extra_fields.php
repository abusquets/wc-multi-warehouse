<?php

// Add Variation Settings
add_action( 'woocommerce_product_after_variable_attributes', 'variation_settings_fields', 10, 3 );

// Save Variation Settings
add_action( 'woocommerce_save_product_variation', 'save_variation_settings_fields', 10, 2 );


// Create new fields for variations
function variation_settings_fields( $loop, $variation_data, $variation ) {
  global $wpdb;
  $table_name = "{$wpdb->prefix}wc_warehouse";
  $warehouses = $wpdb->get_results("SELECT code, name FROM $table_name ORDER BY public ASC, sort ASC");
  foreach ($warehouses as $warehouse) {
    $code = stripslashes($warehouse->code);
    $name = stripslashes($warehouse->name);
    $key = $variation->ID . '_'. $code;
    woocommerce_wp_text_input(
      array(
        'id'          => '_warehouse[' . $key .']',
        'label'       => _('Warehouse:') . ' ' . $name,
        'placeholder' => '',
        'description' => '',
        'value'       => get_post_meta(
          $variation->ID, 'warehouse_' . $code, true ),
          'custom_attributes' => array(
            'step' 	=> 'any',
            'min'	=> '0'
          )
      )
    );
  }
}



// Save new fields for variations
function save_variation_settings_fields( $post_id ) {
  global $wpdb;
  $table_name = "{$wpdb->prefix}wc_warehouse";
  $warehouses = $wpdb->get_results("SELECT code FROM $table_name ORDER BY public ASC, sort ASC");
  foreach ($warehouses as $warehouse) {
    $code = stripslashes($warehouse->code);
    $key = $post_id . '_'. $code;
    $number_field = $_POST['_warehouse'][ $key ];
    if ($number_field === '') $number_field = 0;
    if( ! empty( $number_field ) ) {
      update_post_meta(
        $post_id, 'warehouse_' . $code, esc_attr( $number_field ) );
      }
    }
  }
