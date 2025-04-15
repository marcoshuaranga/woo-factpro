<?php

namespace Factpro\Domain;

use Automattic\WooCommerce\Utilities\NumberUtil;

final class Discount
{
    private $code;

    private $subtotal;

    private $totalTax;

    /**
     * @param string $code
     * @param float $subtotal
     * @param float $totalTax
     */
    public function __construct($code, $subtotal, $totalTax)
    {
        $this->code = $code;
        $this->subtotal = $subtotal;
        $this->totalTax = $totalTax;
    }

    public static function createFromWooCoupon(\WC_Order_Item_Coupon $coupon)
    {
        $subtotal = NumberUtil::round($coupon->get_discount(), 2);
        $totalTax = NumberUtil::round($coupon->get_discount_tax(), 2);

        if ($totalTax <= 0) {
            $subtotal = NumberUtil::round($coupon->get_discount() / 1.18, 2);
            $totalTax = NumberUtil::round($coupon->get_discount() - $subtotal, 2);
        }

        return new Discount($coupon->get_code(), $subtotal, $totalTax);
    }

    public function getCode()
    {
        return $this->code;
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
        return $this->subtotal + $this->totalTax;
    }
}
