<?php

namespace IbnNajjaar\DhiraaguSMSLaravel\Responses;

class DhiraaguResponse
{

    public function __construct(
        public string $transactionId,
        public string $transactionStatus,
        public string $transactionDescription,
        public string $referenceNumber,
        public int $statusCode,
    )
    {
    }

    public static function fromResponse($response): self
    {
        $data = $response->json();
        return new self(
            transactionId: $data['transactionId'] ?? '',
            transactionStatus: $data['transactionStatus'] ?? '',
            transactionDescription: $data['transactionDescription'] ?? '',
            referenceNumber: $data['referenceNumber'] ?? '',
            statusCode: $response->status()
        );
    }


}
