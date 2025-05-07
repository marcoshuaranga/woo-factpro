<?php

namespace Factpro\WP\Admin;

use Automattic\WooCommerce\Utilities\OrderUtil;
use Factpro\Helper\View;
use Factpro\ThirdParties\Factpro\Response\DocumentResponse;

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
        $order = is_a($post_or_order, \WC_Order::class) ? $post_or_order : new \WC_Order($post_or_order->ID);
        $documentResponse = DocumentResponse::fromJson($order->get_meta('_factpro_invoice_json'));
        $template = $documentResponse->isEmpty() ? 'admin/order-edit/invoice-metabox-empty' : 'admin/order-edit/invoice-metabox';

        echo View::make(WOO_FACTPRO_VIEW_DIR)->render(
          $template,
          [
            'documentResponse' => $documentResponse,
          ]
        );
      },
      $current_screen->id,
      'side',
      'high'
    );
  }
}
