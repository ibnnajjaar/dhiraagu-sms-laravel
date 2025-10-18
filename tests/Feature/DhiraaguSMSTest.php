<?php

use IbnNajjaar\DhiraaguSMSLaravel\DhiraaguSMS;
use IbnNajjaar\DhiraaguSMSLaravel\DataObjects\DhiraaguSMSData;
use IbnNajjaar\DhiraaguSMSLaravel\Responses\DhiraaguResponse;

function makeData(array $overrides = []): DhiraaguSMSData
{
    $recipients = $overrides['recipients'] ?? '9601234567,9607654321';
    $message = $overrides['message'] ?? 'Test SMS';
    $source = $overrides['source'] ?? 'Test';

    return DhiraaguSMSData::make()
        ->setRecipients($recipients)
        ->setMessage($message)
        ->setSource($source);
}

it('sends SMS to multiple recipients via POST and returns DhiraaguResponse', function () {
    $client = new DhiraaguSMS(username: 'user', password: 'pass');
    $resp = $client->send(makeData());

    expect($resp)->toBeInstanceOf(DhiraaguResponse::class)
        ->and($resp->statusCode)->toBe(200)
        ->and($resp->transactionStatus)->toBe('true')
        ->and($resp->transactionDescription)->toBe('Message was sent for delivery');
});

it('sends SMS to single recipient via GET and returns DhiraaguResponse', function () {
    $client = new DhiraaguSMS(username: 'user', password: 'pass');
    $resp = $client->sendToSingleRecipient(makeData());

    expect($resp)->toBeInstanceOf(DhiraaguResponse::class)
        ->and($resp->statusCode)->toBe(200)
        ->and($resp->transactionStatus)->toBe('true');
});

it('returns 401-style response for incorrect credentials (POST)', function () {
    $client = new DhiraaguSMS(username: 'bad', password: 'bad');
    $resp = $client->send(makeData());

    expect($resp->statusCode)->toBe(401)
        ->and($resp->transactionStatus)->toBe('false')
        ->and($resp->transactionDescription)->toContain('Incorrect credentials');
});

it('returns 401-style response for incorrect credentials (GET)', function () {
    $client = new DhiraaguSMS(username: 'bad', password: 'bad');
    $resp = $client->sendToSingleRecipient(makeData());

    expect($resp->statusCode)->toBe(401)
        ->and($resp->transactionStatus)->toBe('false');
});

it('handles 422 request errors by returning the raw API response through DhiraaguResponse', function () {
    $client = new DhiraaguSMS(username: 'user', password: 'pass');
    $data = makeData(['message' => 'invalid']);

    $resp = $client->send($data);

    // In current implementation, 422 will still be wrapped in DhiraaguResponse
    expect($resp->statusCode)->toBe(422)
        ->and($resp->transactionId)->toBe('')
        ->and($resp->transactionStatus)->toBe('')
        ->and($resp->transactionDescription)->toBe('')
        ->and($resp->referenceNumber)->toBe('');
});
