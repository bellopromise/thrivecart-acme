#!/usr/bin/env php
<?php

require __DIR__.'/../vendor/autoload.php';

use AcmeWidget\Basket\BasketCalculator;
use AcmeWidget\Delivery\DeliveryFeeCalculator;
use AcmeWidget\Discounts\DiscountCalculator;
use AcmeWidget\Discounts\Strategies\BuyOneHalfOffStrategy;
use AcmeWidget\Inventory\Inventory;
use AcmeWidget\Inventory\Product;
use Money\Money;
use Money\Currency;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

$products = [
    new Product('R01', 'Red Widget', Money::USD(3295)),
    new Product('G01', 'Green Widget', Money::USD(2495)), 
    new Product('B01', 'Blue Widget', Money::USD(795)),
];

$inventory = new Inventory($products);
$deliveryCalculator = DeliveryFeeCalculator::createThresholdCalculator(4.95, 2.95, 90, new Currency('USD'));
$discountCalculator = new DiscountCalculator([new BuyOneHalfOffStrategy('R01')]);
$basketCalculator = new BasketCalculator($deliveryCalculator, $discountCalculator, new Currency('USD'));

$app = new Application('Acme Widget Basket Calculator', '1.0.0');

$app->add(new class($inventory, $basketCalculator) extends Command {
    public function __construct(
        private Inventory $inventory,
        private BasketCalculator $calculator
    ) {
        parent::__construct('calculate');
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Calculate basket total')
            ->addArgument(
                'products', 
                InputArgument::IS_ARRAY, 
                'Product codes to add to basket (space separated)'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $items = $input->getArgument('products');
        $total = $this->calculator->calculateTotal($items, $this->inventory);
        
        $output->writeln(sprintf('Total: $%.2f', $total));
        return Command::SUCCESS;
    }
});

$app->run();
