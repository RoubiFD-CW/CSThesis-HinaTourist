<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    /**
     * Show login form
     */
    public function showLogin()
    {
        return view('auth.login');
    }

    /**
     * Handle login
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($request->only('email', 'password'), $request->boolean('remember'))) {
            $request->session()->regenerate();

            $user = Auth::user();

            // Redirect based on role
            if ($user->is_admin ?? false) {
                return redirect()->intended('/admin/dashboard');
            }

            return redirect()->intended('/user/dashboard');
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    // Registration methods removed as users are now created by admin

    /**
     * Logout
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }

    // ========================================
    // Force Password Change
    // ========================================

    public function showForcePasswordChange()
    {
        return view('auth.force-change-password');
    }

    public function forcePasswordUpdate(Request $request)
    {
        $request->validate([
            'password' => [
                'required',
                'confirmed',
                'min:8',
                'regex:/[A-Z]/',
                'regex:/[0-9]/',
                'regex:/[^A-Za-z0-9]/',
            ],
        ], [
            'password.regex' => 'Password must contain at least one uppercase letter, one number, and one special character.',
        ]);

        $user = auth()->user();
        $user->password = $request->password;
        $user->must_change_password = false;
        $user->save();

        return redirect()->route('user.dashboard')->with('success', 'Password successfully updated.');
    }

    // ========================================
    // Forgot Password / Reset Password
    // ========================================

    /**
     * Show forgot password form
     */
    public function showForgotPassword()
    {
        return view('auth.forgot-password');
    }

    /**
     * Send OTP for password reset
     */
    public function sendResetOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $email = $request->email;

        // Check if user exists
        $user = User::where('email', $email)->first();
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'No account found with this email address.',
            ], 422);
        }

        // Generate 6-digit OTP
        $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        // Store in session
        $request->session()->put('password_reset_otp', [
            'code' => $otp,
            'email' => $email,
            'expires_at' => now()->addMinutes(10),
        ]);

        // Send OTP via email
        try {
            Mail::send('emails.reset-otp', ['otp' => $otp], function ($message) use ($email) {
                $message->to($email)
                    ->subject('HinaTourist - Password Reset Code');
            });

            Log::info("Password reset OTP sent to {$email}: {$otp}");

            return response()->json([
                'success' => true,
                'message' => 'Verification code sent to your email.',
            ]);
        } catch (\Exception $e) {
            Log::error("Failed to send password reset OTP to {$email}: " . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to send verification code. Please try again.',
            ], 500);
        }
    }

    /**
     * Verify OTP (Step 2 of reset)
     */
    public function verifyOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'otp' => 'required|string|size:6',
        ]);

        $stored = $request->session()->get('password_reset_otp');

        if (!$stored || $stored['email'] !== $request->email) {
            return response()->json(['success' => false, 'message' => 'Invalid or expired verification code.'], 422);
        }

        if (now()->greaterThan($stored['expires_at'])) {
            $request->session()->forget('password_reset_otp');
            return response()->json(['success' => false, 'message' => 'Verification code has expired.'], 422);
        }

        if ($stored['code'] !== $request->otp) {
            return response()->json(['success' => false, 'message' => 'Invalid verification code.'], 422);
        }

        // Tag the session as 'otp_verified' so we can allow password reset in next step
        $stored['verified'] = true;
        $request->session()->put('password_reset_otp', $stored);

        return response()->json(['success' => true, 'message' => 'Code verified! Please set your new password.']);
    }

    /**
     * Reset password with OTP verification
     */
    public function resetPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'otp' => 'required|string|size:6',
            'password' => [
                'required',
                'confirmed',
                'min:8',
                'regex:/[A-Z]/',
                'regex:/[0-9]/',
                'regex:/[^A-Za-z0-9]/',
            ],
        ], [
            'password.regex' => 'Password must contain at least one uppercase letter, one number, and one special character.',
        ]);

        $stored = $request->session()->get('password_reset_otp');

        if (!$stored) {
            return response()->json([
                'success' => false,
                'message' => 'No verification code found. Please request a new one.',
            ], 422);
        }

        // Check if verified
        if (empty($stored['verified']) || !$stored['verified']) {
            return response()->json([
                'success' => false,
                'message' => 'Verification required. Please verify your code first.',
            ], 422);
        }

        // Check email
        if ($stored['email'] !== $request->email) {
            return response()->json([
                'success' => false,
                'message' => 'Email mismatch. Please request a new code.',
            ], 422);
        }

        // Check expiry
        if (now()->greaterThan($stored['expires_at'])) {
            $request->session()->forget('password_reset_otp');
            return response()->json([
                'success' => false,
                'message' => 'Verification code has expired. Please request a new one.',
            ], 422);
        }

        // Check code
        if ($stored['code'] !== $request->otp) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid verification code.',
            ], 422);
        }

        // Update password
        $user = User::where('email', $request->email)->first();
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found.',
            ], 422);
        }

        $user->password = $request->password; // Auto-hashed via User model cast
        $user->save();

        // Clean up session
        $request->session()->forget('password_reset_otp');

        Log::info("Password reset successfully for {$request->email}");

        return response()->json([
            'success' => true,
            'message' => 'Password reset successfully! Redirecting to login...',
        ]);
    }
}
