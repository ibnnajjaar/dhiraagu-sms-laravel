<?php

namespace IbnNajjaar\DhiraaguSMSLaravel;

use Closure;
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

    /**
     * If set, all messages will be sent to these recipients instead of the provided ones.
     * This is useful in development/testing to prevent sending to real users.
     * @var array<int, string>|null
     */
    protected static ?array $always_send_to = null;

    public function __construct(
        private readonly string $username,
        private readonly string $password,
    ) {
        $this->authorization_key = base64_encode($this->username . ':' . $this->password);
    }

    /**
     * Define a global list of recipients to always send to, overriding provided recipients.
     * Accepts a comma-separated string of numbers or null/empty to clear.
     *
     * The override is only applied when $condition evaluates to true.
     * $condition may be a boolean or a Closure returning a boolean. Defaults to true.
     */
    public static function alwaysSendTo(?string $recipients, bool|Closure $condition = true): void
    {
        // Evaluate condition
        $shouldApply = is_bool($condition) ? $condition : (bool) $condition();
        if (! $shouldApply) {
            return; // no-op when condition is false
        }

        $recipients = trim((string) $recipients);
        if ($recipients === '') {
            self::$always_send_to = null;
            return;
        }

        // Normalize and store unique numbers
        $normalized = collect(explode(',', $recipients))
            ->map(fn ($n) => (new \IbnNajjaar\DhiraaguSMSLaravel\Actions\NormalizeNumberAction())->handle($n))
            ->unique()
            ->filter()
            ->values()
            ->toArray();

        self::$always_send_to = empty($normalized) ? null : $normalized;
    }

    /** Get the override recipients if set; falls back to config value if none explicitly set. */
    public static function getAlwaysSendTo(): ?array
    {
        if (is_array(self::$always_send_to)) {
            return self::$always_send_to;
        }

        $fromConfig = config('dhiraagu_sms.dev_mobile_number');
        $fromConfig = is_string($fromConfig) ? trim($fromConfig) : '';
        if ($fromConfig === '') {
            return null;
        }

        $normalized = collect(explode(',', $fromConfig))
            ->map(fn ($n) => (new \IbnNajjaar\DhiraaguSMSLaravel\Actions\NormalizeNumberAction())->handle($n))
            ->unique()
            ->filter()
            ->values()
            ->toArray();

        return empty($normalized) ? null : $normalized;
    }

    /** Clear the override recipients. */
    public static function clearAlwaysSendTo(): void
    {
        self::$always_send_to = null;
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
