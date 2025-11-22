<?php

namespace Webkul\SmartHome\Providers;

use Illuminate\Support\ServiceProvider;

class SmartHomeServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        // Load views with namespace
        $this->loadViewsFrom(__DIR__.'/../Resources/views', 'smart-home');

        // Service provider code will be added here
        $this->publishes([
            __DIR__.'/../Resources/views'  => resource_path('themes/smart-home/views'),
        ]);

        $this->loadRoutesFrom(__DIR__.'/../Routes/api.php');

        $this->loadTranslationsFrom(__DIR__ . '/../Resources/lang', 'smarthome');

        // Load config
        $this->mergeConfigFrom(__DIR__.'/../Config/google-reviews.php', 'smart-home.google-reviews');

        // Register custom shipping carriers
        $this->registerShippingCarriers();

        // Extend admin system configuration with custom shipping methods
        $this->extendSystemConfiguration();
    }

    /**
     * Register custom shipping carriers.
     *
     * @return void
     */
    protected function registerShippingCarriers()
    {
        // Get existing carriers
        $carriers = config('carriers', []);

        // Override core FlatRate with custom SmartHome FlatRate
        // This version hides itself when free shipping is available
        if (isset($carriers['flatrate'])) {
            $carriers['flatrate']['class'] = \Webkul\SmartHome\Carriers\FlatRate::class;
        }

        // Add custom FreeAboveThreshold carrier
        $carriers['freeabovethreshold'] = [
            'code'         => 'freeabovethreshold',
            'title'        => 'Gratis verzending boven €150',
            'description'  => 'Gratis verzending bij bestellingen boven €150',
            'active'       => true,
            'threshold'    => '150',
            'class'        => \Webkul\SmartHome\Carriers\FreeAboveThreshold::class,
        ];

        // Update config with custom carriers
        config(['carriers' => $carriers]);
    }

    /**
     * Extend Bagisto's admin system configuration.
     *
     * @return void
     */
    protected function extendSystemConfiguration()
    {
        // Load and merge SmartHome system configuration
        $systemConfig = require __DIR__.'/../Config/system.php';

        $existingConfig = config('core') ?? [];

        // Append SmartHome carriers to existing sales.carriers config
        foreach ($systemConfig as $config) {
            if (isset($config['key']) && strpos($config['key'], 'sales.carriers.') === 0) {
                $existingConfig[] = $config;
            }
        }

        config(['core' => $existingConfig]);
    }

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
