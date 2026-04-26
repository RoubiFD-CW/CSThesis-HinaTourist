<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/* |-------------------------------------------------------------------------- | Web Routes |-------------------------------------------------------------------------- */

// Serve PWA manifest at root (VitePWA SW expects it at /manifest.webmanifest)
Route::get('/manifest.webmanifest', function () {
    $path = public_path('build/manifest.webmanifest');
    if (file_exists($path)) {
        return response()->file($path, [
            'Content-Type' => 'application/manifest+json',
        ]);
    }
    abort(404);
});

// Landing page
Route::get('/', function () {
    // If already logged in, go to dashboard
    if (Auth::check()) {
        return redirect(Auth::user()->is_admin ? '/admin/dashboard' : '/user/dashboard');
    }

    // For guests, show landing page
    return view('home');
});

// Destinations Page
Route::get('/destinations', function () {
    return view('destinations');
})->name('destinations');

// Homepage (after splash)
Route::get('/home', function () {
    return view('home');
});

// Visitor Pass (Public Form & QR Generation)
Route::get('/pass', [\App\Http\Controllers\PublicVisitorController::class, 'showForm'])->name('visitor.pass');

// ========================================
// Auth Routes (Guest only)
// ========================================
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);

    // Forgot Password / Reset
    Route::get('/forgot-password', [AuthController::class, 'showForgotPassword'])->name('password.request');
    Route::post('/forgot-password/send-otp', [AuthController::class, 'sendResetOtp']);
    Route::post('/forgot-password/verify-otp', [AuthController::class, 'verifyOtp']);
    Route::post('/forgot-password/reset', [AuthController::class, 'resetPassword']);
});

