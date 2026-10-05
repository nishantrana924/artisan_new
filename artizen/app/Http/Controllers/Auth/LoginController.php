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
    public function showLoginForm(Request $request)
    {
        if (Auth::check()) {
            $redirect = $request->query('redirect');
            return $redirect ? redirect($redirect) : redirect()->route('home');
        }

        return view('auth.login', [
            'redirect' => $request->query('redirect')
        ]);
    }

    /**
     * Handle user login authentication.
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

        $remember = $request->boolean('remember');

        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();

            $redirect = $request->input('redirect');
            if (!empty($redirect) && filter_var($redirect, FILTER_VALIDATE_URL) === false && str_starts_with($redirect, '/')) {
                return redirect($redirect);
            }

            return redirect()->intended(route('home'));
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    /**
     * Log the user out of the application.
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }
}
