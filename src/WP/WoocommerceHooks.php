<?php

namespace Factpro\WP;

defined('ABSPATH') || exit;

use Factpro\Helper\View;
use Factpro\SunatCode\IdentityDocument;
use Factpro\SunatCode\InvoiceType;
use Factpro\Woo\Actions\CreateInvoice;

final class WoocommerceHooks
{
    public static function init()
    {
        add_filter('woocommerce_my_account_my_orders_actions', function ($actions, $order) {

            if ($order->get_meta('_factpro_invoice_pdf_url')) {
                $actions['invoice'] = array(
                    'url'  => $order->get_meta('_factpro_invoice_pdf_url'),
                    'name' => __('Ver Comprobante', 'factpro-for-woocommerce')
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
                        wc_add_notice(__('El tipo de documento no es válido.', 'factpro-for-woocommerce'), 'error');
                    }

                    ! $_POST['factpro_customer_document_number'] && wc_add_notice(__('El número de documento no es válido.', 'factpro-for-woocommerce'), 'error');
                    break;
                case InvoiceType::FACTURA:
                    ! $_POST['factpro_company_ruc'] && wc_add_notice(__('El número de RUC no es válido.', 'factpro-for-woocommerce'), 'error');
                    ! $_POST['factpro_company_name'] && wc_add_notice(__('El nombre de razón social no es válido.', 'factpro-for-woocommerce'), 'error');
                    ! $_POST['factpro_company_address'] && wc_add_notice(__('El domicilio fiscal no es es válido.', 'factpro-for-woocommerce'), 'error');

                    if (\strlen($_POST['factpro_company_ruc']) !== 11) {
                        wc_add_notice(__('El RUC debe tener 11 dígitos.', 'factpro-for-woocommerce'), 'error');
                    }

                    break;
                default:
                    wc_add_notice(__('Debe seleccionar el tipo de comprobante.', 'factpro-for-woocommerce'), 'error');
            }
        });

        add_action('woocommerce_checkout_update_order_meta', function ($order_id) {

            $invoice_is_mandatory = get_option('wc_settings_factpro_invoice_is_mandatory', 'no') === 'yes';
            $has_invoice = isset($_POST['has_invoice_address']) ? 1 : 0;

            if (! $has_invoice && ! $invoice_is_mandatory) {
                return;
            }

            $order = wc_get_order($order_id);

            $order->update_meta_data('_factpro_invoice_type', wc_clean($_POST['factpro_invoice_type']));

            if (InvoiceType::is_factura($_POST['factpro_invoice_type'])) {
                $company_ruc = wc_clean($_POST['factpro_company_ruc']);
                $isCompany = substr(wc_clean($_POST['factpro_company_ruc']), 0, 2) === '20';

                $order->update_meta_data('_factpro_company_name', wc_clean($_POST['factpro_company_name']));
                $order->update_meta_data('_factpro_company_address', wc_clean($_POST['factpro_company_address']));
                $order->update_meta_data('_factpro_company_ubigeo', $isCompany ? wc_clean($_POST['factpro_company_ubigeo']) : '');
                $order->update_meta_data('_factpro_company_ruc', $company_ruc);
                $order->update_meta_data('_factpro_customer_document_type', IdentityDocument::RUC);
                $order->update_meta_data('_factpro_customer_document_number', $company_ruc);
            } else if (InvoiceType::is_boleta($_POST['factpro_invoice_type'])) {
                $order->update_meta_data('_factpro_company_name', '');
                $order->update_meta_data('_factpro_company_address', wc_clean($_POST['factpro_company_address']));
                $order->update_meta_data('_factpro_company_ubigeo', '');
                $order->update_meta_data('_factpro_customer_document_type', wc_clean($_POST['factpro_customer_document_type']));
                $order->update_meta_data('_factpro_customer_document_number', wc_clean($_POST['factpro_customer_document_number']));
            }

            $order->save_meta_data();
        });

        add_action('woocommerce_order_status_completed', [CreateInvoice::class, 'invoke']);
    }

    public static function initWoocommerceFields()
    {
        /**
         * Display in Website
         */
        add_action('woocommerce_after_checkout_billing_form', function (\WC_Checkout $checkout) {
            $html = View::make(WOO_FACTPRO_VIEW_DIR)->render('invoice-address-section', [
                'checkout' => $checkout,
                'identity_documents' => [
                    IdentityDocument::DNI => __('DNI', 'factpro-for-woocommerce'),
                    IdentityDocument::CARNET_EXTRANJERIA => __('C.E', 'factpro-for-woocommerce'),
                    IdentityDocument::PASAPORTE => __('Pasaporte', 'factpro-for-woocommerce')
                ],
                'invoice_is_mandatory' => get_option('wc_settings_factpro_invoice_is_mandatory', 'no') === 'yes',
                'invoices_types' => InvoiceType::getOptions(),
            ]);

            $allowed_html = [
                'style' => [],
                'div' => ['class' => true, 'style' => true, 'id' => true],
                'h3' => ['id' => true, 'class' => true],
                'label' => ['class' => true, 'for' => true],
                'input' => [
                    'type' => true,
                    'id' => true,
                    'name' => true,
                    'value' => true,
                    'class' => true,
                    'checked' => true,
                    'placeholder' => true,
                ],
                'span' => ['class' => true],
                'select' => [
                    'id' => true,
                    'name' => true,
                    'class' => true,
                    'data-placeholder' => true,
                ],
                'option' => ['value' => true, 'selected' => true],
                'p' => ['class' => true, 'id' => true],
            ];

            echo wp_kses($html, $allowed_html);
        });

        add_action('wp_enqueue_scripts', function () {

            $publicUrl = plugins_url('public', WOO_FACTPRO_PLUGIN_FILE);

            wp_register_script('woo_checkout', $publicUrl . '/woo_checkout.js', ['jquery'], 1.2, true);

            if (is_checkout()) {
                wp_enqueue_script('woo_checkout');
                wp_localize_script('woo_checkout', 'factproSettings', [
                    'root' => esc_url_raw(rest_url('factpro-for-woocommerce/v1')),
                    'nonce' => wp_create_nonce('wp_rest'),
                ]);
            }
        });
    }

