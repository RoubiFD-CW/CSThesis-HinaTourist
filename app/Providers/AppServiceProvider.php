<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Notifications\Messages\MailMessage;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        \Illuminate\Support\Facades\Schema::defaultStringLength(191);

        if (str_starts_with(config('app.url'), 'https://')) {
            \Illuminate\Support\Facades\URL::forceScheme('https');
        }

        $host = request()->getHost();

        // Force the Root URL from .env (Ngrok) if configured
        if (str_contains(config('app.url'), 'ngrok-free.dev')) {
            \Illuminate\Support\Facades\URL::forceRootUrl(config('app.url'));
            \Illuminate\Support\Facades\URL::forceScheme('https');
        }

        // Ensure HTTPS on Ngrok or Forwarded Proxies
        if (request()->hasHeader('X-Forwarded-Proto') && request()->header('X-Forwarded-Proto') === 'https') {
            \Illuminate\Support\Facades\URL::forceScheme('https');
        }

        // Fix for "Messed Up Design" over Ngrok or Local Network:
        // Automatically bypass the Vite dev server (HMR loopback) if the user is NOT on localhost.
        $isLocalhost = in_array($host, ['localhost', '127.0.0.1', '::1']);
        if (!$isLocalhost || str_contains($host, 'ngrok')) {
            \Illuminate\Support\Facades\Vite::useHotFile(storage_path('vite.hot.ignore'));
        }

        VerifyEmail::toMailUsing(function (object $notifiable, string $url) {
            return (new MailMessage)
                ->subject('HinaTourist - Account Verification & Login Credentials')
                ->view('emails.verify-account', [
                    'url' => $url,
                    'email' => $notifiable->email,
                    'area' => $notifiable->dedicated_area
                ]);
        });
    }
}
