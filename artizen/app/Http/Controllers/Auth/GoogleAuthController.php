<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class GoogleAuthController extends Controller
{
    /**
     * Resolve the appropriate Google redirect URI dynamically if not explicitly forced in .env.
     */
    protected function getGoogleRedirectUri(): string
    {
        $explicit = env('GOOGLE_REDIRECT_URI');
        if (!empty($explicit) && filter_var($explicit, FILTER_VALIDATE_URL)) {
            return $explicit;
        }

        // Dynamically match current host and port (e.g. http://localhost:8000 or https://artizens.in)
        return url('/auth/google/callback');
    }

    /**
     * Determine if a redirect target is a safe, application-local URL.
     */
    protected function sanitizeRedirectUrl(?string $url, Request $request): string
    {
        $default = route('home');

        if (empty($url)) {
            return $default;
        }

        // Prevent control characters / CRLF injection
        if (preg_match('/[\r\n\t]/', $url)) {
            return $default;
        }

        // Disallow scheme-relative URLs (e.g. //evil.com or ///evil.com)
        if (str_starts_with($url, '//')) {
            return $default;
        }

        // Safe relative path starting with single /
        if (str_starts_with($url, '/')) {
            return $url;
        }

        // Absolute URL: ensure same scheme and exact same host
        $parsed = parse_url($url);
        if ($parsed === false || empty($parsed['host'])) {
            return $default;
        }

        $appHost = $request->getHost();
        $targetHost = $parsed['host'];

        if (strcasecmp($appHost, $targetHost) !== 0) {
            return $default;
        }

        $scheme = strtolower($parsed['scheme'] ?? '');
        if (!in_array($scheme, ['http', 'https'], true)) {
            return $default;
        }

        return $url;
    }

    /**
     * Redirect the user to Google OAuth consent screen.
     */
    public function redirect(Request $request)
    {
        $rawRedirect = $request->query('redirect', route('home'));
        $safeRedirect = $this->sanitizeRedirectUrl($rawRedirect, $request);
        session(['google_auth_redirect' => $safeRedirect]);

        $clientId = config('services.google.client_id');
        $clientSecret = config('services.google.client_secret');

        // Check if real Google credentials are configured
        if (empty($clientId) || empty($clientSecret)) {
            Log::info('Google OAuth attempted but GOOGLE_CLIENT_ID / GOOGLE_CLIENT_SECRET are not set in .env.');
            return redirect()->route('login')->with('error', 'Google Sign-In is temporarily unavailable. Please sign in with your email and password.');
        }

        try {
            $redirectUri = $this->getGoogleRedirectUri();
            return Socialite::driver('google')
                ->redirectUrl($redirectUri)
                ->redirect();
        } catch (\Throwable $e) {
            Log::error('Google OAuth Redirect Error: ' . $e->getMessage());
            return redirect()->route('login')->with('error', 'Google Sign-In is currently unavailable. Please sign in with your email and password.');
        }
    }

    /**
     * Handle the callback returned by Google OAuth.
     */
    public function callback(Request $request)
    {
        $targetRedirect = $this->sanitizeRedirectUrl(session()->pull('google_auth_redirect', route('home')), $request);

        if ($request->has('error')) {
            $errorDesc = $request->input('error_description', $request->input('error'));
            Log::warning('Google OAuth User Cancelled or Error: ' . $errorDesc);
            return redirect()->route('login')->with('error', 'Google Sign-In was cancelled. Please sign in with your email and password.');
        }

        $clientId = config('services.google.client_id');
        $clientSecret = config('services.google.client_secret');

        if (empty($clientId) || empty($clientSecret)) {
            return redirect()->route('login')->with('error', 'Google Sign-In is temporarily unavailable. Please sign in with your email and password.');
        }

        try {
            $redirectUri = $this->getGoogleRedirectUri();
            $googleUser = Socialite::driver('google')
                ->redirectUrl($redirectUri)
                ->user();

            $email = strtolower(trim((string)$googleUser->getEmail()));
            $googleId = (string)$googleUser->getId();
            $avatar = (string)$googleUser->getAvatar();
            $name = (string)$googleUser->getName() ?: 'Google User';

            if (empty($email)) {
                return redirect()->route('login')->with('error', 'Could not retrieve email address from your Google Account.');
            }

            // Upgrade Google avatar resolution for crisp display
            if (!empty($avatar) && preg_match('/=s\d+(-c)?$/', $avatar)) {
                $avatar = preg_replace('/=s\d+(-c)?$/', '=s240-c', $avatar);
            }

            // Find existing user by google_id or email
            $user = User::where('google_id', $googleId)
                ->orWhere('email', $email)
                ->first();

            if ($user) {
                $user->update([
                    'google_id' => $googleId,
                    'avatar'    => $avatar ?: $user->avatar,
                    'name'      => $user->name ?: $name,
                ]);
            } else {
                $user = User::create([
                    'name'      => $name,
                    'email'     => $email,
                    'google_id' => $googleId,
                    'avatar'    => $avatar,
                    'password'  => Hash::make(Str::random(32)),
                ]);
            }

            // Log user in
            Auth::login($user, true);
            $request->session()->regenerate();

            // Set user session info
            session([
                'user_logged_in' => true,
                'user_name'      => $user->name,
                'user_email'     => $user->email,
            ]);

            // Check if user is an Administrator
            $isAdmin = $user && $user->isAdmin();

            if ($isAdmin) {
                session([
                    'admin_logged_in' => true,
                    'admin_email'     => $user->email,
                    'admin_name'      => $user->name,
                    'admin_user_id'   => $user->id,
                ]);

                if (!empty($targetRedirect) && str_contains($targetRedirect, '/admin')) {
                    return redirect($targetRedirect)->with('success', 'Signed in as Administrator via Google.');
                }

                return redirect()->route('admin.dashboard')->with('success', 'Signed in as Administrator via Google.');
            }

            return redirect($targetRedirect)->with('success', 'Signed in successfully with Google!');
        } catch (\Throwable $e) {
            Log::error('Google OAuth Callback Error: ' . $e->getMessage());
            return redirect()->route('login')->with('error', 'Google authentication failed. Please try logging in with email and password.');
        }
    }
}
