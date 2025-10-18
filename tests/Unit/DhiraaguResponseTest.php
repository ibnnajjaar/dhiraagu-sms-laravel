<?php

use IbnNajjaar\DhiraaguSMSLaravel\Responses\DhiraaguResponse;
use Illuminate\Http\Client\Response as ClientResponse;
use GuzzleHttp\Psr7\Response as GuzzleResponse;

it('parses successful response correctly', function () {
    $guzzle = new GuzzleResponse(200, ['Content-Type' => 'application/json'], json_encode([
        'transactionId' => 't-id',
        'transactionStatus' => 'true',
        'transactionDescription' => 'Message was sent for delivery',
        'referenceNumber' => 'ref-1',
    ]));

    $httpResponse = new ClientResponse($guzzle);

    $resp = DhiraaguResponse::fromResponse($httpResponse);

    expect($resp->transactionId)->toBe('t-id')
        ->and($resp->transactionStatus)->toBe('true')
        ->and($resp->transactionDescription)->toBe('Message was sent for delivery')
        ->and($resp->referenceNumber)->toBe('ref-1')
        ->and($resp->statusCode)->toBe(200);
});

it('handles missing fields gracefully', function () {
    $guzzle = new GuzzleResponse(500, ['Content-Type' => 'application/json'], json_encode([]));
    $httpResponse = new ClientResponse($guzzle);

    $resp = DhiraaguResponse::fromResponse($httpResponse);

    expect($resp->transactionId)->toBe('')
        ->and($resp->transactionStatus)->toBe('')
        ->and($resp->transactionDescription)->toBe('')
        ->and($resp->referenceNumber)->toBe('')
        ->and($resp->statusCode)->toBe(500);
});
