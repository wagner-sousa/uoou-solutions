# UOOU Solutions — Agent Guide

Symfony 8.0 e-commerce (PHP 8.4, Doctrine ORM 3, Twig, PostgreSQL, Stripe).

## Quick commands

| Action | Command |
|--------|---------|
| Run tests | `php bin/phpunit` or `composer exec phpunit |
| Run migrations | `php bin/console doctrine:migrations:migrate` |
| Make entity | `php bin/console make:entity` |
| Make migration | `php bin/console make:migration` |
| List routes | `php bin/console debug:router` |
| Build frontend | `npm run build` (esbuild `assets/bootstrap.js` → `public/build/`) |
| Dev frontend | `npm run dev` (watch mode) |

## Database

- **Dev**: PostgreSQL 16 via Docker (`database:5432`, credentials in `.env`)
- **Test**: SQLite at `var/data_test.db` (configured in `.env.test`)
- **Prod compose**: PostgreSQL (`compose.yaml`)
- Test bootstrap (`tests/bootstrap.php`) drops and recreates all schema on every run — no manual migration needed for tests

## Architecture

```
src/
├── Controller/ProductController.php   # CRUD under /products (attribute routes)
├── Entity/Product.php                 # Doctrine entity, table `products`
├── Form/ProductType.php               # Symfony form (BRL currency, pt_BR labels)
├── Repository/ProductRepository.php
└── Service/StripeService.php          # Stripe integration (offline/online)
```

Entity `Product` has `stripe_code` (DB column for Stripe product ID) and `stripe_price_id` columns.

| Column | DB name | Getter |
|--------|---------|--------|
| `stripe_code` | `stripe_code` | `getStripeProductId()` |
| `stripe_price_id` | `stripe_price_id` | `getStripePriceId()` |

## Testing quirks

- PHPUnit 13 with strict flags (`failOnDeprecation`, `failOnNotice`, `failOnWarning`)
- `WebTestCase` tests clean DB in `setUp()` (delete all products)
- `StripeService` is `public: true` in DI config (both dev and test)
- No Stripe service tests exist yet (`tests/Service/` is empty)

## Known issues / gotchas

- Symfony Maker Bundle available (`make:entity`, `make:form`, `make:controller`, etc.).
- Security bundle is installed but **no user provider or access control** configured yet.
- No CI/CD pipelines in repo.
- No validation constraints on `Product` entity yet.
- No flash messages in controller operations.
- No pagination on product listing (uses `findAll()`).
- No webhook handling or `Order` entity.
- `.env.local` is gitignored — safe for local keys, but never commit real secrets.
- `stock_quantity` is `float` in entity but `IntegerType` in form — potential mismatch.

## Stripe MCP

Stripe MCP is configured in this repo:
- **`.vscode/mcp.json`**: remote endpoint at `https://mcp.stripe.com` (OAuth).
- **OAuth sessions** are managed in [Stripe Dashboard → User settings → OAuth sessions](https://dashboard.stripe.com/settings/user).
- Alternatively, set `STRIPE_SECRET_KEY` in your shell to use a local npx server with `npx -y @stripe/mcp@latest`.
