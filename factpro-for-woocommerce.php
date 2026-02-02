<?php

/**
 * Plugin Name: Factpro for WooCommerce
 * Description: Factpro.pe para facturación electrónica en el Perú
 * Version: 3.3.2
 * Author: Factpro.pe
 * Requires at least: 6.0
 * Requires PHP: 7.4
 * Text Domain: factpro-for-woocommerce
 * Domain Path: /languages
 * License: MIT
 * License URI: https://opensource.org/licenses/MIT
 */

use Factpro\WooFactpro;

defined('ABSPATH') || exit;

define('WOO_FACTPRO_VIEW_DIR', __DIR__ . '/views');
define('WOO_FACTPRO_PLUGIN_FILE', __FILE__);

require __DIR__ . '/vendor/autoload.php';

register_activation_hook(__FILE__, [WooFactpro::class, 'install']);
register_deactivation_hook(__FILE__, [WooFactpro::class, 'uninstall']);

WooFactpro::init();
