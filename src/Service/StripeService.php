<?php

namespace App\Service;

use App\Entity\Product;
use Stripe\StripeClient;

class StripeService
{
    private bool $offline = false;
    private StripeClient $client;

    public function __construct()
    {
        $key = getenv('STRIPE_SECRET_KEY');
        $this->client = new StripeClient($key);
    }

    public function syncProduct(Product $product): void
    {
        if ($this->offline) {
            $this->assignOfflineIds($product);

            return;
        }

        $stripeProductId = $this->syncStripeProduct($product);
        $stripePriceId = $this->createStripePrice($product, $stripeProductId);

        $product
            ->setStripeProductId($stripeProductId)
            ->setStripePriceId($stripePriceId);
    }

    public function deleteProduct(Product $product): void
    {
        if ($this->offline) {
            return;
        }

        if ($product->getStripePriceId()) {
            $this->deactivateStripePrice($product->getStripePriceId());
        }

        if ($product->getStripeProductId()) {
            $this->deactivateStripeProduct($product->getStripeProductId());
        }
    }

    public function createCheckoutSession(Product $product, string $successUrl, string $cancelUrl): string
    {
        if (!$product->getStripePriceId()) {
            throw new \RuntimeException('Product is not synchronized with Stripe.');
        }

        if ($this->offline) {
            return 'https://stripe.test/checkout/'.($product->getId() ?? $product->getStripePriceId());
        }

        $session = $this->client->checkout->sessions->create([
            'payment_method_types' => ['card'],
            'line_items' => [[
                'price' => $product->getStripePriceId(),
                'quantity' => 1,
            ]],
            'mode' => 'payment',
            'success_url' => $successUrl,
            'cancel_url' => $cancelUrl,
        ]);

        return $session->url;
    }

    private function assignOfflineIds(Product $product): void
    {
        $product
            ->setStripeProductId($product->getStripeProductId() ?? 'prod_test_'.($product->getId() ?? uniqid('', true)))
            ->setStripePriceId('price_test_'.($product->getId() ?? uniqid('', true)));
    }

    private function syncStripeProduct(Product $product): string
    {
        $payload = [
            'name' => $product->getName(),
            'description' => $product->getDescription(),
            'images' => $product->getImage() ? [$product->getImage()] : [],
            'metadata' => array_filter([
                'local_product_id' => $product->getId() ? (string) $product->getId() : null,
                'stock_quantity' => $product->getStockQuantity(),
            ], static fn ($value) => null !== $value),
        ];

        if ($product->getStripeProductId()) {
            $stripeProduct = $this->client->products->update($product->getStripeProductId(), $payload);
        } else {
            $stripeProduct = $this->client->products->create($payload);
        }

        return $stripeProduct->id;
    }

    private function createStripePrice(Product $product, string $stripeProductId): string
    {
        $stripePrice = $this->client->prices->create([
            'unit_amount' => $product->getPriceInCents(),
            'currency' => 'brl',
            'product' => $stripeProductId,
        ]);

        return $stripePrice->id;
    }

    private function deactivateStripePrice(string $priceId): void
    {
        $this->client->prices->update($priceId, ['active' => false]);
    }

    private function deactivateStripeProduct(string $productId): void
    {
        $this->client->products->update($productId, ['active' => false]);
    }

    private function shouldRunOffline(): bool
    {
        $env = getenv('APP_ENV') ?: ($_SERVER['APP_ENV'] ?? null);
        if ($env === 'test') {
            return true;
        }

        $flag = getenv('STRIPE_OFFLINE');

        return $flag === '1' || strcasecmp((string) $flag, 'true') === 0;
    }
}
