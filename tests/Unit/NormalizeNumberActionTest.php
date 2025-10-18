<?php

use IbnNajjaar\DhiraaguSMSLaravel\Actions\NormalizeNumberAction;

it('removes whitespace from recipients', function () {
    $action = new NormalizeNumberAction();
    expect($action->handle(' 9607123456'))
        ->toEqual('9607123456');
});

it('removes plus signs from recipients', function () {
    $action = new NormalizeNumberAction();
    expect($action->handle(' +9607123456'))
        ->toEqual('9607123456');
});

it('removes numbers that are not 7 or 10 digits from recipients', function () {
    $action = new NormalizeNumberAction();
    expect($action->handle(' 333222'))
        ->toEqual('')
        ->and($action->handle('664957123456'))
        ->toEqual('')
        ->and($action->handle(' 7123457'))
        ->toEqual('9607123457')
        ->and($action->handle(' 9607123456'))
        ->toEqual('9607123456');
});

it('removes numbers that are 10 digits and does not have maldivian code from recipients', function () {
    $action = new NormalizeNumberAction();
    expect($action->handle('9997454543'))
        ->toEqual('');
});

it('removes any number that does not start with 7 or 9', function () {
    $action = new NormalizeNumberAction();
    expect($action->handle('9608123456'))
        ->toEqual('')
        ->and($action->handle('5123456'))
        ->toEqual('')
        ->and($action->handle('7123456'))
        ->toEqual('9607123456')
        ->and($action->handle('9123456'))
        ->toEqual('9609123456');
});

it('adds maldivian code to recipients', function () {
    $action = new NormalizeNumberAction();
    expect($action->handle('7123456'))
        ->toEqual('9607123456');
});

