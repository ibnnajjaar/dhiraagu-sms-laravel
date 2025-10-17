# Dhiraagu SMS for Laravel

[![Tests](https://github.com/ibnnajjaar/dhiraagu-sms-laravel/workflows/Tests/badge.svg)](https://github.com/ibnnajjaar/dhiraagu-sms-laravel/actions)
![Code Coverage Badge](./.github/coverage.svg)

A simple, lightweight package for sending SMS via Dhiraagu SMS API.

## Requirements

- PHP 8.4 or higher
- Laravel 10.x or higher
- Composer

## Installation

Install the package using Composer:

```bash
composer require ibnnajjaar/dhiraagu-sms-laravel
```

You can optionally publish configuration file
```bash
php artisan vendor:publish --tag=dhiraagu_sms
```

## Setup & Configuration

### Environment Setup

Before using the package, you need to obtain credentials (username and password) from the Dhiraagu Bulk SMS portal and add them to your environment variables:

```env
DHIRAAGU_SMS_USERNAME=your_username
DHIRAAGU_SMS_PASSWORD=your_password
```

## Usage
### Sending SMS to Multiple Recipients
When sending SMS to multiple recipients, first create a DhiraaguSMSData object with the recipient list and message:
```php
use IbnNajjaar\DhiraaguSMSLaravel\DataObjects\DhiraaguSMSData;

// Option 1: Using the fromArray method
$data = DhiraaguSMSData::fromArray([
    'recipients' => '7xxxxxx, +9607xxxxxx, 9xxxxxx', // string of recipients separated by comma
    'message' => 'Hello World', // message to be sent
]);

// Option 2: Using fluent methods
$data = new DhiraaguSMSData()
    ->setRecipients('7xxxxxx, +9607xxxxxx, 9xxxxxx')
    ->setMessage('Hello World');
```

Then, to send the SMS, use the DhiraaguSMS class.
```php

use IbnNajjaar\DhiraaguSMSLaravel\DhiraaguSMS;

app(DhiraaguSMS::class)->send($data);
```

### Sending SMS to a Single Recipient
For convenience, you can send SMS to a single recipient without creating a data object:
**Note:** This uses a get method to send the SMS.
```php
$recipient = '7xxxxxx';
$message = 'Hello World';

app(DhiraaguSMS::class)->sendToSingleRecipient($recipient, $message);
```
### Using Dependency Injection
The DhiraaguSMS class is registered as a singleton, so you can use dependency injection in your services:

```php
use IbnNajjaar\DhiraaguSMSLaravel\DhiraaguSMS;
use IbnNajjaar\DhiraaguSMSLaravel\DataObjects\DhiraaguSMSData;

class NotificationService
{
    public function __construct(private DhiraaguSMS $dhiraaguSMS)
    {
    }
    
    public function handle(DhiraaguSMSData $data)
    {
        $this->dhiraaguSMS->send($data);
    }
}

```

## Security Considerations

### Credential Management
- Store credentials in environment variables
- Never commit credentials to version control
- Add .env to your .gitignore file

## Testing
```bash
composer test
```
## Changelog

See CHANGELOG.md for release notes: [CHANGELOG.md](./CHANGELOG.md)

## Contributing
Contributions are welcome! Please feel free to submit a Pull Request.

## Contributors
- [Hussain Afeef](https://abunooh.com)

## Support
For questions, issues, or feature requests, please open an issue on GitHub.

## License

This package is open-sourced software licensed under the MIT License.
