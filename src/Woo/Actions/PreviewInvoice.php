<?php

namespace Factpro\Woo\Actions;

use Factpro\Domain\Invoice;
use Factpro\SunatCode\InvoiceType;
use Factpro\ThirdParties\Factpro\Request\CreateDocumentV2Request;
use Factpro\ThirdParties\Factpro\Request\CreateDocumentV3Request;
use WC_Order;

final class PreviewInvoice
{
  /**
   * @param int|WC_Order $id_or_order
   */
  public static function invoke($id_or_order)
  {
    $order = is_a($id_or_order, WC_Order::class) ? $id_or_order : new WC_Order($id_or_order);
    $testmode = get_option('wc_settings_factpro_testmode', 'no') === 'yes';
    $includeTax = get_option('woocommerce_calc_taxes', 'yes') === 'yes';
    $invoiceType = $order->get_meta('_factpro_invoice_type');

    switch ($invoiceType) {
      case InvoiceType::BOLETA:
        $serie  = get_option('wc_settings_factpro_bsiglafactura');
        $number = $testmode ? '#' : get_option('wc_settings_factpro_bnsiglafactura');
        break;
      case InvoiceType::FACTURA:
        $serie   = get_option('wc_settings_factpro_siglafactura');
        $number = $testmode ? '#' : get_option('wc_settings_factpro_nsiglafactura');
        break;
      default:
        $order->add_order_note('No se encontró el tipo de comprobante.');
        return wc_get_logger()->error("Pedido #{$order->get_id()}: 'No se encontró el tipo de comprobante.");
    }

    $invoice = Invoice::createFromWooOrder($serie, $number, $order, $includeTax);
    $isV2 = get_option('wc_settings_factpro_api_version', 'v2') === 'v2';
    $createDocumentRequest = $isV2 ? new CreateDocumentV2Request($invoice) : new CreateDocumentV3Request($invoice);

    print('<pre>');
    print(json_encode($createDocumentRequest->toArray(), JSON_PRETTY_PRINT));
    print('</pre>');

    die();
  }
}
