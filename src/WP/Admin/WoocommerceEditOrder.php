<?php

namespace Factpro\WP\Admin;

defined('ABSPATH') || exit;

use Automattic\WooCommerce\Utilities\OrderUtil;
use Factpro\Helper\View;
use Factpro\ThirdParties\Factpro\Response\DocumentResponse;

final class WoocommerceEditOrder
{
  public static function init()
  {
    add_action('add_meta_boxes', [self::class, 'add_order_meta_boxes'], 999999);
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
        $version = get_option('wc_settings_factpro_api_version', 'v2');
        $order = is_a($post_or_order, \WC_Order::class) ? $post_or_order : new \WC_Order($post_or_order->ID);
        $documentResponse = DocumentResponse::fromJson($version, $order->get_meta('_factpro_invoice_json'));
        $template = $documentResponse->isEmpty() ? 'admin/order-edit/invoice-metabox-empty' : 'admin/order-edit/invoice-metabox';

        $html = View::make(WOO_FACTPRO_VIEW_DIR)->render(
          $template,
          [
            'documentResponse' => $documentResponse,
          ]
        );

        $allowed_html = [
          'style' => [],
          'div' => ['class' => true, 'style' => true],
          'h4' => ['class' => true],
          'span' => ['class' => true, 'style' => true],
          'button' => ['class' => true, 'id' => true, 'disabled' => true],
          'a' => ['href' => true, 'target' => true, 'class' => true, 'aria-label' => true, 'title' => true],
        ];

        echo wp_kses($html, $allowed_html);
      },
      $current_screen->id,
      'side',
      'high'
    );
  }
}
