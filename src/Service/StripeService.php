<?php

namespace App\Service;

use App\Entity\Product;
use Stripe\Exception\InvalidRequestException;
use Stripe\StripeClient;

class StripeService
{
    private bool $offline;
    private ?StripeClient $client = null;

    public function __construct()
    {
        $this->offline = $this->shouldRunOffline();

        if (!$this->offline) {
            $key = $this->loadSecretKey();
            if (!$key) {
                throw new \RuntimeException('STRIPE_SECRET_KEY environment variable must be set when not in offline mode.');
            }
            $this->client = new StripeClient($key);
        }
    }

    public function syncProduct(Product $product): void
    {
        if ($this->offline) {
            $this->assignOfflineIds($product);

            return;
        }

        if ($product->getStripeProductId() && $this->stripeProductExists($product->getStripeProductId())) {
            $this->updateStripeProduct($product);

            return;
        }

        $this->createStripeProduct($product);
    }

    private function stripeProductExists(string $stripeProductId): bool
    {
        try {
            $this->client->products->retrieve($stripeProductId);

            return true;
        } catch (InvalidRequestException) {
            return false;
        }
    }

    private function createStripeProduct(Product $product): void
    {
        $stripeProductId = $this->createProductInStripe($product);
        $stripePriceId = $this->createPriceInStripe($product, $stripeProductId);

        $product
            ->setStripeProductId($stripeProductId)
            ->setStripePriceId($stripePriceId);
    }

    private function updateStripeProduct(Product $product): void
    {
        $this->client->products->update($product->getStripeProductId(), [
            'name' => $product->getName(),
            'description' => $product->getDescription(),
            'images' => $product->getImage() ? [$product->getImage()] : [],
            'metadata' => array_filter([
                'stock_quantity' => (string) $product->getStockQuantity(),
            ], static fn ($value) => null !== $value),
        ]);
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

    private function createProductInStripe(Product $product): string
    {
        $stripeProduct = $this->client->products->create([
            'name' => $product->getName(),
            'description' => $product->getDescription(),
            'images' => $product->getImage() ? [$product->getImage()] : [],
            'metadata' => array_filter([
                'local_product_id' => $product->getId() ? (string) $product->getId() : null,
                'stock_quantity' => (string) $product->getStockQuantity(),
            ], static fn ($value) => null !== $value),
        ]);

        return $stripeProduct->id;
    }


    private function createPriceInStripe(Product $product, string $stripeProductId): string
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
        $flag = $this->getEnv('STRIPE_OFFLINE');

        if ($flag === '1' || strcasecmp((string) $flag, 'true') === 0) {
            return true;
        }

        if ($flag === '0' || strcasecmp((string) $flag, 'false') === 0) {
            return false;
        }

        $env = $this->getEnv('APP_ENV') ?: 'dev';

        if ($env === 'test') {
            return true;
        }

        if ($env === 'dev') {
            return !$this->loadSecretKey();
        }

        return false;
    }

    private function loadSecretKey(): string
    {
        return $this->getEnv('STRIPE_SECRET_KEY');
    }

    private function getEnv(string $name): string
    {
        return $_SERVER[$name] ?? $_ENV[$name] ?? '';
    }
}
