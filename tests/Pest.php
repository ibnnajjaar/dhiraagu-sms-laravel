<?php

use Illuminate\Support\Facades\Http;
use Orchestra\Testbench\TestCase as OrchestraTestCase;

// Use Orchestra Testbench for a lightweight Laravel application in tests
uses(OrchestraTestCase::class)->in('Feature', 'Unit');

// Globally mock the Dhiraagu API as per docs/api-guide.md
beforeEach(function () {
    // Ensure no real network calls are made
    Http::preventStrayRequests();

    Http::fake(['*' => function ($request) {
        $url = $request->url();
        $method = strtoupper($request->method());

        $base = 'https://messaging.dhiraagu.com.mv/v1/api';

        // Helper: success payload from docs
        $success = [
            'transactionId' => 'e3f94753-8a4c-4349-9d76-680ae9da2880',
            'transactionStatus' => 'true',
            'transactionDescription' => 'Message was sent for delivery',
            'referenceNumber' => '060806032411233232311216',
        ];

        // Helper: 401 payload from docs
        $unauthorized = [
            'transactionId' => 'e3f94753-8a4c-4349-9d76-321ae9da2880',
            'transactionStatus' => 'false',
            'transactionDescription' => 'Incorrect credentials',
            'referenceNumber' => '',
        ];

        // Helper: 4xx/5xx payload from docs
        $genericError = [
            'transactionId' => 'e3f94753-8a4c-4349-9d76-680ae9da2880',
            'transactionStatus' => 'false',
            'transactionDescription' => 'Failed to send message',
            'referenceNumber' => '',
        ];

        if ($method === 'POST') {
            $body = (string) $request->body();
            $json = json_decode($body, true) ?: [];

            // If auth key corresponds to base64('bad:bad') => unauthorized
            if (($json['authorizationKey'] ?? '') === base64_encode('bad:bad')) {
                return Http::response($unauthorized, 401);
            }

            // Simulate a 422 validation error when content is 'invalid'
            if (($json['content'] ?? '') === 'invalid') {
                return Http::response([
                    'message' => 'Validation Failed',
                    'detail' => 'The provided content is invalid.',
                ], 422);
            }

            // Success by default
            return Http::response($success, 200);
        }

        if ($method === 'GET') {
            // Parse query string
            $parts = parse_url($url);
            parse_str($parts['query'] ?? '', $query);

            if (($query['key'] ?? '') === base64_encode('bad:bad')) {
                return Http::response($unauthorized, 401);
            }

            return Http::response($success, 200);
        }

        return Http::response($genericError, 500);
    }]);
});
