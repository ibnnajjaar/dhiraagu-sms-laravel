<?php

namespace IbnNajjaar\DhiraaguSMSLaravel;

use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\ConnectionException;
use IbnNajjaar\DhiraaguSMSLaravel\Contracts\SmsRequest;
use IbnNajjaar\DhiraaguSMSLaravel\Responses\DhiraaguResponse;
use IbnNajjaar\DhiraaguSMSLaravel\DataObjects\DhiraaguSMSData;
use IbnNajjaar\DhiraaguSMSLaravel\Exceptions\RequestException;
use IbnNajjaar\DhiraaguSMSLaravel\Exceptions\TransactionException;
use IbnNajjaar\DhiraaguSMSLaravel\Requests\SendMessageToSingleRecipient;
use IbnNajjaar\DhiraaguSMSLaravel\Exceptions\IncorrectCredentialsException;
use IbnNajjaar\DhiraaguSMSLaravel\Requests\SendMessageToMultipleRecipients;

class DhiraaguSMS
{
    private string $base_url = 'https://messaging.dhiraagu.com.mv/v1/api';
    private string $authorization_key;

    public function __construct(
        private readonly string $username,
        private readonly string $password,
    ) {
        $this->authorization_key = base64_encode($this->username . ':' . $this->password);
    }

    /**
     * @throws ConnectionException
     * @throws TransactionException
     * @throws IncorrectCredentialsException
     */
    public function send(DhiraaguSMSData $data): DhiraaguResponse
    {
        return $this->sendRequest(
            new SendMessageToMultipleRecipients(
                data: $data,
                authorization_key: $this->getAuthorizationKey()
            )
        );
    }

    /**
     * @throws ConnectionException
     * @throws IncorrectCredentialsException
     * @throws TransactionException
     */
    public function sendToSingleRecipient(string $recipient, string $message, ?string $source = null): DhiraaguResponse
    {
        return $this->sendRequest(
            new SendMessageToSingleRecipient(
                recipient: $recipient,
                message: $message,
                source: $source,
                authorization_key: $this->getAuthorizationKey()
            )
        );
    }

    /**
     * @throws ConnectionException
     * @throws TransactionException
     * @throws IncorrectCredentialsException
     * @throws RequestException
     */
    public function sendRequest(SmsRequest $sms_request): DhiraaguResponse
    {
        $client = $this->getHttpClient();
        $method = $sms_request->getMethod();

        try {
            $response = $client->$method(
                $sms_request->getEndpoint(),
                $sms_request->getPayload(),
            );
        } catch (\Exception $exception) {
            throw RequestException::fromResponse($exception);
        }

        if ($response->failed() && $response->status() === 401) {
            throw TransactionException::fromResponse($response);
        }

        if ($response->failed()) {
            throw IncorrectCredentialsException::fromResponse($response);
        }

        return DhiraaguResponse::fromResponse($response);
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
