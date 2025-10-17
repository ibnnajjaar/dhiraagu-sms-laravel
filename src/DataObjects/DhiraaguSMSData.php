<?php

namespace Dhiraagu\DhiraaguSMS\DataObjects;

readonly final class DhiraaguSMSData
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

    public function getRecipients(): string|array
    {
        if (app()->environment('local')) {
            return config('services.dhiraagu_sms.dev_mobile_number');
        }

        $number_array = explode(',', $this->recipients);
        $number_array = array_map('trim', $number_array);
        $number_array = array_filter($number_array);
        $number_array = array_unique($number_array);

        // if the number starts with + remove +
        $number_array = collect($number_array)->each(function (&$number) {
            if (str_starts_with($number, '+')) {
                $number = substr($number, 1);
            }
        });

        // if number does not start with 960 and has 7 digits, prepend 960
        $number_array = $number_array->each(function (&$number) {
            if (! str_starts_with($number, '960') && strlen($number) == 7) {
                $number = '960' . $number;
            }
        });

        // now prepend all numbers starting with 960 with +
        $number_array = $number_array->map(function ($number) {
            return '+' . $number;
        })->toArray();

        // If total number of numbers is 1, return as string, otherwise return array
        if (count($number_array) === 1) {
            return $number_array[0];
        }

        return $number_array;
    }


}
