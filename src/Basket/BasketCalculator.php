<?php

declare(strict_types=1);

namespace AcmeWidget\Basket;

use AcmeWidget\Delivery\DeliveryFeeCalculator;
use AcmeWidget\Discounts\DiscountCalculator;
use AcmeWidget\Inventory\Inventory;
use Money\Currency;
use Money\Money;

class BasketCalculator
{
    public function __construct(
        private DeliveryFeeCalculator $deliveryCalculator,
        private DiscountCalculator $discountCalculator,
        private Currency $currency
    ) {
    }

    /**
     * @param array<string> $items
     */
    public function calculateTotal(array $items, Inventory $inventory): float
    {
        $subtotal = $this->calculateSubtotal($items, $inventory);
        $discount = $this->discountCalculator->computeDiscount($items, $inventory);

        $total = $subtotal->subtract($discount);
        $deliveryFee = $this->deliveryCalculator->compute($total);

        return (float) $total->add($deliveryFee)->getAmount() / 100;
    }

    /**
     * @param array<string> $items
     */
    private function calculateSubtotal(array $items, Inventory $inventory): Money
    {
        $total = new Money(0, $this->currency);
        foreach ($items as $itemCode) {
            $total = $total->add($inventory->findProduct($itemCode)->getPrice());
        }
        return $total;
    }
}
