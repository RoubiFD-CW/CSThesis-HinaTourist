<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    public function show()
    {
        return view('dashboard.profile', ['user' => Auth::user()]);
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        $rules = [
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:8',
        ];

        // If it's a site attendant, enforce @gmail.com validation
        if (!$user->is_admin) {
            $rules['email'] .= '|regex:/^.+@gmail\.com$/i';
        }

        $messages = [
            'email.regex' => 'The email must be a valid @gmail.com address.'
        ];

        $request->validate($rules, $messages);

        $user->email = $request->email;

        // If email changed and is not admin, we may want to force re-verification, 
        // but for now we just update it.
        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        if ($request->filled('password')) {
            $user->password = $request->password; // Auto-hashed by model
        }

        $user->save();

        if ($user->wasChanged('email') && !$user->is_admin) {
            $user->sendEmailVerificationNotification();
            return redirect()->back()->with('success', 'Profile updated. A verification link has been sent to your new email address.');
        }

        return redirect()->back()->with('success', 'Profile updated successfully.');
    }
}
