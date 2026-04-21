<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

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

        // Ensure HTTPS on Ngrok or Forwarded Proxies
        if (request()->hasHeader('X-Forwarded-Proto') && request()->header('X-Forwarded-Proto') === 'https') {
            \Illuminate\Support\Facades\URL::forceScheme('https');
        } else if (str_contains($host, 'ngrok')) {
            \Illuminate\Support\Facades\URL::forceScheme('https');
        }

        // Fix for "Messed Up Design" over Ngrok or Local Network:
        // Automatically bypass the Vite dev server (HMR loopback) if the user is NOT on localhost.
        $isLocalhost = in_array($host, ['localhost', '127.0.0.1', '::1']);
        if (!$isLocalhost || str_contains($host, 'ngrok')) {
            \Illuminate\Support\Facades\Vite::useHotFile(storage_path('vite.hot.ignore'));
        }
    }
}
