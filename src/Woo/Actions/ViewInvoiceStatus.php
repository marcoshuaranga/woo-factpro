<?php

namespace Factpro\Woo\Actions;

use Factpro\ThirdParties\Factpro\FactproApi;
use Factpro\ThirdParties\Factpro\Request\ConsultDocumentV2Request;
use Factpro\ThirdParties\Factpro\Request\ConsultDocumentV3Request;
use Factpro\ThirdParties\Factpro\Response\DocumentResponse;
use WC_Order;

final class ViewInvoiceStatus
{
  /**
   * @param int|WC_Order $id_or_order
   */
  public static function invoke($id_or_order)
  {
    try {
      $version = get_option('wc_settings_factpro_api_version', 'v2');
      $order = is_a($id_or_order, WC_Order::class) ? $id_or_order : new WC_Order($id_or_order);
      $document = DocumentResponse::fromJson($version, $order->get_meta('_factpro_invoice_json', '{}'));
      $documentType = $order->get_meta('_factpro_invoice_type');

      $factproApi = new FactproApi(
        get_option('wc_settings_factpro_base_url'),
        get_option('wc_settings_factpro_token'),
        wc_get_logger()
      );

      [$serie, $number] = explode('-', $document->getSerialNumber());

      $consultDocument = $version === 'v2' ? new ConsultDocumentV2Request([
        'documentType' => $documentType,
        'serie' => $serie,
        'number' => $number,
      ]) : new ConsultDocumentV3Request([
        'serie' => $serie,
        'number' => $number,
      ]);

      $jsonResponse = $factproApi->send($consultDocument);

      $order->update_meta_data('_factpro_invoice_json', $jsonResponse);
      $order->save_meta_data();
      $order->add_order_note(
        "El comprobante electrónico {$serie}-{$number} fue consultado correctamente."
      );
    } catch (\Exception $e) {
      $order->add_order_note('Falló al consultar el estado del comprobante electrónico: ' . $e->getMessage());
    }
  }
}
