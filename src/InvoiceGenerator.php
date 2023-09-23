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
                get_option('wc_settings_ebilling_token'),
                wc_get_logger()
            );

            $result = json_decode($invoiceSender->send($invoice), true);
            $success = isset($result['success']) ? $result['success'] : isset($result['links']);
            $message = isset($result['message']) ? $result['message'] : '';
            $isBoleta = $invoiceType === InvoiceType::BOLETA;
            $isFactura = $invoiceType === InvoiceType::FACTURA;

            if (! $success) {
                throw new \Exception(is_string($message) ? $message : json_encode($message));
            }

            update_post_meta($order->get_id(), '_ebilling_invoice_xml_url', $result['links']['xml']);
            update_post_meta($order->get_id(), '_ebilling_invoice_pdf_url', $result['links']['pdf']);

            $isBoleta && ! $testmode && update_option('wc_settings_ebilling_bnsiglafactura', $number + 1);
            $isFactura && ! $testmode && update_option('wc_settings_ebilling_nsiglafactura', $number + 1);

            $order->add_order_note(
               "El compronante electrónico {$serie}-{$number} fue generado correctamente."
            );
        } catch (\Exception $e) {
            $order->add_order_note('Falló al generar el comprobante electrónico: ' . $e->getMessage());
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
