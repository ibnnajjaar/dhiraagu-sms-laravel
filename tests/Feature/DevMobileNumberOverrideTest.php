<?php

use Illuminate\Support\Facades\Http;
use IbnNajjaar\DhiraaguSMSLaravel\DhiraaguSMS;
use IbnNajjaar\DhiraaguSMSLaravel\Responses\DhiraaguResponse;

it('overrides any provided recipients with the configured dev mobile number', function () {
    // Arrange: force a dev mobile number in config
    config(['dhiraagu_sms.dev_mobile_number' => '9609876543']);

    // Fake Dhiraagu API
    Http::fake([
        '*' => Http::response([
            'transactionId'          => '319075e0-25a3-4a4b-a330-30c1dbb865fd',
            'transactionStatus'      => 'true',
            'transactionDescription' => 'Message was sent for delivery',
            'referenceNumber'        => '060806032411233232311216',
        ], 200, []),
    ]);

    $client = new DhiraaguSMS(username: 'user', password: 'pass');

    // Act: attempt to send to multiple recipients
    $resp = $client->send(\IbnNajjaar\DhiraaguSMSLaravel\DataObjects\DhiraaguSMSData::make()
        ->setRecipients('7234567,9607654321,  1234567') // includes invalid one to ensure it is ignored too
        ->setMessage('Hello from test')
        ->setSource('Test'));

    // Assert: response type
    expect($resp)->toBeInstanceOf(DhiraaguResponse::class);

    // Assert: only dev number is used as destination, regardless of provided recipients
    Http::assertSent(function (\Illuminate\Http\Client\Request $request) {
        $data = $request->data();
        // destination must be an array with exactly one value equal to normalized dev number
        return isset($data['destination'])
            && $data['destination'] === ['9609876543'];
    });
});
