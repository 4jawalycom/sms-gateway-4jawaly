<?php

namespace Jawalycom\SMSGateway4Jawaly\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static array send(string|array $numbers, string $message, string $sender = null)
 * @method static array getBalance(array $options = [])
 * @method static array getSenderNames(array $options = [])
 * 
 * @see \Jawalycom\SMSGateway4Jawaly\SMSGateway
 */
class SMSGateway extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'jawaly-sms';
    }
}
