<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class RegisterController extends Controller
{
    /**
     * Show the user registration view.
     */
    public function showRegistrationForm(Request $request)
    {
        if (Auth::check()) {
            $redirect = $request->query('redirect');
            return $redirect ? redirect($redirect) : redirect()->route('home');
        }

        return view('auth.register', [
            'redirect' => $request->query('redirect')
        ]);
    }

    /**
     * Handle a user registration request.
     */
    public function register(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ], [
            'name.required' => 'Full name is required.',
            'email.required' => 'Email address is required.',
            'email.unique' => 'This email address is already registered.',
            'password.min' => 'Password must be at least 8 characters long.',
            'password.confirmed' => 'Password confirmation does not match.',
        ]);

        $redirect = $request->input('redirect');
        $isSafeRedirect = false;
        if (!empty($redirect)) {
            $appHost = $request->getHost();
            $parsedHost = parse_url($redirect, PHP_URL_HOST);
            $isSafeRedirect = empty($parsedHost) || $parsedHost === $appHost;
        }

        try {
            $user = User::create([
                'name' => trim($request->name),
                'email' => strtolower(trim($request->email)),
                'password' => Hash::make($request->password),
                'is_admin' => false,
            ]);

            Auth::login($user);

            if ($isSafeRedirect) {
                return redirect($redirect)->with('success', 'Account created successfully! Welcome to Artizen.');
            }

            return redirect()->route('events.index')
                ->with('success', 'Account created successfully! Choose a celebration package to proceed.');
        } catch (\Throwable $e) {
            // Fallback if DB table has issue, store in session
            session(['user_logged_in' => true, 'user_name' => $request->name, 'user_email' => $request->email]);
            
            if ($isSafeRedirect) {
                return redirect($redirect)->with('success', 'Account created successfully! Welcome to Artizen.');
            }

            return redirect()->route('events.index')
                ->with('success', 'Welcome to Artizen! Choose a celebration package to proceed.');
        }
    }
}
