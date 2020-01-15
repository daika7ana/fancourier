<?php
namespace SeniorProgramming\FanCourier\Providers;

use Illuminate\Support\ServiceProvider;
/**
 * Class TwitchApiServiceProvider
 * @package Skmetaly\TwitchApi\Providers
 */
class ApiServiceProvider extends ServiceProvider  {
    
    /**
	 * Indicates if loading of the provider is deferred.
	 *
	 * @var bool
	 */
	//protected $defer = false;
    
    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->registerServices();
    }
    /**
     *  Boot
     */
    public function boot()
    {
       $this->addConfig();
    }
    /**
     *  Registering services
     */
    private function registerServices()
    {
        $this->app->bind('fancourier','SeniorProgramming\FanCourier\Services\ApiService');
    }
    
    /**
     *  Config publishing
     */
    private function addConfig()
    {
        $this->mergeConfigFrom(
            __DIR__.'/../../config/fancourier.php', 'fancourier'
        );
    }
    
    /**
	 * Get the services provided by the provider.
	 *
	 * @return array
	 */
	public function provides()
	{
		return array();
	}
}