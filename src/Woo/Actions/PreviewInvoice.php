<?php

namespace Factpro\Woo\Actions;

use Factpro\Domain\Invoice;
use Factpro\InvoiceFormatter;
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
    $formatter = new InvoiceFormatter($invoice, get_option('wc_settings_factpro_url_api'));

    print('<pre>');
    print(json_encode($formatter->toArray(), JSON_PRETTY_PRINT));
    print('</pre>');

    die();
  }
}
