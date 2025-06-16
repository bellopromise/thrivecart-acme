<?php

declare(strict_types=1);

namespace AcmeWidget\Discounts\Strategies;

use AcmeWidget\Discounts\Interfaces\OfferStrategyInterface;
use AcmeWidget\Discounts\OfferContext;
use Money\Money;

class BuyOneHalfOffStrategy implements OfferStrategyInterface
{
    public function __construct(private string $productCode)
    {
    }

    public function applyDiscount(OfferContext $context): Money
    {
        $count = $context->countProduct($this->productCode);
        return $context->getProductPrice($this->productCode)
                      ->divide(2)
                      ->multiply((int) floor($count / 2));
    }
}
