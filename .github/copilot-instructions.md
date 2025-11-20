# Bagisto E-Commerce Platform - AI Coding Agent Instructions

## Project Overview
This is **Bagisto**, a Laravel 11-based e-commerce platform built on a modular package architecture. The system uses the **Konekt Concord** package management system to organize functionality into self-contained packages under `packages/Webkul/`.

## Architecture & Structure

### Modular Package System
- **30+ domain packages** in `packages/Webkul/` (Admin, Shop, Product, Customer, Sales, etc.)
- Each package follows a consistent structure:
  - `src/Providers/ModuleServiceProvider.php` - Registers models with Concord
  - `src/Providers/{Package}ServiceProvider.php` - Main service provider for routes, views, migrations
  - `src/Repositories/` - Data access layer using Repository pattern
  - `src/DataGrids/` - Admin grid implementations
  - `src/Http/Controllers/` - Package-specific controllers
  - `src/Routes/` - Package route files
  - `src/Resources/views/` - Blade templates
  - `src/Resources/lang/` - Multi-language translations
- Packages are registered in `config/concord.php` and `bootstrap/providers.php`

### Key Architectural Patterns

#### Repository Pattern (via prettus/l5-repository)
All data access goes through repositories extending `Webkul\Core\Eloquent\Repository`:
```php
// Find models
$repository->findOneByField('email', $email);
$repository->findWhere(['status' => 'active']);

// Repositories have built-in caching - be aware when debugging
```

#### DataGrid System
Admin listings use the custom DataGrid system (`packages/Webkul/DataGrid`):
- Define `prepareQueryBuilder()`, `prepareColumns()`, `prepareActions()`
- Supports filtering, sorting, mass actions, and Excel export
- Example: `packages/Webkul/Admin/src/DataGrids/` (though currently empty in Admin, see Shop for examples)

#### Event-Driven Architecture
Extensive use of Laravel events for extensibility:
```php
Event::dispatch('checkout.order.save.before', [$data]);
Event::dispatch('sales.invoice.save.after', $invoice);
```
Key events: `checkout.order.*`, `sales.*`, `catalog.product.*`
Event listeners live in `packages/Webkul/*/src/Listeners/`

#### View Render Events
Special Blade helper for theme customization:
```php
{!! view_render_event('bagisto.shop.products.view.before', ['product' => $product]) !!}
```
Used throughout Shop templates to allow theme overrides

### Global Helpers
From `packages/Webkul/Core/src/Http/helpers.php`:
- `core()` - Access core functionality, especially `core()->getConfigData('path.to.config')`
- `menu()` - Menu management
- `acl()` - Access control
- `system_config()` - System configuration

### Configuration
- Store configuration saved in database via `core_config` table
- Access via `core()->getConfigData('catalog.products.search.engine')`
- Admin config UI defined in `packages/Webkul/*/Config/system.php`

## Development Workflows

### Local Development with Docker (Laravel Sail)
```bash
# Start services (MySQL, Redis, Elasticsearch, Kibana, Mailpit)
./vendor/bin/sail up -d

# Access container
./vendor/bin/sail shell

# Run artisan commands
./vendor/bin/sail artisan <command>
```

**Critical services:**
- **Elasticsearch (port 9200)** - Product search when configured
- **Kibana (port 5601)** - Elasticsearch UI
- **Mailpit (port 8025)** - Email testing UI
- **MySQL (port 3306)** - Primary database

### Testing
Uses **Pest PHP** for testing:
```bash
# Run tests
./vendor/bin/sail test

# Run specific package tests
./vendor/bin/sail test --testsuite="Shop Feature Test"
```

Test setup in `tests/Pest.php` - different TestCases for Admin, Shop, Core, DataGrid packages.

### Asset Compilation (Vite)
```bash
npm run dev    # Development with hot reload
npm run build  # Production build
```
Each package can have its own Vite entry points configured in `config/bagisto-vite.php`

### Database & Indexing
```bash
# Product indexing (price, inventory, flat tables, elasticsearch)
php artisan indexer:index --type=price --mode=full
php artisan indexer:index --type=elastic --mode=selective

# Scheduled daily at 00:01 for price indexing
```

### Code Quality
```bash
# Laravel Pint (formatting)
./vendor/bin/pint

# Run linter
./vendor/bin/pint --test
```

## Project-Specific Conventions

### Creating New Packages
1. Create directory structure in `packages/Webkul/YourPackage/src/`
2. Create `ModuleServiceProvider.php` to register models with Concord
3. Create main service provider for routes/views/migrations
4. Register in `config/concord.php` and `bootstrap/providers.php`
5. Add PSR-4 autoload entry in `composer.json`

### Translations
- Translation keys use `package::path.to.key` format
- Example: `trans('shop::app.customers.signup-form.page-title')`
- Support for 20+ languages in `lang/` and `packages/Webkul/*/src/Resources/lang/`

### Multi-Channel / Multi-Locale
- Core supports multiple channels, locales, currencies out of the box
- Channel-specific configuration via `core()->getCurrentChannel()`
- Locale switching impacts translations, currency, and catalog

### Performance Considerations
- Repository caching enabled by default (use `config/repository.php` to control)
- Full Page Cache (FPC) package for storefront caching
- Product indexing for performance (flat tables, elasticsearch)
- Use `--mode=selective` for incremental indexing

## Common Tasks

### Adding a New Admin Menu Item
Define in package's `Config/menu.php`, merge in service provider:
```php
$this->mergeConfigFrom(dirname(__DIR__).'/Config/menu.php', 'menu.admin');
```

### Creating Admin Forms/Grids
- Use DataGrid class for listings
- Form requests for validation
- ACL defined in `Config/acl.php`

### Product Types
Configurable, simple, virtual, downloadable, booking, bundle, grouped - defined in `packages/Webkul/Product/src/Config/product_types.php`

### Payment/Shipping Methods
- Payment methods in `packages/Webkul/Payment/` and `packages/Webkul/Paypal/`
- Shipping methods in `packages/Webkul/Shipping/`
- Register in `Config/paymentmethods.php` or `Config/shipping.php`

## Key Files to Reference
- `config/concord.php` - Package registration
- `bootstrap/providers.php` - Service provider order
- `packages/Webkul/Core/src/Providers/CoreServiceProvider.php` - Core bindings and overrides
- `packages/Webkul/Product/src/Console/Commands/Indexer.php` - Indexing logic
- `packages/Webkul/DataGrid/src/DataGrid.php` - DataGrid base class

## Important Notes
- **Never modify `vendor/` directly** - packages are symlinked from `packages/`
- Use `bouncer` middleware for ACL checks in admin routes
- Middleware customizations in `bootstrap/app.php` (removed `ConvertEmptyStringsToNull`, custom `EncryptCookies`)
- Theme system supports shop and admin themes separately (see `config/themes.php`)
