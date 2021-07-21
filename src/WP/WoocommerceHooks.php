<?php

namespace EBilling\WP;

use EBilling\InvoiceDownloader;
use EBilling\InvoiceGenerator;
use EBilling\SunatCode\IdentityDocument;
use EBilling\SunatCode\InvoiceType;

final class WoocommerceHooks
{
    public static function init()
    {
        add_filter('woocommerce_my_account_my_orders_actions', function ($actions, $order) {

            if ($order->get_meta('_ebilling_invoice_pdf_url')) {
                $actions['invoice'] = array(
                    'url'  => $order->get_meta('_ebilling_invoice_pdf_url'),
                    'name' => __( 'Ver Comprobante', 'woocommerce' )
                );
            }

            return $actions;
        }, 10, 2);

        add_action('woocommerce_checkout_process', function () {

            $invoice_is_mandatory = get_option('wc_settings_ebilling_invoice_is_mandatory', 'no') === 'yes' ? 1 : 0;
            $has_invoice = isset($_POST['has_invoice_address']) ? 1 : 0;

            if (! $has_invoice && ! $invoice_is_mandatory) {
                return;
            }

            $invoiceType = isset($_POST['ebilling_invoice_type']) ? $_POST['ebilling_invoice_type'] : null;

            switch ($invoiceType) {
                case InvoiceType::BOLETA:
                    ! $_POST['ebilling_customer_document_number'] && wc_add_notice(__('El número de documento no es válido.', 'woo-ebilling'), 'error');
                    break;
                case InvoiceType::FACTURA:
                    ! $_POST['ebilling_customer_document_number'] && wc_add_notice(__('El número de documento no es válido.', 'woo-ebilling'), 'error');
                    ! $_POST['ebilling_company_name'] && wc_add_notice(__('El nombre de razón social no es válido.', 'woo-ebilling'), 'error');
                    ! $_POST['ebilling_company_address'] && wc_add_notice(__('El domicilio fiscal no es es válido.', 'woo-ebilling'), 'error');
                    break;
                default:
                    wc_add_notice(__('Debe seleccionar el tipo de comprobante.', 'woo-ebilling'), 'error');
            }
        });

        add_action('woocommerce_checkout_update_order_meta', function ($order_id) {

            $invoice_is_mandatory = get_option('wc_settings_ebilling_invoice_is_mandatory', 'no') === 'yes';
            $has_invoice = isset($_POST['has_invoice_address']) ? 1 : 0;

            if (! $has_invoice && ! $invoice_is_mandatory) {
                return;
            }

            $identityDocument = InvoiceType::get_identity_document($_POST['ebilling_invoice_type']);

            update_post_meta($order_id, '_ebilling_invoice_type', wc_clean($_POST['ebilling_invoice_type']));
            update_post_meta($order_id, '_ebilling_customer_document_type', $identityDocument);
            update_post_meta($order_id, '_ebilling_customer_document_number', wc_clean($_POST['ebilling_customer_document_number']));

            if (InvoiceType::is_factura($_POST['ebilling_invoice_type'])) {
                update_post_meta($order_id, '_ebilling_company_name', wc_clean($_POST['ebilling_company_name']));
                update_post_meta($order_id, '_ebilling_company_address', wc_clean($_POST['ebilling_company_address']));
            }
        });

        add_action('woocommerce_order_status_completed', [InvoiceGenerator::class, 'generate']);
    }

    public static function initAdminHooks()
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
            }
        });

        add_filter( 'woocommerce_order_actions',  function ( $actions) {
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
    }
}
