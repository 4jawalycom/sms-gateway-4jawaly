<?php

namespace Jawalycom\SMSGateway4Jawaly;

use Illuminate\Support\ServiceProvider;

class SMSGatewayServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/jawaly-sms.php', 'jawaly-sms'
        );

        $this->app->singleton('jawaly-sms', function ($app) {
            return new SMSGateway(config('jawaly-sms'));
        });
    }

    public function boot()
    {
        $this->publishes([
            __DIR__.'/../config/jawaly-sms.php' => config_path('jawaly-sms.php'),
        ], 'config');
    }
}
