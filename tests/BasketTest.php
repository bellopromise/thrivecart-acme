<?php

declare(strict_types=1);

namespace AcmeWidget\Tests;

use AcmeWidget\Basket\Basket;
use AcmeWidget\Basket\BasketCalculator;
use AcmeWidget\Delivery\DeliveryFeeCalculator;
use AcmeWidget\Discounts\DiscountCalculator;
use AcmeWidget\Discounts\Strategies\BuyOneHalfOffStrategy;
use AcmeWidget\Inventory\Inventory;
use AcmeWidget\Inventory\Product;
use Money\Currency;
use Money\Money;
use PHPUnit\Framework\TestCase;

class BasketTest extends TestCase
{
    private Inventory $inventory;
    private DeliveryFeeCalculator $deliveryFeeCalculator;
    private DiscountCalculator $discountCalculator;
    private BasketCalculator $basketCalculator;

    protected function setUp(): void
    {
        $products = [
            new Product('R01', 'Red Widget', Money::USD(3295)), //32.95 USD converted to cents
            new Product('G01', 'Green Widget', Money::USD(2495)), //24.95 USD converted to cents
            new Product('B01', 'Blue Widget', Money::USD(795)), //7.95 USD converted cents
        ];

        $this->inventory = new Inventory($products);
        $this->deliveryFeeCalculator = DeliveryFeeCalculator::createThresholdCalculator(4.95, 2.95, 90, new Currency('USD'));
        $this->discountCalculator = new DiscountCalculator([
            new BuyOneHalfOffStrategy('R01'),
        ]);
        $this->basketCalculator = new BasketCalculator($this->deliveryFeeCalculator, $this->discountCalculator, new Currency('USD'));
    }

    public function test_b01_and_g01_total_37_85(): void
    {
        $basket = new Basket($this->inventory, $this->basketCalculator);
        $basket->add('B01');
        $basket->add('G01');

        $this->assertEquals(37.85, $basket->total());
    }

    public function test_two_r01_total_54_37(): void
    {
        $basket = new Basket($this->inventory, $this->basketCalculator);
        $basket->add('R01');
        $basket->add('R01');

        $this->assertEquals(54.37, $basket->total());
    }

    public function test_r01_and_g01_total_60_85(): void
    {
        $basket = new Basket($this->inventory, $this->basketCalculator);
        $basket->add('R01');
        $basket->add('G01');

        $this->assertEquals(60.85, $basket->total());
    }

    public function test_multiple_items_total_98_27(): void
    {
        $basket = new Basket($this->inventory, $this->basketCalculator);
        $basket->add('B01');
        $basket->add('B01');
        $basket->add('R01');
        $basket->add('R01');
        $basket->add('R01');

        $this->assertEquals(98.27, $basket->total());
    }

    public function test_empty_basket_has_delivery_fee_only(): void
    {
        $basket = new Basket($this->inventory, $this->basketCalculator);
        $this->assertEqualsWithDelta(4.95, $basket->total(), 0.001);
    }

    public function test_single_item_basket_with_delivery(): void
    {
        $basket = new Basket($this->inventory, $this->basketCalculator);
        $basket->add('B01');
        $this->assertEqualsWithDelta(12.90, $basket->total(), 0.001); // $7.95 + $4.95 delivery
    }

    public function test_odd_number_of_discountedItems(): void
    {
        $basket = new Basket($this->inventory, $this->basketCalculator);
        // 3 red widgets = 1 full price + 1 half price + 1 full price
        $basket->add('R01');
        $basket->add('R01');
        $basket->add('R01');

        $this->assertEqualsWithDelta(85.32, $basket->total(), 0.001);
    }

    public function test_delivery_fee_at_different_thresholds(): void
    {
        $basket1 = new Basket($this->inventory, $this->basketCalculator);
        $basket1->add('G01');
        $basket1->add('G01');

        $this->assertEqualsWithDelta(54.85, $basket1->total(), 0.001);

        $basket2 = new Basket($this->inventory, $this->basketCalculator);
        $basket2->add('R01');
        $basket2->add('B01');
        $basket2->add('B01');

        $basket2 = new Basket($this->inventory, $this->basketCalculator);
        $basket2->add('R01');
        $basket2->add('G01');

        $this->assertEqualsWithDelta(60.85, $basket2->total(), 0.001);

        $basket3 = new Basket($this->inventory, $this->basketCalculator);
        $basket3->add('R01');
        $basket3->add('R01'); // $32.95 (with discount: $16.48)
        $basket3->add('G01');

        $this->assertEqualsWithDelta(77.32, $basket3->total(), 0.001);

        $basket4 = new Basket($this->inventory, $this->basketCalculator);
        $basket4->add('R01');
        $basket4->add('R01'); // $32.95 (with discount: $16.48)
        $basket4->add('R01');
        $basket4->add('B01');

        $this->assertEqualsWithDelta(90.32, $basket4->total(), 0.001);
    }

    public function test_adding_invalid_product_throws_exception(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Product INVALID not found');

        $basket = new Basket($this->inventory, $this->basketCalculator);
        $basket->add('INVALID');
    }

    public function test_negative_amount_throws_in_delivery_calculation(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Amount cannot be negative');

        $negativeAmount = Money::USD(-100);
        $this->deliveryFeeCalculator->compute($negativeAmount);
    }

    public function test_invalid_discount_strategy_throws_exception(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('All strategies must implement OfferStrategyInterface');

        /** @var array<object> $invalidStrategies */
        $invalidStrategies = [new \stdClass()];
        /** @phpstan-ignore-next-line */
        new DiscountCalculator($invalidStrategies);
    }
}
