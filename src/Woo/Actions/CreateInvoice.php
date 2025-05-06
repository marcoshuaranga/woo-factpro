<?php

namespace Factpro\Woo\Actions;

use Factpro\Domain\Invoice;
use Factpro\SunatCode\InvoiceType;
use Factpro\ThirdParties\Factpro\FactproApi;
use Factpro\ThirdParties\Factpro\Request\CreateDocumentRequest;
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

        switch ($invoiceType) {
            case InvoiceType::BOLETA:
                $serie  = $testmode ? 'B001' : get_option('wc_settings_factpro_bsiglafactura');
                $number = $testmode ? '#' : get_option('wc_settings_factpro_bnsiglafactura');
                break;
            case InvoiceType::FACTURA:
                $serie   = $testmode ? 'F001' : get_option('wc_settings_factpro_siglafactura');
                $number = $testmode ? '#' : get_option('wc_settings_factpro_nsiglafactura');
                break;
            default:
                return wc_get_logger()->error("Pedido #{$order->get_id()}: 'No se encontró el tipo de comprobante.");
        }

        if ($order->get_meta('_factpro_invoice_pdf_url')) {
            return wc_get_logger()->error("Pedido #{$order->get_id()}: 'El comprobante ya fue generado.");
        }

        if (count($order->get_items()) === 0) {
            return wc_get_logger()->error("Pedido #{$order->get_id()}: 'No hay ítems agregados en la canasta.");
        }

        try {
            $invoice = Invoice::createFromWooOrder($serie, $number, $order, $includeTax);

            $factproApi = new FactproApi(
                get_option('wc_settings_factpro_base_url'),
                get_option('wc_settings_factpro_token'),
                wc_get_logger()
            );

            $jsonResult = $factproApi->send(new CreateDocumentRequest($invoice));

            // Save the json result in the order meta
            $order->add_meta_data('_factpro_invoice_json', $jsonResult, true);

            $result = json_decode($jsonResult, true);

            $success = isset($result['success']) ? $result['success'] : isset($result['links']);
            $message = isset($result['message']) ? $result['message'] : '';
            $isBoleta = $invoiceType === InvoiceType::BOLETA;
            $isFactura = $invoiceType === InvoiceType::FACTURA;

            if (! $success) {
                throw new \Exception(is_string($message) ? $message : json_encode($message));
            }

            $order->add_meta_data('_factpro_invoice_xml_url', $result['links']['xml'], true);
            $order->add_meta_data('_factpro_invoice_pdf_url', $result['links']['pdf'], true);
            $order->save_meta_data();

            $isBoleta && ! $testmode && update_option('wc_settings_factpro_bnsiglafactura', $number + 1);
            $isFactura && ! $testmode && update_option('wc_settings_factpro_nsiglafactura', $number + 1);

            $order->add_order_note(
                "El compronante electrónico {$serie}-{$number} fue generado correctamente."
            );
        } catch (\Exception $e) {
            $order->add_order_note('Falló al generar el comprobante electrónico: ' . $e->getMessage());
        }
    }
}
