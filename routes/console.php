<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

use Illuminate\Support\Facades\Schedule;
use App\Models\User;
use Carbon\Carbon;

// Purge Pending Users > 30 days
Schedule::call(function () {
    $deletedCount = User::whereNull('email_verified_at')
        ->where('is_admin', false)
        ->where('created_at', '<', Carbon::now()->subDays(30))
        ->delete();
        
    if ($deletedCount > 0) {
        \Illuminate\Support\Facades\Log::info("Purged {$deletedCount} pending attendant accounts older than 30 days.");
    }
})->daily();
