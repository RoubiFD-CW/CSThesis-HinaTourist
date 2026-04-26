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
            'Enchanted River',
            'Hinatuan Adventure Park',
            'Lodestone Shores Resort',
            'Baculin Amazing Sand Bar',
            'Harip Oceanside Beach',
            'Rock Island Resort',
            'Mamaon Beach Resort',
            'Amparitas Integrated Nature Farm',
            'Sibadan Fish Cage and Resort',
            'Landong Bay',
            'Davince Hidden Paradise',
            'Tarusan Cold Spring',
            'Llamas Beach Resort',
            'Puro Brigida’s Beach',
            'Bunsadan Falls',
        ];

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users', 'regex:/^.+@gmail\.com$/i'],
            'dedicated_area' => 'required|string|in:' . implode(',', $validAreas),
        ], [
            'email.regex' => 'The email must be a valid @gmail.com address.'
        ]);

        $user = \App\Models\User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => 'hinatourist',
            'dedicated_area' => $request->dedicated_area,
            'is_admin' => false,
        ]);

        $user->sendEmailVerificationNotification();

        return redirect()->back()->with('account_created', 'Account Successfully Created! A verification link has been sent to the Gmail provided. The account will remain Pending until verified.');
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

    public function resend($id)
    {
        $user = \App\Models\User::findOrFail($id);

        if ($user->hasVerifiedEmail()) {
            return redirect()->back()->with('error', 'This account is already verified.');
        }

        $user->sendEmailVerificationNotification();

        return redirect()->back()->with('success', 'Verification email resent to ' . $user->email);
    }

    public function update(Request $request, $id)
    {
        $user = \App\Models\User::findOrFail($id);

        if ($user->is_admin) {
            return redirect()->back()->with('error', 'Cannot edit admin accounts.');
        }

        $validAreas = [
            'Enchanted River',
            'Hinatuan Adventure Park',
            'Lodestone Shores Resort',
            'Baculin Amazing Sand Bar',
            'Harip Oceanside Beach',
            'Rock Island Resort',
            'Mamaon Beach Resort',
            'Amparitas Integrated Nature Farm',
            'Sibadan Fish Cage and Resort',
            'Landong Bay',
            'Davince Hidden Paradise',
            'Tarusan Cold Spring',
            'Llamas Beach Resort',
            'Puro Brigida’s Beach',
            'Bunsadan Falls',
        ];

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $id, 'regex:/^.+@gmail\.com$/i'],
            'dedicated_area' => 'required|string|in:' . implode(',', $validAreas),
        ], [
            'email.regex' => 'The email must be a valid @gmail.com address.'
        ]);

        $emailChanged = $user->email !== $request->email;

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'dedicated_area' => $request->dedicated_area,
        ]);

        if ($emailChanged) {
            $user->email_verified_at = null;
            $user->save();
            $user->sendEmailVerificationNotification();
            return redirect()->back()->with('success', 'Site Attendant updated. Email changed, so a new verification link was sent.');
        }

        return redirect()->back()->with('success', 'Site Attendant updated successfully.');
    }
}
