<?php

namespace EBilling\Domain;

final class InvoiceItem
{
    private $id;
    private $sku;
    private $description;
    private $quantity;
    private $unitOfMeasure;
    private $unitValue;
    private $unitPrice;
    private $subtotal;
    private $totalTax;
    private $total;

    public function __construct(
        $id,
        $sku,
        $description,
        $quantity,
        $unitOfMeasure,
        $unitValue,
        $unitPrice,
        $subtotal,
        $totalTax,
        $total
    ) {
        $this->id = $id;
        $this->sku = $sku;
        $this->description = $description;
        $this->quantity = $quantity;
        $this->unitOfMeasure = $unitOfMeasure;
        $this->unitValue = $unitValue;
        $this->unitPrice = $unitPrice;
        $this->subtotal = $subtotal;
        $this->totalTax = $totalTax;
        $this->total = $total;
    }

    public static function createFromWooLineItem(\WC_Order_Item_Product $item, $includeTax)
    {
        $subtotal_item = (float) $item->get_subtotal();
        $tax_item = (float) $item->get_subtotal_tax();
        $total_item = $subtotal_item + $tax_item;

        //No tiene los impuestos configurados. Por lo tanto debo extraer el subtotal y el igv.
        if (! $includeTax) {
            $subtotal_item = round($total_item / 1.18, 4);
            $tax_item = round($total_item - $subtotal_item, 4);
        }

        $unitValue = $subtotal_item / $item->get_quantity();
        $unitPrice = $unitValue * 1.18;

        return new self(
            $item->get_id(),
            $item->get_product()->get_sku(), 
            $item->get_name(), 
            $item->get_quantity(),
            'NIU',
            round($unitValue, 2),
            round($unitPrice, 2),
            $subtotal_item,
            $tax_item,
            $total_item
        );
    }

    /**
     * @param \WC_Order_Item_Shipping|\WC_Order_Item_Fee $item
     */
    public static function createFromWooExtraItem($sku, \WC_Order_Item $item)
    {
        $subtotal_item = (float) $item->get_total();
        $tax_item  = (float) $item->get_total_tax();
        $includeTax = $tax_item > 0;
        $total_item = $subtotal_item + $tax_item;
        $unitValue = $subtotal_item / $item->get_quantity();
        $unitPrice = $unitValue * ($includeTax ?  1.18 : 1);

        return new self(
            $item->get_id(),
            $sku,
            $item->get_name(),
            $item->get_quantity(),
            'ZZ',
            round($unitValue, 2),
            round($unitPrice, 2),
            $subtotal_item,
            $tax_item,
            $total_item,
        );
    }

    public function isGravado()
    {
        return $this->totalTax > 0;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getSku()
    {
        return $this->sku;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function getQuantity()
    {
        return $this->quantity;
    }

    public function getUnitOfMeasure()
    {
        return $this->unitOfMeasure;
    }

    public function getUnitValue()
    {
        return $this->unitValue;
    }

    public function getUnitPrice()
    {
        return $this->unitPrice;
    }

    public function getSubtotal()
    {
        return $this->subtotal;
    }

    public function getTotalTax()
    {
        return $this->totalTax;
    }

    public function getTotal()
    {
        return $this->total;
    }
}
