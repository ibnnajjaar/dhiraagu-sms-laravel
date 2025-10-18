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
        $payload = [
            'destination'      => $this->data->getRecipients(),
            'content'          => $this->data->getMessage(),
            'authorizationKey' => $this->authorization_key,
        ];

        if ($this->data->getSource()) {
            $payload['source'] = $this->data->getSource();
        }

        return $payload;
    }

    public function getMethod(): string
    {
        return strtolower($this->method);
    }
}
