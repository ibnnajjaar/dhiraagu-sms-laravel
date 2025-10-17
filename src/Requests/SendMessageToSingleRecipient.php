<?php

namespace IbnNajjaar\DhiraaguSMSLaravel\Requests;

use IbnNajjaar\DhiraaguSMSLaravel\Contracts\SmsRequest;
use IbnNajjaar\DhiraaguSMSLaravel\DataObjects\DhiraaguSMSData;

class SendMessageToSingleRecipient implements SmsRequest
{
    public string $method = 'GET';

    public function __construct(
        public string $recipient,
        public string $message,
        public ?string $source = null,
    )
    {
    }

    public function getEndpoint(): string
    {
        return '/sms';
    }

    public function getPayload(): array
    {
        return [
            'destination'      => $this->recipient,
            'content'          => $this->message,
            'source'           => $this->source,
        ];
    }

    public function getMethod(): string
    {
        return strtolower($this->method);
    }
}
