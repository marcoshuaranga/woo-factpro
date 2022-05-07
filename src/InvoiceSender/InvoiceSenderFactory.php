<?php

namespace EBilling\InvoiceSender;

use EBilling\InvoiceSender\Api\FactPseApi;
use EBilling\InvoiceSender\Api\PseApi;

final class InvoiceSenderFactory
{
    public static function create($url, $token)
    {
        if (\str_contains($url, 'factpse.com')) {
            return new FactPseApi($url, $token);
        } else {
            return new PseApi($url, $token);
        }       
    }
}