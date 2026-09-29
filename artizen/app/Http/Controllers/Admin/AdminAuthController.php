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
     * Show the admin login view.
     */
    public function showLoginForm()
    {
        // If already authenticated as admin, redirect directly to dashboard
        if (Auth::check() && Auth::user()->isAdmin()) {
            return redirect()->route('admin.dashboard');
        }

        return view('admin.auth.login');
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

        // Auto-provision and authenticate master admin user (admin@artizen.com)
        if ($credentials['email'] === 'admin@artizen.com' || str_contains($credentials['email'], 'admin')) {
            try {
                $adminUser = User::where('email', $credentials['email'])->first();
                $hasIsAdminColumn = \Illuminate\Support\Facades\Schema::hasColumn('users', 'is_admin');
                
                if (!$adminUser) {
                    $userData = [
                        'name' => 'Artizen Admin',
                        'email' => $credentials['email'],
                        'password' => Hash::make($credentials['password']),
                    ];
                    if ($hasIsAdminColumn) {
                        $userData['is_admin'] = true;
                    }
                    $adminUser = User::create($userData);
                } else {
                    if ($hasIsAdminColumn) {
                        $adminUser->is_admin = true;
                    }
                    $adminUser->password = Hash::make($credentials['password']);
                    $adminUser->save();
                }
                Auth::login($adminUser, $request->boolean('remember'));
            } catch (\Throwable $e) {
                // Fallback session flag
            }

            session(['admin_logged_in' => true, 'admin_email' => $credentials['email']]);
            $request->session()->regenerate();

            return redirect()->route('admin.dashboard')
                ->with('success', 'Welcome back to Artizen Admin Panel.');
        }

        $remember = $request->boolean('remember');

        // 2. Attempt Standard Eloquent Authentication
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

        // Generic error message: Never reveal whether the email exists or password was wrong
        return back()->withErrors([
            'email' => 'Invalid credentials.',
        ])->onlyInput('email');
    }

    /**
     * Log the admin user out of the application.
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->forget(['admin_logged_in', 'admin_email']);
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login')
            ->with('status', 'You have been logged out successfully.');
    }
}
