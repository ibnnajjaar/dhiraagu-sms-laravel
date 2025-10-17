<?php

namespace IbnNajjaar\DhiraaguSMSLaravel\DataObjects;

final readonly class DhiraaguSMSData
{
    private string $recipients;
    private string $message;

    public static function make(): DhiraaguSMSData
    {
        return new self();
    }

    public static function fromArray(array $data): self
    {
        return self::make()
            ->setRecipients(data_get($data, 'recipients'))
            ->setMessage(data_get($data, 'message'));
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

    public function getMessage(): string
    {
        return $this->message;
    }

    public function getSource(): string
    {
        return 'Test';
    }

    public function getRecipients(): array
    {
        if (app()->environment('local')) {
            $devNumber = config('dhiraagu_sms.dev_mobile_number');
            return is_array($devNumber) ? $devNumber : [$devNumber];
        }

        return collect(explode(',', $this->recipients))
            ->map('trim')
            ->filter()
            ->unique()
            ->map(fn ($number) => ltrim($number, '+'))
            ->map(fn ($number) => strlen($number) === 7 ? "960{$number}" : $number)
            ->map(fn ($number) => "+{$number}")
            ->values()
            ->toArray();
    }

}
