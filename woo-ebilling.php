<?php

/**
* Plugin Name: Factpro.pe
* Description: Factpro.pe para facturación electrónica en el Perú
* Version: 2.9.8
* Author: Factpro.pe
* Text Domain: woo-ebilling
* Domain Path: /languages/
* License: MIT
*/

defined( 'ABSPATH' ) || exit;

use EBilling\Domain\Invoice;
use EBilling\WP\AdminHooks;
use EBilling\WP\RestApiHooks;
use EBilling\WP\WoocommerceDisplayHooks;
use EBilling\WP\WoocommerceHooks;

define('EBILLING_VIEW_DIR', __DIR__ . '/views');
define('EBILLING_PLUGIN_FILE', __FILE__);

require __DIR__ . '/vendor/autoload.php';

AdminHooks::init();

RestApiHooks::init();

WoocommerceHooks::init();

WoocommerceHooks::initAdminHooks();

WoocommerceDisplayHooks::init();

WoocommerceDisplayHooks::initAdminHooks();

WoocommerceDisplayHooks::initEmailHooks();

add_action('admin_post_show_invoice', function () {
    $order = new WC_Order(filter_input(INPUT_GET, 'order_id'));
    $invoice = Invoice::createFromWooOrder('F001', '#', $order, get_option('woocommerce_calc_taxes', 'yes') === 'yes');

    print('<pre>');
    print(json_encode($invoice->toArray(), JSON_PRETTY_PRINT));
    print('</pre>');

    die();
});
