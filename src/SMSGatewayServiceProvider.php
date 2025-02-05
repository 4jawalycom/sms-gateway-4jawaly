<?php

namespace Jawalycom\SMSGateway4Jawaly;

use Illuminate\Support\ServiceProvider;

class SMSGatewayServiceProvider extends ServiceProvider
{
    public function register()
    {
        // Register the config file
        $this->mergeConfigFrom(
            __DIR__.'/../config/jawaly-sms.php', 'jawaly-sms'
        );

        // Register the main class to use with the facade
        $this->app->singleton('sms-gateway-4jawaly', function ($app) {
            return new SMSGateway(config('jawaly-sms'));
        });
    }

    public function boot()
    {
        // Publish the config file
        $this->publishes([
            __DIR__.'/../config/jawaly-sms.php' => config_path('jawaly-sms.php'),
        ], 'config');
    }
}
