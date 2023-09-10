<?php

/**
* Plugin Name: Factpro.pe
* Description: Factpro.pe para facturación electrónica en el Perú
* Version: 2.9.8
* Author: Factpro.pe
* Requires at least: 5.0
* Requires PHP: 7.4
* Text Domain: woo-ebilling
* Domain Path: /languages/
* License: MIT
*/

defined( 'ABSPATH' ) || exit;

use EBilling\WP\AdminHooks;
use EBilling\WP\RestApiHooks;
use EBilling\WP\WoocommerceAdminHooks;
use EBilling\WP\WoocommerceEmailHooks;
use EBilling\WP\WoocommerceHooks;

define('EBILLING_VIEW_DIR', __DIR__ . '/views');
define('EBILLING_PLUGIN_FILE', __FILE__);

require __DIR__ . '/vendor/autoload.php';

AdminHooks::init();
RestApiHooks::init();
WoocommerceAdminHooks::init();
WoocommerceHooks::init();
WoocommerceEmailHooks::init();
