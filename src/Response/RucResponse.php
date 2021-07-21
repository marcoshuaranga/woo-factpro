<?php

namespace EBilling\Response;

final class RucResponse {
    private $companyName;

    private $companyAddress;

    public function __construct($companyName, $companyAddress)
    {
        $this->companyName = $companyName;
        $this->companyAddress = $companyAddress;
    }

    public function toArray()
    {
        return [
            'nombre_o_razon_social' => $this->companyName,
            'direccion_completa' => $this->companyAddress,
        ];
    }
}
