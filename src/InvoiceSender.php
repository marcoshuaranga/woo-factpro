<?php

namespace EBilling;

use EBilling\Domain\Invoice;
use EBilling\InvoiceFormatter\OldPseFormatter;
use GuzzleHttp\Client;

final class InvoiceSender
{
    private Client $http;

    private $url;

    private $token;

    /** @var \WC_Logger */
    private $logger;

    public function __construct($url, $token, \WC_Logger $logger)
    {
        $this->http = new Client([
            'http_errors' => false,
            'headers' => [
                'Authorization' => 'Bearer ' . $token,
                'Accept' => 'application/json',
                'Content-Type' => 'application/json; charset=utf-8',
            ]
        ]);

        $this->url = $url;
        $this->token = $token;
        $this->logger = $logger;
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

        $response = $this->http->post($this->url, [
            'json' => $formatter->toArray()
        ]);

        $jsonResponse = $response->getBody()->getContents();

        if (in_array($response->getStatusCode(), [200, 201])) {
            $this->logger->info(
                'Response for order #' . $invoice->getOrderId() . ': ' . PHP_EOL .
                $jsonResponse . PHP_EOL,
                ['source' => 'woo-ebilling']
            );
        } else {
            $this->logger->error(
                'Error sending invoice for order #' . $invoice->getOrderId() . ': ' . PHP_EOL .
                $jsonResponse . PHP_EOL,
                ['source' => 'woo-ebilling', 'body' => $formatter->toArray()]
            );
        }

        return $jsonResponse;
    }
}
