<?php

namespace SeniorProgramming\FanCourier\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Contracts\Support\DeferrableProvider;

/**
 * Class TwitchApiServiceProvider
 * @package Skmetaly\TwitchApi\Providers
 */
class ApiServiceProvider extends ServiceProvider implements DeferrableProvider
{
    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bindIf('fancourier', \SeniorProgramming\FanCourier\Services\ApiService::class, true);
    }

    /**
     *  Boot
     */
    public function boot()
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../../config/fancourier.php',
            'fancourier'
        );
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['fancourier'];
    }
}
