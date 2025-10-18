<?php

namespace IbnNajjaar\DhiraaguSMSLaravel\DataObjects;

use IbnNajjaar\DhiraaguSMSLaravel\Actions\NormalizeNumberAction;

final readonly class DhiraaguSMSData
{
    private string $recipients;
    private string $message;
    private ?string $source;

    public static function make(): DhiraaguSMSData
    {
        return new self();
    }

    public static function fromArray(array $data): self
    {
        return self::make()
            ->setRecipients(data_get($data, 'recipients'))
            ->setMessage(data_get($data, 'message'))
            ->setSource(data_get($data, 'source'));
    }

    public function setRecipients(string $recipients): self
    {
        $this->recipients = $recipients;
        return $this;
    }

    public function setMessage(string $message): self
    {
        $this->message = $message;
        return $this;
    }

    public function setSource(?string $source = null): self
    {
        $this->source = $source;
        return $this;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function getSource(): ?string
    {
        return $this->source;
    }

    public function getRecipients(): array
    {
        if (app()->environment('local')) {
            $devNumber = config('dhiraagu_sms.dev_mobile_number');
            return is_array($devNumber) ? $devNumber : [$devNumber];
        }

        return collect(explode(',', $this->recipients))
            ->unique()
            ->map(fn ($number) => (new NormalizeNumberAction)->handle($number))
            ->filter()
            ->values()
            ->toArray();
    }

    public function getRecipient(): ?string
    {
        $recipients = $this->getRecipients();
        return !empty($recipients) ? $recipients[0] : null;
    }
}
