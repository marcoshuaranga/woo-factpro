<?php

namespace Factpro\Woo\Actions;

defined('ABSPATH') || exit;

final class DownloadInvoice
{
    public static function invoke($order_id, $order_key)
    {
        if (!isset($_REQUEST['order']) || !isset($_REQUEST['key'])) {
            wp_die('Parámetros inválidos.');
        }

        $order_id = sanitize_text_field(wp_unslash($_REQUEST['order']));
        $order_key = sanitize_text_field(wp_unslash($_REQUEST['key']));
        $order = wc_get_order(wc_sanitize_order_id($order_id));

        if (! $order) {
            wp_die('El pedido no existe.');
        }

        if ($order && $order->get_order_key() !== $order_key) {
            wp_die('No tiene permisos para descargar el comprobante.');
        }

        if (! $order->get_meta('_factpro_invoice_pdf_url')) {
            wp_die('No hay ningún comprobante asociado al pedido.');
        }

        wp_safe_redirect($order->get_meta('_factpro_invoice_pdf_url'));

        exit();
    }
}
