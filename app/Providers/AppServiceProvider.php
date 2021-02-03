<?php

namespace App\Providers;

use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        VerifyEmail::toMailUsing(function ($notifiable, $verificationUrl) {
            return (new MailMessage)
                ->subject(Lang::get('mail.verification.email.subject'))
                ->line(Lang::get('mail.verification.email.button.description'))
                ->action(
                    Lang::get('mail.verification.email.button'),
                    $verificationUrl
                )
                ->line(Lang::get('mail.verification.email.warning'));
        });
    }
}
