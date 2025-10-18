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

it('removes whitespace from recipients', function () {
    $data = DhiraaguSMSData::make()
                           ->setRecipients(' 9607123456') // duplicates + invalid foreign
                           ->setMessage('Hi');

    expect($data->getRecipients())
        ->toEqual(['9607123456']);
});

it('removes duplicates from recipients', function () {
    $data = DhiraaguSMSData::make()
                           ->setRecipients('9607123456, 9607123456') // duplicates + invalid foreign
                           ->setMessage('Hi');

    expect($data->getRecipients())
        ->toEqual(['9607123456']);
});

it('removes plus signs from recipients', function () {
    $data = DhiraaguSMSData::make()
                           ->setRecipients('+9607123456') // duplicates + invalid foreign
                           ->setMessage('Hi');

    expect($data->getRecipients())
        ->toEqual(['9607123456']);
});

it('removes numbers that are not 7 or 10 digits from recipients', function () {
    $data = DhiraaguSMSData::make()
                           ->setRecipients('333222, 7123457, 9607123456') // duplicates + invalid foreign
                           ->setMessage('Hi');

    expect($data->getRecipients())
        ->toEqual(['9607123457', '9607123456']);
});

it('removes numbers that are 10 digits and does not have maldivian code from recipients', function () {
    $data = DhiraaguSMSData::make()
                           ->setRecipients('9607123456, 9997454543') // duplicates + invalid foreign
                           ->setMessage('Hi');

    expect($data->getRecipients())
        ->toEqual(['9607123456']);
});

it('removes adds maldivian code to recipients', function () {
    $data = DhiraaguSMSData::make()
                           ->setRecipients('7123456') // duplicates + invalid foreign
                           ->setMessage('Hi');

    expect($data->getRecipients())
        ->toEqual(['9607123456']);
});

it('removes any number that does not start with 7 or 9', function () {
    $data = DhiraaguSMSData::make()
                           ->setRecipients('5123456, 9608123456, 7123456') // duplicates + invalid foreign
                           ->setMessage('Hi');

    expect($data->getRecipients())
        ->toEqual(['9607123456']);
});

it('returns the first recipient with getRecipient', function () {
    $data = DhiraaguSMSData::make()
        ->setRecipients('9607654321, 9607234567')
        ->setMessage('Hi');

    expect($data->getRecipient())->toBe('9607654321');
});
