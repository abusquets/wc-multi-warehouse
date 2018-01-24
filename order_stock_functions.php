<?php

add_action('woocommerce_reduce_order_stock', 'order_stock_to_warehouses', 10);

function order_stock_to_warehouses($order)
{
    # Això ara només està funcionant amb el producte per defecte
    # Hauriem de sincronitzar tots els stocks

    global $wpdb;
    $table_post_meta = "{$wpdb->prefix}postmeta";
    $table_warehouse = "{$wpdb->prefix}warehouse";

    // Iterating through each WC_Order_Item_Product objects
    foreach ($order->get_items() as $item_key => $item_values) {
        $item_id = $item_values->get_id();

        $item_name = $item_values->get_name(); // Name of the product
        $item_type = $item_values->get_type(); // Type of the order item ("line_item")

        $product_id = $item_values->get_product_id(); // the Product id
        $wc_product = $item_values->get_product(); // the WC_Product object
        ## Access Order Items data properties (in an array of values) ##
        $item_data = $item_values->get_data();
        $quantity = $item_data['quantity'];

        $variation_id = $item_data['variation_id'];
        $variation = new WC_Product_Variation($variation_id);

        $sql = "SELECT * FROM (";
        $sql .= "SELECT * FROM $table_post_meta a, $table_warehouse b WHERE a.meta_key like 'warehouse_%' AND a.post_id=".$item_data['variation_id']." AND CAST(a.meta_value AS UNSIGNED INTEGER) > 0 AND CONCAT('warehouse_', b.code)=a.meta_key ORDER BY b.public ASC, b.sort DESC";
        $sql .= ") as foo WHERE meta_value > 0";
        $rows = $wpdb->get_results($sql, ARRAY_A);

        $repartiment = order_stock_to_warehouses_process($rows, $quantity);

        # Update stock variant
        $warehouses_meta = array();
        foreach ($repartiment as $r) {
            update_post_meta(
              $variation_id,
                $r['meta_key'],
                esc_attr($r['meta_value'])
            );
            $warehouses_meta[str_replace('warehouse_', '', $r['meta_key'])] = $r['quantity'];
        }
        wc_add_order_item_meta($item_id, 'warehouses', json_encode($warehouses_meta));
    }

    order_stock_warehouse_notifications($order);
}


function order_stock_to_warehouses_process($product_stock, $quantity){
    $repartiment = array();
    foreach ($product_stock as $row) {
        if ($quantity == 0) {
            break;
        }
        if ($row['meta_value'] == 0) {
            continue;
        }
        if ($quantity >= $row['meta_value']) {
            $repartiment[] = array(
                'quantity' => $row['meta_value'],
                'meta_id' => $row['meta_id'],
                'meta_key' => $row['meta_key'],
                'meta_value' => 0
            );
            $quantity = $quantity - $row['meta_value'];
        } else {
            $repartiment[] = array(
                'quantity' => $quantity,
                'meta_id' => $row['meta_id'],
                'meta_key' => $row['meta_key'],
                'meta_value' => $row['meta_value'] - $quantity # Quantitat que queda al magatzem
            );
            $quantity = 0;
        }
    }
    return $repartiment;
}


function order_stock_warehouse_notifications($order)
{
    $warehouses = array();
    foreach ($order->get_items() as $item_key => $item_values) {
        $item_id = $item_values->get_id();
        $variation_data = json_decode(wc_get_order_item_meta($item_id, 'warehouses'));
        $wc_product = $item_values->get_product();
        $sku = $wc_product->get_sku();
        foreach ($variation_data as $warehouse=>$v) {
            if (!isset($warehouses[$warehouse])) {
                $warehouses[$warehouse] = array();
            }
            $warehouses[$warehouse][] = array(
                'sku'=> $sku,
                'name'=> $item_values->get_name(),
                'quantity'=> $v
            );
        }
    }


    foreach ($warehouses as $warehouse=>$data) {
        $email_notifications = WC()->mailer()->get_emails();
        $email_notifications['WC_Stock_Order_Email']->trigger($order, $warehouse, $data);
    }
}
