<?php

/**
 * Plugin Name: Factpro.pe
 * Description: Factpro.pe para facturación electrónica en el Perú
 * Version: 3.0.0
 * Author: Factpro.pe
 * Requires at least: 5.0
 * Requires PHP: 7.4
 * Text Domain: woo-factpro
 * Domain Path: /languages/
 * License: MIT
 */

defined('ABSPATH') || exit;

define('WOO_FACTPRO_VIEW_DIR', __DIR__ . '/views');
define('WOO_FACTPRO_PLUGIN_FILE', __FILE__);

require __DIR__ . '/vendor/autoload.php';

use Factpro\WP\Admin\WoocommerceEditOrder;
use Factpro\WP\AdminHooks;
use Factpro\WP\RestApiHooks;
use Factpro\WP\WoocommerceAdminHooks;
use Factpro\WP\WoocommerceEmailHooks;
use Factpro\WP\WoocommerceHooks;

AdminHooks::init();
RestApiHooks::init();
WoocommerceAdminHooks::init();
WoocommerceEmailHooks::init();
WoocommerceHooks::init();
WoocommerceHooks::initWoocommerceFields();

WoocommerceEditOrder::init();
