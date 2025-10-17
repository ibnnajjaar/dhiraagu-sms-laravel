<?php

namespace IbnNajjaar\DhiraaguSMSLaravel\Exceptions;

use Exception;

class IncorrectCredentialsException extends Exception
{
    public function __construct(
        public string $transactionId,
        public bool $transactionStatus,
        public string $transactionDescription,
        public string $referenceNumber,
        public int $statusCode = 401,
    ) {
        parent::__construct($transactionDescription, $statusCode);
    }

    public static function fromResponse($response): self
    {
        $data = $response->json();
        return new self(
            transactionId: $data['transactionId'] ?? '',
            transactionStatus: $data['transactionStatus'] === 'true',
            transactionDescription: $data['transactionDescription'] ?? 'Incorrect credentials',
            referenceNumber: $data['referenceNumber'] ?? '',
            statusCode: $response->status()
        );
    }
}
