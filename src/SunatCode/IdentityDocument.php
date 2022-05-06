<?php

namespace EBilling\SunatCode;

final class IdentityDocument
{
    const NO_IDENTITY_DOCUMENT = '0';
    const DNI = '1';
    const CARNET_EXTRANJERIA = '4';
    const RUC = '6';
    const PASAPORTE = '7';

    public static function getOptions()
    {
        return [
            self::NO_IDENTITY_DOCUMENT => 'Seleccione un documento',
            self::DNI => 'DNI',
            self::CARNET_EXTRANJERIA => 'Carnet de extranjería',
            self::RUC => 'RUC',
            self::PASAPORTE => 'Pasaporte',
        ];
    }
}
