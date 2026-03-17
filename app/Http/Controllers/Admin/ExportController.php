<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Exports\VAR2PGenerator;

class ExportController extends Controller
{
    public function exportVAR2P(Request $request)
    {
        $year = $request->input('year', now()->year);

        $generator = new VAR2PGenerator();
        $xmlContent = $generator->generate($year);

        $filename = "VAR 2P {$year}.xlsx";

        if (ob_get_length()) {
            ob_end_clean(); // Clears any whitespace before outputting XML
        }

        return response($xmlContent)
            ->header('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet')
            ->header('Content-Disposition', "attachment; filename=\"{$filename}\"")
            ->header('Pragma', 'public')
            ->header('Cache-Control', 'max-age=0');
    }
}
