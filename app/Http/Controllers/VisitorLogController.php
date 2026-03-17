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

            return response()->json($query->limit(50)->get());
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('VisitorLog Index Error: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to fetch logs: ' . $e->getMessage()], 500);
        }
    }

    public function store(Request $request)
    {
        $request->validate([
            'visitor_type' => 'required|string|max:255',
            'group_size' => 'required|integer|min:1',
            'male_count' => 'required|integer|min:0',
            'female_count' => 'required|integer|min:0',
            'origin' => 'required|string|max:255',
            'visit_reason' => 'required|string|max:255',
            'visit_reason_other' => 'nullable|string|max:255',
            'dedicated_area' => 'required|string',
            'visit_date' => 'required|date',
        ]);

        try {
            $user = auth()->user();
            if (!$user->is_admin && !empty($user->dedicated_area)) {
                if (strtolower(trim($request->dedicated_area)) !== strtolower(trim($user->dedicated_area))) {
                    return response()->json(['error' => 'Unauthorized: This QR pass does not belong to your assigned location.'], 403);
                }
            }

            $log = \App\Models\VisitorLog::create($request->all() + [
                'attendant_id' => $user->id,
            ]);

            return response()->json(['success' => true, 'log' => $log]);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('VisitorLog Store Error: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to save log: ' . $e->getMessage()], 500);
        }
    }
}
