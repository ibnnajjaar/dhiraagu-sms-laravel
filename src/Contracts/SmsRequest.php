<?php

namespace IbnNajjaar\DhiraaguSMSLaravel\Contracts;

interface SmsRequest
{
    public function getEndpoint(): string;
    public function getPayload(): array;
}

