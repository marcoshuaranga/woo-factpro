<?php

namespace EBilling\WP\AdminPanel;

use EBilling\Helper\View;

final class OrderTable
{
    public static function addColumn($columns) {
        $columns['download_pdf_or_xml'] = __('Comprobante', 'woocommerce');
        
        return $columns;
    }

    public static function renderColumn($column, $order_id)
    {
        if ($column !== 'download_pdf_or_xml') {
            return;
        }

        $actions = [];
        $order = wc_get_order($order_id);

        if ($order->get_meta('_ebilling_invoice_pdf_url')) {
            $actions[] = [
                'url'  => $order->get_meta('_ebilling_invoice_pdf_url'),
                'name' => __('PDF', 'woo-ebilling'),
                'action' => 'pdf',
            ];
        }

        if ($order->get_meta('_ebilling_invoice_xml_url')) {
            $actions[] = [
                'url'  => $order->get_meta('_ebilling_invoice_xml_url'),
                'name' => __('XML', 'woo-ebilling'),
                'action' => 'xml',
            ];
        }

        print View::make(EBILLING_VIEW_DIR)->render('admin/orders-table/custom_column', ['actions' => $actions]);
    }
}
