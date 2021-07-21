<?php

namespace EBilling;

use EBilling\Domain\Invoice;

final class InvoiceSender
{
    private $invoiceApi;

    public function __construct($url, $token)
    {
        $this->invoiceApi = new InvoiceApi($url, $token);
    }

    public function requestDetails()
    {
        return $this->invoiceApi->getRequestDetails();
    }

    public function send(Invoice $invoice)
    {
        return $this->invoiceApi->send($invoice->toArray());        
    }
}
