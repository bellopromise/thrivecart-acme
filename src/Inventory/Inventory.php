<?php

declare(strict_types=1);

namespace AcmeWidget\Inventory;

class Inventory
{
    /** @var array<string, Product> */
    private array $products = [];

    /**
     * @param array<Product> $products
     */
    public function __construct(array $products)
    {
        foreach ($products as $product) {
            $this->products[$product->getCode()] = $product;
        }
    }

    public function findProduct(string $code): Product
    {
        return $this->products[$code] ?? throw new \InvalidArgumentException("Product {$code} not found");
    }
}
