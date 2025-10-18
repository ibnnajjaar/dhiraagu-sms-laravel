<?php

namespace IbnNajjaar\DhiraaguSMSLaravel;

use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\Client\ConnectionException;
use IbnNajjaar\DhiraaguSMSLaravel\Contracts\SmsRequest;
use IbnNajjaar\DhiraaguSMSLaravel\Support\SendsRequest;
use IbnNajjaar\DhiraaguSMSLaravel\Responses\DhiraaguResponse;
use IbnNajjaar\DhiraaguSMSLaravel\DataObjects\DhiraaguSMSData;
use IbnNajjaar\DhiraaguSMSLaravel\Exceptions\DhiraaguRequestException;
use IbnNajjaar\DhiraaguSMSLaravel\Exceptions\TransactionException;
use IbnNajjaar\DhiraaguSMSLaravel\Requests\SendMessageToSingleRecipient;
use IbnNajjaar\DhiraaguSMSLaravel\Exceptions\IncorrectCredentialsException;
use IbnNajjaar\DhiraaguSMSLaravel\Requests\SendMessageToMultipleRecipients;

class DhiraaguSMS
{
    use SendsRequest;

    private string $base_url = 'https://messaging.dhiraagu.com.mv/v1/api';
    private string $authorization_key {
        get {
            return $this->authorization_key;
        }
    }

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
     * @throws DhiraaguRequestException
     */
    public function send(DhiraaguSMSData $data): DhiraaguResponse
    {
        return $this->sendRequest(
            new SendMessageToMultipleRecipients(
                data: $data,
                authorization_key: $this->authorization_key
            )
        );
    }

    /**
     * @throws ConnectionException
     * @throws IncorrectCredentialsException
     * @throws TransactionException
     * @throws DhiraaguRequestException
     */
    public function sendToSingleRecipient(DhiraaguSMSData $data): DhiraaguResponse
    {
        return $this->sendRequest(
            new SendMessageToSingleRecipient(
                data: $data,
                authorization_key: $this->authorization_key
            )
        );
    }

}
