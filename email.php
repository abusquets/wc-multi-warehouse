<?php

add_filter('woocommerce_email_classes', 'add_stock_order_email');
function add_stock_order_email($email_classes)
{

    require_once('class-wc-stock-order-email.php');

    $email_classes['WC_Stock_Order_Email'] = new WC_Stock_Order_Email();

    return $email_classes;
}
