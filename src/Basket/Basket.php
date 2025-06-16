<?php

declare(strict_types=1);

namespace AcmeWidget\Basket;

use AcmeWidget\Inventory\Inventory;

class Basket
{
    /** @var array<string> */
    private array $items = [];

    public function __construct(
        private Inventory $inventory,
        private BasketCalculator $calculator,
    ) {
    }

    /**
     * @throws \InvalidArgumentException
     */
    public function add(string $productCode): void
    {
        $this->items[] = $this->inventory->findProduct($productCode)->getCode();
    }

    public function total(): float
    {
        return $this->calculator->calculateTotal($this->items, $this->inventory);
    }
}
