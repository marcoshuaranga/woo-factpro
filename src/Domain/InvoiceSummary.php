<?php

namespace EBilling\Domain;

final class InvoiceSummary
{
    private $globalDiscount;

    private $totalGravadas;

    private $totalIgv;

    private $totalExonerado;

    private $totalImpuestos;

    private $totalValor;

    private $totalVentas;

    public function __construct(
        GlobalDiscount $globalDiscount,
        $totalGravadas,
        $totalIgv,
        $totalExonerado,
        $totalVentas
    ) {
        $this->globalDiscount = $globalDiscount;
        $this->totalGravadas = $totalGravadas;
        $this->totalIgv = $totalIgv;
        $this->totalExonerado = $totalExonerado;
        $this->totalImpuestos = $totalIgv;
        $this->totalValor = $totalGravadas + $totalExonerado;
        $this->totalVentas = $totalVentas;
    }

    public function getDiscount()
    {
        return $this->globalDiscount;
    }

    public function getTotalGravadas()
    {
        return $this->totalGravadas;
    }

    public function getTotalIgv()
    {
        return $this->totalIgv;
    }

    public function getTotalExonerado()
    {
        return $this->totalExonerado;
    }

    public function getTotalImpuestos()
    {
        return $this->totalImpuestos;
    }

    public function getTotalValor()
    {
        return $this->totalValor;
    }

    public function getTotal()
    {
        return $this->totalVentas;
    }
}
