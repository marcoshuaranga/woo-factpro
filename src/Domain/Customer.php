<?php

namespace EBilling\Domain;

final class Customer
{
    const COUNTRY = 'PE';

    private $countryCode = self::COUNTRY;

    private $documentType;

    private $documentNumber;

    private $nameOrCompany;

    private $address;

    private $email;

    private $postalCode;

    private $phoneNumber;

    public function __construct(
        $documentType,
        $documentNumber,
        $nameOrCompany,
        $address,
        $email,
        $postalCode,
        $phoneNumber
    ) {
        $this->documentType = $documentType;
        $this->documentNumber = $documentNumber;
        $this->nameOrCompany = $nameOrCompany;
        $this->address = $address;
        $this->email = $email;
        $this->postalCode = $postalCode;
        $this->phoneNumber = $phoneNumber;
    }

    public function getCountryCode()
    {
        return $this->countryCode;
    }

    public function getDocumentType()
    {
        return $this->documentType;
    }

    public function getDocumentNumber()
    {
        return $this->documentNumber;
    }

    public function getNameOrCompany()
    {
        return $this->nameOrCompany;
    }

    public function getAddress()
    {
        return $this->address;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function getPostalCode()
    {
        return $this->postalCode;
    }
    
    public function getPhoneNumber()
    {
        return $this->phoneNumber;
    }

    public function toArray()
    {
        return [
            'codigo_pais' => $this->countryCode,
            'codigo_tipo_documento_identidad' => $this->documentType,
            'numero_documento' => $this->documentNumber,
            'apellidos_y_nombres_o_razon_social' => $this->nameOrCompany,
            'ubigeo' => $this->postalCode,
            'direccion' => $this->address,
            'correo_electronico' => $this->email,
            'telefono' => $this->phoneNumber,
        ];
    }
}
