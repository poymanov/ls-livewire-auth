<?php

namespace App\Providers;

use Illuminate\Auth\Notifications\ResetPassword;
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

        ResetPassword::toMailUsing(function ($notifiable, $token) {
            $url = url(route('auth.password.reset', [
                'token' => $token,
                'email' => $notifiable->getEmailForPasswordReset(),
            ], false));

            return (new MailMessage)
                ->subject(Lang::get('mail.forgot.password.subject'))
                ->line(Lang::get('mail.forgot.password.button.description'))
                ->action(
                    Lang::get('mail.forgot.password.button'),
                    $url
                )
                ->line(Lang::get('mail.forgot.password.expire', ['count' => config('auth.passwords.'.config('auth.defaults.passwords').'.expire')]))
                ->line(Lang::get('mail.forgot.password.warning'));
        });
    }
}
