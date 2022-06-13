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

    public function getCode()
    {
        return $this->code;
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
}
