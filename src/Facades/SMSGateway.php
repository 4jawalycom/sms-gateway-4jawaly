<?php

namespace Jawalycom\SMSGateway4Jawaly\Facades;

use Illuminate\Support\Facades\Facade;

class SMSGateway extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'sms-gateway-4jawaly';
    }
}
