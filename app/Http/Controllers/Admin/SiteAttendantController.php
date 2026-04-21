<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SiteAttendantController extends Controller
{
    public function index(Request $request)
    {
        $query = \App\Models\User::where('is_admin', false);

        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $attendants = $query->latest()->paginate(5);
        return view('dashboard.admin', compact('attendants'));
    }

    public function store(Request $request)
    {
        $validAreas = [
            'Enchanted River', 'Hinatuan Adventure Park', 'Lodestone Shores Resort',
            'Baculin Amazing Sand Bar', 'Harip Oceanside Beach', 'Rock Island Resort',
            'Mamaon Beach Resort', 'Amparitas Integrated Nature Farm', 'Sibadan Fish Cage and Resort',
            'Landong Bay', 'Davince Hidden Paradise', 'Tarusan Cold Spring',
            'Llamas Beach Resort', 'Puro Brigida’s Beach', 'Bunsadan Falls',
        ];

        $request->validate([
            'name'           => 'required|string|max:255',
            'email'          => 'required|string|email|max:255|unique:users',
            'password'       => 'required|string|min:8',
            'dedicated_area' => 'required|string|in:' . implode(',', $validAreas),
        ]);

        // The User model's password cast auto-hashes on assignment — pass plain text only
        \App\Models\User::create([
            'name'           => $request->name,
            'email'          => $request->email,
            'password'       => $request->password,
            'dedicated_area' => $request->dedicated_area,
            'is_admin'       => false,
        ]);

        return redirect()->back()->with('success', 'Site Attendant created successfully.');
    }

    public function destroy($id)
    {
        $user = \App\Models\User::findOrFail($id);
        if ($user->is_admin) {
            return redirect()->back()->with('error', 'Cannot delete admin accounts.');
        }
        $user->delete();

        return redirect()->back()->with('success', 'Site Attendant deleted successfully.');
    }
}
