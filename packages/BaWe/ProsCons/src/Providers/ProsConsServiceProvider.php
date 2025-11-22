<?php

namespace BaWe\ProsCons\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Event;
use Webkul\Product\Models\Product;
use BaWe\ProsCons\Repositories\ProsConRepository;

class ProsConsServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__ . '/../../database/migrations');
        $this->loadViewsFrom(__DIR__ . '/../../resources/views', 'proscons');
        $this->loadRoutesFrom(__DIR__ . '/../Http/admin.php');

        // Place panel under the Description group on the product edit page.
        Event::listen('bagisto.admin.catalog.product.edit.form.description.after', function ($viewRenderEventManager) {
            $viewRenderEventManager->addTemplate('proscons::admin.pros_cons.panel');
        });

        // Persist pros/cons when the main product Save button is used.
        Product::saved(function (Product $product) {
            $request = request();

            if (! $request->routeIs('admin.catalog.products.update', 'admin.catalog.products.store')) {
                return;
            }

            $pros = $request->input('pros', []);
            $cons = $request->input('cons', []);

            if (! is_array($pros) && ! is_array($cons)) {
                return;
            }

            app(ProsConRepository::class)->upsertMany($product->id, (array) $pros, (array) $cons);
        });
    }
}
