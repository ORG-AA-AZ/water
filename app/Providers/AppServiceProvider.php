<?php

namespace App\Providers;

use App\Services\Sms\FakeSmsService;
use App\Services\Sms\ServiceTwilioSms;
use App\Services\Sms\SmsService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Use the real SmsService by default
        $this->app->singleton(ServiceTwilioSms::class, function ($app) {
            return new SmsService();
        });

        // In test environment, use the FakeSmsService
        if (! $this->app->environment('production')) {
            $this->app->singleton(ServiceTwilioSms::class, function ($app) {
                return new FakeSmsService();
            });
        }
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
