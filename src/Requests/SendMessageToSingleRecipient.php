<?php

namespace IbnNajjaar\DhiraaguSMSLaravel\Requests;

use IbnNajjaar\DhiraaguSMSLaravel\Contracts\SmsRequest;
use IbnNajjaar\DhiraaguSMSLaravel\DataObjects\DhiraaguSMSData;
use IbnNajjaar\DhiraaguSMSLaravel\DhiraaguSMS;

class SendMessageToSingleRecipient implements SmsRequest
{
    public string $method = 'GET';

    public function __construct(
        public DhiraaguSMSData $data,
        public string $authorization_key,
    ) {
    }

    public function getEndpoint(): string
    {
        return '/sms';
    }

    public function getPayload(): array
    {
        $override = DhiraaguSMS::getAlwaysSendTo();
        $destination = is_array($override) && !empty($override) ? $override[0] : $this->data->getRecipient();

        $payload = [
            'destination'      => $destination,
            'content'          => $this->data->getMessage(),
            'key'              => $this->authorization_key,
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
