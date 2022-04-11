<?php

namespace EBilling;

use EBilling\Domain\Invoice;

interface InvoiceSender
{
    const FACTPSE = 'factpse';
    const PSE = 'pse';

    public function getRequestDetails();

    public function send(Invoice $invoice);
}
