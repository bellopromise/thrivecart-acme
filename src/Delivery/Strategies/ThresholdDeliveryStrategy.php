<?php

declare(strict_types=1);

namespace AcmeWidget\Delivery\Strategies;

use AcmeWidget\Delivery\Interfaces\DeliveryStrategyInterface;
use Money\Money;

class ThresholdDeliveryStrategy implements DeliveryStrategyInterface
{
    public function __construct(
        private Money $under50,
        private Money $under90,
        private Money $over90Threshold
    ) {
    }

    public function calculateFee(Money $amount): Money
    {
        if ($amount->isNegative()) {
            throw new \InvalidArgumentException('Amount cannot be negative');
        }

        return match (true) {
            $amount->greaterThanOrEqual($this->over90Threshold) => new Money(0, $amount->getCurrency()),
            $amount->greaterThanOrEqual(new Money(5000, $amount->getCurrency())) => $this->under90,
            default => $this->under50
        };
    }
}
