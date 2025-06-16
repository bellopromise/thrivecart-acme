<?php

declare(strict_types=1);

namespace AcmeWidget\Delivery;

use AcmeWidget\Delivery\Interfaces\DeliveryStrategyInterface;

use AcmeWidget\Delivery\Strategies\ThresholdDeliveryStrategy;
use Money\Currency;
use Money\Money;

class DeliveryFeeCalculator
{
    public function __construct(
        private DeliveryStrategyInterface $strategy
    ) {
    }

    public function compute(Money $amount): Money
    {
        return $this->strategy->calculateFee($amount);
    }

    /**
     * Helper factory for the standard threshold-based delivery
     */
    public static function createThresholdCalculator(
        float $under50,
        float $under90,
        float $over90Threshold,
        Currency $currency
    ): self {
        return new self(
            new ThresholdDeliveryStrategy(
                new Money((int) round($under50 * 100), $currency),
                new Money((int) round($under90 * 100), $currency),
                new Money((int) round($over90Threshold * 100), $currency)
            )
        );
    }
}
