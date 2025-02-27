# 4Jawaly SMS Gateway Package for Laravel

A Laravel package for sending SMS messages through 4Jawaly SMS Gateway (4jawaly.com).

## Requirements

- PHP ^7.4|^8.0|^8.1|^8.2|^8.3
- Laravel 7.x|8.x|9.x|10.x
- Guzzle HTTP ^7.0

## Installation

1. Install the package via composer:

```bash
composer require 4jawalycom/sms-gateway-4jawaly
```

2. Add the service provider to your `config/app.php`:

```php
'providers' => [
    // ...
    Jawalycom\SMSGateway4Jawaly\SMSGatewayServiceProvider::class,
],

'aliases' => [
    // ...
    'SMSGateway' => Jawalycom\SMSGateway4Jawaly\Facades\SMSGateway::class,
],
```

3. Publish the configuration file:

```bash
php artisan vendor:publish --provider="Jawalycom\SMSGateway4Jawaly\SMSGatewayServiceProvider"
```

4. Add these variables to your .env file:

```
JAWALY_SMS_API_KEY=your_api_key
JAWALY_SMS_API_SECRET=your_api_secret
JAWALY_SMS_SENDER=your_sender_name
```

## Usage

### Method 1: Using the Facade

```php
use Jawalycom\SMSGateway4Jawaly\Facades\SMSGateway;

// Important: All three parameters (number, message, sender) are required
// رقم الجوال ونص الرسالة واسم المرسل كلها متطلبات إلزامية

// Send SMS to a single number
$result = SMSGateway::send('966500000000', 'Your message here', 'SENDER_NAME');

// Send SMS to multiple numbers
$result = SMSGateway::send(
    ['966500000000', '966500000001'],
    'Your message here',
    'SENDER_NAME'
);

// Note: The sender name must be pre-approved by 4jawaly.com
// ملاحظة: يجب أن يكون اسم المرسل معتمداً مسبقاً من 4jawaly.com

// Check the result
if (!empty($result['success'])) {
    echo "Message sent successfully!";
    if (!empty($result['job_ids'])) {
        echo "Job IDs: " . implode(', ', $result['job_ids']);
    }
} else {
    echo "Failed to send message.";
    if (!empty($result['errors'])) {
        foreach ($result['errors'] as $error => $numbers) {
            echo "Error: $error - Numbers: " . implode(', ', $numbers);
        }
    }
}
```

### Method 2: Using Direct Instantiation

```php
use Jawalycom\SMSGateway4Jawaly\SMSGateway;

// Setup configuration array
$config = [
    'api_key' => env('JAWALY_SMS_API_KEY'),
    'api_secret' => env('JAWALY_SMS_API_SECRET'),
    'base_url' => 'https://api-sms.4jawaly.com/api/v1/',
    'default_sender' => env('JAWALY_SMS_SENDER'), // Must be pre-approved by 4jawaly.com
    'timeout' => 30,
    'verify_ssl' => true
];

// Create a new instance
$gateway = new SMSGateway($config);

// Send SMS (all parameters are required)
$result = $gateway->send('966500000000', 'Your message here', 'SENDER_NAME');

// Check the result
if (!empty($result['success'])) {
    echo "Message sent successfully!";
    if (!empty($result['job_ids'])) {
        echo "Job IDs: " . implode(', ', $result['job_ids']);
    }
} else {
    echo "Failed to send message.";
    if (!empty($result['errors'])) {
        foreach ($result['errors'] as $error => $numbers) {
            echo "Error: $error - Numbers: " . implode(', ', $numbers);
        }
    }
}
```

### Getting Account Balance

```php
// Using Facade
$balance = SMSGateway::getBalance();

// Using Direct Instantiation
$sms = new SMSGateway(config('jawaly-sms'));
$balance = $sms->getBalance();

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
// Using Facade
$senders = SMSGateway::getSenderNames();

// Using Direct Instantiation
$sms = new SMSGateway(config('jawaly-sms'));
$senders = $sms->getSenderNames();

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
    $result = SMSGateway::send('966500000000', 'Your message', 'SENDER_NAME');
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
- Laravel integration with Facade support
- Support for PHP 7.4 to 8.3
- Support for Laravel 7.x to 10.x

## Support

For support, please contact info@4jawaly.com

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
