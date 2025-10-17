<?php

namespace IbnNajjaar\DhiraaguSMSLaravel\Requests;

use IbnNajjaar\DhiraaguSMSLaravel\Contracts\SmsRequest;
use IbnNajjaar\DhiraaguSMSLaravel\DataObjects\DhiraaguSMSData;

class SendMessageToMultipleRecipients implements SmsRequest
{
    public string $method = 'POST';

    public function __construct(
        public DhiraaguSMSData $data,
        public string          $authorization_key,
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
            'destination'      => $this->data->getRecipients(),
            'content'          => $this->data->getMessage(),
            'source'           => $this->data->getSource(),
            'authorizationKey' => $this->authorization_key,
        ];
    }

    public function getMethod(): string
    {
        return strtolower($this->method);
    }
}
