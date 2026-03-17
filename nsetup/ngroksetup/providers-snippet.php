<?php

/**
 * NGROK SETUP - bootstrap/providers.php
 * 
 * Add NgrokServiceProvider to your providers array:
 */

return [
    App\Providers\AppServiceProvider::class,
    App\Providers\NgrokServiceProvider::class,  // <-- Add this line
];
