<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Models\User;

class AdminAuthController extends Controller
{
    /**
     * Show the admin login view (redirects to unified login page).
     */
    public function showLoginForm(Request $request)
    {
        if (Auth::check() && Auth::user()->isAdmin()) {
            return redirect()->route('admin.dashboard');
        }

        return redirect()->route('login', $request->query());
    }

    /**
     * Handle an admin login request.
     */
    public function login(Request $request)
    {
        // 1. Strict Form Request Validation
        $credentials = $request->validate([
            'email' => ['required', 'string', 'email', 'max:255'],
            'password' => ['required', 'string'],
        ], [
            'email.required' => 'Email address is required.',
            'email.email' => 'Please enter a valid email address.',
            'password.required' => 'Password is required.',
        ]);

        // Trim/Sanitize email input
        $credentials['email'] = strtolower(trim($credentials['email']));

        // 2. Direct Admin User Check (Verifies real hashed password)
        $adminUser = User::where('email', $credentials['email'])->first();
        if ($adminUser && $adminUser->isAdmin()) {
            if (Hash::check($credentials['password'], $adminUser->password)) {
                Auth::login($adminUser, $request->boolean('remember'));
                session(['admin_logged_in' => true, 'admin_email' => $adminUser->email]);
                $request->session()->regenerate();

                return redirect()->intended(route('admin.dashboard'))
                    ->with('success', 'Welcome back to Artizen Admin Panel.');
            }

            return back()->withErrors([
                'email' => 'Invalid credentials.',
            ])->onlyInput('email');
        }

        $remember = $request->boolean('remember');

        // 3. Attempt Standard Eloquent Authentication
        if (Auth::attempt(['email' => $credentials['email'], 'password' => $credentials['password']], $remember)) {
            $user = Auth::user();

            if ($user && $user->isAdmin()) {
                session(['admin_logged_in' => true, 'admin_email' => $user->email]);
                $request->session()->regenerate();

                return redirect()->intended(route('admin.dashboard'))
                    ->with('success', 'Welcome back to Artizen Admin Panel.');
            }

            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return back()->withErrors([
                'email' => 'Invalid credentials.',
            ])->onlyInput('email');
        }

        // Generic error message
        return back()->withErrors([
            'email' => 'Invalid credentials.',
        ])->onlyInput('email');
    }

    /**
     * Show admin forgot password form.
     */
    public function showForgotPasswordForm()
    {
        if (Auth::check() && Auth::user()->isAdmin()) {
            return redirect()->route('admin.settings');
        }

        return view('admin.auth.forgot-password');
    }

