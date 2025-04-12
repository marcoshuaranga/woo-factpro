<?php

namespace Factpro\WP;

use Factpro\Helper\View;
use Factpro\InvoiceGenerator;
use Factpro\SunatCode\IdentityDocument;
use Factpro\SunatCode\InvoiceType;

final class WoocommerceHooks
{
    public static function init()
    {
        add_filter('woocommerce_my_account_my_orders_actions', function ($actions, $order) {

            if ($order->get_meta('_factpro_invoice_pdf_url')) {
                $actions['invoice'] = array(
                    'url'  => $order->get_meta('_factpro_invoice_pdf_url'),
                    'name' => __('Ver Comprobante', 'woocommerce')
                );
            }

            return $actions;
        }, 10, 2);

        add_action('woocommerce_checkout_process', function () {

            $invoice_is_mandatory = get_option('wc_settings_factpro_invoice_is_mandatory', 'no') === 'yes' ? 1 : 0;
            $has_invoice = isset($_POST['has_invoice_address']) ? 1 : 0;

            if (! $has_invoice && ! $invoice_is_mandatory) {
                return;
            }

            $invoiceType = isset($_POST['factpro_invoice_type']) ? $_POST['factpro_invoice_type'] : null;

            switch ($invoiceType) {
                case InvoiceType::BOLETA:
                    if (! in_array($_POST['factpro_customer_document_type'], [IdentityDocument::DNI, IdentityDocument::CARNET_EXTRANJERIA, IdentityDocument::PASAPORTE])) {
                        wc_add_notice(__('El tipo de documento no es válido.', 'woo-factpro'), 'error');
                    }

                    ! $_POST['factpro_customer_document_number'] && wc_add_notice(__('El número de documento no es válido.', 'woo-factpro'), 'error');
                    break;
                case InvoiceType::FACTURA:
                    ! $_POST['factpro_customer_document_number'] && wc_add_notice(__('El número de documento no es válido.', 'woo-factpro'), 'error');
                    ! $_POST['factpro_company_name'] && wc_add_notice(__('El nombre de razón social no es válido.', 'woo-factpro'), 'error');
                    ! $_POST['factpro_company_address'] && wc_add_notice(__('El domicilio fiscal no es es válido.', 'woo-factpro'), 'error');
                    ! $_POST['factpro_company_ubigeo'] && wc_add_notice(__('Hubo un error al obtener el ubigeo. Intente la búsqueda nuevamente.', 'woo-factpro'), 'error');
                    break;
                default:
                    wc_add_notice(__('Debe seleccionar el tipo de comprobante.', 'woo-factpro'), 'error');
            }
        });

        add_action('woocommerce_checkout_update_order_meta', function ($order_id) {

            $invoice_is_mandatory = get_option('wc_settings_factpro_invoice_is_mandatory', 'no') === 'yes';
            $has_invoice = isset($_POST['has_invoice_address']) ? 1 : 0;

            if (! $has_invoice && ! $invoice_is_mandatory) {
                return;
            }

            if (InvoiceType::is_factura($_POST['factpro_invoice_type'])) {
                $identityDocument = IdentityDocument::RUC;
            } else {
                $identityDocument = $_POST['factpro_customer_document_type'];
            }

            $order = wc_get_order($order_id);

            $order->update_meta_data('_factpro_invoice_type', wc_clean($_POST['factpro_invoice_type']));
            $order->update_meta_data('_factpro_customer_document_type', $identityDocument);
            $order->update_meta_data('_factpro_customer_document_number', wc_clean($_POST['factpro_customer_document_number']));

            if (InvoiceType::is_factura($_POST['factpro_invoice_type'])) {
                $isCompany = substr(wc_clean($_POST['factpro_customer_document_number']), 0, 2) === '20';

                $order->update_meta_data('_factpro_company_name', wc_clean($_POST['factpro_company_name']));
                $order->update_meta_data('_factpro_company_address', wc_clean($_POST['factpro_company_address']));

                if ($isCompany) {
                    $order->update_meta_data('_factpro_company_ubigeo', wc_clean($_POST['factpro_company_ubigeo']));
                } else {
                    $order->update_meta_data('_factpro_company_ubigeo', '140101');
                }
            }

            $order->save_meta_data();
        });

        add_action('woocommerce_order_status_completed', [InvoiceGenerator::class, 'generate']);

        /**
         * Display in Website
         */
        add_action('woocommerce_after_checkout_billing_form', function (\WC_Checkout $checkout) {
            print View::make(EBILLING_VIEW_DIR)->render('invoice-address-section', [
                'checkout' => $checkout,
                'identity_documents' => [
                    IdentityDocument::DNI => __('DNI', 'woo-factpro'),
                    IdentityDocument::CARNET_EXTRANJERIA => __('C.E', 'woo-factpro'),
                    IdentityDocument::PASAPORTE => __('Pasaporte', 'woo-factpro')
                ],
                'invoice_is_mandatory' => get_option('wc_settings_factpro_invoice_is_mandatory', 'no') === 'yes',
                'invoices_types' => InvoiceType::getOptions(),
            ]);
        });

        add_action('wp_enqueue_scripts', function () {

            $publicUrl = plugins_url('public', EBILLING_PLUGIN_FILE);

            wp_register_script('woo_checkout', $publicUrl . '/woo_checkout.js', ['jquery'], 1.2, true);

            if (is_checkout()) {
                wp_enqueue_script('woo_checkout');
                wp_localize_script('woo_checkout', 'factproSettings', [
                    'root' => esc_url_raw(rest_url('woo-factpro/v1')),
                    'nonce' => wp_create_nonce('wp_rest'),
                ]);
            }
        });
    }
}
