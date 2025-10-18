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

        // if number is 10-digit and does not starts with 960, remove the number
        if ($length === 10 && ! str_starts_with($number, '960')) {
            return '';
        }

        // if number is 10-digit and starts with 960, remove the 960
        if ($length === 10 && str_starts_with($number, '960')) {
            $number = substr($number, 3);
        }

        // Remove any number that does not start with 7 or 9
        if (!preg_match('/^7|^9/', $number)) {
            return '';
        }

        // Convert 7-digit local number to international format
        return "960{$number}";
    }
}
