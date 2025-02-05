<?php

namespace Jawalycom\SMSGateway4Jawaly;

use Illuminate\Support\ServiceProvider;

class SMSGatewayServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        // Register the config file
        $this->mergeConfigFrom(
            __DIR__.'/../config/jawaly-sms.php', 'jawaly-sms'
        );

        // Register the main class
        $this->app->bind('jawaly-sms', function ($app) {
            return new SMSGateway($app['config']['jawaly-sms']);
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // Publish the config file
        $this->publishes([
            __DIR__.'/../config/jawaly-sms.php' => config_path('jawaly-sms.php'),
        ], 'config');
    }

    public function provides()
    {
        return ['sms-gateway'];
    }
}
