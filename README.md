# 📦 Acme Widget Basket System

This is a proof-of-concept PHP basket system for **Acme Widget Co**.  
It demonstrates a flexible, testable checkout flow with:

- ✅ **Inventory management**
- ✅ **Configurable discounts**
- ✅ **Strategy-based delivery fees**
- ✅ **Accurate monetary calculations using `moneyphp/money`**
- ✅ **PHPUnit tests**
- ✅ **Dockerized environment**

---

## 🚀 How it Works

### 1️⃣ Core Concepts

| Component | Responsibility |
| --------- | --------------- |
| **`Inventory`** | Holds available `Product` data (code, name, price). |
| **`Basket`** | Stores selected product codes. Delegates cost calculation. |
| **`BasketCalculator`** | Computes: Subtotal → Discounts → Delivery → Grand Total. |
| **`DiscountCalculator`** | Applies one or more `OfferStrategyInterface` strategies. |
| **`DeliveryFeeCalculator`** | Applies a `DeliveryStrategyInterface` to compute delivery fee. |
| **`ThresholdDeliveryStrategy`** | Example delivery fee strategy: cheaper or free shipping above certain order totals. |
| **`BuyOneHalfOffStrategy`** | Example discount: Buy One, get the next half off. |

### 2️⃣ Key Flow

1. `Basket` holds product codes.
2. On checkout (`Basket::total()`):
   - `BasketCalculator` computes subtotal.
   - Applies configured discounts.
   - Applies delivery fee.
   - Returns final amount in dollars (float).

3. All monetary operations use `moneyphp/money` for correctness.

---

## 🔑 Design Assumptions

- ✅ **Fixed Currency:** USD is used by default.
- ✅ **Product Uniqueness:** Each product is uniquely identified by its `code`.
- ✅ **Pluggable Strategies:** Delivery rules and discount offers are plug-and-play via interfaces.
- ✅ **Unit Test Coverage:** Tests cover valid flows, invalid product codes, edge delivery thresholds, and negative amounts.

---

## 📋 Prerequisites

Before running or developing this project, make sure you have:

- [Docker](https://www.docker.com/) (tested with Docker 24+)
- [Docker Compose](https://docs.docker.com/compose/) (v2 recommended)
- [Composer](https://getcomposer.org/) installed **if running locally without Docker**
- PHP 8.3+ (recommended to use Docker for consistent PHP version)

---

## 🐳 Run with Docker

```bash
# Build the container image
docker compose build

# Start a container shell
docker compose run --rm app bash

# Inside the container, run tests
composer test

# Run static analysis
composer phpstan

# Run CLI
php bin/acme-widget.php calculate R01 R01                

# Check code style
composer cs-check
