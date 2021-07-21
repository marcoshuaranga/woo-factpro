<?php

namespace EBilling\WP;

use EBilling\SunatCode\IdentityDocument;
use EBilling\SunatCode\InvoiceType;
use EBilling\Helper\View;
use WC_Checkout;
use WC_Order;

final class WoocommerceDisplayHooks
{
    public static function init()
    {
        add_action('woocommerce_after_checkout_billing_form', function (WC_Checkout $checkout) {
            print View::make(EBILLING_VIEW_DIR)->render('invoice-address-section', [
                'checkout' => $checkout,
                'invoice_is_mandatory' => get_option('wc_settings_ebilling_invoice_is_mandatory', 'no') === 'yes',
                'invoices_types' => InvoiceType::getOptions(),
            ]);
        });

        add_action('wp_enqueue_scripts', function () {
        
            $publicUrl = plugins_url('public', EBILLING_PLUGIN_FILE);
        
            wp_register_script('woo_checkout', $publicUrl . '/woo_checkout.js', ['jquery'], 1.0, true);
        
            if (is_checkout()) {
                wp_enqueue_script('woo_checkout');
                wp_localize_script('woo_checkout', 'ebillingSettings', [
                    'root' => esc_url_raw( rest_url('woo-ebilling/v1') ),
                    'nonce' => wp_create_nonce( 'wp_rest' ),
                ]);
            }
        });
    }

    public static function initAdminHooks()
    {
        add_action( 'woocommerce_admin_order_data_after_billing_address', function (WC_Order $order) {

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

    public static function initEmailHooks()
    {  
        add_action( 'woocommerce_email_after_order_table', function (WC_Order $order) {

            if (! $order->has_status('completed') && ! $order->get_meta('_ebilling_invoice_type')) {
                return;
            }

            $ebilling_invoice_pdf_url = add_query_arg([
                'action' => 'ebilling_download_invoice',
                'order' => $order->get_id(),
                'key' => $order->get_order_key(),
            ], admin_url('admin-post.php'));

            print View::make(EBILLING_VIEW_DIR)->render('emails/email-ebilling-pdf-url', [
                'ebilling_invoice_pdf_url' =>  $ebilling_invoice_pdf_url,
            ]);
        });

    }
}
