<?php

namespace EBilling\Sunat\Response;

final class RucResponse {
    private $companyName;

    private $companyAddress;

    private $ubigeo;

    public function __construct($companyName, $companyAddress, $ubigeo)
    {
        $this->companyName = $companyName;
        $this->companyAddress = $companyAddress;
        $this->ubigeo = $ubigeo;
    }

    public function toArray()
    {
        return [
            'nombre_o_razon_social' => $this->companyName,
            'direccion_completa' => $this->companyAddress,
            'ubigeo' => $this->ubigeo,
        ];
    }
}
