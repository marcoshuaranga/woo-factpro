<?php

namespace Factpro\Domain;

use Automattic\WooCommerce\Utilities\NumberUtil;

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
        $subtotal_item = NumberUtil::round($item->get_subtotal(), 4);
        $tax_item = NumberUtil::round($item->get_subtotal_tax(), 4);
        $total_item = $subtotal_item + $tax_item;
        $has_dicount = $item->get_subtotal() !== $item->get_total();
        $is_taxable = $item->get_tax_status() === 'taxable';
        $is_zero_rate = $item->get_tax_class() === 'zero-rate';

        //No tiene los impuestos configurados. Por tanto, se debe extraer el subtotal y el igv.
        if (! $is_taxable) {
            $subtotal_item = NumberUtil::round($total_item / 1.18, 4);
            $tax_item = NumberUtil::round($total_item - $subtotal_item, 4);
        }

        $unitValue = NumberUtil::round($subtotal_item / $item->get_quantity(), 4);
        $unitPrice = NumberUtil::round($unitValue * ($is_zero_rate ? 1 : 1.18), 4);

        return new self(
            $item->get_id(),
            $item->get_product()->get_sku(),
            $item->get_name(),
            $item->get_quantity(),
            'NIU',
            $unitValue,
            $unitPrice,
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
        $subtotal_item = NumberUtil::round($item->get_total(), 4);
        $tax_item  = NumberUtil::round($item->get_total_tax(), 4);
        $has_tax = $tax_item > 0;
        $total_item = $subtotal_item + $tax_item;
        $unitValue = NumberUtil::round($subtotal_item / $item->get_quantity(), 4);
        $unitPrice = NumberUtil::round($unitValue * ($has_tax ?  1.18 : 1), 4);

        return new self(
            $item->get_id(),
            $sku,
            $item->get_name(),
            $item->get_quantity(),
            'ZZ',
            $unitValue,
            $unitPrice,
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