    /**
     * Send public password reset OTP to an administrator's email.
     */
    public function sendResetOtp(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
        ], [
            'email.required' => 'Admin email address is required.',
            'email.email'    => 'Please enter a valid email address.',
        ]);

        $email = strtolower(trim($request->email));

        // Rapid dispatch cooldown: prevent sending OTP more than once every 60 seconds per email
        $cooldownKey = 'admin_otp_send_cooldown_' . md5($email);
        if (\Illuminate\Support\Facades\Cache::has($cooldownKey)) {
            $msg = 'Please wait 60 seconds before requesting another verification code.';
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['status' => 'error', 'message' => $msg], 429);
            }
            return back()->withErrors(['email' => $msg]);
        }

        $admin = User::where('email', $email)->first();

        if ($admin && $admin->isAdmin()) {
            $otp = (string) random_int(100000, 999999);

            session([
                'admin_public_reset_otp'        => $otp,
                'admin_public_reset_expires_at' => now()->addMinutes(10)->timestamp,
                'admin_public_reset_email'      => $admin->email,
            ]);

            \Illuminate\Support\Facades\Cache::put($cooldownKey, true, 60);

            try {
                Mail::raw(
                    "Hello {$admin->name},\n\nYour Artizen administrator password reset verification code (OTP) is: {$otp}\n\nThis verification code is valid for 10 minutes.\n\nIf you did not request this verification code, please ignore this email.\n\nArtizen Platform Security",
                    function ($message) use ($admin) {
                        $message->to($admin->email)
                            ->subject("Artizen Admin: Password Reset Code");
                    }
                );
            } catch (\Throwable $e) {
                Log::error('Admin Public Reset OTP mail delivery error: ' . $e->getMessage());
            }

            Log::info("Admin Password Reset OTP generated and dispatched for {$admin->email}.");
        }

        $message = "If an administrator account exists for {$email}, a 6-digit verification code has been dispatched.";

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'status'  => 'success',
                'message' => $message,
                'email'   => $email,
            ]);
        }

        return back()->with('success', $message)->with('otp_sent', true)->with('reset_email', $email);
    }

    /**
     * Verify public OTP before setting new password.
     */
    public function verifyResetOtp(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
            'otp'   => ['required', 'string', 'size:6'],
        ], [
            'email.required' => 'Admin email address is required.',
            'otp.required'   => 'The 6-digit OTP code is required.',
            'otp.size'       => 'The OTP code must be exactly 6 digits.',
        ]);

        $email       = strtolower(trim($request->email));
        $storedOtp   = session('admin_public_reset_otp');
        $expiresAt   = session('admin_public_reset_expires_at');
        $storedEmail = session('admin_public_reset_email');

        // Check verification brute-force attempt counter (max 5 failed attempts)
        $failsKey = 'admin_otp_verify_fails_' . md5($email);
        $failedAttempts = (int) \Illuminate\Support\Facades\Cache::get($failsKey, 0);

        if ($failedAttempts >= 5) {
            session()->forget([
                'admin_public_reset_otp',
                'admin_public_reset_expires_at',
                'admin_public_reset_email',
            ]);
            return response()->json([
                'status'  => 'error',
                'message' => 'Too many failed verification attempts. This verification code has been invalidated. Please request a new code.',
            ], 429);
        }

        if (!$storedOtp || !$expiresAt || now()->timestamp > $expiresAt || $storedEmail !== $email) {
            return response()->json([
                'status'  => 'error',
                'message' => 'The verification code has expired or was not requested. Please request a new code.',
            ], 422);
        }

        if (!hash_equals((string) $storedOtp, trim($request->otp))) {
            $failedAttempts++;
            \Illuminate\Support\Facades\Cache::put($failsKey, $failedAttempts, 600); // 10 minutes
            if ($failedAttempts >= 5) {
                session()->forget([
                    'admin_public_reset_otp',
                    'admin_public_reset_expires_at',
                    'admin_public_reset_email',
                ]);
                return response()->json([
                    'status'  => 'error',
                    'message' => 'Too many failed verification attempts. This verification code has been invalidated. Please request a new code.',
                ], 429);
            }
            $remaining = 5 - $failedAttempts;
            return response()->json([
                'status'  => 'error',
                'message' => "Incorrect verification code. ({$remaining} " . ($remaining === 1 ? 'attempt' : 'attempts') . " remaining).",
            ], 422);
        }

        \Illuminate\Support\Facades\Cache::forget($failsKey);
        session(['admin_public_reset_verified' => true]);

        return response()->json([
            'status'  => 'success',
            'message' => 'OTP verified successfully! You can now set your new password.',
        ]);
    }

    /**
     * Handle admin password reset request with mandatory OTP verification.
     */
    public function handleForgotPassword(Request $request)
    {
        $request->validate([
            'email'    => ['required', 'email'],
            'otp'      => ['required', 'string', 'size:6'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ], [
            'email.required'     => 'Admin email address is required.',
            'otp.required'       => 'Verification code (OTP) is required.',
            'otp.size'           => 'Verification code must be exactly 6 digits.',
            'password.required'  => 'New password is required.',
            'password.min'       => 'Password must be at least 8 characters.',
            'password.confirmed' => 'Password confirmation does not match.',
        ]);

        $email       = strtolower(trim($request->email));
        $storedOtp   = session('admin_public_reset_otp');
        $expiresAt   = session('admin_public_reset_expires_at');
        $storedEmail = session('admin_public_reset_email');

        // Verify that OTP exists and was issued for this email
        if (!$storedOtp || !$expiresAt || now()->timestamp > $expiresAt || $storedEmail !== $email) {
            return back()->withErrors([
                'otp' => 'The verification code has expired or is invalid. Please request a new code.',
            ])->onlyInput('email');
        }

        // Constant-time comparison
        if (!hash_equals((string) $storedOtp, trim($request->otp))) {
            return back()->withErrors([
                'otp' => 'Incorrect verification code. Please check your email and try again.',
            ])->onlyInput('email');
        }

        $admin = User::where('email', $email)->first();

        if (!$admin || !$admin->isAdmin()) {
            return back()->withErrors([
                'email' => 'No administrator account found with this email address.',
            ])->onlyInput('email');
        }

        // Update password securely
        $admin->password = Hash::make($request->password);
        $admin->save();

        // Invalidate OTP immediately upon successful reset
        session()->forget([
            'admin_public_reset_otp',
            'admin_public_reset_expires_at',
            'admin_public_reset_email',
            'admin_public_reset_verified',
        ]);

        return redirect()->route('login')->with('success', 'Admin password has been reset successfully! Please sign in with your new password.');
    }

    /**
     * Log the admin user out of the application and redirect to the home page.
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->forget(['admin_logged_in', 'admin_email', 'admin_name', 'admin_user_id']);
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home')
            ->with('success', 'You have been logged out successfully.');
    }
}
