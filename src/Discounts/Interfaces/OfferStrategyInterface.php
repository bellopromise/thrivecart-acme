<?php

declare(strict_types=1);

namespace AcmeWidget\Discounts\Interfaces;

use AcmeWidget\Discounts\OfferContext;
use Money\Money;

interface OfferStrategyInterface
{
    /**
     * Applies offer rules to calculate discount
     * @throws \RuntimeException if strategy cannot be applied
     */
    public function applyDiscount(OfferContext $context): Money;
}
