# Dhiraagu SMS for Laravel

[![Tests](https://github.com/ibnnajjaar/dhiraagu-sms-laravel/workflows/Tests/badge.svg)](https://github.com/ibnnajjaar/dhiraagu-sms-laravel/actions)
![Code Coverage Badge](./.github/coverage.svg)

A simple, lightweight package for sending SMS via **new** Dhiraagu SMS API.

## Requirements

- PHP 8.4 or higher
- Laravel 12.x or higher
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
#DHIRAAGU_SMS_DEV_MOBILE_NUMBER=your_dev_mobile_number
```
> [!NOTE] You can also add the dev mobile number to your environment variables. This is useful for testing purposes when you don't want to send SMS to real recipients during development. When this variable is set, all SMS will be sent to the specified dev mobile number instead of the actual recipients.
> This can be used when you do not have a separate testing account.

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
**Note:** This uses a **get** method to send the SMS.
```php
app(DhiraaguSMS::class)->sendToSingleRecipient($data);
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

### Test Coverage
You can generate a coverage report without requiring Xdebug or PCOV by running tests under phpdbg via the provided Composer script:
```bash
composer test-coverage
```
This will execute Pest with code coverage enabled and write a Clover report to coverage.xml. If you prefer to run Pest directly, ensure you have a coverage driver installed/enabled (e.g., Xdebug with XDEBUG_MODE=coverage):
```bash
XDEBUG_MODE=coverage vendor/bin/pest --coverage
```
## Changelog

See CHANGELOG.md for release notes: [CHANGELOG.md](./CHANGELOG.md)

## Contributing
Contributions are welcome! Please feel free to submit a Pull Request.

## Contributors
- [Hussain Afeef](https://abunooh.com)

## Support
For questions, issues, or feature requests, please open an issue on GitHub.

## Alternatives
- [Dhiraagu SMS](https://github.com/dash8x/dhiraagu-sms) by [dash8x](https://github.com/dash8x) - A framework-agnostic PHP library for sending SMS via the Dhiraagu SMS API. This is the most popular package for integrating with the Dhiraagu SMS service.

## License

This package is open-sourced software licensed under the MIT License.
