<?php

namespace Factpro;

use Factpro\Domain\Invoice;
use Factpro\InvoiceFormatter\OldPseFormatter;

final class InvoiceSender
{
    private $url;

    private $token;

    /** @var \WC_Logger */
    private $logger;

    public function __construct($url, $token, \WC_Logger $logger)
    {
        $this->url = $url;
        $this->token = $token;
        $this->logger = $logger;

        add_action('http_api_debug', function ($response, $type, $class, $args, $url) {
            return;

            $this->logger->info(
                'Request to ' . $url . PHP_EOL .
                    'Type: ' . $type . PHP_EOL .
                    'Class: ' . $class . PHP_EOL .
                    'Args: ' . print_r($args, true) . PHP_EOL .
                    'Response: ' . print_r($response, true),
                ['source' => 'woo-factpro']
            );
        }, 10, 5);
    }

    public function getUrl()
    {
        return $this->url;
    }

    public function getToken()
    {
        return $this->token;
    }

    public function send(Invoice $invoice)
    {
        $formatter = new InvoiceFormatter($invoice, $this->url);

        if ($formatter->is(OldPseFormatter::class)) {
            array_push($headers, 'x-access-token: ' . $this->token);
        }

        $response = wp_remote_post($this->url, [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->token,
                'Accept' => 'application/json',
                'Content-Type' => 'application/json; charset=utf-8',
            ],
            'body' => json_encode($formatter->toArray(), true)
        ]);

        if (is_wp_error($response)) {
            throw new \Exception($response->get_error_message());
        }

        $statusCode = wp_remote_retrieve_response_code($response);
        $jsonResponse = wp_remote_retrieve_body($response);

        if (in_array($statusCode, [200, 201])) {
            $this->logger->info(
                'Response for order #' . $invoice->getOrderId() . ': ' . PHP_EOL .
                    $jsonResponse . PHP_EOL,
                ['source' => 'woo-factpro']
            );
        } else {
            $this->logger->error(
                'Error sending invoice for order #' . $invoice->getOrderId() . ': ' . PHP_EOL .
                    $jsonResponse . PHP_EOL,
                ['source' => 'woo-factpro', 'body' => $formatter->toArray()]
            );
        }

        return $jsonResponse;
    }
}
