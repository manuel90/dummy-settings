<?php

namespace Manuel90\DummySettings;

use Illuminate\Support\ServiceProvider;


class DummySettingsServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadRoutesFrom(__DIR__ . '/../routes/routes.php');
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'dummysettings');
        $this->loadTranslationsFrom(__DIR__.'/../publishable/lang', 'dummysettings');
        $this->publishes([__DIR__."/assets" => public_path('manuel90/dummysettings')], 'public');

    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        
    }
}
