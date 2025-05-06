<?php

namespace Factpro\WP\Admin;

use Automattic\WooCommerce\Utilities\OrderUtil;
use Factpro\Helper\View;

final class WoocommerceEditOrder
{
  public static function init()
  {
    add_action('add_meta_boxes', [self::class, 'add_order_meta_boxes']);
  }

  public static function add_order_meta_boxes()
  {
    $current_screen = \get_current_screen();

    if (! OrderUtil::is_order_edit_screen()) {
      return;
    }

    add_meta_box(
      'woo-factpro-invoice',
      __('Comprobante', 'woo-factpro'),
      function ($post_or_order) {
        echo View::make(WOO_FACTPRO_VIEW_DIR)->render(
          'admin/order-edit/invoice-metabox',
          [
            'wc_order' => wc_get_order($post_or_order),
            'meta' => [
              'origin' => 'Google Ads',
              'source_type' => 'Paid Search',
              'utm_campaign' => 'Summer_Sale_2025',
              'utm_source' => 'google',
              'utm_medium' => 'cpc',
              'utm_source_platform' => 'Desktop',
              'utm_creative_format' => 'Banner',
              'utm_marketing_tactic' => 'Retargeting',
              'device_type' => 'Desktop',
              'session_pages' => 5,
            ],
            'has_more_details' => false,
          ]
        );
      },
      $current_screen->id,
      'side',
      'high'
    );
  }
}
