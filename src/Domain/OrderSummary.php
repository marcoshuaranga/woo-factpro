<?php

namespace Factpro\Domain;

use Automattic\WooCommerce\Utilities\NumberUtil;

final class OrderSummary
{
  private $discountTotal;

  private $shippingTotal;

  private $totalPaid;

  public function __construct(\WC_Order $order)
  {
    $this->discountTotal = array_reduce(
      $order->get_items('coupon'),
      function ($acc, \WC_Order_Item_Coupon $item) {
        return $acc + $item->get_discount() + $item->get_discount_tax();
      },
      0
    );

    $this->shippingTotal = array_reduce(
      $order->get_items('shipping'),
      function ($acc, \WC_Order_Item_Shipping $item) {
        return $acc + $item->get_total() + $item->get_total_tax();
      },
      0
    );

    $this->totalPaid = NumberUtil::round($order->get_total(), 2);
  }

  public static function createFromWooOrder(\WC_Order $order): self
  {
    return new self($order);
  }

  public function getTotalDiscount(): float
  {
    return $this->discountTotal;
  }

  public function getPercentDiscountForSunat(): float
  {
    // Shipping is not included in the discount percentage calculation
    $percentage = $this->discountTotal / ($this->totalPaid + $this->discountTotal - $this->shippingTotal);

    return NumberUtil::round($percentage, 4);
  }


  public function getTotalDiscountForSunat()
  {
    return NumberUtil::round($this->discountTotal / 1.18, 4);
  }

  public function getTotalShipping(): float
  {
    return $this->shippingTotal;
  }

  public function getTotalPaid(): float
  {
    return $this->totalPaid;
  }
}
