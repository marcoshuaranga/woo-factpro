<?php

namespace Factpro\WP;

defined('ABSPATH') || exit;

use Factpro\Helper\View;

final class WoocommerceEmailHooks
{
    public static function init()
    {
        add_action('woocommerce_email_after_order_table', function (\WC_Order $order, $send_to_admin, $_, $email) {
            $emailIsNotAnOrderCompleted = !($email instanceof \WC_Email_Customer_Completed_Order);
            $emailIsNotACustomerInvoice = !($email instanceof \WC_Email_Customer_Invoice);

            if (! $order->has_status('completed')) {
                return;
            }

            if ($emailIsNotAnOrderCompleted && $emailIsNotACustomerInvoice) {
                return;
            }

            /**
             * This is because when the order is completed, the ticket or invoice is not yet generated.
             * So there is no link in the database.
             */
            $factpro_invoice_pdf_url = add_query_arg([
                'action' => 'factpro_download_invoice',
                'order' => $order->get_id(),
                'key' => $order->get_order_key(),
            ], admin_url('admin-post.php'));

            $html = View::make(WOO_FACTPRO_VIEW_DIR)->render('emails/email-factpro-pdf-url', [
                'factpro_invoice_pdf_url' =>  $factpro_invoice_pdf_url,
            ]);

            echo wp_kses($html, [
                'div' => ['style' => true],
                'h2' => [],
                'p' => [],
                'strong' => [],
                'a' => ['href' => true, 'target' => true],
            ]);
        }, 10, 4);
    }
}
