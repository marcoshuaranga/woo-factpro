<?php

namespace EBilling\Domain;

final class DiscountLine
{
    private $code;

    private $description;

    private $subtotal;

    private $tax;

    public function __construct($description, $subtotal, $tax)
    {
        $this->code = '02';
        $this->description = $description;
        $this->subtotal = $subtotal;
        $this->tax = $tax;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function getSubtotal()
    {
        return $this->subtotal;
    }

    public function getTax()
    {
        return $this->tax;
    }

    public function toArray()
    {
        return [
            'codigo' => $this->code,
            'descripcion' => $this->description,
            'porcentaje' => 1,
            'monto' => round($this->subtotal, 2),
            'base' => round($this->subtotal, 2),
        ];
    }
}
