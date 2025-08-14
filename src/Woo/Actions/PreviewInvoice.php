<?php

namespace Factpro\Woo\Actions;

use Factpro\Domain\Invoice;
use Factpro\ThirdParties\Factpro\Request\CreateDocumentV2Request;
use WC_Order;

final class PreviewInvoice
{
  /**
   * @param int|WC_Order $id_or_order
   */
  public static function invoke($id_or_order)
  {
    $order = is_a($id_or_order, WC_Order::class) ? $id_or_order : new WC_Order($id_or_order);
    $invoice = Invoice::createFromWooOrder('F001', '#', $order, get_option('woocommerce_calc_taxes', 'yes') === 'yes');
    $createDocumentRequest = new CreateDocumentV2Request($invoice);

    print('<pre>');
    print(json_encode($createDocumentRequest->toArray(), JSON_PRETTY_PRINT));
    print('</pre>');

    die();
  }
}
