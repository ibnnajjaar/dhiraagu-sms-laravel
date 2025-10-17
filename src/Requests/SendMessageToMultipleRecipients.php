<?php

namespace IbnNajjaar\DhiraaguSMSLaravel\Requests;

use IbnNajjaar\DhiraaguSMSLaravel\Contracts\SmsRequest;
use Dhiraagu\DhiraaguSMS\DataObjects\DhiraaguSMSData;

class SendMessageToMultipleRecipients implements SmsRequest
{

    public function __construct(
        public DhiraaguSMSData $data
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
        ];
    }
}
