<?php

namespace Factpro\WP;

use Factpro\Helper\View;
use Factpro\InvoiceDownloader;
use Factpro\InvoiceGenerator;
use Factpro\SunatCode\IdentityDocument;
use Factpro\SunatCode\InvoiceType;
use Factpro\WP\AdminPanel\OrderTable;

final class WoocommerceAdminHooks
{
    public static function init()
    {
        add_action('woocommerce_process_shop_order_meta', function ($order_id) {

            if ($_POST['factpro_customer_document_type'] === IdentityDocument::NO_IDENTITY_DOCUMENT) {
                return;
            }

            if ($_POST['factpro_customer_document_type'] === IdentityDocument::RUC) {
                $invoiceType = InvoiceType::FACTURA;
            } else {
                $invoiceType = InvoiceType::BOLETA;
            }

            $order = wc_get_order($order_id);

            $order->update_meta_data('_factpro_invoice_type', $invoiceType);
            $order->update_meta_data('_factpro_customer_document_type', wc_clean($_POST['factpro_customer_document_type']));
            $order->update_meta_data('_factpro_customer_document_number', wc_clean($_POST['factpro_customer_document_number']));
            $order->update_meta_data('_factpro_company_address', wc_clean($_POST['factpro_company_address']));

            if (InvoiceType::is_factura($invoiceType)) {
                $isCompany = substr(wc_clean($_POST['factpro_customer_document_number']), 0, 2) === '20';

                $order->update_meta_data('_factpro_company_name', wc_clean($_POST['factpro_company_name']));

                if ($isCompany) {
                    $order->update_meta_data('_factpro_company_ubigeo', wc_clean($_POST['factpro_company_ubigeo']));
                } else {
                    $order->update_meta_data('_factpro_company_ubigeo', '140101');
                }
            }

            $order->save_meta_data();
        });

        add_filter('woocommerce_order_actions',  function ($actions) {
            $testmode = get_option('wc_settings_factpro_testmode', 'no') === 'yes';

            $actions['generate_factpro'] = __('Generar comprobante electrónico');
            $testmode && $actions['generate_factpro_preview'] = __('Generar JSON de comprobante electrónico');

            return $actions;
        });

        add_action('woocommerce_order_action_generate_factpro', [InvoiceGenerator::class, 'generate']);
        add_action('woocommerce_order_action_generate_factpro_preview', [InvoiceGenerator::class, 'preview']);

        add_action('admin_post_factpro_download_invoice', function () {
            $order_id = wc_sanitize_order_id($_REQUEST['order']);
            $order_key = sanitize_text_field($_REQUEST['key']);

            InvoiceDownloader::download($order_id, $order_key);
        });

        add_action('admin_post_nopriv_factpro_download_invoice', function () {
            $order_id = wc_sanitize_order_id($_REQUEST['order']);
            $order_key = sanitize_text_field($_REQUEST['key']);

            InvoiceDownloader::download($order_id, $order_key);
        });

        /**
         * Add custom column in Orders Table (LEGACY)
         */
        add_filter('manage_edit-shop_order_columns', [OrderTable::class, 'addColumn']);

        /**
         * Add custom column in Orders Table
         */
        add_filter('manage_woocommerce_page_wc-orders_columns', [OrderTable::class, 'addColumn']);

        add_action('manage_shop_order_posts_custom_column', [OrderTable::class, 'renderColumn'], 20, 2);
        add_action('manage_woocommerce_page_wc-orders_custom_column', [OrderTable::class, 'renderColumn'], 20, 2);

        /**
         * Show custom form fields on Edit order page
         */
        add_action('woocommerce_admin_order_data_after_billing_address', function (\WC_Order $order) {

            $pdf_url = false;

            if ($order->get_meta('_urlpdf_ifactura')) {
                $pdf_url = $order->get_meta('_urlpdf_ifactura');
            } elseif ($order->get_meta('_factpro_invoice_pdf_url')) {
                $pdf_url = $order->get_meta('_factpro_invoice_pdf_url');
            }

            print View::make(WOO_FACTPRO_VIEW_DIR)->render('admin/invoice-address-section', [
                'factpro_invoice_was_generated' => !!$pdf_url,
                'factpro_invoice_pdf_url' =>  $pdf_url,
                'identity_documents' => IdentityDocument::getOptions(),
                'order' => $order,
            ]);
        });

        add_action('admin_enqueue_scripts', function () {

            $publicUrl = plugins_url('public', WOO_FACTPRO_PLUGIN_FILE);

            wp_register_script('woo_order', $publicUrl . '/admin/woo_order.js', ['jquery'], 1.1, true);
            wp_enqueue_script('woo_order');
            wp_localize_script('woo_order', 'factproSettings', [
                'root' => esc_url_raw(rest_url('woo-factpro/v1')),
                'nonce' => wp_create_nonce('wp_rest'),
            ]);
        });
    }
}
