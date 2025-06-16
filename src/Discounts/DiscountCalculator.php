<?php

declare(strict_types=1);

namespace AcmeWidget\Discounts;

use AcmeWidget\Discounts\Interfaces\OfferStrategyInterface;
use AcmeWidget\Inventory\Inventory;
use Money\Currency;
use Money\Money;

/**
 * Applies multiple discount strategies to a basket.
 * Supports combining different promotions (e.g. product-specific + order-wide discounts).
 */
class DiscountCalculator
{
    /**
     * @param OfferStrategyInterface[] $strategies Strategies to apply (order matters)
     */
    public function __construct(private array $strategies)
    {
        foreach ($strategies as $strategy) {
            if (!$strategy instanceof OfferStrategyInterface) {
                throw new \InvalidArgumentException('All strategies must implement OfferStrategyInterface');
            }
        }
    }

    /**
     * @param array<string> $items
     */
    public function computeDiscount(array $items, Inventory $inventory): Money
    {
        $context = new OfferContext($items, $inventory);
        $totalDiscount = new Money(0, new Currency('USD'));

        foreach ($this->strategies as $strategy) {
            $totalDiscount = $totalDiscount->add(
                $strategy->applyDiscount($context)
            );
        }

        return $totalDiscount;
    }
}
