<?php

namespace EBilling\SunatCode;

final class InvoiceType
{
    const FACTURA = '01';
    const BOLETA = '03';

    public static function getOptions()
    {
        return [
            self::FACTURA => 'Factura',
            self::BOLETA => 'Boleta',
        ];
    }

    public static function get_identity_document($invoiceType)
    {
        if (self::FACTURA === $invoiceType) {
            return IdentityDocument::RUC;
        }

        return IdentityDocument::DNI;
    }

    public static function is_boleta($invoiceType)
    {
        return self::BOLETA === $invoiceType;
    }

    public static function is_factura($invoiceType)
    {
        return self::FACTURA === $invoiceType;
    }
}
