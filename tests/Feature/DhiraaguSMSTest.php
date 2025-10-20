<?php

use IbnNajjaar\DhiraaguSMSLaravel\DhiraaguSMS;
use IbnNajjaar\DhiraaguSMSLaravel\DataObjects\DhiraaguSMSData;
use IbnNajjaar\DhiraaguSMSLaravel\Responses\DhiraaguResponse;
use IbnNajjaar\DhiraaguSMSLaravel\Exceptions\TransactionException;
use IbnNajjaar\DhiraaguSMSLaravel\Exceptions\DhiraaguRequestException;

function makeData(array $overrides = []): DhiraaguSMSData
{
    $recipients = $overrides['recipients'] ?? '7234567,9607654321';
    $message = $overrides['message'] ?? 'Test SMS';
    $source = $overrides['source'] ?? 'Test';

    return DhiraaguSMSData::make()
                          ->setRecipients($recipients)
                          ->setMessage($message)
                          ->setSource($source);
}

it('sends SMS to multiple recipients via POST and returns DhiraaguResponse', function () {
    Http::fake([
        '*' => Http::response([
            'transactionId'          => '319075e0-25a3-4a4b-a330-30c1dbb865fd',
            'transactionStatus'      => 'true',
            'transactionDescription' => 'Message was sent for delivery',
            'referenceNumber'        => '060806032411233232311216',
        ], 200, []),
    ]);

    $client = new DhiraaguSMS(username: 'user', password: 'pass');
    $resp = $client->send(makeData());

    expect($resp)->toBeInstanceOf(DhiraaguResponse::class)
                 ->and($resp->statusCode)->toBe(200)
                 ->and($resp->transactionStatus)->toBe('true')
                 ->and($resp->transactionDescription)->toBe('Message was sent for delivery');
});

it('sends SMS to single recipient via GET and returns DhiraaguResponse', function () {
    Http::fake([
        '*' => Http::response([
            'transactionId'          => '319075e0-25a3-4a4b-a330-30c1dbb865fd',
            'transactionStatus'      => 'true',
            'transactionDescription' => 'Message was sent for delivery',
            'referenceNumber'        => '060806032411233232311216',
        ], 200, []),
    ]);

    $client = new DhiraaguSMS(username: 'user', password: 'pass');
    $resp = $client->sendToSingleRecipient(makeData());

    expect($resp)->toBeInstanceOf(DhiraaguResponse::class)
                 ->and($resp->statusCode)->toBe(200)
                 ->and($resp->transactionStatus)->toBe('true');
});

it('returns 401 response for incorrect credentials post request', function () {

    Http::fake([
        '*' => Http::response([
            'transactionId'          => 'e3f94753-8a4c-4349-9d76-321ae9da2880',
            'transactionStatus'      => 'false',
            'transactionDescription' => 'Incorrect credentials',
            'referenceNumber'        => '',
        ], 401, []),
    ]);

    $client = new DhiraaguSMS(username: 'bad', password: 'bad');
    expect(fn() => $client->send(makeData()))
        ->toThrow(function (\IbnNajjaar\DhiraaguSMSLaravel\Exceptions\IncorrectCredentialsException $e) {
            expect($e->transactionId)->toBe('e3f94753-8a4c-4349-9d76-321ae9da2880')
                                     ->and($e->transactionStatus)->toBe(false)
                                     ->and($e->transactionDescription)->toBe('Incorrect credentials')
                                     ->and($e->referenceNumber)->toBe('')
                                     ->and($e->statusCode)->toBe(401);
        });
});

it('returns 401 response for incorrect credentials get request', function () {
    Http::fake([
        '*' => Http::response([
            'transactionId'          => 'e3f94753-8a4c-4349-9d76-321ae9da2880',
            'transactionStatus'      => 'false',
            'transactionDescription' => 'Incorrect credentials',
            'referenceNumber'        => '',
        ], 401, []),
    ]);

    $client = new DhiraaguSMS(username: 'bad', password: 'bad');
    expect(fn() => $client->sendToSingleRecipient(makeData()))
        ->toThrow(function (\IbnNajjaar\DhiraaguSMSLaravel\Exceptions\IncorrectCredentialsException $e) {
            expect($e->transactionId)->toBe('e3f94753-8a4c-4349-9d76-321ae9da2880')
                                     ->and($e->transactionStatus)->toBe(false)
                                     ->and($e->transactionDescription)->toBe('Incorrect credentials')
                                     ->and($e->referenceNumber)->toBe('')
                                     ->and($e->statusCode)->toBe(401);
        });
});

it('handles 422 request errors by returning the raw API response through DhiraaguRequestException', function () {

    Http::fake([
        '*' => Http::response([
            'error' => '',
            'message' => 'Validation Failed',
            'detail' => 'The provided content is invalid.',
        ], 422, []),
    ]);

    $client = new DhiraaguSMS(username: 'user', password: 'pass');

    expect(fn() => $client->send(makeData()))
        ->toThrow(function (\IbnNajjaar\DhiraaguSMSLaravel\Exceptions\DhiraaguRequestException $e) {
            expect($e->text)->toBe('Validation Failed. The provided content is invalid.')
                                     ->and($e->statusCode)->toBe(422);
        });
});

it('handles 500 request errors by returning the raw API response through TransactionException', function () {

    Http::fake([
        '*' => Http::response([
            'transactionId'          => 'e3f94753-8a4c-4349-9d76-321ae9da2880',
            'transactionStatus'      => 'false',
            'transactionDescription' => 'Failed to send message',
            'referenceNumber'        => '',
        ], 500, []),
    ]);

    $client = new DhiraaguSMS(username: 'user', password: 'pass');

    expect(fn() => $client->send(makeData()))
        ->toThrow(function (\IbnNajjaar\DhiraaguSMSLaravel\Exceptions\TransactionException $e) {
            expect($e->transactionId)->toBe('e3f94753-8a4c-4349-9d76-321ae9da2880')
                                     ->and($e->transactionStatus)->toBe(false)
                                     ->and($e->transactionDescription)->toBe('Failed to send message')
                                     ->and($e->referenceNumber)->toBe('')
                                     ->and($e->statusCode)->toBe(500);
        });

});
