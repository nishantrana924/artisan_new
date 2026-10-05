@extends('layouts.app')

@section('title', 'Log In to Your Account | ARTIZEN')

@section('content')
<div class="py-6 md:py-10 bg-[#FAF9F6] dark:bg-[#080808] text-[#171719] dark:text-white flex flex-col justify-center relative font-body selection:bg-[#FFD600] selection:text-[#171719] transition-colors duration-300">

    <!-- Background Ambient Glow Accents -->
    <div class="absolute top-0 left-0 w-72 h-72 bg-[#FFD600]/15 dark:bg-[#FFD600]/10 rounded-full blur-3xl pointer-events-none -translate-x-1/2 -translate-y-1/2"></div>
    <div class="absolute bottom-0 right-0 w-[20rem] h-[20rem] bg-[#FFD600]/10 dark:bg-[#FFD600]/5 rounded-full blur-3xl pointer-events-none translate-x-1/3 translate-y-1/3"></div>

    <!-- Main Grid Container (Full Width Edge-to-Edge) -->
    <div class="w-full grid grid-cols-1 lg:grid-cols-12 items-stretch relative z-10 max-w-6xl mx-auto px-4">

        <!-- Left Showcase Side (Desktop Only) -->
        <div class="hidden lg:flex lg:col-span-6 bg-[#171719] border border-[#E6E2D8] dark:border-white/10 p-8 flex-col justify-between relative overflow-hidden rounded-2xl text-white">
            <!-- Background Image with High Contrast Overlay -->
            <div class="absolute inset-0 z-0 opacity-40 bg-cover bg-center" style="background-image: url('https://images.unsplash.com/photo-1511795409834-ef04bbd61622?q=80&w=1200&auto=format&fit=crop');"></div>
            <div class="absolute inset-0 z-0 bg-gradient-to-t from-[#171719] via-[#171719]/85 to-[#171719]/50"></div>

            <!-- Top Brand Badge -->
            <div class="relative z-10 flex items-center gap-2 mb-2">
                <a href="{{ route('home') }}" class="font-heading font-black text-2xl tracking-tighter text-white">
                    ARTIZEN<span class="text-[#FFD600]">.</span>
                </a>
                <span class="text-[8px] font-heading font-extrabold uppercase tracking-widest text-[#FFD600] bg-[#FFD600]/20 px-2.5 py-0.5 rounded-full border border-[#FFD600]/40 backdrop-blur-md">
                    Customer Portal
                </span>
            </div>

            <!-- Center Content: Benefits -->
            <div class="relative z-10 my-auto py-6 text-left">
                <h2 class="font-heading font-extrabold text-2xl uppercase tracking-tight leading-snug mb-3 text-white">
                    Welcome Back to <br>
                    <span class="text-[#FFD600]">Artizen Indore</span>
                </h2>
                <p class="text-xs max-w-md leading-relaxed mb-6 font-medium text-gray-300">
                    Sign in to leave reviews for your booked celebrations, manage your event reservations, and access customer perks across Indore.
                </p>

                <!-- Key Feature Highlights Grid -->
                <div class="space-y-3 max-w-md">
                    <div class="p-3.5 rounded-xl border border-white/20 backdrop-blur-md flex items-center gap-3 bg-[#171719]/80">
                        <div class="w-8 h-8 rounded-lg bg-[#FFD600]/20 border border-[#FFD600]/40 text-[#FFD600] flex items-center justify-center shrink-0">
                            <i class="fa-solid fa-star text-xs"></i>
                        </div>
                        <div>
                            <h3 class="font-heading font-bold text-xs uppercase tracking-wide text-white">Verified Customer Reviews</h3>
                            <p class="text-[11px] text-gray-300">Share ratings & feedback on setups you experienced.</p>
                        </div>
                    </div>

                    <div class="p-3.5 rounded-xl border border-white/20 backdrop-blur-md flex items-center gap-3 bg-[#171719]/80">
                        <div class="w-8 h-8 rounded-lg bg-[#FFD600]/20 border border-[#FFD600]/40 text-[#FFD600] flex items-center justify-center shrink-0">
                            <i class="fa-solid fa-shield-halved text-xs"></i>
                        </div>
                        <div>
                            <h3 class="font-heading font-bold text-xs uppercase tracking-wide text-white">Zero Online Advance</h3>
                            <p class="text-[11px] text-gray-300">Pay offline only after inspecting the event setup.</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Bottom Copyright Footer -->
            <div class="relative z-10 text-[11px] font-medium text-gray-400">
                © {{ date('Y') }} ARTIZEN Event Booking Platform.
            </div>
        </div>

        <!-- Right Login Form Side -->
        <div class="lg:col-span-6 bg-white dark:bg-[#121215] border border-[#E6E2D8] dark:border-white/10 rounded-2xl p-6 sm:p-10 flex flex-col justify-center items-center relative w-full lg:ml-4">
            <div class="w-full max-w-sm mx-auto">

                <!-- Form Header -->
                <div class="mb-6 text-left">
                    <span class="text-[9px] font-heading font-extrabold uppercase tracking-widest text-[#171719] dark:text-[#FFD600] bg-[#FFD600]/25 dark:bg-[#FFD600]/15 px-2.5 py-0.5 rounded-full border border-[#FFD600]/40 inline-block mb-2">
                        Customer Login
                    </span>
                    <h1 class="font-heading font-extrabold text-2xl uppercase tracking-tight text-[#171719] dark:text-white mb-1">
                        Sign In
                    </h1>
                    <p class="text-xs text-[#666666] dark:text-gray-400 font-medium">
                        Enter your email and password to access your account.
                    </p>
                </div>

                @if (isset($errors) && $errors->any())
                    <div class="mb-4 p-3 rounded-xl bg-red-50 dark:bg-red-500/10 border border-red-200 dark:border-red-500/30 text-red-700 dark:text-red-400 text-xs font-semibold text-left">
                        <div class="flex items-center gap-1.5 font-bold mb-1">
                            <i class="fa-solid fa-circle-exclamation"></i>
                            <span>Login Failed</span>
                        </div>
                        <ul class="list-disc list-inside space-y-0.5 text-[11px]">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @if (session('error'))
                    <div class="mb-4 p-3 rounded-xl bg-red-50 dark:bg-red-500/10 border border-red-200 dark:border-red-500/30 text-red-700 dark:text-red-400 text-xs font-semibold text-left">
                        {{ session('error') }}
                    </div>
                @endif

                @if (session('success'))
                    <div class="mb-4 p-3 rounded-xl bg-emerald-50 dark:bg-emerald-500/10 border border-emerald-200 dark:border-emerald-500/30 text-emerald-700 dark:text-emerald-400 text-xs font-semibold text-left">
                        {{ session('success') }}
                    </div>
                @endif

                <!-- Form Card -->
                <div class="relative text-left">
                    <form action="{{ route('login.submit') }}" method="POST" class="space-y-4" novalidate>
                        @csrf
                        @if(request('redirect'))
                            <input type="hidden" name="redirect" value="{{ request('redirect') }}">
                        @endif

                        <!-- Email Address -->
                        <div>
                            <label for="login-email" class="block text-xs font-bold uppercase tracking-wider text-[#171719] dark:text-gray-300 mb-1.5">
                                Email Address <span class="text-[#FFD600]">*</span>
                            </label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none text-gray-400 dark:text-gray-500">
                                    <i class="fa-solid fa-envelope text-xs"></i>
                                </span>
                                <input type="email" id="login-email" name="email" value="{{ old('email') }}" required autofocus placeholder="you@example.com"
                                    class="w-full pl-9 pr-3.5 py-2.5 bg-[#FAF9F6] dark:bg-[#1E1E24] border border-[#E6E2D8] dark:border-white/10 rounded-xl text-xs text-[#171719] dark:text-white placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:border-[#FFD600] focus:ring-1 focus:ring-[#FFD600] transition-all">
                            </div>
                        </div>

                        <!-- Password -->
                        <div>
                            <div class="flex items-center justify-between mb-1.5">
                                <label for="login-password" class="block text-xs font-bold uppercase tracking-wider text-[#171719] dark:text-gray-300">
                                    Password <span class="text-[#FFD600]">*</span>
                                </label>
                            </div>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none text-gray-400 dark:text-gray-500">
                                    <i class="fa-solid fa-lock text-xs"></i>
                                </span>
                                <input type="password" id="login-password" name="password" required placeholder="Enter your password"
                                    class="w-full pl-9 pr-10 py-2.5 bg-[#FAF9F6] dark:bg-[#1E1E24] border border-[#E6E2D8] dark:border-white/10 rounded-xl text-xs text-[#171719] dark:text-white placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:border-[#FFD600] focus:ring-1 focus:ring-[#FFD600] transition-all">
                                <button type="button" onclick="togglePasswordVisibility('login-password', 'login-eye')" class="absolute inset-y-0 right-0 flex items-center pr-3.5 text-gray-400 hover:text-[#FFD600] cursor-pointer">
                                    <i id="login-eye" class="fa-solid fa-eye text-xs"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Remember Me -->
                        <div class="flex items-center justify-between pt-1">
                            <label class="flex items-center gap-2 cursor-pointer text-xs text-gray-600 dark:text-gray-300">
                                <input type="checkbox" name="remember" class="w-4 h-4 rounded text-[#FFD600] focus:ring-[#FFD600] border-gray-300">
                                <span>Remember me</span>
                            </label>
                        </div>

                        <!-- Submit CTA Button -->
                        <button type="submit" class="w-full py-3 bg-[#FFD600] hover:bg-[#E6C200] text-[#171719] font-heading font-extrabold text-xs uppercase tracking-wider rounded-xl shadow-md hover:shadow-lg transition-all flex items-center justify-center gap-2 cursor-pointer mt-2">
                            <i class="fa-solid fa-right-to-bracket text-xs"></i> Log In
                        </button>
                    </form>

                    <!-- Don't Have Account Link -->
                    <div class="mt-5 pt-4 border-t border-[#E6E2D8] dark:border-white/10 text-center">
                        <span class="text-xs text-[#666666] dark:text-gray-400 font-medium">Don't have an account?</span>
                        <a href="{{ route('register') }}" class="text-xs font-bold text-[#171719] dark:text-[#FFD600] hover:underline ml-1">Create Account</a>
                    </div>
                </div>

                <!-- Footer Return Link -->
                <div class="mt-5 text-center">
                    <a href="{{ request('redirect') ?: route('home') }}" class="text-xs text-[#666666] dark:text-gray-400 hover:text-[#171719] dark:hover:text-[#FFD600] transition-colors font-medium inline-flex items-center gap-1.5">
                        <i class="fa-solid fa-arrow-left text-[10px]"></i> Back
                    </a>
                </div>

            </div>
        </div>

    </div>
</div>

<script>
    function togglePasswordVisibility(inputId, eyeId) {
        const input = document.getElementById(inputId);
        const eye = document.getElementById(eyeId);
        if (input && eye) {
            if (input.type === 'password') {
                input.type = 'text';
                eye.classList.remove('fa-eye');
                eye.classList.add('fa-eye-slash');
            } else {
                input.type = 'password';
                eye.classList.remove('fa-eye-slash');
                eye.classList.add('fa-eye');
            }
        }
    }
</script>
@endsection
