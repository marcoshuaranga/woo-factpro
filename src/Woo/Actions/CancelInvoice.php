<?php

namespace Factpro\Woo\Actions;

use Factpro\ThirdParties\Factpro\FactproApi;
use Factpro\ThirdParties\Factpro\Request\CancelDocumentRequest;
use Factpro\ThirdParties\Factpro\Response\DocumentResponse;
use WC_Order;

final class CancelInvoice
{
  /**
   * @param int|WC_Order $id_or_order
   */
  public static function invoke($id_or_order)
  {
    try {
      $order = is_a($id_or_order, WC_Order::class) ? $id_or_order : new WC_Order($id_or_order);
      $document = DocumentResponse::fromJson($order->get_meta('_factpro_invoice_json', '{}'));
      $documentType = $order->get_meta('_factpro_invoice_type');

      $factproApi = new FactproApi(
        get_option('wc_settings_factpro_base_url'),
        get_option('wc_settings_factpro_token'),
        wc_get_logger()
      );

      [$serie, $number] = explode('-', $document->getSerialNumber());

      $jsonResult = $factproApi->send(new CancelDocumentRequest([
        'documentType' => $documentType,
        'serie' => $serie,
        'number' => $number,
        'reason' => 'Anulación de Woo Order #' . $order->get_id(),
      ]));

      $order->add_order_note(
        "El compronante electrónico {$serie}-{$number} fue anulado correctamente."
      );
    } catch (\Exception $e) {
      $order->add_order_note('Falló al anular el comprobante electrónico: ' . $e->getMessage());
    }
  }
}
