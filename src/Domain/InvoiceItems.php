<?php

namespace Factpro\Domain;

final class InvoiceItems
{
    private $subtotal = 0;
    private $totalTax = 0;
    private $total = 0;

    private $sunatTotalExonerado = 0;
    private $sunatTotalGravadas = 0;
    private $sunatTotalTax = 0;
    private $sunatTotalDiscount = 0;

    /** @var InvoiceItem[] */
    private $items = [];

    /** @var Discount[] */
    private $discounts = [];

    /**
     * @param \WC_Order_Item_Fee[]|\WC_Order_Item_Product[]|\WC_Order_Item_Shipping[] $items
     * @param \WC_Order_Item_Coupon[] $coupons
     * @param bool $includeTax
     */
    public function __construct(array $items, array $coupons, $includeTax)
    {
        foreach ($items as $item) {
            if ($item->get_total() <= 0) {
                continue;
            }

            if ($item instanceof \WC_Order_Item_Product) {
                $this->items[] = InvoiceItem::createFromWooLineItem($item, $includeTax);
            } elseif ($item instanceof \WC_Order_Item_Fee) {
                $this->items[] = InvoiceItem::createFromWooExtraItem('fee', $item);
            } elseif ($item instanceof \WC_Order_Item_Shipping) {
                $this->items[] = InvoiceItem::createFromWooExtraItem('shipping', $item);
            }
        }

        foreach ($coupons as $coupon) {
            $this->discounts[] = Discount::createFromWooCoupon($coupon);
        }

        $this->calculateTotals();
    }

    private function calculateTotals()
    {
        foreach ($this->items as $item) {
            $this->sunatTotalExonerado += $item->isGravado() ? 0 : $item->getSubtotal();
            $this->sunatTotalGravadas += $item->isGravado() ? $item->getSubtotal() : 0;
            $this->sunatTotalTax += $item->getTotalTax();
        }

        foreach ($this->discounts as $discount) {
            $this->sunatTotalDiscount += $discount->getSubtotal();

            // Apply discount to subtotal
            // $this->sunatTotalGravadas -= $discount->getSubtotal();
            // $this->sunatTotalTax -= $discount->getTotalTax();
        }

        // count($this->discounts) && $this->applyDiscounts();

        $this->subtotal = $this->sunatTotalGravadas + $this->sunatTotalExonerado;
        $this->totalTax = $this->sunatTotalTax;
        $this->total = $this->subtotal + $this->totalTax;
    }

    public function getDiscounts()
    {
        return $this->discounts;
    }

    public function getItems()
    {
        return $this->items;
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

    public function getSunatTotalDiscount()
    {
        return $this->sunatTotalDiscount;
    }

    public function getSunatTotalExonerado()
    {
        return $this->sunatTotalExonerado;
    }

    public function getSunatTotalGravadas()
    {
        return $this->sunatTotalGravadas;
    }

    public function getSunatTotalTax()
    {
        return $this->sunatTotalTax;
    }
}
