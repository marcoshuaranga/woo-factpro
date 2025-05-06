<?php

namespace Factpro\Woo\Actions;

use WC_Order;

final class ViewInvoiceStatus
{
  /**
   * @param int|WC_Order $id_or_order
   */
  public static function invoke($id_or_order)
  {
    $order = is_a($id_or_order, WC_Order::class) ? $id_or_order : new WC_Order($id_or_order);

    $a = json_encode([
      'status' => $order->get_meta('_factpro_invoice_status'),
      'message' => $order->get_meta('_factpro_invoice_message'),
      'pdf_url' => $order->get_meta('_factpro_invoice_pdf_url'),
      'json_url' => $order->get_meta('_factpro_invoice_json_url'),
    ]);

    print('<pre>');
    print($a);
    print('</pre>');
    die();
  }
}
