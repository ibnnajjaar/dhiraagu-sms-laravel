<?php

use IbnNajjaar\DhiraaguSMSLaravel\DataObjects\DhiraaguSMSData;

it('builds from array and retrieves message and source', function () {
    $data = DhiraaguSMSData::fromArray([
        'recipients' => '9601234567,9607654321',
        'message' => 'Hello',
        'source' => 'Test',
    ]);

    expect($data->getMessage())->toBe('Hello')
        ->and($data->getSource())->toBe('Test');
});

it('parses recipients into unique normalized list', function () {
    $data = DhiraaguSMSData::make()
        ->setRecipients('9601234567, 1234567, +9601234567, 9710000000') // duplicates + invalid foreign
        ->setMessage('Hi');

    expect($data->getRecipients())
        ->toBeArray()
        ->toEqual(['9601234567', '9601234567', '9601234567']);
});

it('returns the first recipient with getRecipient', function () {
    $data = DhiraaguSMSData::make()
        ->setRecipients('1234567,9607654321')
        ->setMessage('Hi');

    expect($data->getRecipient())->toBe('9601234567');
});
