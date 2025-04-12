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

use Factpro\WP\AdminHooks;
use Factpro\WP\RestApiHooks;
use Factpro\WP\WoocommerceAdminHooks;
use Factpro\WP\WoocommerceEmailHooks;
use Factpro\WP\WoocommerceHooks;

define('EBILLING_VIEW_DIR', __DIR__ . '/views');
define('EBILLING_PLUGIN_FILE', __FILE__);

require __DIR__ . '/vendor/autoload.php';

AdminHooks::init();
RestApiHooks::init();
WoocommerceAdminHooks::init();
WoocommerceHooks::init();
WoocommerceEmailHooks::init();
