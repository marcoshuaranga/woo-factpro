<?php

namespace EBilling\Domain;

final class InvoiceItem
{
    const TAX_EXEMPTION_GRAVADO = '10';
    const TAX_EXEMPTION_EXONERADO = '20';

    private $id;
    private $sku;
    private $description;
    private $quantity;
    private $unitOfMeasure;
    private $unitValue;
    private $unitPrice;
    private $subtotal;
    private $totalIgv;
    private $totalImpuestos;
    private $totalValorItem;
    private $total;
    private $taxExemptionReasonCode;

    public function __construct(
        $id,
        $sku,
        $description,
        $quantity,
        $unitOfMeasure,
        $unitValue,
        $unitPrice,
        $subtotal,
        $totalIgv,
        $total,
        $taxExemptionReasonCode = InvoiceItem::TAX_EXEMPTION_GRAVADO
    ) {
        $this->id = $id;
        $this->sku = $sku;
        $this->description = $description;
        $this->quantity = $quantity;
        $this->unitOfMeasure = $unitOfMeasure;
        $this->unitValue = $unitValue;
        $this->unitPrice = $unitPrice;
        $this->subtotal = $subtotal;
        $this->totalIgv = $totalIgv;
        $this->totalValorItem = $subtotal;
        $this->totalImpuestos = $totalIgv;
        $this->total = $total;
        $this->taxExemptionReasonCode = $taxExemptionReasonCode;
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
    public static function createFromWooExtraItem(\WC_Order_Item $item)
    {
        $subtotal_item = (float) $item->get_total();
        $tax_item  = (float) $item->get_total_tax();
        $includeTax = $tax_item > 0;
        $total_item = $subtotal_item + $tax_item;
        $unitValue = $subtotal_item / $item->get_quantity();
        $unitPrice = $unitValue * ($includeTax ?  1.18 : 1);

        return new self(
            $item->get_id(),
            '',
            $item->get_name(),
            $item->get_quantity(),
            'ZZ',
            round($unitValue, 2),
            round($unitPrice, 2),
            $subtotal_item,
            $tax_item,
            $total_item,
            $includeTax ? InvoiceItem::TAX_EXEMPTION_GRAVADO : InvoiceItem::TAX_EXEMPTION_EXONERADO
        );
    }

    /**
     * @param float $totalSaleAmount
     * @param float $totalDiscount
     * 
     * @return float
     */
    public function applyDiscount($totalSaleAmount, $totalDiscount)
    {
        $discount = $this->total / $totalSaleAmount * $totalDiscount;

        if ($this->taxExemptionReasonCode === self::TAX_EXEMPTION_GRAVADO) {
            $discount /= 1.18;
        }

        $this->subtotal -= $discount;
        $this->totalIgv -= ($this->totalIgv > 0) ? $discount * 0.18 : 0;

        return $discount;
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

    /**
     * @return float
     */
    public function getSubtotal()
    {
        return $this->subtotal;
    }

    /**
     * @return float
     */
    public function getTotalIgv()
    {
        return $this->totalIgv;
    }

    /**
     * @return float
     */
    public function getTotalExonerado()
    {
        return $this->taxExemptionReasonCode === self::TAX_EXEMPTION_EXONERADO ? $this->subtotal : 0;
    }

    /**
     * @return float
     */
    public function getTotalImpuestos()
    {
        return $this->totalImpuestos;
    }

    /**
     * @return float
     */
    public function getTotalValorItem()
    {
        return $this->totalValorItem;
    }

    /**
     * @return float
     */
    public function getTotal()
    {
        return $this->total;
    }

    public function getTaxExemptionReasonCode()
    {
        return $this->taxExemptionReasonCode;
    }
}
