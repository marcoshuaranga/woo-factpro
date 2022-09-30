<?php

namespace EBilling\Domain;

final class GlobalDiscount
{
    /**
     * @var DiscountLine[]
     */
    private $items = [];

    /**
     * @param \WC_Order_Item_Coupon[] $coupons
     * @param \WC_Order_Item_Fee[] $feeItems
     */
    public function __construct(array $coupons, array $feeItems = [])
    {
        $discountItems = array_filter($feeItems, function (\WC_Order_Item_Fee $itemFee) {
            return $itemFee < 0;
        });

        foreach ($coupons as $coupon) {
            $subtotal = (float) $coupon->get_discount();
            $igv = (float) $coupon->get_discount_tax();

            $this->items[] = new DiscountLine(
                'Cupon de Woocommerce (' . $coupon->get_code() . ').',
                $subtotal,
                $igv
            );
        }

        foreach ($discountItems as $discountItem) {
            $subtotal_item = (float) $discountItem->get_total();
            $tax_item  = (float) $discountItem->get_total_tax();

            $this->items[] = new DiscountLine($discountItem->get_name(), $subtotal_item * (-1), $tax_item * (-1));
        }
    }

    public function addDiscount(DiscountLine $discountLine)
    {
        $this->items[] = $discountLine;
    }

    public function getDiscounts()
    {
        return $this->items;
    }

    /**
     * @return float
     */
    public function getSubtotal()
    {
        /**
         * @param float $carry
         */
        return array_reduce($this->items, function ($carry, DiscountLine $item) {
            return $carry + $item->getSubtotal();
        }, 0);
    }

    /**
     * @return float
     */
    public function getIgv()
    {
        /**
         * @param float $carry
         */
        return array_reduce($this->items, function ($carry, DiscountLine $item) {
            return $carry + $item->getTax();
        }, 0);
    }
}
