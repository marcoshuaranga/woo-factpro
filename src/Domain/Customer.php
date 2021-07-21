<?php

namespace EBilling\Domain;

final class Customer
{
    const COUNTRY = 'PE';

    private $documentType;

    private $documentNumber;

    private $nameOrCompany;

    private $address;

    private $email;

    private $phoneNumber;

    public function __construct(
        $documentType,
        $documentNumber,
        $nameOrCompany,
        $address,
        $email,
        $phoneNumber
    ) {
        $this->documentType = $documentType;
        $this->documentNumber = $documentNumber;
        $this->nameOrCompany = $nameOrCompany;
        $this->address = $address;
        $this->email = $email;
        $this->phoneNumber = $phoneNumber;
    }

    public function toArray()
    {
        return [
            'codigo_tipo_documento_identidad' => $this->documentType,
            'numero_documento' => $this->documentNumber,
            'apellidos_y_nombres_o_razon_social' => $this->nameOrCompany,
            'codigo_pais' => self::COUNTRY,
            'ubigeo' => '',
            'direccion' => $this->address,
            'correo_electronico' => $this->email,
            'telefono' => $this->phoneNumber,
        ];
    }
}
