<?php

namespace EBilling\Domain;

final class FeeItemProcessor
{
    private $feeItems;

    /**
     * @param \WC_Order_Item_Fee[] $feeItems
     */
    public function __construct(array $feeItems)
    {
        $this->feeItems = $feeItems;        
    }

    public function process(InvoiceItemsCollection $items, GlobalDiscount $globalDiscount)
    {
        foreach ($this->feeItems as $feeItem) {
            $subtotal_item = (float) $feeItem->get_total();
            $tax_item  = (float) $feeItem->get_total_tax();
            $total_item = $subtotal_item + $tax_item;

            if ($total_item > 0) {
                $items->addItem(InvoiceItem::createFromWooExtraItem('fee', $feeItem));
            } else {
                $globalDiscount->addDiscount(
                    new DiscountLine($feeItem->get_name(), $subtotal_item * (-1), $tax_item * (-1))
                );
            }
        }
    }
}
