<?php

declare(strict_types=1);

namespace AcmeWidget\Delivery\Interfaces;

use Money\Money;

interface DeliveryStrategyInterface
{
    /**
     * Calculates delivery fee based on order amount
     * @throws \InvalidArgumentException for negative amounts
     */
    public function calculateFee(Money $amount): Money;
}
