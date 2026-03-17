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

// Splash Screen (landing page)
Route::get('/', function () {
    // If already logged in, skip splash and go to dashboard
    if (Auth::check()) {
        return redirect(Auth::user()->is_admin ? '/admin/dashboard' : '/user/dashboard');
    }

    return view('splash');
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
Route::get('/pass', [\App\Http\Controllers\PublicVisitorController::class , 'showForm'])->name('visitor.pass');

// ========================================
// Auth Routes (Guest only)
// ========================================
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class , 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class , 'login']);

    // Forgot Password / Reset
    Route::get('/forgot-password', [AuthController::class , 'showForgotPassword'])->name('password.request');
    Route::post('/forgot-password/send-otp', [AuthController::class , 'sendResetOtp']);
    Route::post('/forgot-password/reset', [AuthController::class , 'resetPassword']);
});

// ========================================
// Authenticated Routes
// ========================================
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class , 'logout'])->name('logout');

    Route::get('/user/dashboard', function () {
            return view('dashboard.user');
        }
        )->name('user.dashboard');

    Route::get('/admin/dashboard', function () {
        $totalLogs = \App\Models\VisitorLog::count();
        return view('dashboard.index', compact('totalLogs'));
    })->middleware('is_admin')->name('admin.dashboard');

        Route::get('/admin/statistics', function () {
            $today = now()->startOfDay();

            // ── Summary Cards ──
            $stats = [
                'today' => \App\Models\VisitorLog::where('visit_date', '>=', $today)->selectRaw('COALESCE(SUM(male_count),0) + COALESCE(SUM(female_count),0) as total')->value('total') ?? 0,
                'month' => \App\Models\VisitorLog::whereMonth('visit_date', now()->month)
                ->whereYear('visit_date', now()->year)->selectRaw('COALESCE(SUM(male_count),0) + COALESCE(SUM(female_count),0) as total')->value('total') ?? 0,
                'tourist' => \App\Models\VisitorLog::whereIn('visitor_type', ['Tourist', 'Foreign Tourist'])->selectRaw('COALESCE(SUM(male_count),0) + COALESCE(SUM(female_count),0) as total')->value('total') ?? 0,
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
                $key = now()->subMonths($i)->format('Y-m');
                $trendLabels[] = now()->subMonths($i)->format('M Y');
                $trendData[] = (int)($monthlyRaw[$key] ?? 0);
            }

            // ── Doughnut: Visit Reason Breakdown ──
            $reasonStats = \App\Models\VisitorLog::selectRaw('visit_reason, count(*) as total')
                ->groupBy('visit_reason')
                ->orderByDesc('total')
                ->pluck('total', 'visit_reason');

            // ── Pie/Doughnut: Origin Breakdown ──
            $originStats = \App\Models\VisitorLog::selectRaw('origin, count(*) as total')
                ->groupBy('origin')
                ->orderByDesc('total')
                ->pluck('total', 'origin');

            // ── Detailed Table: Per-Area Breakdown ──
            $areaTable = \App\Models\VisitorLog::selectRaw(
                'dedicated_area,
             (COALESCE(SUM(male_count),0) + COALESCE(SUM(female_count),0)) as total_visitors,
             SUM(male_count) as males,
             SUM(female_count) as females,
             SUM(CASE WHEN visitor_type IN ("Tourist", "Foreign Tourist") THEN (COALESCE(male_count,0) + COALESCE(female_count,0)) ELSE 0 END) as tourists,
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

        Route::get('/admin/users', [\App\Http\Controllers\Admin\SiteAttendantController::class , 'index'])
            ->middleware('is_admin')->name('admin.users.index');

        Route::post('/admin/attendants', [\App\Http\Controllers\Admin\SiteAttendantController::class , 'store'])
            ->middleware('is_admin')->name('admin.attendants.store');

        Route::delete('/admin/attendants/{id}', [\App\Http\Controllers\Admin\SiteAttendantController::class , 'destroy'])
            ->middleware('is_admin')->name('admin.attendants.destroy');

        // Visitor Logbook
        Route::get('/logbook', [\App\Http\Controllers\VisitorLogController::class , 'page'])->name('logbook.index');
        Route::get('/api/logs', [\App\Http\Controllers\VisitorLogController::class , 'index'])->name('api.logs.index');
        Route::post('/api/logs', [\App\Http\Controllers\VisitorLogController::class , 'store'])->name('api.logs.store');
        
        // Export
        Route::get('/admin/export-var2p', [\App\Http\Controllers\Admin\ExportController::class, 'exportVAR2P'])
            ->middleware('is_admin')->name('admin.export.var2p');

        // Statistics – Area Breakdown API (dynamic, by date)
        Route::get('/api/statistics/month-days', function (\Illuminate\Http\Request $request) {
            $year  = $request->input('year',  now()->year);
            $month = $request->input('month', now()->month);

            $days = \App\Models\VisitorLog::whereYear('visit_date',  $year)
                ->whereMonth('visit_date', $month)
                ->selectRaw('DAY(visit_date) as day')
                ->distinct()
                ->orderBy('day')
                ->pluck('day');

            // Latest log timestamp, Philippine Time (ASia/Manila, UTC+8)
            $lastSync = \App\Models\VisitorLog::latest('created_at')->value('created_at');

            return response()->json([
                'days'      => $days,
                'last_sync' => $lastSync
                    ? \Carbon\Carbon::parse($lastSync)->setTimezone('Asia/Manila')->format('g:i A')
                    : null,
            ]);
        })->middleware('is_admin')->name('api.statistics.month-days');

        Route::get('/api/statistics/area-breakdown', function (\Illuminate\Http\Request $request) {
            $year  = $request->input('year',  now()->year);
            $month = $request->input('month', now()->month);
            $day   = $request->input('day');   // null means whole-month view

            $query = \App\Models\VisitorLog::whereYear('visit_date',  $year)
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
                 SUM(CASE WHEN visitor_type IN ("Tourist","Foreign Tourist") THEN (COALESCE(male_count,0)+COALESCE(female_count,0)) ELSE 0 END) as tourists,
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
                'rows'      => $rows,
                'last_sync' => $lastSync
                    ? \Carbon\Carbon::parse($lastSync)->setTimezone('Asia/Manila')->format('g:i A')
                    : null,
            ]);
        })->middleware('is_admin')->name('api.statistics.area-breakdown');
    });