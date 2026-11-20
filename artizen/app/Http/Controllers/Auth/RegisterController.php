<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\VerifyEmailOtpMail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rules\Password;

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
     * Send email verification OTP code.
     */
    public function sendVerificationOtp(User $user): string
    {
        $otp = (string) random_int(100000, 999999);
        $cacheKey = 'email_verify_otp_' . md5($user->email);
        $cooldownKey = 'email_verify_cooldown_' . md5($user->email);

        Cache::put($cacheKey, [
            'otp' => $otp,
            'user_id' => $user->id,
            'expires_at' => now()->addMinutes(10)->timestamp,
            'attempts' => 0,
        ], now()->addMinutes(10));

        Cache::put($cooldownKey, now()->addSeconds(60)->timestamp, 60);

        Log::info("Email verification OTP generated for [{$user->email}]: {$otp}");

        try {
            Mail::to($user->email)->send(new VerifyEmailOtpMail($user, $otp));
        } catch (\Throwable $e) {
            Log::error("Failed sending verification email to {$user->email}: " . $e->getMessage());
        }

        return $otp;
    }

    /**
     * Handle a user registration request with strong password policy & email OTP verification.
     */
    public function register(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                'unique:users,email',
                function ($attribute, $value, $fail) {
                    $normalized = strtolower(trim((string) $value));
                    $reservedPrefixes = ['admin@', 'administrator@', 'superadmin@', 'root@'];
                    $isReserved = str_ends_with($normalized, '@artizen.com');

                    if (! $isReserved) {
                        foreach ($reservedPrefixes as $prefix) {
                            if (str_starts_with($normalized, $prefix)) {
                                $isReserved = true;
                                break;
                            }
                        }
                    }

                    if ($isReserved) {
                        $fail('The selected email address is reserved and cannot be registered.');
                    }
                },
            ],
            'password' => [
                'required',
                'string',
                'min:8',
                'confirmed',
                Password::min(8)
                    ->letters()
                    ->mixedCase()
                    ->numbers()
                    ->symbols(),
            ],
            'cf-turnstile-response' => ['nullable', new \App\Rules\TurnstileRule],
        ], [
            'name.required' => 'Full name is required.',
            'email.required' => 'Email address is required.',
            'email.unique' => 'This email address is already registered.',
            'password.required' => 'Password is required.',
            'password.min' => 'Password must be at least 8 characters long.',
            'password.letters' => 'Password must contain letters.',
            'password.mixed' => 'Password must contain both uppercase and lowercase letters.',
            'password.numbers' => 'Password must contain at least one number (0-9).',
            'password.symbols' => 'Password must contain at least one special character (@, $, !, %, *, ?, &, etc.).',
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
                'email_verified_at' => null,
            ]);

            // Dispatch OTP verification code
            $this->sendVerificationOtp($user);

            // Authenticate initial session for verification
            Auth::login($user);

            session([
                'verify_email' => $user->email,
                'verify_redirect' => $isSafeRedirect ? $redirect : null,
            ]);

            return redirect()->route('verification.notice')
                ->with('success', 'Account created! A 6-digit verification code has been sent to ' . $user->email . '.');
        } catch (\Throwable $e) {
            Log::error('Registration creation error: ' . $e->getMessage());
            return back()->with('error', 'Unable to complete registration. Please try again.')->withInput();
        }
    }

    /**
     * Show the OTP verification form.
     */
    public function showVerifyEmailForm(Request $request)
    {
        if (Auth::check() && Auth::user()->email_verified_at !== null) {
            return redirect()->route('home');
        }

        $email = session('verify_email') ?? $request->query('email') ?? (Auth::check() ? Auth::user()->email : null);

        if (empty($email)) {
            return redirect()->route('login');
        }

        $cooldownKey = 'email_verify_cooldown_' . md5($email);
        $cooldownTimestamp = Cache::get($cooldownKey);
        $resendCooldown = $cooldownTimestamp ? max(0, $cooldownTimestamp - now()->timestamp) : 0;

        return view('auth.verify-email', [
            'email' => $email,
            'redirect' => session('verify_redirect') ?? $request->query('redirect'),
            'resend_cooldown' => $resendCooldown,
        ]);
    }

    /**
     * Verify the 6-digit email OTP.
     */
    public function verifyOtp(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
            'otp'   => ['required', 'string', 'size:6'],
        ], [
            'otp.required' => 'Please enter the 6-digit verification code.',
            'otp.size'     => 'The verification code must be exactly 6 digits.',
        ]);

        $email = strtolower(trim($request->email));
        $enteredOtp = trim($request->otp);
        $cacheKey = 'email_verify_otp_' . md5($email);

        $record = Cache::get($cacheKey);

        if (!$record || empty($record['otp'])) {
            return back()->withErrors([
                'otp' => 'Verification code has expired or is invalid. Please request a new code.',
            ])->withInput();
        }

        if (now()->timestamp > $record['expires_at']) {
            Cache::forget($cacheKey);
            return back()->withErrors([
                'otp' => 'Verification code has expired. Please request a new one.',
            ])->withInput();
        }

        if ($record['attempts'] >= 5) {
            Cache::forget($cacheKey);
            return back()->withErrors([
                'otp' => 'Too many failed verification attempts. Please request a new code.',
            ])->withInput();
        }

        if (!hash_equals((string) $record['otp'], $enteredOtp)) {
            $record['attempts']++;
            Cache::put($cacheKey, $record, now()->addMinutes(10));
            $remaining = 5 - $record['attempts'];
            return back()->withErrors([
                'otp' => "Incorrect verification code. {$remaining} attempts remaining.",
            ])->withInput();
        }

        // OTP is valid! Mark user as verified
        $user = User::where('email', $email)->first();
        if (!$user) {
            return redirect()->route('register')->with('error', 'User account not found. Please register again.');
        }

        $user->email_verified_at = now();
        $user->save();

        Cache::forget($cacheKey);
        Cache::forget('email_verify_cooldown_' . md5($email));

        // Log the user in
        Auth::login($user, true);
        $request->session()->regenerate();

        session([
            'user_logged_in' => true,
            'user_name'      => $user->name,
            'user_email'     => $user->email,
            'user_id'        => $user->id,
        ]);

        session()->forget(['verify_email', 'verify_redirect']);

        $redirect = $request->input('redirect');
        if (!empty($redirect)) {
            $appHost = $request->getHost();
            $parsedHost = parse_url($redirect, PHP_URL_HOST);
            $isSameHost = empty($parsedHost) || $parsedHost === $appHost;
            if ($isSameHost) {
                return redirect($redirect)->with('success', 'Email verified successfully! Welcome to Artizen.');
            }
        }

        return redirect()->route('events.index')
            ->with('success', 'Email verified successfully! Welcome to Artizen.');
    }

    /**
     * Resend the verification OTP.
     */
    public function resendOtp(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        $email = strtolower(trim($request->email));
        $cooldownKey = 'email_verify_cooldown_' . md5($email);

        if (Cache::has($cooldownKey)) {
            $cooldownTimestamp = Cache::get($cooldownKey);
            $remainingSeconds = max(1, $cooldownTimestamp - now()->timestamp);
            return back()->withErrors([
                'otp' => "Please wait {$remainingSeconds} seconds before requesting a new code.",
            ]);
        }

        $user = User::where('email', $email)->first();
        if (!$user) {
            return back()->withErrors(['otp' => 'User account not found.']);
        }

        if ($user->email_verified_at !== null) {
            return redirect()->route('login')->with('success', 'Your email is already verified. Please sign in.');
        }

        $this->sendVerificationOtp($user);

        return back()->with('success', 'A new 6-digit verification code has been sent to ' . $user->email . '.')
                     ->with('resent', true)
                     ->with('resend_cooldown', 60);
    }
}
