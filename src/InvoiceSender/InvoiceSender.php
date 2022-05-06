<?php

namespace EBilling\InvoiceSender;

use EBilling\Domain\Invoice;

interface InvoiceSender
{
    public function getRequestDetails();

    public function send(Invoice $invoice);
}
