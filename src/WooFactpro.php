<?php

namespace Factpro;

defined('ABSPATH') || exit;

use Automattic\WooCommerce\Internal\DataStores\Orders\OrdersTableDataStore;
use Factpro\WP\Admin\WoocommerceEditOrder;
use Factpro\WP\AdminHooks;
use Factpro\WP\RestApiHooks;
use Factpro\WP\WoocommerceAdminHooks;
use Factpro\WP\WoocommerceEmailHooks;
use Factpro\WP\WoocommerceHooks;
use Fragen\Git_Updater\Lite;

final class WooFactpro
{
  const VERSION = '3.3.1';

  public static function init()
  {
    (new Lite(__FILE__))->run();

    AdminHooks::init();
    RestApiHooks::init();
    WoocommerceAdminHooks::init();
    WoocommerceEmailHooks::init();
    WoocommerceHooks::init();
    WoocommerceHooks::initWoocommerceFields();
    WoocommerceEditOrder::init();
  }

  public static function install()
  {
    $is_installing = get_transient('woo_factpro_installing') === 'yes';

    if ($is_installing) {
      return;
    }

    if (! is_plugin_active('woocommerce/woocommerce.php')) {
      return;
    }

    set_transient('woo_factpro_installing', 'yes', MINUTE_IN_SECONDS * 2);

    try {
      self::install_core();
    } finally {
      delete_transient('woo_factpro_installing');
    }

    add_option('woo_factpro_install_timestamp', time());
    do_action('woo_factpro_installed');
  }

  public static function install_core()
  {
    self::migrate_factpro_order_metafields();
    self::update_factpro_version();
  }

  public static function uninstall() {}

  public static function migrate_factpro_order_metafields()
  {
    global $wpdb;

    $metafieldsTable = OrdersTableDataStore::get_meta_table_name();
    $metafield_keys_changed = [
      '_ebilling_invoice_pdf_url' => '_factpro_invoice_pdf_url',
      '_ebilling_invoice_xml_url' => '_factpro_invoice_xml_url',
      '_ebilling_invoice_type' => '_factpro_invoice_type',
      '_ebilling_company_name' => '_factpro_company_name',
      '_ebilling_company_address' => '_factpro_company_address',
      '_ebilling_company_ubigeo' => '_factpro_company_ubigeo',
      '_ebilling_cutomer_document_type' => '_factpro_cutomer_document_type',
      '_ebilling_cutomer_document_number' => '_factpro_cutomer_document_number',
    ];

    foreach ($metafield_keys_changed as $old_key => $new_key) {
      $wpdb->update(
        $metafieldsTable,
        ['meta_key' => $new_key],
        ['meta_key' => $old_key],
        ['%s'],
        ['%s']
      );
    }
  }

  public static function check_version()
  {
    $woo_factpro_db_version = get_option('woo_factpro_version');
    $woo_factpro_current_version = self::VERSION;
    $requires_update = version_compare($woo_factpro_db_version, $woo_factpro_current_version, '<');

    if ($requires_update) {
      self::install();

      do_action('woo_factpro_updated');
    }
  }

  private static function update_factpro_version()
  {
    $woo_factpro_db_version = get_option('woo_factpro_version');
    $woo_factpro_current_version = self::VERSION;

    if (version_compare($woo_factpro_db_version, $woo_factpro_current_version, '<')) {
      update_option('woo_factpro_version', $woo_factpro_current_version);
    }
  }
}
