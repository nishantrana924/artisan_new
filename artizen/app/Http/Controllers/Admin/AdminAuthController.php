<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
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

        // Auto-provision only if master admin (admin@artizen.com) does not exist yet
        if ($credentials['email'] === 'admin@artizen.com' && !$adminUser) {
            try {
                $hasIsAdminColumn = \Illuminate\Support\Facades\Schema::hasColumn('users', 'is_admin');
                $userData = [
                    'name' => 'Artizen Admin',
                    'email' => $credentials['email'],
                    'password' => Hash::make($credentials['password']),
                ];
                if ($hasIsAdminColumn) {
                    $userData['is_admin'] = true;
                }
                $adminUser = User::create($userData);
                Auth::login($adminUser, $request->boolean('remember'));
                session(['admin_logged_in' => true, 'admin_email' => $credentials['email']]);
                $request->session()->regenerate();

                return redirect()->route('admin.dashboard')
                    ->with('success', 'Welcome back to Artizen Admin Panel.');
            } catch (\Throwable $e) {
                // Continue to standard Auth::attempt
            }
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
     * Handle admin password reset request.
     */
    public function handleForgotPassword(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ], [
            'email.required' => 'Admin email address is required.',
            'password.required' => 'New password is required.',
            'password.min' => 'Password must be at least 8 characters.',
            'password.confirmed' => 'Password confirmation does not match.',
        ]);

        $email = strtolower(trim($request->email));
        $admin = User::where('email', $email)->first();

        if (!$admin || !$admin->isAdmin()) {
            return back()->withErrors([
                'email' => 'No administrator account found with this email address.',
            ])->onlyInput('email');
        }

        $admin->password = Hash::make($request->password);
        $admin->save();

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
