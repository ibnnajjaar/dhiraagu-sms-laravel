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

it('removes duplicates from recipients', function () {
    $data = DhiraaguSMSData::make()
                           ->setRecipients('9607123456, 9607123456') // duplicates + invalid foreign
                           ->setMessage('Hi');

    expect($data->getRecipients())
        ->toEqual(['9607123456']);
});

it('removes empty strings from recipients', function () {
    $data = DhiraaguSMSData::make()
                           ->setRecipients('9607123456, , 9609123456') // duplicates + invalid foreign
                           ->setMessage('Hi');

    expect($data->getRecipients())
        ->toEqual(['9607123456', '9609123456']);
});

it('returns the first recipient with getRecipient', function () {
    $data = DhiraaguSMSData::make()
        ->setRecipients('9607654321, 9607234567')
        ->setMessage('Hi');

    expect($data->getRecipient())->toBe('9607654321');
});
