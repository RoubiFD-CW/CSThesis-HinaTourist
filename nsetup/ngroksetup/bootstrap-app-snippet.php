<?php

/**
 * NGROK SETUP - bootstrap/app.php modifications
 * 
 * Add the import at the top of your bootstrap/app.php:
 */

use Illuminate\Http\Request;

/**
 * Then modify your withMiddleware section to include TrustProxies:
 */

// Example complete bootstrap/app.php:

/*
<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Trust all proxies (for ngrok and other tunneling services)
        $middleware->trustProxies(at: '*');
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
*/

/**
 * KEY CHANGES:
 * 1. Add: use Illuminate\Http\Request;
 * 2. Add inside withMiddleware: $middleware->trustProxies(at: '*');
 */
