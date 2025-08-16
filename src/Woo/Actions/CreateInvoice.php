<?php

namespace Factpro\Woo\Actions;

use Factpro\Domain\Invoice;
use Factpro\SunatCode\InvoiceType;
use Factpro\ThirdParties\Factpro\FactproApi;
use Factpro\ThirdParties\Factpro\Request\CreateDocumentV2Request;
use Factpro\ThirdParties\Factpro\Request\CreateDocumentV3Request;
use Factpro\ThirdParties\Factpro\Response\DocumentResponse;
use WC_Order;

final class CreateInvoice
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
        $isBoleta = $invoiceType === InvoiceType::BOLETA;
        $isFactura = $invoiceType === InvoiceType::FACTURA;

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

        if ($order->get_meta('_factpro_invoice_pdf_url')) {
            $order->add_order_note('El comprobante electrónico ya fue generado.');
            wc_get_logger()->error("Pedido #{$order->get_id()}: 'El comprobante ya fue generado.");

            return;
        }

        if (count($order->get_items()) === 0) {
            $order->add_order_note('No se encontraron ítems en la canasta.');
            wc_get_logger()->error("Pedido #{$order->get_id()}: 'No hay ítems agregados en la canasta.");

            return;
        }

        try {
            $version = get_option('wc_settings_factpro_api_version', 'v2');
            $invoice = Invoice::createFromWooOrder($serie, $number, $order, $includeTax);

            $factproApi = new FactproApi(
                get_option('wc_settings_factpro_base_url'),
                get_option('wc_settings_factpro_token'),
                wc_get_logger()
            );

            $createDocumentRequest = $version === 'v2' ? new CreateDocumentV2Request($invoice) : new CreateDocumentV3Request($invoice);
            $jsonResponse = $factproApi->send($createDocumentRequest);
            $createDocumentResponse = DocumentResponse::fromJson($version, $jsonResponse);

            if (! $createDocumentResponse->isSuccessful()) {
                throw new \Exception($createDocumentResponse->getErrorMessage());
            }

            $order->add_meta_data('_factpro_invoice_json', $jsonResponse, true);
            $order->add_meta_data('_factpro_invoice_xml_url', $createDocumentResponse->getXmlUrl(), true);
            $order->add_meta_data('_factpro_invoice_pdf_url', $createDocumentResponse->getPdfUrl(), true);
            $order->save_meta_data();

            $isBoleta && ! $testmode && update_option('wc_settings_factpro_bnsiglafactura', $number + 1);
            $isFactura && ! $testmode && update_option('wc_settings_factpro_nsiglafactura', $number + 1);

            $order->add_order_note(
                "El compronante electrónico {$serie}-{$number} fue generado correctamente."
            );
        } catch (\Exception $e) {
            if ($e->getMessage() === 'El documento ya se encuentra en uso.') {
                $isBoleta && ! $testmode && update_option('wc_settings_factpro_bnsiglafactura', $number + 1);
                $isFactura && ! $testmode && update_option('wc_settings_factpro_nsiglafactura', $number + 1);
            }

            $order->add_order_note('Falló al generar el comprobante electrónico: ' . $e->getMessage());
        }
    }
}
