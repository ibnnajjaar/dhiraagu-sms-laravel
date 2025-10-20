<?php

namespace IbnNajjaar\DhiraaguSMSLaravel\Requests;

use IbnNajjaar\DhiraaguSMSLaravel\Contracts\SmsRequest;
use IbnNajjaar\DhiraaguSMSLaravel\DataObjects\DhiraaguSMSData;
use IbnNajjaar\DhiraaguSMSLaravel\DhiraaguSMS;

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
        $destination = DhiraaguSMS::getAlwaysSendTo() ?? $this->data->getRecipients();

        $payload = [
            'destination'      => $destination,
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
