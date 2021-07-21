<?php

namespace EBilling\Domain;

final class InvoiceItem
{
    const TAX_EXEMPTION_GRAVADO = '10';
    const TAX_EXEMPTION_EXONERADO = '20';

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
            $subtotal_item = round($total_item / 1.18, 2);
            $tax_item = $total_item - $subtotal_item;
        }

        $unitValue = $subtotal_item / $item->get_quantity();
        $unitPrice = $unitValue * 1.18;

        return new self(
            $item->get_product()->get_sku() ?: $item->get_product()->get_id(), 
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

    public function toArray()
    {
        return [
            'unidad_de_medida' => $this->unitOfMeasure,
            'codigo_interno' => $this->sku,
            'descripcion' => $this->description,
            'codigo_producto_sunat' => '',
            'cantidad' => $this->quantity,
            'valor_unitario' => $this->unitValue,
            'codigo_tipo_precio' => '01',
            'precio_unitario' => $this->unitPrice,
            'codigo_tipo_afectacion_igv' => $this->taxExemptionReasonCode,
            'total_base_igv' => $this->subtotal,
            'porcentaje_igv' => ($this->totalIgv > 0) ? 18 : 0,
            'total_igv' => $this->totalIgv,
            'total_impuestos' => $this->totalImpuestos,
            'total_valor_item' => $this->totalValorItem,
            'total_item' => $this->total,
        ];
    }
}
