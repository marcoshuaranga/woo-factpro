<?php

namespace EBilling;

use EBilling\Domain\Invoice;
use EBilling\InvoiceFormatter\OldPseFormatter;

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
        $json = json_encode($formatter->toArray());
        $handler = curl_init($this->url);
        $headers = [
            'Content-Type: application/json; charset=utf-8',
            'Content-Length: ' . strlen($json),
            'Authorization: Bearer ' . $this->token
        ];

        if ($formatter->is(OldPseFormatter::class)) {
            array_push($headers, 'x-access-token: ' . $this->token);
        }

        curl_setopt($handler, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($handler, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($handler, CURLOPT_POSTFIELDS, $json);
        curl_setopt($handler, CURLOPT_HTTPHEADER, $headers);

        $result = curl_exec($handler);

        curl_close($handler);

        $this->logger->info(
            'Response for order #' . $invoice->getOrderId() . ': ' . PHP_EOL .
            json_encode(json_decode($result), JSON_PRETTY_PRINT) . PHP_EOL,
            ['source' => 'woo-ebilling']
        );

        return $result;
    }
}
