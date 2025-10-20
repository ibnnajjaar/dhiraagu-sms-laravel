<?php

namespace IbnNajjaar\DhiraaguSMSLaravel;

use Illuminate\Support\ServiceProvider;

class DhiraaguSMSLaravelServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/dhiraagu_sms.php',
            'dhiraagu_sms'
        );

        // Optionally set a global override destination from config for development
        \IbnNajjaar\DhiraaguSMSLaravel\DhiraaguSMS::alwaysSendTo(config('dhiraagu_sms.dev_mobile_number'));

        $this->app->singleton(DhiraaguSMS::class, function () {
            $username = config('dhiraagu_sms.username');
            $password = config('dhiraagu_sms.password');

            if (empty($username) || empty($password)) {
                throw new \InvalidArgumentException('Dhiraagu SMS username and password are required.');
            }

            return new DhiraaguSMS(
                username: $username,
                password: $password,
            );
        });
    }

    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/dhiraagu_sms.php' => config_path('dhiraagu_sms.php'),
            ], 'dhiraagu_sms');
        }
    }
}