    public static function initBlockFields()
    {
        woocommerce_register_additional_checkout_field([
            'id' => 'factpro/invoice_is_mandatory',
            'label' => __('¿Necesitas un comprobante electrónico?', 'factpro-for-woocommerce'),
            // 'optionalLabel' => __('¿Necesitas un comprobante electrónico?', 'factpro-for-woocommerce'),
            'location' => 'contact',
            'type' => 'checkbox',
            'attributes' => [],
            'required' => false,
            'hidden' => false,
            'validation' => [],
            'sanitize_callback' => fn($value) => $value,
            'validate_callback' => fn($value) => null,
        ]);

        woocommerce_register_additional_checkout_field([
            'id' => 'factpro/invoice_type',
            'label' => __('Tipo de Comprobante', 'factpro-for-woocommerce'),
            // 'optionalLabel' => __('¿Cómo nos conociste?', 'factpro-for-woocommerce'),
            'location' => 'contact',
            'type' => 'select',
            'attributes' => [],
            'required' => false,
            'hidden' => false,
            'validation' => [],
            'placeholder' => __('Seleccione un tipo de comprobante', 'factpro-for-woocommerce'),
            'options' => [
                ['value' => InvoiceType::FACTURA, 'label' => __('Factura', 'factpro-for-woocommerce')],
                ['value' => InvoiceType::BOLETA, 'label' => __('Boleta', 'factpro-for-woocommerce')],
            ]
        ]);

        woocommerce_register_additional_checkout_field([
            'id' => 'factpro/customer_document_type',
            'label' => __('Tipo de Documento', 'factpro-for-woocommerce'),
            // 'optionalLabel' => __('¿Necesitas un comprobante electrónico?', 'factpro-for-woocommerce'),
            'location' => 'contact',
            'type' => 'select',
            'required' => false,
            'hidden' => [
                'type' => 'object',
                'properties' => [],
            ],
            'validation' => [],
            'placeholder' => __('Seleccione un tipo de documento', 'factpro-for-woocommerce'),
            'options' => [
                ['value' => IdentityDocument::DNI, 'label' => __('DNI', 'factpro-for-woocommerce')],
                ['value' => IdentityDocument::CARNET_EXTRANJERIA, 'label' => __('C.E', 'factpro-for-woocommerce')],
                ['value' => IdentityDocument::PASAPORTE, 'label' => __('Pasaporte', 'factpro-for-woocommerce')],
            ]
        ]);

        woocommerce_register_additional_checkout_field([
            'id' => 'factpro/customer_document_number',
            'label' => __('Número de Documento', 'factpro-for-woocommerce'),
            // 'optionalLabel' => __('¿Necesitas un comprobante electrónico?', 'factpro-for-woocommerce'),
            'location' => 'contact',
            'type' => 'text',
            'attributes' => [
                'pattern' => '[0-9]{8}',
            ],
            'required' => false,
            'hidden' => false,
            'validation' => [],
        ]);

        woocommerce_register_additional_checkout_field([
            'id' => 'factpro/company_vat_number',
            'label' => __('RUC', 'factpro-for-woocommerce'),
            //  'optionalLabel' => __('¿Necaaaarónico?', 'factpro-for-woocommerce'),
            'location' => 'contact',
            'type' => 'text',
            'attributes' => [
                'pattern' => '[0-9]{11}',
                'title' => __('El RUC debe tener 11 dígitos', 'factpro-for-woocommerce'),
            ],
            'required' => false,
            'hidden' => false,
            'validation' => [],
        ]);

        woocommerce_register_additional_checkout_field([
            'id' => 'factpro/company_name',
            'label' => __('Nombre de razón social', 'factpro-for-woocommerce'),
            // 'optionalLabel' => __('¿Necesitas un comprobante electrónico?', 'factpro-for-woocommerce'),
            'location' => 'contact',
            'type' => 'text',
            'attributes' => [
                'pattern' => '[a-zA-Z0-9\s]+',
            ],
            'required' => false,
            'hidden' => false,
            'validation' => [],
        ]);

        woocommerce_register_additional_checkout_field([
            'id' => 'factpro/company_address',
            'label' => __('Direccción', 'factpro-for-woocommerce'),
            // 'optionalLabel' => __('¿Necesitas un comprobante electrónico?', 'factpro-for-woocommerce'),
            'location' => 'contact',
            'type' => 'text',
            'attributes' => [],
            'required' => false,
            'hidden' => false,
            'validation' => [],
        ]);

        woocommerce_register_additional_checkout_field([
            'id' => 'factpro/company_ubigeo',
            'label' => __('Ubigeo', 'factpro-for-woocommerce'),
            // 'optionalLabel' => __('¿Necesitas un comprobante electrónico?', 'factpro-for-woocommerce'),
            'location' => 'contact',
            'type' => 'text',
            'attributes' => [],
            'required' => false,
            'hidden' => false,
            'validation' => [],
        ]);
    }
}
