<?php

namespace EBilling\Domain;

final class InvoiceItemsCollection
{
    private $itemsCollection = [];

    /**
     * @param InvoiceItem[] $items
     */
    private function __construct(array $items)
    {
        $this->itemsCollection = $items;
    }

    /**
     * @param \WC_Order_Item_Product[] $items
     * 
     * @return self
     */
    public static function createFromWooItems(array $items, $includeTax)
    {
        $itemsCollection = new self([]);

        $lineItems = array_filter($items, function (\WC_Order_Item_Product $item) {
            return $item->get_total() > 0;
        });

        foreach ($lineItems as $lineItem) {
            $itemsCollection->addItem(InvoiceItem::createFromWooLineItem($lineItem, $includeTax));
        }

        return $itemsCollection;
    }

    /**
     * @param \WC_Order_Item_Shipping[] $items
     */
    public function addShippingItems(array $items)
    {
        $shippingItems = array_filter($items, function (\WC_Order_Item_Shipping $item) {
            return $item->get_total() > 0;
        });

        foreach ($shippingItems as $shippingItem) {
            $this->addItem(InvoiceItem::createFromWooExtraItem('shipping', $shippingItem));
        }
    }

    /**
     * @param \WC_Order_Item_Fee[] $items
     */
    public function addFeeItems(array $items)
    {
        $feeItems = array_filter($items, function (\WC_Order_Item_Fee $item) {
            return $item->get_total() > 0;
        });

        foreach ($feeItems as $feeItem) {
            $this->addItem(InvoiceItem::createFromWooExtraItem('fee', $feeItem));
        }
    }

    public function createSummary(GlobalDiscount $globalDiscount)
    {
        if ($this->containsExoneratedItems()) {
            return $this->createSummaryWithExoneratedItems($globalDiscount);
        }

        $totalGravadas = 0;
        $totalIgv = 0;
        $totalExonerado = 0;
        $totalVentas = 0;

        foreach ($this->itemsCollection as $item) {
            $totalGravadas += $item->getSubtotal();
            $totalIgv += $item->getTotalIgv();
            $totalExonerado += $item->getTotalExonerado();
            $totalVentas += $item->getTotal();
        }

        if ($globalDiscount->getSubtotal() > 0) {
            $totalGravadas -= $globalDiscount->getSubtotal();
            $totalIgv -= $globalDiscount->getIgv();
            $totalVentas = $totalGravadas + $totalIgv;
        }

        return new InvoiceSummary($globalDiscount, $totalGravadas, $totalIgv, $totalExonerado, $totalVentas);
    }

    public function addItem(InvoiceItem $item)
    {
        $this->itemsCollection[] = $item;
    }

    public function getItems()
    {
        return $this->itemsCollection;
    }

    private function createSummaryWithExoneratedItems(GlobalDiscount $globalDiscount)
    {
        $totalDiscount = $globalDiscount->getSubtotal() + $globalDiscount->getIgv();
        $totalGravadas = 0;
        $totalIgv = 0;
        $totalExonerado = 0;
        $totalSaleAmount = array_reduce($this->itemsCollection, function ($carry, InvoiceItem $item) {
            return $carry + $item->getTotal();
        }, 0);
        $itemsWithDiscount = array_map(function (InvoiceItem $item) {
            return clone $item;
        }, $this->itemsCollection);

        $newGlobalDiscount = new GlobalDiscount([]);

        foreach ($globalDiscount->getDiscounts() as $discountLine) {
            $discount = round($discountLine->getSubtotal() + $discountLine->getTax(), 2);
            $discountApplied = 0;

            foreach ($itemsWithDiscount as $item) {
                $discountApplied += $item->applyDiscount($totalSaleAmount, $discount);
            }

            $newGlobalDiscount->addDiscount(
                new DiscountLine($discountLine->getDescription(), $discountApplied, $discountApplied * 0.18)
            );
        }

        foreach ($itemsWithDiscount as $item) {
            $totalGravadas += ($item->getTotalExonerado() > 0) ? 0 : $item->getSubtotal();
            $totalIgv += $item->getTotalIgv();
            $totalExonerado += $item->getTotalExonerado();
        }

        return new InvoiceSummary($newGlobalDiscount, $totalGravadas, $totalIgv, $totalExonerado, $totalSaleAmount - $totalDiscount);
    }

    private function containsExoneratedItems()
    {
        return count(
            array_filter($this->itemsCollection, function (InvoiceItem $item) {
                return $item->getTotalExonerado() > 0;
            })
        );
    }
}
