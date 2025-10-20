<?php

use IbnNajjaar\DhiraaguSMSLaravel\DhiraaguSMS;
use IbnNajjaar\DhiraaguSMSLaravel\DhiraaguSMSLaravelServiceProvider;

uses()->group('service-provider');

beforeEach(function () {
    loadServiceProvider();
});

it('registers singleton and resolves dhiraagu sms with valid config', function () {
    // Arrange: set valid credentials in config
    config()->set('dhiraagu_sms.username', 'user');
    config()->set('dhiraagu_sms.password', 'pass');

    // Act: resolve the bound instance twice
    $first = app(DhiraaguSMS::class);
    $second = app(DhiraaguSMS::class);

    // Assert: correct type and singleton behavior
    expect($first)
        ->toBeInstanceOf(DhiraaguSMS::class)
        ->and($second)->toBe($first);
})->group('binding');

it('throws invalid argument when credentials are missing', function () {
    // Ensure no credentials are present
    config()->set('dhiraagu_sms.username', null);
    config()->set('dhiraagu_sms.password', null);

    // Attempt to resolve should throw
    app(DhiraaguSMS::class);
})->throws(\InvalidArgumentException::class, 'Dhiraagu SMS username and password are required.');

it('can publish the config file', function () {
    // Ensure the target publish path does not already exist
    $target = config_path('dhiraagu_sms.php');
    if (file_exists($target)) {
        @unlink($target);
    }

    // Act: publish using the provider's tag
    $this->artisan('vendor:publish', [
        '--provider' => DhiraaguSMSLaravelServiceProvider::class,
        '--tag' => 'dhiraagu_sms',
        '--force' => true,
    ])->assertExitCode(0);

    // Assert: file was published to the application's config directory
    expect($target)->toBeFile();

    // Basic sanity check the file returns an array-like config
    $config = include $target;
    expect($config)
        ->toBeArray()
        ->toHaveKey('username')
        ->toHaveKey('password');
})->group('publishing');

