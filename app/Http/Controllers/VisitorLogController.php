<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class VisitorLogController extends Controller
{
    public function page()
    {
        return view('dashboard.logbook');
    }

    public function index(Request $request)
    {
        $user = auth()->user();
        $query = \App\Models\VisitorLog::latest();

        try {
            if (!$user->is_admin) {
                // If user has a dedicated area, filter by it. 
                // Or filter by attendant_id if you want them to see only their own inputs.
                // Given the context of "Site Attendant", filtering by Area is safer if multiple attendants work there.
                if ($user->dedicated_area) {
                    $query->where('dedicated_area', $user->dedicated_area);
                } else {
                    $query->where('attendant_id', $user->id);
                }
            }

            return response()->json($query->paginate(50));
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('VisitorLog Index Error: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to fetch logs: ' . $e->getMessage()], 500);
        }
    }

    public function store(Request $request)
    {
        $request->validate([
            'visitor_type'       => 'required|string|in:Local,Foreign Tourist',
            'group_size'         => 'required|integer|min:1|max:1000',
            'male_count'         => 'required|integer|min:0',
            'female_count'       => 'required|integer|min:0',
            'origin'             => 'required|string|in:Within the province,Other province,Foreign country residence',
            'visit_reason'       => 'required|string|in:Vacation or Leisure,Business,Others',
            'visit_reason_other' => 'nullable|string|max:255',
            'dedicated_area'     => 'nullable|string|max:255',
            'visit_date'         => 'required|date',
        ]);

        if ((int)$request->group_size !== ((int)$request->male_count + (int)$request->female_count)) {
            return response()->json(['message' => 'Data inconsistency: Group size must equal the exact sum of male and female counts.'], 422);
        }

        try {
            $user = auth()->user();
            if (!$user->is_admin && !empty($user->dedicated_area)) {
                if (strtolower(trim($request->dedicated_area)) !== strtolower(trim($user->dedicated_area))) {
                    return response()->json(['message' => 'Unauthorized: This QR pass does not belong to your assigned location.'], 403);
                }
            }

            // Prevent exact duplicates created rapidly via double-scans or double-taps
            $recentLog = \App\Models\VisitorLog::where('attendant_id', $user->id)
                ->where('visitor_type', $request->visitor_type)
                ->where('group_size', $request->group_size)
                ->where('male_count', $request->male_count)
                ->where('female_count', $request->female_count)
                ->where('origin', $request->origin)
                ->where('dedicated_area', $request->dedicated_area)
                ->where('created_at', '>=', now()->subSeconds(30))
                ->first();

            if ($recentLog) {
                // Silently accept duplicate payload by returning the old log info, avoiding frontend errors while maintaining data integrity
                return response()->json(['success' => true, 'log' => $recentLog]);
            }

            // Only pass whitelisted fields — prevents JS meta-fields (local_id, pending, syncing) from leaking into the model
            $log = \App\Models\VisitorLog::create([
                'visitor_type'       => $request->visitor_type,
                'group_size'         => $request->group_size,
                'male_count'         => $request->male_count,
                'female_count'       => $request->female_count,
                'origin'             => $request->origin,
                'visit_reason'       => $request->visit_reason,
                'visit_reason_other' => $request->visit_reason === 'Others' ? $request->visit_reason_other : null,
                'dedicated_area'     => $request->dedicated_area,
                'visit_date'         => $request->visit_date,
                'attendant_id'       => $user->id,
            ]);

            return response()->json(['success' => true, 'log' => $log]);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('VisitorLog Store Error: ' . $e->getMessage());
            return response()->json(['message' => 'Failed to save log: ' . $e->getMessage()], 500);
        }
    }
}
