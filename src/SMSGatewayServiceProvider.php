<?php

namespace Jawalycom\SMSGateway4Jawaly;

use Illuminate\Support\ServiceProvider;
use Jawalycom\SMSGateway4Jawaly\Facades\SMSGateway as SMSGatewayFacade;

class SMSGatewayServiceProvider extends ServiceProvider
{
    public function register()
    {
        // Register the config file
        $this->mergeConfigFrom(
            __DIR__.'/../config/jawaly-sms.php', 'jawaly-sms'
        );

        // Register the main class to use with the facade
        $this->app->bind('sms-gateway', function ($app) {
            return new SMSGateway(config('jawaly-sms'));
        });

        // Register the facade
        $this->app->alias('sms-gateway', SMSGateway::class);
    }

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
