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

            if ($_POST['ebilling_customer_document_type'] === IdentityDocument::RUC) {
                $invoiceType = InvoiceType::FACTURA;
            } else {
                $invoiceType = InvoiceType::BOLETA;
            }

            $order = wc_get_order($order_id);

            $order->update_meta_data('_ebilling_invoice_type', $invoiceType);
            $order->update_meta_data('_ebilling_customer_document_type', wc_clean($_POST['ebilling_customer_document_type']));
            $order->update_meta_data('_ebilling_customer_document_number', wc_clean($_POST['ebilling_customer_document_number']));
            $order->update_meta_data('_ebilling_company_address', wc_clean($_POST['ebilling_company_address']));

            if (InvoiceType::is_factura($invoiceType)) {
                $isCompany = substr(wc_clean($_POST['ebilling_customer_document_number']), 0, 2) === '20';

                $order->update_meta_data('_ebilling_company_name', wc_clean($_POST['ebilling_company_name']));

                if ($isCompany) {
                    $order->update_meta_data('_ebilling_company_ubigeo', wc_clean($_POST['ebilling_company_ubigeo']));
                } else {
                    $order->update_meta_data('_ebilling_company_ubigeo', '140101');
                }
            }

            $order->save_meta_data();
        });

        add_filter( 'woocommerce_order_actions',  function ($actions) {
            $testmode = get_option('wc_settings_ebilling_testmode', 'no') === 'yes';

            $actions['generate_ebilling'] = __('Generar comprobante electrónico');
            $testmode && $actions['generate_ebilling_preview'] = __('Generar JSON de comprobante electrónico');

            return $actions;
        });

        add_action('woocommerce_order_action_generate_ebilling', [InvoiceGenerator::class, 'generate']);
        add_action('woocommerce_order_action_generate_ebilling_preview', [InvoiceGenerator::class, 'preview']);

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
         * Add custom column in Orders Table
         */
        add_filter('manage_edit-shop_order_columns', function (array $columns) {
            $columns['download_pdf_or_xml'] = __('Comprobante', 'woocommerce');
        
            return $columns;
        });

        add_action('manage_shop_order_posts_custom_column', function ($column, $order_id) {
        
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

        }, 20, 2);

        /**
         * Show custom form fields on Edit order page
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
        
            wp_register_script('woo_order', $publicUrl . '/admin/woo_order.js', ['jquery'], 1.1, true);
            wp_enqueue_script('woo_order');
            wp_localize_script('woo_order', 'ebillingSettings', [
                'root' => esc_url_raw( rest_url('woo-ebilling/v1') ),
                'nonce' => wp_create_nonce( 'wp_rest' ),
            ]);
        });
    }
}
