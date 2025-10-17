<?php

namespace IbnNajjaar\DhiraaguSMSLaravel\Exceptions;

use Exception;

class RequestException extends Exception
{
    public function __construct(
        public string $text,
        public int $errorCode,
    ) {
        $details = $this->parseErrorDetails($this->text);
        $message = $this->getErrorMessage($details);
        parent::__construct($message, $errorCode);
    }

    public static function fromResponse($response): self
    {
        return new self(
            text: $response->getMessage(),
            errorCode: $response->getCode()
        );
    }

    public function parseErrorDetails(string $message): array
    {
        $details = [];
        if (preg_match('/\{.*\}/s', $message, $matches)) {
            $jsonPart = $matches[0];

            // Step 2: Decode JSON into an associative array
            $data = json_decode($jsonPart, true);

            // Step 3: Access the values safely
            if (json_last_error() === JSON_ERROR_NONE) {
                $details['error']  =  $data['error'] ?? null;
                $details['message']  = $data['message'] ?? null;
                $details['detail']  = $data['detail'] ?? null;
            }

        }

        return $details;
    }

    public function getErrorMessage(array $details): string
    {
        $message = $details['message'] ?? '';
        $error = $details['detail'] ?? '';

        return $message . '! ' . $error;
    }
}
