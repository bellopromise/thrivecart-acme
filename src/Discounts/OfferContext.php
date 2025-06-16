<?php

declare(strict_types=1);

namespace AcmeWidget\Discounts;

use AcmeWidget\Inventory\Inventory;
use Money\Money;

class OfferContext
{
    /**
     * @param array<string> $items
     */
    public function __construct(
        private array $items,
        private Inventory $inventory
    ) {
    }

    public function countProduct(string $code): int
    {
        return count(array_filter($this->items, fn ($i) => $i === $code));
    }

    public function getProductPrice(string $code): Money
    {
        return $this->inventory->findProduct($code)->getPrice();
    }
}
