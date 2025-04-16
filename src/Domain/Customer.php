<?php

namespace Factpro\Domain;

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

    private $customerNote;

    public function __construct(
        $documentType,
        $documentNumber,
        $nameOrCompany,
        $address,
        $email,
        $postalCode,
        $phoneNumber,
        $customerNote
    ) {
        $this->documentType = $documentType;
        $this->documentNumber = $documentNumber;
        $this->nameOrCompany = $nameOrCompany;
        $this->address = $address;
        $this->email = $email;
        $this->postalCode = $postalCode;
        $this->phoneNumber = $phoneNumber;
        $this->customerNote = $customerNote;
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

    public function getCustomerNote()
    {
        return $this->customerNote;
    }
}
