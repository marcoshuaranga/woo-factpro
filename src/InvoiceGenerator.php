<?php

namespace EBilling;

use EBilling\Domain\Invoice;
use EBilling\SunatCode\InvoiceType;
use WC_Order;

final class InvoiceGenerator
{
    /**
     * @param int|WC_Order $id_or_order
     */
    public static function generate($id_or_order)
    {
        $order = is_a($id_or_order, WC_Order::class) ? $id_or_order : new WC_Order($id_or_order);
        $testmode = get_option('wc_settings_ebilling_testmode', 'no') === 'yes';
        $includeTax = get_option('woocommerce_calc_taxes', 'yes') === 'yes';
        $invoiceType = $order->get_meta('_ebilling_invoice_type');

        switch ($invoiceType) {
            case InvoiceType::BOLETA:
                $serie  = $testmode ? 'B001' : get_option('wc_settings_ebilling_bsiglafactura');
                $number = $testmode ? '#' : get_option('wc_settings_ebilling_bnsiglafactura');
                break;
            case InvoiceType::FACTURA:
                $serie   = $testmode ? 'F001' : get_option('wc_settings_ebilling_siglafactura');
                $number = $testmode ? '#' : get_option('wc_settings_ebilling_nsiglafactura');
                break;
            default:
                return wc_get_logger()->error("Pedido #{$order->get_id()}: 'No se encontró el tipo de comprobante.");
        }

        if ($order->get_meta('_ebilling_invoice_pdf_url')) {
            return wc_get_logger()->error("Pedido #{$order->get_id()}: 'El comprobante ya fue generado.");
        }

        if (count($order->get_items()) === 0) {
            return wc_get_logger()->error("Pedido #{$order->get_id()}: 'No hay ítems agregados en la canasta.");
        }

        try {
            $invoice = Invoice::createFromWooOrder($serie, $number, $order, $includeTax);

            $invoiceSender = new InvoiceSender(
                get_option('wc_settings_ebilling_url_api'),
                get_option('wc_settings_ebilling_token')
            );

            $response = $invoiceSender->send($invoice);
            $results = json_decode($response);

            if (! $results->success) {
                throw new \Exception(is_string($results->message) ? $results->message : json_encode($results->message));
            }

            update_post_meta($order->get_id(), '_ebilling_invoice_xml_url', $results->links->xml);
            update_post_meta($order->get_id(), '_ebilling_invoice_pdf_url', $results->links->pdf);

            $invoiceType === InvoiceType::FACTURA && ! $testmode && update_option('wc_settings_ebilling_nsiglafactura', $number + 1);
            $invoiceType === InvoiceType::BOLETA && ! $testmode && update_option('wc_settings_ebilling_bnsiglafactura', $number + 1);

            $order->add_order_note('El comprobante electrónico fue generado correctamente.');

            wc_get_logger()->info("Pedido #{$order->get_id()}: \n" . $response . "\n", ['source' => 'woo-ebilling']);

        } catch (\Exception $e) {
            $order->add_order_note('Falló al generar el comprobante electrónico: ' . $e->getMessage());

            wc_get_logger()->error(
                "Pedido #{$order->get_id()}: " . $e->getMessage() . "\n" . 
                "Request Failed: " .  json_encode($invoiceSender->getRequestDetails()) . "\n", ['source' => 'woo-ebilling']
            );
        }
    }

    public static function preview($id_or_order)
    {
        $order = is_a($id_or_order, WC_Order::class) ? $id_or_order : new WC_Order($id_or_order);
        $invoice = Invoice::createFromWooOrder('F001', '#', $order, get_option('woocommerce_calc_taxes', 'yes') === 'yes');
        $formatter = new InvoiceFormatter($invoice, get_option('wc_settings_ebilling_url_api'));

        print('<pre>');
        print(json_encode($formatter->toArray(), JSON_PRETTY_PRINT));
        print('</pre>');
        
        die();
    }
}
