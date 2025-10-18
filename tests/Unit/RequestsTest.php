<?php

use IbnNajjaar\DhiraaguSMSLaravel\DataObjects\DhiraaguSMSData;
use IbnNajjaar\DhiraaguSMSLaravel\Requests\SendMessageToMultipleRecipients;
use IbnNajjaar\DhiraaguSMSLaravel\Requests\SendMessageToSingleRecipient;

it('builds POST payload for multiple recipients including optional source', function () {
    $data = DhiraaguSMSData::make()
        ->setRecipients('1234567,9607654321,1234567')
        ->setMessage('Test SMS')
        ->setSource('Test');

    $req = new SendMessageToMultipleRecipients($data, authorization_key: 'abc');

    expect($req->getEndpoint())->toBe('/sms')
        ->and($req->getMethod())->toBe('post')
        ->and($req->getPayload())->toEqual([
            'destination' => ['9601234567', '9607654321'],
            'content' => 'Test SMS',
            'authorizationKey' => 'abc',
            'source' => 'Test',
        ]);
});

it('builds GET payload for single recipient and omits null source', function () {
    $data = DhiraaguSMSData::make()
        ->setRecipients('1234567,9607654321')
        ->setMessage('Hi');

    $req = new SendMessageToSingleRecipient($data, authorization_key: 'xyz');

    expect($req->getEndpoint())->toBe('/sms')
        ->and($req->getMethod())->toBe('get')
        ->and($req->getPayload())->toEqual([
            'destination' => '9601234567',
            'content' => 'Hi',
            'key' => 'xyz',
        ]);
});
