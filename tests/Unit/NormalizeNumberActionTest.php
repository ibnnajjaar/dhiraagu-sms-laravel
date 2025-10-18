<?php

use IbnNajjaar\DhiraaguSMSLaravel\Actions\NormalizeNumberAction;

it('normalizes local 7-digit numbers to Maldives international format', function () {
    $action = new NormalizeNumberAction();
    expect($action->handle('1234567'))->toBe('9601234567');
});

it('keeps 10-digit Maldives numbers as is', function () {
    $action = new NormalizeNumberAction();
    expect($action->handle('9607654321'))->toBe('9607654321');
});

it('strips plus and spaces and handles correctly', function () {
    $action = new NormalizeNumberAction();
    expect($action->handle(' +9601234567 '))->toBe('9601234567');
});

it('filters out invalid lengths and non-Maldives numbers', function () {
    $action = new NormalizeNumberAction();
    expect($action->handle('123'))
        ->toBe('')
        ->and($action->handle('9711234567')) // Non-960 prefix
        ->toBe('');
});
