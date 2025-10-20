<?php

use Illuminate\Support\Facades\Http;
use Orchestra\Testbench\TestCase as OrchestraTestCase;
use IbnNajjaar\DhiraaguSMSLaravel\DhiraaguSMSLaravelServiceProvider;

// Use Orchestra Testbench for a lightweight Laravel application in tests
uses(OrchestraTestCase::class)->in('Feature', 'Unit');

// Globally mock the Dhiraagu API as per docs/api-guide.md
function loadServiceProvider(): void
{
    app()->register(DhiraaguSMSLaravelServiceProvider::class);
}
