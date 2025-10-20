<?php

use Illuminate\Support\Facades\Http;
use Illuminate\Support\ServiceProvider;
use IbnNajjaar\DhiraaguSMSLaravel\DhiraaguSMS;
use IbnNajjaar\DhiraaguSMSLaravel\Responses\DhiraaguResponse;

it('overrides any provided recipients using ServiceProvider alwaysSendTo registration', function () {
    // Arrange: register a test-only service provider that sets the alwaysSendTo override
    app()->register(new class(app()) extends ServiceProvider {
        public function register(): void
        {
            \IbnNajjaar\DhiraaguSMSLaravel\DhiraaguSMS::alwaysSendTo('9609876543');
        }
    });

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


it('clears the override and uses provided recipients after clearAlwaysSendTo()', function () {
    // Arrange: register a test-only service provider that initially sets the override
    app()->register(new class(app()) extends \Illuminate\Support\ServiceProvider {
        public function register(): void
        {
            \IbnNajjaar\DhiraaguSMSLaravel\DhiraaguSMS::alwaysSendTo('9609876543');
        }
    });

    // Now clear the override explicitly
    \IbnNajjaar\DhiraaguSMSLaravel\DhiraaguSMS::clearAlwaysSendTo();

    // Fake Dhiraagu API
    \Illuminate\Support\Facades\Http::fake([
        '*' => \Illuminate\Support\Facades\Http::response([
            'transactionId'          => '319075e0-25a3-4a4b-a330-30c1dbb865fd',
            'transactionStatus'      => 'true',
            'transactionDescription' => 'Message was sent for delivery',
            'referenceNumber'        => '060806032411233232311216',
        ], 200, []),
    ]);

    $client = new \IbnNajjaar\DhiraaguSMSLaravel\DhiraaguSMS(username: 'user', password: 'pass');

    // Act: attempt to send to multiple recipients (with one invalid to be dropped)
    $resp = $client->send(\IbnNajjaar\DhiraaguSMSLaravel\DataObjects\DhiraaguSMSData::make()
        ->setRecipients('7234567,9607654321,  1234567')
        ->setMessage('Hello from test')
        ->setSource('Test'));

    // Assert: response type
    expect($resp)->toBeInstanceOf(\IbnNajjaar\DhiraaguSMSLaravel\Responses\DhiraaguResponse::class);

    // Assert: destination uses the normalized provided recipients (override cleared)
    \Illuminate\Support\Facades\Http::assertSent(function (\Illuminate\Http\Client\Request $request) {
        $data = $request->data();
        return isset($data['destination'])
            && $data['destination'] === ['9607234567', '9607654321'];
    });
});


it('does not apply alwaysSendTo when condition is false', function () {
    // Arrange: call alwaysSendTo with a false condition (should be a no-op)
    \IbnNajjaar\DhiraaguSMSLaravel\DhiraaguSMS::alwaysSendTo('9609876543', false);

    // Fake Dhiraagu API
    \Illuminate\Support\Facades\Http::fake([
        '*' => \Illuminate\Support\Facades\Http::response([
            'transactionId'          => '319075e0-25a3-4a4b-a330-30c1dbb865fd',
            'transactionStatus'      => 'true',
            'transactionDescription' => 'Message was sent for delivery',
            'referenceNumber'        => '060806032411233232311216',
        ], 200, []),
    ]);

    $client = new \IbnNajjaar\DhiraaguSMSLaravel\DhiraaguSMS(username: 'user', password: 'pass');

    // Act: attempt to send to multiple recipients (with one invalid to be dropped)
    $resp = $client->send(\IbnNajjaar\DhiraaguSMSLaravel\DataObjects\DhiraaguSMSData::make()
        ->setRecipients('7234567,9607654321,  1234567')
        ->setMessage('Hello from test')
        ->setSource('Test'));

    // Assert: response type
    expect($resp)->toBeInstanceOf(\IbnNajjaar\DhiraaguSMSLaravel\Responses\DhiraaguResponse::class);

    // Assert: destination uses the normalized provided recipients (override did not apply)
    \Illuminate\Support\Facades\Http::assertSent(function (\Illuminate\Http\Client\Request $request) {
        $data = $request->data();
        return isset($data['destination'])
            && $data['destination'] === ['9607234567', '9607654321'];
    });
});


it('clears the override when alwaysSendTo is called with empty string', function () {
    // Ensure no leftover state from previous tests
    \IbnNajjaar\DhiraaguSMSLaravel\DhiraaguSMS::clearAlwaysSendTo();

    // Set an override first, then clear it by passing an empty string
    \IbnNajjaar\DhiraaguSMSLaravel\DhiraaguSMS::alwaysSendTo('9609876543');
    \IbnNajjaar\DhiraaguSMSLaravel\DhiraaguSMS::alwaysSendTo('');

    // Fake Dhiraagu API
    \Illuminate\Support\Facades\Http::fake([
        '*' => \Illuminate\Support\Facades\Http::response([
            'transactionId'          => '319075e0-25a3-4a4b-a330-30c1dbb865fd',
            'transactionStatus'      => 'true',
            'transactionDescription' => 'Message was sent for delivery',
            'referenceNumber'        => '060806032411233232311216',
        ], 200, []),
    ]);

    $client = new \IbnNajjaar\DhiraaguSMSLaravel\DhiraaguSMS(username: 'user', password: 'pass');

    // Act: attempt to send to multiple recipients (with one invalid to be dropped)
    $resp = $client->send(\IbnNajjaar\DhiraaguSMSLaravel\DataObjects\DhiraaguSMSData::make()
        ->setRecipients('7234567,9607654321,  1234567')
        ->setMessage('Hello from test')
        ->setSource('Test'));

    // Assert: response type
    expect($resp)->toBeInstanceOf(\IbnNajjaar\DhiraaguSMSLaravel\Responses\DhiraaguResponse::class);

    // Assert: destination uses the normalized provided recipients (override cleared by empty string)
    \Illuminate\Support\Facades\Http::assertSent(function (\Illuminate\Http\Client\Request $request) {
        $data = $request->data();
        return isset($data['destination'])
            && $data['destination'] === ['9607234567', '9607654321'];
    });
});
