<?php

namespace IbnNajjaar\DhiraaguSMSLaravel;

class DhiraaguSMS
{

    public function __construct(
        private string $username,
        private string $password,
    )
    {
    }


}
