<?php

use IbnNajjaar\DhiraaguSMSLaravel\Exceptions\DhiraaguRequestException;

it('concatenates message and detail for 422 errors', function () {
    $data = ['message' => 'Validation Failed', 'detail' => 'The provided content is invalid.'];
    expect(DhiraaguRequestException::getErrorMessage($data))
        ->toBe('Validation Failed. The provided content is invalid.');
});

it('handles missing message or detail fields', function () {
    expect(DhiraaguRequestException::getErrorMessage([]))
        ->toBe('. ');
});
