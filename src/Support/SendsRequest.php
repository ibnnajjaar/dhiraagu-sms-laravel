<?php

namespace IbnNajjaar\DhiraaguSMSLaravel\Support;

use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\ConnectionException;
use IbnNajjaar\DhiraaguSMSLaravel\Contracts\SmsRequest;
use IbnNajjaar\DhiraaguSMSLaravel\Responses\DhiraaguResponse;
use IbnNajjaar\DhiraaguSMSLaravel\Exceptions\TransactionException;
use IbnNajjaar\DhiraaguSMSLaravel\Exceptions\DhiraaguRequestException;
use IbnNajjaar\DhiraaguSMSLaravel\Exceptions\IncorrectCredentialsException;

trait SendsRequest
{

    /**
     * @throws ConnectionException
     * @throws TransactionException
     * @throws IncorrectCredentialsException
     * @throws DhiraaguRequestException
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
        } catch (Exception $exception) {
            $response = $exception->response;

            if ($response->failed() && $response->status() === 401) {
                throw IncorrectCredentialsException::fromResponse($response);
            }

            if ($response->failed() && $response->status() === 422) {
                throw DhiraaguRequestException::fromResponse($response);
            }

            if ($response->failed()) {
                throw TransactionException::fromResponse($response);
            }
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
}
