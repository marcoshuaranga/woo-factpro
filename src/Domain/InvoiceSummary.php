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

    public function getTotal()
    {
        return $this->totalVentas;
    }

    public function toArray()
    {
        return [
            'total_exportacion' => round(0, 2),
            'total_descuentos' => round($this->globalDiscount->getSubtotal(), 2),
            'total_operaciones_gravadas' => round($this->totalGravadas, 2),
            'total_operaciones_inafectas' => round(0, 2),
            'total_operaciones_exoneradas' => round($this->totalExonerado, 2),
            'total_operaciones_gratuitas' => round(0, 2),
            'total_igv' => round($this->totalIgv, 2),
            'total_impuestos' => round($this->totalImpuestos, 2),
            'total_valor' => round($this->totalValor, 2),
            'total_venta' => round($this->totalVentas, 2),
        ];
    }
}
