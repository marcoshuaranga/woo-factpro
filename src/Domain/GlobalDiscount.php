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
     */
    public function __construct(array $coupons)
    {
        foreach ($coupons as $coupon) {

            $subtotal = (float) $coupon->get_discount();
            $igv = (float) $coupon->get_discount_tax();

            $this->items[] = new DiscountLine(
                'Cupon de Woocommerce (' . $coupon->get_code() . ').',
                $subtotal,
                $igv
            );
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

    public function toArray()
    {
        return array_map(function (DiscountLine $item) { 
            return [
                'codigo' => $item->getCode(),
                'descripcion' => $item->getDescription(),
                'porcentaje' => 1,
                'monto' => round($item->getSubtotal(), 2),
                'base' => round($item->getSubtotal(), 2),
            ];
        }, $this->items);
    }
}
