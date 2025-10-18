<?php

namespace IbnNajjaar\DhiraaguSMSLaravel\Actions;

class NormalizeNumberAction
{

    public function handle(string $number): string
    {
        // Remove whitespace and the leading '+' if present
        $number = ltrim(trim($number), '+');

        // Only accept 7-digit local or 10-digit international numbers
        $length = strlen($number);
        if ($length !== 7 && $length !== 10) {
            return '';
        }

        // If 10 digits, validate that it starts with Maldives country code (960)
        if ($length === 10) {
            return str_starts_with($number, '960') ? $number : '';
        }

        // Convert 7-digit local number to international format
        return "960{$number}";
    }
}