// ========================================
// Authenticated Routes
// ========================================
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Profile Routes (Available for both Admin and Attendant)
    Route::get('/profile', [\App\Http\Controllers\ProfileController::class, 'show'])->name('profile.show');
    Route::post('/profile', [\App\Http\Controllers\ProfileController::class, 'update'])->name('profile.update');

    // ========================================
    // Email Verification Routes
    // ========================================
    Route::get('/email/verify', function () {
        return view('auth.verify-email');
    })->name('verification.notice');

    Route::get('/email/verify/{id}/{hash}', function (\Illuminate\Foundation\Auth\EmailVerificationRequest $request) {
        $request->fulfill();
        return redirect('/user/dashboard')->with('success', 'Email verified successfully! You can now access your dashboard.');
    })->middleware(['signed'])->name('verification.verify');

    Route::post('/email/verification-notification', function (\Illuminate\Http\Request $request) {
        $request->user()->sendEmailVerificationNotification();
        return back()->with('message', 'Verification link sent!');
    })->middleware(['throttle:6,1'])->name('verification.send');

    Route::get(
        '/user/dashboard',
        function () {
            return view('dashboard.user');
        }
    )->middleware('verified')->name('user.dashboard');

    Route::get('/admin/dashboard', function () {
        $totalLogs = \App\Models\VisitorLog::count();
        return view('dashboard.index', compact('totalLogs'));
    })->middleware('is_admin')->name('admin.dashboard');

    // ── Real-time summary cards API (polled every 10s by Alpine.js) ──
    Route::get('/api/statistics/summary', function () {
        $today = now()->startOfDay();
        return response()->json([
            'today' => (int) (\App\Models\VisitorLog::where('visit_date', '>=', $today)->selectRaw('COALESCE(SUM(male_count),0) + COALESCE(SUM(female_count),0) as total')->value('total') ?? 0),
            'month' => (int) (\App\Models\VisitorLog::whereMonth('visit_date', now()->month)->whereYear('visit_date', now()->year)->selectRaw('COALESCE(SUM(male_count),0) + COALESCE(SUM(female_count),0) as total')->value('total') ?? 0),
            'tourist' => (int) (\App\Models\VisitorLog::where('visitor_type', 'Foreign Tourist')->selectRaw('COALESCE(SUM(male_count),0) + COALESCE(SUM(female_count),0) as total')->value('total') ?? 0),
            'local' => (int) (\App\Models\VisitorLog::where('visitor_type', 'Local')->selectRaw('COALESCE(SUM(male_count),0) + COALESCE(SUM(female_count),0) as total')->value('total') ?? 0),
            'total_male' => (int) \App\Models\VisitorLog::sum('male_count'),
            'total_female' => (int) \App\Models\VisitorLog::sum('female_count'),
        ]);
    });

    Route::get(
        '/admin/statistics',
        function () {
            $today = now()->startOfDay();

            // ── Summary Cards ──
            $stats = [
                'today' => \App\Models\VisitorLog::where('visit_date', '>=', $today)->selectRaw('COALESCE(SUM(male_count),0) + COALESCE(SUM(female_count),0) as total')->value('total') ?? 0,
                'month' => \App\Models\VisitorLog::whereMonth('visit_date', now()->month)
                    ->whereYear('visit_date', now()->year)->selectRaw('COALESCE(SUM(male_count),0) + COALESCE(SUM(female_count),0) as total')->value('total') ?? 0,
                'tourist' => \App\Models\VisitorLog::where('visitor_type', 'Foreign Tourist')->selectRaw('COALESCE(SUM(male_count),0) + COALESCE(SUM(female_count),0) as total')->value('total') ?? 0,
                'local' => \App\Models\VisitorLog::where('visitor_type', 'Local')->selectRaw('COALESCE(SUM(male_count),0) + COALESCE(SUM(female_count),0) as total')->value('total') ?? 0,
                'total_male' => \App\Models\VisitorLog::sum('male_count'),
                'total_female' => \App\Models\VisitorLog::sum('female_count'),
            ];

            // ── Bar Chart: Visitors per Tourist Spot ──
            $spotStats = \App\Models\VisitorLog::selectRaw('dedicated_area, (COALESCE(SUM(male_count),0) + COALESCE(SUM(female_count),0)) as total')
                ->whereNotNull('dedicated_area')
                ->groupBy('dedicated_area')
                ->orderByDesc('total')
                ->get();
            $spotLabels = $spotStats->pluck('dedicated_area')->toJson();
            $spotData = $spotStats->pluck('total')->toJson();

            // ── Line Chart: Monthly Trend (last 12 months) ──
            $monthlyRaw = \App\Models\VisitorLog::selectRaw("DATE_FORMAT(visit_date, '%Y-%m') as ym, (COALESCE(SUM(male_count),0) + COALESCE(SUM(female_count),0)) as total")
                ->where('visit_date', '>=', now()->subMonths(11)->startOfMonth())
                ->groupByRaw("DATE_FORMAT(visit_date, '%Y-%m')")
                ->orderBy('ym')
                ->pluck('total', 'ym');

            // Fill in missing months
            $trendLabels = [];
            $trendData = [];
            for ($i = 11; $i >= 0; $i--) {
                // Use startOfMonth() before subMonths() to prevent month overflow edge cases (e.g., subtracting 1 month from Mar 29 -> Feb 29 (invalid) -> Mar 1)
                $date = now()->startOfMonth()->subMonths($i);
                $key = $date->format('Y-m');
                $trendLabels[] = $date->format('M Y');
                $trendData[] = (int) ($monthlyRaw[$key] ?? 0);
            }

            // ── Doughnut: Visit Reason Breakdown ──
            $rawReasonStats = \App\Models\VisitorLog::selectRaw('visit_reason, count(*) as total')
                ->groupBy('visit_reason')
                ->pluck('total', 'visit_reason');

            $reasonStats = collect([
                'Vacation or Leisure',
                'Business',
                'Others'
            ])->mapWithKeys(fn($item) => [$item => $rawReasonStats->get($item, 0)]);

            // ── Pie/Doughnut: Origin Breakdown ──
            $rawOriginStats = \App\Models\VisitorLog::selectRaw('origin, count(*) as total')
                ->groupBy('origin')
                ->pluck('total', 'origin');

            $originStats = collect([
                'Within the province',
                'Other province',
                'Foreign country residence'
            ])->mapWithKeys(fn($item) => [$item => $rawOriginStats->get($item, 0)]);

            // ── Detailed Table: Per-Area Breakdown ──
            $areaTable = \App\Models\VisitorLog::selectRaw(
                'dedicated_area,
             (COALESCE(SUM(male_count),0) + COALESCE(SUM(female_count),0)) as total_visitors,
             SUM(male_count) as males,
             SUM(female_count) as females,
             SUM(CASE WHEN visitor_type = "Foreign Tourist" THEN (COALESCE(male_count,0) + COALESCE(female_count,0)) ELSE 0 END) as tourists,
             SUM(CASE WHEN visitor_type = "Local" THEN (COALESCE(male_count,0) + COALESCE(female_count,0)) ELSE 0 END) as locals,
             COUNT(*) as total_entries'
            )
                ->whereNotNull('dedicated_area')
                ->groupBy('dedicated_area')
                ->orderByDesc('total_visitors')
                ->get();

            return view('dashboard.statistics', compact(
                'stats',
                'spotLabels',
                'spotData',
                'trendLabels',
                'trendData',
                'reasonStats',
                'originStats',
                'areaTable'
            ));
        }
    )->middleware('is_admin')->name('admin.statistics');

    Route::get('/admin/users', [\App\Http\Controllers\Admin\SiteAttendantController::class, 'index'])
        ->middleware('is_admin')->name('admin.users.index');

    Route::post('/admin/attendants', [\App\Http\Controllers\Admin\SiteAttendantController::class, 'store'])
        ->middleware('is_admin')->name('admin.attendants.store');

    Route::post('/admin/attendants/{id}/resend', [\App\Http\Controllers\Admin\SiteAttendantController::class, 'resend'])
        ->middleware('is_admin')->name('admin.attendants.resend');

    Route::put('/admin/attendants/{id}', [\App\Http\Controllers\Admin\SiteAttendantController::class, 'update'])
        ->middleware('is_admin')->name('admin.attendants.update');

    Route::delete('/admin/attendants/{id}', [\App\Http\Controllers\Admin\SiteAttendantController::class, 'destroy'])
        ->middleware('is_admin')->name('admin.attendants.destroy');

    // Visitor Logbook
    Route::get('/logbook', [\App\Http\Controllers\VisitorLogController::class, 'page'])->name('logbook.index');
    Route::get('/api/logs', [\App\Http\Controllers\VisitorLogController::class, 'index'])->name('api.logs.index');
    Route::post('/api/logs', [\App\Http\Controllers\VisitorLogController::class, 'store'])->name('api.logs.store');

    // Export
    Route::get('/admin/export-var2p', [\App\Http\Controllers\Admin\ExportController::class, 'exportVAR2P'])
        ->middleware('is_admin')->name('admin.export.var2p');

    // Statistics – Area Breakdown API (dynamic, by date)
    Route::get('/api/statistics/month-days', function (\Illuminate\Http\Request $request) {
        $year = $request->input('year', now()->year);
        $month = $request->input('month', now()->month);

        $days = \App\Models\VisitorLog::whereYear('visit_date', $year)
            ->whereMonth('visit_date', $month)
            ->selectRaw('DAY(visit_date) as day')
            ->distinct()
            ->orderBy('day')
            ->pluck('day');

        // Latest log timestamp, Philippine Time (ASia/Manila, UTC+8)
        $lastSync = \App\Models\VisitorLog::latest('created_at')->value('created_at');

        return response()->json([
            'days' => $days,
            'last_sync' => $lastSync
                ? \Carbon\Carbon::parse($lastSync)->setTimezone('Asia/Manila')->format('g:i A')
                : null,
        ]);
    })->middleware('is_admin')->name('api.statistics.month-days');

    Route::get('/api/statistics/area-breakdown', function (\Illuminate\Http\Request $request) {
        $year = $request->input('year', now()->year);
        $month = $request->input('month', now()->month);
        $day = $request->input('day');   // null means whole-month view

        $query = \App\Models\VisitorLog::whereYear('visit_date', $year)
            ->whereMonth('visit_date', $month)
            ->whereNotNull('dedicated_area');

        if ($day) {
            $query->whereDay('visit_date', $day);
        }

        $rows = $query->selectRaw(
            'dedicated_area,
                 (COALESCE(SUM(male_count),0) + COALESCE(SUM(female_count),0)) as total_visitors,
                 SUM(male_count)    as males,
                 SUM(female_count)  as females,
                 SUM(CASE WHEN visitor_type = "Foreign Tourist" THEN (COALESCE(male_count,0)+COALESCE(female_count,0)) ELSE 0 END) as tourists,
                 SUM(CASE WHEN visitor_type = "Local" THEN (COALESCE(male_count,0)+COALESCE(female_count,0)) ELSE 0 END) as locals,
                 COUNT(*) as total_entries,
                 MAX(created_at) as spot_last_sync'
        )
            ->groupBy('dedicated_area')
            ->orderByDesc('total_visitors')
            ->get()
            ->map(function ($row) {
                // Per-spot sync time in Philippine Time (Asia/Manila, UTC+8)
                $row->spot_last_sync = $row->spot_last_sync
                    ? \Carbon\Carbon::parse($row->spot_last_sync)->setTimezone('Asia/Manila')->format('g:i A')
                    : null;
                return $row;
            });

        $lastSync = \App\Models\VisitorLog::latest('created_at')->value('created_at');

        return response()->json([
            'rows' => $rows,
            'last_sync' => $lastSync
                ? \Carbon\Carbon::parse($lastSync)->setTimezone('Asia/Manila')->format('g:i A')
                : null,
        ]);
    })->middleware('is_admin')->name('api.statistics.area-breakdown');

    // ════════════════════════════════════════════════════════════
    // SARIMA Forecast API Proxy
    // Forwards requests to FastAPI (localhost:8000) server-side
    // so the browser never hits localhost directly (fixes CORS /
    // Private Network Access errors when using ngrok or deploy).
    // ════════════════════════════════════════════════════════════
    Route::get('/api/sarima/attractions', function () {
        try {
            $apiUrl = rtrim(config('services.sarima.url', 'http://localhost:8000'), '/');
            $response = \Illuminate\Support\Facades\Http::withoutVerifying()
                ->withHeaders(['ngrok-skip-browser-warning' => '1']) //added
                ->timeout(120)
                ->get($apiUrl . '/attractions');

            return response($response->body(), $response->status())
                ->header('Content-Type', 'application/json');
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'SARIMA API unreachable: ' . $e->getMessage()
            ], 502);
        }
    })->middleware('is_admin')->name('api.sarima.attractions');

    Route::post('/api/sarima/forecast', function (\Illuminate\Http\Request $request) {
        try {
            $apiUrl = rtrim(config('services.sarima.url', 'http://localhost:8000'), '/');
            $response = \Illuminate\Support\Facades\Http::withoutVerifying()
                ->withHeaders(['ngrok-skip-browser-warning' => '1'])
                ->timeout(120)
                ->post($apiUrl . '/forecast', $request->all());

            return response($response->body(), $response->status())
                ->header('Content-Type', 'application/json');
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'SARIMA API unreachable: ' . $e->getMessage()
            ], 502);
        }
    })->middleware('is_admin')->name('api.sarima.forecast');
});