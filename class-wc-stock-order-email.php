<?php

if (! defined('ABSPATH')) {
    exit;
} // Exit if accessed directly

class WC_Stock_Order_Email extends WC_Email
{


    public function __construct()
    {
        $this->id = 'wc_stock_order';
        $this->title = 'Comanda per cada magatzem';
        $this->description = "Envia un email a cada magatzem amb els productes de la comanda que cal reservar";

        // these are the default heading and subject lines that can be overridden using the settings
        $this->heading = '[{site_title}][{warehouse}] Nova comanda de client ({order_number}) - {order_date}';
        $this->subject = '[{site_title}][{warehouse}] Nova comanda de client ({order_number}) - {order_date}';

        parent::__construct();
    }


    public function trigger($order, $warehouse_code, $warehouse_data)
    {
        global $wpdb;
        $table_name = "{$wpdb->prefix}wc_warehouse";

        $this->object = $order;

        $s = $wpdb->get_row($wpdb->prepare("SELECT * from $table_name where code='%s'", $warehouse_code));

        //error_log(date('d-m-Y, H:i:s') . ": ". print_r($s, true)."\n", 3, 'aaaa.log');

        // if (!$s) {
        //     return;
        // }

        $this->warehouse_code = $warehouse_code;
        $this->warehouse_name = stripslashes($s->name);
        $this->warehouse_email = stripslashes($s->email);
        $this->warehouse_data = $warehouse_data;


        // replace variables in the subject/headings
        $this->find[] = '{site_title}';
        $this->replace[] = get_bloginfo( 'name' );

        $this->find[] = '{warehouse}';
        $this->replace[] = $this->warehouse_name;

        $this->find[] = '{order_date}';
        $this->replace[] = wc_format_datetime( $this->object->get_date_created() );

        $this->find[] = '{order_number}';
        $this->replace[] = $this->object->get_order_number();


        // if (! $this->is_enabled() || $this->warehouse_email === '' ||  is_null($this->warehouse_email)) {
        //     return;
        // }

        $this->send($this->warehouse_email, $this->get_subject(), $this->get_content(), $this->get_headers(), $this->get_attachments());
    }


    /**
    * get_content_html function.
    *
    * @since 0.1
    * @return string
    */
    public function get_content_html()
    {
        ob_start();
        echo "<h3>" . _e( 'Order number:', 'woocommerce' ) . " " . $this->object->get_order_number() . "</h3>";
        echo "<ul>";
        foreach ($this->warehouse_data as $row) {
            echo "<li>" . $row['sku'] . " - " . $row['name'] . ": " . $row['quantity'] . " unitats</li>";
        }
        echo "</ul>";
        return ob_get_clean();
    }


    /**
    * get_content_plain function.
    *
    * @since 0.1
    * @return string
    */
    public function get_content_plain()
    {
        ob_start();
        echo _e( 'Order number:', 'woocommerce' ) . " " . $this->object->get_order_number() . "\n";
        echo "\n";
        foreach ($this->warehouse_data as $row) {
            echo $row['sku'] . " - " . $row['name'] . ": " . $row['quantity'] . " unitats\n";
        }
        return ob_get_clean();
    }


    /**
    * Initialize Settings Form Fields
    *
    * @since 2.0
    */
    public function init_form_fields()
    {
        $this->form_fields = array(
            'enabled'    => array(
                'title'   => 'Enable/Disable',
                'type'    => 'checkbox',
                'label'   => 'Enable this email notification',
                'default' => 'yes'
            ),
            'subject'    => array(
                'title'       => 'Subject',
                'type'        => 'text',
                'description' => sprintf('Deixar en blanc per fer servir el valor per defecte: <code>%s</code>.', $this->subject),
                'placeholder' => '',
                'default'     => ''
            ),
            'heading'    => array(
                'title'       => 'Email Heading',
                'type'        => 'text',
                'description' => sprintf(__('Deixar en blanc per fer servir el valor per defecte: <code>%s</code>.'), $this->heading),
                'placeholder' => '',
                'default'     => ''
            ),
            'email_type' => array(
                'title'       => 'Email type',
                'type'        => 'select',
                'description' => "Format d'enviament.",
                'default'     => 'html',
                'class'       => 'email_type',
                'options'     => array(
                    'plain'	    => __('Text Pla', 'woocommerce'),
                    'html' 	    => __('HTML', 'woocommerce')
                )
            )
        );
    }
}
