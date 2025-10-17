<?php

namespace IbnNajjaar\DhiraaguSMSLaravel;

use Illuminate\Support\ServiceProvider;

class DhiraaguSMSLaravelServiceProvider extends ServiceProvider
{

    public function register(): void
    {
        $this->app->singleton(DhiraaguSms::class, function ($app) {
            return new DhiraaguSms(
                config('dhiraagu_sms.username'),
                config('dhiraagu_sms.password'),
            );
        });
    }

    public function boot(): void
    {
        $this->publishes([
            __DIR__.'/../config/dhiraagu_sms.php' => config_path('dhiraagu_sms.php'),
        ], 'dhiraagu-sms');
    }
}
