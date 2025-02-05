# Jawaly SMS Gateway Package for Laravel

A Laravel package for sending SMS messages through Jawaly SMS Gateway (4jawaly.net).

## Installation

You can install the package via composer:

```bash
composer require samehsoliman/sms-gateway-4jawaly
```

## Configuration

Publish the configuration file:

```bash
php artisan vendor:publish --provider="Samehsoliman\SMSGateway4Jawaly\SMSGatewayServiceProvider"
```

Add these variables to your .env file:

```
JAWALY_SMS_USERNAME=your_username
JAWALY_SMS_PASSWORD=your_password
JAWALY_SMS_SENDER=your_sender_name
```

## Usage

```php
use Samehsoliman\SMSGateway4Jawaly\Facades\SMSGateway;

// Send SMS to a single number
SMSGateway::send('966500000000', 'Your message here');

// Send SMS to multiple numbers
SMSGateway::send(['966500000000', '966500000001'], 'Your message here');

// Send with custom sender name
SMSGateway::send('966500000000', 'Your message here', 'CUSTOM_SENDER');

// Get account balance
$balance = SMSGateway::getBalance();

// Get sender names
$senders = SMSGateway::getSenderNames();
```

## Features

- Send SMS to single or multiple numbers
- Automatic number formatting (adds 966 prefix if needed)
- Get account balance
- Retrieve approved sender names
- Unicode support for Arabic messages
- Exception handling with meaningful error messages

## Support

For support, please contact samehsoliman@example.com

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
