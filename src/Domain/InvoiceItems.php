<?php

namespace Factpro\Domain;

use Automattic\WooCommerce\Utilities\NumberUtil;

final class InvoiceItems
{
    private $subtotal = 0;
    private $totalTax = 0;
    private $total = 0;

    private $sunatTotalExonerado = 0;
    private $sunatTotalGravadas = 0;
    private $sunatTotalTax = 0;

    /** @var InvoiceItem[] */
    private $items = [];

    private $orderTotalDiscount;
    private $orderTotalShipping;
    private $orderTotalPaid;

    private function __construct(\WC_Order $order)
    {
        $items = $order->get_items(['line_item', 'shipping', 'fee']);
        $pricesIncludeTax = $order->get_prices_include_tax();

        /**
         * @var \WC_Order_Item_Product|\WC_Order_Item_Fee|\WC_Order_Item_Shipping $item
         */
        foreach ($items as $item) {
            if ($item->get_total() <= 0) {
                continue;
            }

            if ($item instanceof \WC_Order_Item_Product) {
                $this->items[] = InvoiceItem::createFromWooLineItem($item, $pricesIncludeTax);
            } elseif ($item instanceof \WC_Order_Item_Fee) {
                $this->items[] = InvoiceItem::createFromWooExtraItem('fee', $item);
            } elseif ($item instanceof \WC_Order_Item_Shipping) {
                $this->items[] = InvoiceItem::createFromWooExtraItem('shipping', $item);
            }
        }

        foreach ($this->items as $item) {
            $this->sunatTotalExonerado += $item->isGravado() ? 0 : $item->getSubtotal();
            $this->sunatTotalGravadas += $item->isGravado() ? $item->getSubtotal() : 0;
            $this->sunatTotalTax += $item->getTotalTax();
        }

        $this->subtotal = $this->sunatTotalGravadas + $this->sunatTotalExonerado;
        $this->totalTax = $this->sunatTotalTax;
        $this->total = $this->subtotal + $this->totalTax;

        $this->orderTotalDiscount = array_reduce(
            $order->get_items('coupon'),
            function ($acc, \WC_Order_Item_Coupon $item) {
                return $acc + $item->get_discount() + $item->get_discount_tax();
            },
            0
        );

        $this->orderTotalShipping = array_reduce(
            $order->get_items('shipping'),
            function ($acc, \WC_Order_Item_Shipping $item) {
                return $acc + $item->get_total() + $item->get_total_tax();
            },
            0
        );

        $this->orderTotalPaid = NumberUtil::round($order->get_total(), 2);
    }

    public static function createFromWooOrder(\WC_Order $order)
    {
        return new self($order);
    }

    public function hasDiscount()
    {
        return $this->orderTotalDiscount > 0;
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

    public function getSunatTotalExonerado()
    {
        return NumberUtil::round($this->sunatTotalExonerado, 4);
    }

    public function getSunatTotalGravadas($applyDiscount = false)
    {
        if ($applyDiscount) {
            return NumberUtil::round($this->sunatTotalGravadas, 4) - $this->getTotalDiscountForSunat();
        }

        return NumberUtil::round($this->sunatTotalGravadas, 4);
    }

    public function getSunatTotalTax($applyDiscount = false)
    {
        if ($applyDiscount) {
            return NumberUtil::round($this->sunatTotalTax, 4) - $this->getTotalDiscountTaxForSunat();
        }

        return NumberUtil::round($this->sunatTotalTax, 4);
    }

    public function getSunatDiscountPercentage()
    {
        return NumberUtil::round($this->getTotalDiscountForSunat() * 100 / $this->getSunatTotalGravadas(), 4) / 100;
    }

    public function getTotalDiscount(): float
    {
        return NumberUtil::round($this->orderTotalDiscount, 4);
    }

    public function getTotalDiscountForSunat()
    {
        return NumberUtil::round($this->orderTotalDiscount / 1.18, 4);
    }

    public function getTotalDiscountTaxForSunat()
    {
        return $this->getTotalDiscount() - $this->getTotalDiscountForSunat();
    }


    public function getTotalShipping(): float
    {
        return NumberUtil::round($this->orderTotalShipping, 4);
    }

    public function getTotalPaid(): float
    {
        return NumberUtil::round($this->orderTotalPaid, 4);
    }
}
