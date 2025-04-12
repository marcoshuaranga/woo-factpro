<?php

namespace Factpro;

final class InvoiceDownloader
{
    public static function download($order_id, $order_key)
    {
        $order_id = wc_sanitize_order_id($_REQUEST['order']);
        $order_key = sanitize_text_field($_REQUEST['key']);
        $order = wc_get_order($order_id);

        if (! $order) {
            wp_die('El pedido no existe.');
        }

        if ($order && $order->get_order_key() !== $order_key) {
            wp_die('No tiene permisos para descargar el comprobante.');
        }

        if (! $order->get_meta('_factpro_invoice_pdf_url')) {
            wp_die('No hay ningún comprobante asociado al pedido.');
        }

        wp_redirect($order->get_meta('_factpro_invoice_pdf_url'));

        exit();
    }
}
