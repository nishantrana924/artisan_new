<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    /**
     * Show the user login view.
     */
    /**
     * Show the user login view.
     */
    public function showLoginForm(Request $request)
    {
        if (Auth::check()) {
            $user = Auth::user();
            if (method_exists($user, 'isAdmin') && $user->isAdmin()) {
                return redirect()->route('admin.dashboard');
            }
            $redirect = $request->query('redirect');
            return $redirect ? redirect($redirect) : redirect()->route('home');
        }

        return view('auth.login', [
            'redirect' => $request->query('redirect')
        ]);
    }

    /**
     * Get throttle key for the given request.
     */
    protected function throttleKey(Request $request): string
    {
        return \Illuminate\Support\Str::transliterate(
            \Illuminate\Support\Str::lower($request->input('email', '')) . '|' . $request->ip()
        );
    }

    /**
     * Handle user login authentication with 3-attempt progressive rate limiting.
     * Supports both customer and administrator sign-in from the single login page.
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ], [
            'email.required' => 'Email address is required.',
            'password.required' => 'Password is required.',
        ]);

        $credentials['email'] = strtolower(trim($credentials['email']));

        $throttleKey = $this->throttleKey($request);

        // 1. Check if user is currently locked out
        if (\Illuminate\Support\Facades\RateLimiter::tooManyAttempts($throttleKey, 3)) {
            $seconds = \Illuminate\Support\Facades\RateLimiter::availableIn($throttleKey);
            $minutes = ceil($seconds / 60);
            $lockoutMsg = "Too many failed login attempts. For security, your account is locked. Please try again after {$minutes} " . ($minutes == 1 ? 'minute' : 'minutes') . ".";

            return back()->withErrors([
                'lockout' => $lockoutMsg,
                'email' => $lockoutMsg,
            ])->with('lockout_seconds', $seconds)
              ->with('lockout_minutes', $minutes)
              ->onlyInput('email');
        }

        $remember = $request->boolean('remember');

        // 2. Attempt authentication
        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();

            // Clear throttle on successful login
            \Illuminate\Support\Facades\RateLimiter::clear($throttleKey);
            \Illuminate\Support\Facades\Cache::forget('login_lockout_count_' . $throttleKey);

            $user = Auth::user();

            // Check if user is an Administrator
            $isAdmin = $user && $user->isAdmin();

            if ($isAdmin) {
                session([
                    'admin_logged_in' => true,
                    'admin_email'     => $user->email,
                    'admin_name'      => $user->name,
                    'admin_user_id'   => $user->id,
                ]);

                $redirect = $request->input('redirect');
                if (!empty($redirect)) {
                    $appHost = $request->getHost();
                    $parsedHost = parse_url($redirect, PHP_URL_HOST);
                    $isSameHost = empty($parsedHost) || $parsedHost === $appHost;
                    if ($isSameHost && str_contains($redirect, '/admin')) {
                        return redirect($redirect)->with('success', 'Welcome to Artizen Admin Panel.');
                    }
                }

                return redirect()->intended(route('admin.dashboard'))
                    ->with('success', 'Welcome to Artizen Admin Panel.');
            }

            // Customer user redirect
            $redirect = $request->input('redirect');
            if (!empty($redirect)) {
                $appHost = $request->getHost();
                $parsedHost = parse_url($redirect, PHP_URL_HOST);
                $isSameHost = empty($parsedHost) || $parsedHost === $appHost;
                if ($isSameHost) {
                    return redirect($redirect);
                }
            }

            return redirect()->intended(route('home'));
        }

        // 3. Authentication failed: record hit with progressive decay (5m first, 10m thereafter)
        $lockoutCountKey = 'login_lockout_count_' . $throttleKey;
        $lockoutCount = (int)\Illuminate\Support\Facades\Cache::get($lockoutCountKey, 0);
        $decaySeconds = ($lockoutCount === 0) ? 300 : 600; // 5 minutes on first lockout, 10 minutes on subsequent

        \Illuminate\Support\Facades\RateLimiter::hit($throttleKey, $decaySeconds);

        $currentAttempts = \Illuminate\Support\Facades\RateLimiter::attempts($throttleKey);

        if ($currentAttempts >= 3) {
            \Illuminate\Support\Facades\Cache::put($lockoutCountKey, $lockoutCount + 1, 86400);
            $seconds = \Illuminate\Support\Facades\RateLimiter::availableIn($throttleKey);
            $minutes = ceil($seconds / 60);
            $lockoutMsg = "Too many failed login attempts (3 incorrect tries). For security, your account is locked. Please try again after {$minutes} " . ($minutes == 1 ? 'minute' : 'minutes') . ".";

            return back()->withErrors([
                'lockout' => $lockoutMsg,
                'email' => $lockoutMsg,
            ])->with('lockout_seconds', $seconds)
              ->with('lockout_minutes', $minutes)
              ->onlyInput('email');
        }

        $remaining = 3 - $currentAttempts;
        $errorMsg = "The provided credentials do not match our records. ({$remaining} " . ($remaining === 1 ? 'attempt' : 'attempts') . " remaining before lockout).";

        return back()->withErrors([
            'email' => $errorMsg,
        ])->onlyInput('email');
    }

    /**
     * Log the user out of the application and redirect to the home page.
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->forget(['admin_logged_in', 'admin_email', 'admin_name', 'admin_user_id']);
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home')->with('success', 'You have been logged out successfully.');
    }
}
