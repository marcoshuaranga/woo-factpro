<?php

namespace EBilling\WP;

use EBilling\Helper\View;
use EBilling\InvoiceDownloader;
use EBilling\InvoiceGenerator;
use EBilling\SunatCode\IdentityDocument;
use EBilling\SunatCode\InvoiceType;

final class WoocommerceAdminHooks
{
    public static function init()
    {
        add_action('woocommerce_process_shop_order_meta', function ($order_id) {

            if ($_POST['ebilling_customer_document_type'] === IdentityDocument::NO_IDENTITY_DOCUMENT) {
                return;
            }

            $invoiceType = IdentityDocument::get_invoice_type($_POST['ebilling_customer_document_type']);

            update_post_meta($order_id, '_ebilling_invoice_type', $invoiceType);
            update_post_meta($order_id, '_ebilling_customer_document_type', wc_clean($_POST['ebilling_customer_document_type']));
            update_post_meta($order_id, '_ebilling_customer_document_number', wc_clean($_POST['ebilling_customer_document_number']));

            if (InvoiceType::is_factura($invoiceType)) {
                update_post_meta($order_id, '_ebilling_company_name', wc_clean($_POST['ebilling_company_name']));
                update_post_meta($order_id, '_ebilling_company_address', wc_clean($_POST['ebilling_company_address']));
                update_post_meta($order_id, '_ebilling_company_ubigeo', wc_clean($_POST['ebilling_company_ubigeo']));
            }
        });

        add_action('admin_head', function () {
            print ('<style>
                .widefat .column-wc_actions a.pdf::after { content: "\f190"; }
                .widefat .column-wc_actions a.xml::after { content: "\f491"; }
            </style>');
        });

        add_filter('woocommerce_admin_order_actions', function (array $actions, \WC_Order $order) {

            if ($order->get_meta('_ebilling_invoice_pdf_url')) {
                $actions['download_pdf'] = array(
                    'url'  => $order->get_meta('_ebilling_invoice_pdf_url'),
                    'name' => __('Descargar PDF', 'woo-ebilling'),
                    'action' => 'pdf',
                );
            }

            if ($order->get_meta('_ebilling_invoice_xml_url')) {
                $actions['download_xml'] = array(
                    'url'  => $order->get_meta('_ebilling_invoice_xml_url'),
                    'name' => __('Descargar XML', 'woo-ebilling'),
                    'action' => 'xml',
                );
            }

            return $actions;
        }, 100, 2);

        add_filter( 'woocommerce_order_actions',  function ($actions) {
            $actions['generate_ebilling'] = __('Generar comprobante electrónico');

            return $actions;
        });

        add_action('woocommerce_order_action_generate_ebilling', [InvoiceGenerator::class, 'generate']);

        add_action('admin_post_ebilling_download_invoice', function () {
            $order_id = wc_sanitize_order_id($_REQUEST['order']);
            $order_key = sanitize_text_field($_REQUEST['key']);
            
            InvoiceDownloader::download($order_id, $order_key);
        });

        add_action('admin_post_nopriv_ebilling_download_invoice', function () {
            $order_id = wc_sanitize_order_id($_REQUEST['order']);
            $order_key = sanitize_text_field($_REQUEST['key']);

            InvoiceDownloader::download($order_id, $order_key);
        });

        /**
         * Display in WP Admin
         */
        add_action( 'woocommerce_admin_order_data_after_billing_address', function (\WC_Order $order) {

            $pdf_url = false;

            if ($order->get_meta('_urlpdf_ifactura')) {
                $pdf_url = $order->get_meta('_urlpdf_ifactura');
            } elseif ($order->get_meta('_ebilling_invoice_pdf_url')) {
                $pdf_url = $order->get_meta('_ebilling_invoice_pdf_url');
            }

            print View::make(EBILLING_VIEW_DIR)->render('admin/invoice-address-section', [
                'ebilling_invoice_was_generated' => !!$pdf_url,
                'ebilling_invoice_pdf_url' =>  $pdf_url,
                'identity_documents' => IdentityDocument::getOptions(),
                'order' => $order,
            ]);
        });

        add_action('admin_enqueue_scripts', function () {
        
            $publicUrl = plugins_url('public', EBILLING_PLUGIN_FILE);
        
            wp_register_script('woo_order', $publicUrl . '/admin/woo_order.js', ['jquery'], 1.0, true);
        
            if (get_post_type() === 'shop_order') {
                wp_enqueue_script('woo_order');
                wp_localize_script('woo_order', 'ebillingSettings', [
                    'root' => esc_url_raw( rest_url('woo-ebilling/v1') ),
                    'nonce' => wp_create_nonce( 'wp_rest' ),
                ]);
            }
        });
    }
}
