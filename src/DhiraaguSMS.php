<?php

namespace IbnNajjaar\DhiraaguSMSLaravel;

use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\Response;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\ConnectionException;
use IbnNajjaar\DhiraaguSMSLaravel\Contracts\SmsRequest;
use Dhiraagu\DhiraaguSMS\DataObjects\DhiraaguSMSData;
use IbnNajjaar\DhiraaguSMSLaravel\Requests\SendMessageToMultipleRecipients;

class DhiraaguSMS
{
    private string $base_url = 'https://messaging.dhiraagu.com.mv/v1/api';
    private string $authorization_key;

    public function __construct(
        private readonly string $username,
        private readonly string $password,
    )
    {
        $this->authorization_key = base64_encode($this->username . ':' . $this->password);
    }

    /**
     * @throws ConnectionException
     */
    public function send(DhiraaguSMSData $data): Response
    {
        return $this->sendRequest(new SendMessageToMultipleRecipients($data));
    }

    public function sendToSingleRecipient(string $recipient, string $message)
    {

    }

    /**
     * @throws ConnectionException
     */
    public function sendRequest(SmsRequest $sms_request): Response
    {
        return $this->getHttpClient()->post(
            $sms_request->getEndpoint(),
            array_merge(
                $sms_request->getPayload(),
                [
                    'authorizationKey' => $this->getAuthorizationKey()
                ],
            ),
        );
    }

    protected function getHttpClient(): PendingRequest
    {
        return Http::baseUrl($this->base_url)
                   ->acceptJson()
                   ->timeout(30)
                   ->retry(3);
    }

    protected function getAuthorizationKey(): string
    {
        return $this->authorization_key;
    }
}
