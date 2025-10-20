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
        return $this->source ?? null;
    }

    public function getRecipients(): array
    {
        $recipients = $this->recipients;

        return collect(explode(',', $recipients))
            ->map(fn ($number) => (new NormalizeNumberAction)->handle($number))
            ->unique()
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
