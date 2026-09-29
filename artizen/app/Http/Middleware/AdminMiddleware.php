<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // 1. Check if user is logged in via Auth Facade OR Session Flag
        $isSessionAdmin = session('admin_logged_in') === true;
        $isAuthAdmin = Auth::check() && method_exists(Auth::user(), 'isAdmin') && Auth::user()->isAdmin();

        if ($isSessionAdmin || $isAuthAdmin) {
            return $next($request);
        }

        if ($request->expectsJson()) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        return redirect()->route('admin.login')->with('error', 'Please login to access the admin portal.');
    }
}
