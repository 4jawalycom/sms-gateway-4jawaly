# Jawaly SMS Gateway Package for Laravel

A Laravel package for sending SMS messages through Jawaly SMS Gateway (4jawaly.com).

## Installation

You can install the package via composer:

```bash
composer require 4jawalycom/sms-gateway-4jawaly
```

## Configuration

Publish the configuration file:

```bash
php artisan vendor:publish --provider="Jawalycom\SMSGateway4Jawaly\SMSGatewayServiceProvider"
```

Add these variables to your .env file:

```
JAWALY_SMS_GATEWAY_API_KEY=your_api_key
JAWALY_SMS_GATEWAY_API_SECRET=your_api_secret
JAWALY_SMS_GATEWAY_SENDER=your_sender_name
```

## Usage

### Sending SMS

```php
use Jawalycom\SMSGateway4Jawaly\Facades\SMSGateway;

// Send SMS to a single number
SMSGateway::send('966500000000', 'Your message here');

// Send SMS to multiple numbers
SMSGateway::send(['966500000000', '966500000001'], 'Your message here');

// Send with custom sender name
SMSGateway::send('966500000000', 'Your message here', 'CUSTOM_SENDER');
```

### Getting Account Balance

```php
// Get balance with default options
$balance = SMSGateway::getBalance();

// Get balance with custom options
$balance = SMSGateway::getBalance([
    'is_active' => 1,           // get active packages only
    'order_by' => 'id',         // package_points, current_points, expire_at or id
    'order_by_type' => 'desc',  // desc or asc
    'page' => 1,                // page number
    'page_size' => 10,          // items per page
    'return_collection' => 1    // get all collection
]);
```

### Getting Sender Names

```php
// Get sender names with default options
$senders = SMSGateway::getSenderNames();

// Get sender names with custom options
$senders = SMSGateway::getSenderNames([
    'page_size' => 10,          // items per page
    'page' => 1,                // page number
    'status' => 1,              // 1 for active, 2 for inactive
    'sender_name' => '',        // search by sender name
    'is_ad' => '',             // 1 for ads, 2 for not ads
    'return_collection' => 1    // get all collection
]);
```

## Response Format

### Send SMS Response
```php
[
    'success' => true,
    'job_id' => 'xxxxx',
    'data' => [
        // Full response from the API
    ]
]
```

### Balance Response
```php
{
    "collection": [
        {
            "id": xxx,
            "package_points": xxx,
            "current_points": xxx,
            "expire_at": "yyyy-mm-dd",
            // ... other package details
        }
    ],
    "pagination": {
        "total": xxx,
        "count": xxx,
        "per_page": xxx,
        "current_page": x,
        "total_pages": x
    }
}
```

### Sender Names Response
```php
{
    "collection": [
        {
            "id": xxx,
            "sender_name": "SENDER_NAME",
            "status": x,
            "note": "xxx",
            // ... other sender details
        }
    ],
    "pagination": {
        "total": xxx,
        "count": xxx,
        "per_page": xxx,
        "current_page": x,
        "total_pages": x
    }
}
```

## Error Handling

The package throws exceptions for various error cases:
- Invalid credentials
- Empty message
- Invalid sender name
- Network errors
- API errors

Example error handling:
```php
try {
    $result = SMSGateway::send('966500000000', 'Your message');
    // Message sent successfully
    echo "Message sent! Job ID: " . $result['job_id'];
} catch (\Exception $e) {
    // Handle the error
    echo "Error: " . $e->getMessage();
}
```

## Features

- Send SMS to single or multiple numbers
- Support for Arabic and Unicode messages
- Get account balance and package details
- Retrieve approved sender names with filtering options
- Pagination support for balance and sender names
- Exception handling with meaningful error messages
- Laravel integration

## Support

For support, please contact info@4jawaly.com

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
