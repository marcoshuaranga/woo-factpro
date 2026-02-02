<?php

namespace Factpro\WP\AdminPanel;

defined('ABSPATH') || exit;

use Factpro\Helper\View;

final class OrderTable
{
    public static function addColumn($columns)
    {
        $columns['download_pdf_or_xml'] = __('Comprobante', 'factpro-for-woocommerce');

        return $columns;
    }

    public static function renderColumn($column, $order_id)
    {
        if ($column !== 'download_pdf_or_xml') {
            return;
        }

        $actions = [];
        $order = wc_get_order($order_id);

        if ($order->get_meta('_factpro_invoice_pdf_url')) {
            $actions[] = [
                'url'  => $order->get_meta('_factpro_invoice_pdf_url'),
                'name' => __('PDF', 'factpro-for-woocommerce'),
                'action' => 'pdf',
            ];
        }

        if ($order->get_meta('_factpro_invoice_xml_url')) {
            $actions[] = [
                'url'  => $order->get_meta('_factpro_invoice_xml_url'),
                'name' => __('XML', 'factpro-for-woocommerce'),
                'action' => 'xml',
            ];
        }

        if ($order->get_meta('_ebilling_invoice_pdf_url')) {
            $actions[] = [
                'url'  => $order->get_meta('_ebilling_invoice_pdf_url'),
                'name' => __('PDF', 'factpro-for-woocommerce'),
                'action' => 'pdf',
            ];
        }

        if ($order->get_meta('_ebilling_invoice_xml_url')) {
            $actions[] = [
                'url'  => $order->get_meta('_ebilling_invoice_xml_url'),
                'name' => __('XML', 'factpro-for-woocommerce'),
                'action' => 'xml',
            ];
        }

        $html = View::make(WOO_FACTPRO_VIEW_DIR)->render('admin/orders-table/custom_column', ['actions' => $actions]);
        $allowed_html = [
            'div' => ['class' => true],
            'a' => [
                'class' => true,
                'href' => true,
                'aria-label' => true,
                'title' => true,
            ],
            'span' => [
                'class' => true,
                'style' => true,
            ],
        ];
        echo wp_kses($html, $allowed_html);
    }
}
