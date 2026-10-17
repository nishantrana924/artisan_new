<?php

namespace App\Providers;

use App\Models\Category;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }

        // Share navigation categories (with subcategories) to ALL views globally.
        // Used by layouts.navbar for the dynamic header ribbon + mega-dropdowns.
        View::composer('*', function ($view) {
            static $navCategories = null;
            if ($navCategories === null) {
                try {
                    $navCategories = Category::where('active', true)
                        ->with(['activeSubcategories'])
                        ->orderBy('display_order')
                        ->orderBy('id')
                        ->get();
                } catch (\Throwable $e) {
                    $navCategories = collect();
                }
            }
            $view->with('navCategories', $navCategories);
        });

        $this->configureRateLimiting();
    }

    /**
     * Configure application-wide rate limiters for security and abuse prevention.
     */
    protected function configureRateLimiting(): void
    {
        // 1. Authentication (Login & Admin Login): 10 per min per IP + 5 per min per email|ip
        \Illuminate\Support\Facades\RateLimiter::for('login', function (\Illuminate\Http\Request $request) {
            $email = strtolower(trim((string)$request->input('email', '')));
            return [
                \Illuminate\Cache\RateLimiting\Limit::perMinute(10)->by($request->ip()),
                \Illuminate\Cache\RateLimiting\Limit::perMinute(5)->by(\Illuminate\Support\Str::transliterate($email . '|' . $request->ip())),
            ];
        });

        // 2. User Registration: 5 registrations per minute per IP
        \Illuminate\Support\Facades\RateLimiter::for('register', function (\Illuminate\Http\Request $request) {
            return \Illuminate\Cache\RateLimiting\Limit::perMinute(5)->by($request->ip());
        });

        // 3. Admin Reset OTP Dispatch: 3 requests per minute per IP + 3 requests per 15 minutes per email
        \Illuminate\Support\Facades\RateLimiter::for('admin_otp_send', function (\Illuminate\Http\Request $request) {
            $email = strtolower(trim((string)$request->input('email', '')));
            return [
                \Illuminate\Cache\RateLimiting\Limit::perMinute(3)->by($request->ip()),
                \Illuminate\Cache\RateLimiting\Limit::perMinutes(15, 3)->by($email ?: $request->ip()),
            ];
        });

        // 4. Admin Reset OTP Verification: 5 attempts per minute per IP + 5 per 10 minutes per email
        \Illuminate\Support\Facades\RateLimiter::for('admin_otp_verify', function (\Illuminate\Http\Request $request) {
            $email = strtolower(trim((string)$request->input('email', '')));
            return [
                \Illuminate\Cache\RateLimiting\Limit::perMinute(5)->by($request->ip()),
                \Illuminate\Cache\RateLimiting\Limit::perMinutes(10, 5)->by($email ?: $request->ip()),
            ];
        });

        // 5. Booking Submissions: 6 bookings per minute per authenticated user (or IP)
        \Illuminate\Support\Facades\RateLimiter::for('booking_submit', function (\Illuminate\Http\Request $request) {
            $key = $request->user()?->id ?: $request->ip();
            return \Illuminate\Cache\RateLimiting\Limit::perMinute(6)->by($key);
        });

        // 6. Booking Tracking Searches: 10 searches per minute per IP
        \Illuminate\Support\Facades\RateLimiter::for('booking_track', function (\Illuminate\Http\Request $request) {
            return \Illuminate\Cache\RateLimiting\Limit::perMinute(10)->by($request->ip());
        });

        // 7. Contact & Inquiry Form: 5 inquiries per minute per IP
        \Illuminate\Support\Facades\RateLimiter::for('contact_enquiry', function (\Illuminate\Http\Request $request) {
            return \Illuminate\Cache\RateLimiting\Limit::perMinute(5)->by($request->ip());
        });

        // 8. Reviews Submissions: 5 submissions per minute per user / IP
        \Illuminate\Support\Facades\RateLimiter::for('reviews', function (\Illuminate\Http\Request $request) {
            $key = $request->user()?->id ?: $request->ip();
            return \Illuminate\Cache\RateLimiting\Limit::perMinute(5)->by($key);
        });

        // 9. Google OAuth Initiation & Callback: 10 per minute per IP
        \Illuminate\Support\Facades\RateLimiter::for('oauth', function (\Illuminate\Http\Request $request) {
            return \Illuminate\Cache\RateLimiting\Limit::perMinute(10)->by($request->ip());
        });

        // 10. Admin Booking Status Management: 20 updates per minute per admin
        \Illuminate\Support\Facades\RateLimiter::for('admin_booking_status', function (\Illuminate\Http\Request $request) {
            $key = $request->user()?->id ?: ($request->session()->get('admin_user_id') ?: $request->ip());
            return \Illuminate\Cache\RateLimiting\Limit::perMinute(20)->by($key);
        });
    }
}
