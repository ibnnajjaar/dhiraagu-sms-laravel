<?php

namespace IbnNajjaar\DhiraaguSMSLaravel\Exceptions;

use Exception;

class DhiraaguRequestException extends Exception
{
    public function __construct(
        public string $text,
        public int $statusCode,
    ) {
        parent::__construct($text, $statusCode);
    }

    public static function fromResponse($response): self
    {
        return new self(
            text: self::getErrorMessage($response->json()),
            statusCode: $response->status()
        );
    }

    public static function getErrorMessage(array $data): string
    {
        $message = $data['message'] ?? '';
        $detail = $data['detail'] ?? '';
        return $message . '. ' . $detail;
    }
}
