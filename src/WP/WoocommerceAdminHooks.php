<?php

namespace Factpro\WP;

defined('ABSPATH') || exit;

use Factpro\Helper\View;
use Factpro\SunatCode\IdentityDocument;
use Factpro\SunatCode\InvoiceType;
use Factpro\Woo\Actions\CancelInvoice;
use Factpro\Woo\Actions\CreateInvoice;
use Factpro\Woo\Actions\DownloadInvoice;
use Factpro\Woo\Actions\PreviewInvoice;
use Factpro\Woo\Actions\ViewInvoiceStatus;
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

            if (InvoiceType::is_factura($invoiceType)) {
                $company_ruc = wc_clean($_POST['factpro_customer_document_number']);
                $isCompany = substr(wc_clean($company_ruc), 0, 2) === '20';

                $order->update_meta_data('_factpro_company_name', wc_clean($_POST['factpro_company_name']));
                $order->update_meta_data('_factpro_company_address', wc_clean($_POST['factpro_company_address']));
                $order->update_meta_data('_factpro_company_ubigeo', $isCompany ? wc_clean($_POST['factpro_company_ubigeo']) : '');
                $order->update_meta_data('_factpro_company_ruc', wc_clean($company_ruc));
                $order->update_meta_data('_factpro_customer_document_type', IdentityDocument::RUC);
                $order->update_meta_data('_factpro_customer_document_number', wc_clean($company_ruc));
            } else {
                $order->update_meta_data('_factpro_company_name', '');
                $order->update_meta_data('_factpro_company_address', wc_clean($_POST['factpro_company_address']));
                $order->update_meta_data('_factpro_company_ubigeo', '');
                $order->update_meta_data('_factpro_customer_document_type', wc_clean($_POST['factpro_customer_document_type']));
                $order->update_meta_data('_factpro_customer_document_number', wc_clean($_POST['factpro_customer_document_number']));
            }

            $order->save_meta_data();
        });

        add_filter('woocommerce_order_actions',  function ($actions) {
            $testmode = get_option('wc_settings_factpro_testmode', 'no') === 'yes';

            $actions['factpro_invoice_create'] = __('Generar comprobante electrónico', 'factpro-for-woocommerce');

            $testmode && $actions['factpro_invoice_preview'] = __('Generar JSON de comprobante electrónico', 'factpro-for-woocommerce');

            $actions['factpro_invoice_status'] = __('Consultar comprobante electrónico', 'factpro-for-woocommerce');
            $actions['factpro_invoice_cancel'] = __('Anular comprobante electrónico', 'factpro-for-woocommerce');

            return $actions;
        });

        add_action('woocommerce_order_action_factpro_invoice_create', [CreateInvoice::class, 'invoke']);
        add_action('woocommerce_order_action_factpro_invoice_preview', [PreviewInvoice::class, 'invoke']);
        add_action('woocommerce_order_action_factpro_invoice_status', [ViewInvoiceStatus::class, 'invoke']);
        add_action('woocommerce_order_action_factpro_invoice_cancel', [CancelInvoice::class, 'invoke']);

        add_action('admin_post_factpro_download_invoice', function () {
            if (!isset($_REQUEST['order']) || !isset($_REQUEST['key'])) {
                wp_die('Parámetros inválidos.');
            }

            $order_id = sanitize_text_field(wp_unslash($_REQUEST['order']));
            $order_key = sanitize_text_field(wp_unslash($_REQUEST['key']));

            DownloadInvoice::invoke(wc_sanitize_order_id($order_id), $order_key);
        });

        add_action('admin_post_nopriv_factpro_download_invoice', function () {
            if (!isset($_REQUEST['order']) || !isset($_REQUEST['key'])) {
                wp_die('Parámetros inválidos.');
            }

            $order_id = sanitize_text_field(wp_unslash($_REQUEST['order']));
            $order_key = sanitize_text_field(wp_unslash($_REQUEST['key']));

            DownloadInvoice::invoke(wc_sanitize_order_id($order_id), $order_key);
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

            $html = View::make(WOO_FACTPRO_VIEW_DIR)->render('admin/invoice-address-section', [
                'factpro_invoice_was_generated' => !!$pdf_url,
                'factpro_invoice_pdf_url' =>  $pdf_url,
                'identity_documents' => IdentityDocument::getOptions(),
                'order' => $order,
            ]);

            $allowed_html = [
                'div' => ['class' => true, 'style' => true, 'id' => true],
                'p' => ['class' => true, 'style' => true],
                'strong' => [],
                'a' => ['href' => true, 'target' => true, 'rel' => true, 'class' => true],
                'select' => ['id' => true, 'name' => true, 'class' => true],
                'option' => ['value' => true, 'selected' => true],
                'input' => [
                    'type' => true,
                    'id' => true,
                    'name' => true,
                    'value' => true,
                    'class' => true,
                ],
            ];

            echo wp_kses($html, $allowed_html);
        });

        add_action('admin_enqueue_scripts', function () {

            $publicUrl = plugins_url('public', WOO_FACTPRO_PLUGIN_FILE);

            wp_register_script('woo_order', $publicUrl . '/admin/woo_order.js', ['jquery'], '1.1', true);
            wp_enqueue_script('woo_order');
            wp_localize_script('woo_order', 'factproSettings', [
                'root' => esc_url_raw(rest_url('factpro-for-woocommerce/v1')),
                'nonce' => wp_create_nonce('wp_rest'),
            ]);
        });
    }
}
