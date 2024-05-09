<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;

use Filament\Facades\Filament;
use Illuminate\Support\Facades\Lang;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        //
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        VerifyEmail::toMailUsing(function (object $notifiable, string $url) {
            return (new MailMessage())
                ->view('mail.verify-email', ['user' => $notifiable, 'url' => $url]);
        });

        ResetPassword::toMailUsing(function (object $notifiable, string $token) {
            return (new MailMessage())
                ->subject(Lang::get('Reset Password Notification'))
                ->markdown('mail.reset-password', ['user' => $notifiable, 'url' => Filament::getResetPasswordUrl($token, $notifiable)]);
        });
    }
}
