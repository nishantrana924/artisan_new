@extends('layouts.app')

@section('content')
<div class="py-4 md:py-6 bg-white dark:bg-[#0C0C0E] text-[#1E1E24] dark:text-white flex flex-col justify-center relative font-body selection:bg-[#EA741D] selection:text-white transition-colors duration-300">

    <!-- Background Ambient Glow Accents -->
    <div class="absolute top-0 left-0 w-72 h-72 bg-[#EA741D]/15 dark:bg-[#EA741D]/10 rounded-full blur-3xl pointer-events-none -translate-x-1/2 -translate-y-1/2"></div>
    <div class="absolute bottom-0 right-0 w-[20rem] h-[20rem] bg-[#EA741D]/10 dark:bg-[#EA741D]/5 rounded-full blur-3xl pointer-events-none translate-x-1/3 translate-y-1/3"></div>

    <!-- Main Grid Container (Full Width Edge-to-Edge) -->
    <div class="w-full grid grid-cols-1 lg:grid-cols-12 items-stretch relative z-10">

        <!-- Left Showcase Side (Desktop Only - Rich Event Visual & High Contrast) -->
        <div class="hidden lg:flex lg:col-span-6 xl:col-span-7 bg-[#121214] border-r border-t border-b border-gray-300 dark:border-white/10 p-6 xl:p-8 flex-col justify-between relative overflow-hidden rounded-none text-white">
            <!-- Background Image with High Contrast Overlay -->
            <div class="absolute inset-0 z-0 opacity-40 bg-cover bg-center" style="background-image: url('https://images.unsplash.com/photo-1519671482749-fd09be7ccebf?q=80&w=1200&auto=format&fit=crop');"></div>
            <div class="absolute inset-0 z-0 bg-gradient-to-t from-[#121214] via-[#121214]/75 to-[#121214]/40"></div>

            <!-- Top Brand Badge -->
            <div class="relative z-10 flex items-center gap-2 mb-2">
                <a href="{{ route('home') }}" class="font-heading font-black text-2xl tracking-tighter text-white">
                    ARTIZEN<span class="text-[#EA741D]">.</span>
                </a>
                <span class="text-[8px] font-heading font-extrabold uppercase tracking-widest text-[#EA741D] bg-[#EA741D]/20 px-2.5 py-0.5 rounded-full border border-[#EA741D]/40 backdrop-blur-md">
                    Control Center
                </span>
            </div>

            <!-- Center Content: Features & Live Stats -->
            <div class="relative z-10 my-auto py-4 text-left">
                <h2 class="font-heading font-extrabold text-xl xl:text-2xl uppercase tracking-tight leading-snug mb-2 drop-shadow-md" style="color: #ffffff !important;">
                    Manage Event Packages & <br>
                    <span style="color: #EA741D !important;">Customer Bookings</span> Seamlessly.
                </h2>
                <p class="text-[11px] xl:text-xs max-w-lg leading-normal mb-5 font-medium" style="color: #E4E4E7 !important;">
                    Artizen Admin Portal provides real-time oversight for booking requests, custom event category management, package tiers, and customer contact workflows in Indore.
                </p>

                <!-- Key Feature Highlights Grid -->
                <div class="grid grid-cols-2 gap-3 max-w-lg mb-4">
                    <div class="p-3.5 rounded-xl border border-white/20 backdrop-blur-md flex items-start gap-3 shadow-xl" style="background-color: rgba(18, 18, 20, 0.85) !important;">
                        <div class="w-7 h-7 rounded-lg border flex items-center justify-center shrink-0 mt-0.5" style="background-color: rgba(212, 163, 115, 0.2) !important; border-color: rgba(212, 163, 115, 0.4) !important; color: #EA741D !important;">
                            <i class="fa-solid fa-calendar-check text-[11px]"></i>
                        </div>
                        <div>
                            <h3 class="font-heading font-bold text-xs uppercase tracking-wide mb-0.5" style="color: #ffffff !important;">Booking Control</h3>
                            <p class="text-[10px] leading-tight" style="color: #D4D4D8 !important;">Review & track pending offline event requests.</p>
                        </div>
                    </div>

                    <div class="p-3.5 rounded-xl border border-white/20 backdrop-blur-md flex items-start gap-3 shadow-xl" style="background-color: rgba(18, 18, 20, 0.85) !important;">
                        <div class="w-7 h-7 rounded-lg border flex items-center justify-center shrink-0 mt-0.5" style="background-color: rgba(212, 163, 115, 0.2) !important; border-color: rgba(212, 163, 115, 0.4) !important; color: #EA741D !important;">
                            <i class="fa-solid fa-boxes-stacked text-[11px]"></i>
                        </div>
                        <div>
                            <h3 class="font-heading font-bold text-xs uppercase tracking-wide mb-0.5" style="color: #ffffff !important;">Package System</h3>
                            <p class="text-[10px] leading-tight" style="color: #D4D4D8 !important;">Update tiered pricing & inclusions dynamically.</p>
                        </div>
                    </div>
                </div>

                <!-- Live Metrics Chips -->
                <div class="flex items-center gap-5 pt-3 border-t border-white/20">
                    <div>
                        <span class="text-[9px] font-heading uppercase tracking-wider block" style="color: #A1A1AA !important;">Confirmation Speed</span>
                        <span class="font-heading font-bold text-xs" style="color: #ffffff !important;">Under 15 Mins</span>
                    </div>
                    <div class="h-6 w-px" style="background-color: rgba(255, 255, 255, 0.2) !important;"></div>
                    <div>
                        <span class="text-[9px] font-heading uppercase tracking-wider block" style="color: #A1A1AA !important;">Launch City</span>
                        <span class="font-heading font-bold text-xs" style="color: #EA741D !important;">Indore, MP</span>
                    </div>
                    <div class="h-6 w-px" style="background-color: rgba(255, 255, 255, 0.2) !important;"></div>
                    <div>
                        <span class="text-[9px] font-heading uppercase tracking-wider block" style="color: #A1A1AA !important;">Payment Mode</span>
                        <span class="font-heading font-bold text-xs" style="color: #ffffff !important;">100% Offline</span>
                    </div>
                </div>
            </div>

            <!-- Bottom Copyright Footer -->
            <div class="relative z-10 text-[10px] font-medium pt-2" style="color: #A1A1AA !important;">
                © {{ date('Y') }} ARTIZEN Event Booking Platform. All rights reserved.
            </div>
        </div>

        <!-- Right Login Card Side (Light & Dark Mode Responsive) -->
        <div class="lg:col-span-6 xl:col-span-5 border-t border-b border-gray-200 dark:border-white/10 bg-white dark:bg-[#0C0C0E] py-6 px-4 md:px-8 flex flex-col justify-center items-center relative w-full">
            <div class="w-full max-w-sm mx-auto">

                <!-- Mobile Logo Header (Shown only on small screens) -->
                <div class="lg:hidden text-center mb-5">
                    <a href="{{ route('home') }}" class="inline-block mb-2">
                        <span class="font-heading font-black text-2xl tracking-tighter text-[#1E1E24] dark:text-white">
                            ARTIZEN<span class="text-[#EA741D]">.</span>
                        </span>
                    </a>
                    <span class="text-[8px] font-heading font-extrabold uppercase tracking-widest text-[#EA741D] bg-[#EA741D]/15 px-2.5 py-0.5 rounded-full block w-fit mx-auto border border-[#EA741D]/30">
                        Admin Portal
                    </span>
                </div>

                <!-- Form Header -->
                <div class="mb-4 text-left">
                    <span class="text-[8px] font-heading font-extrabold uppercase tracking-widest text-[#EA741D] bg-[#EA741D]/15 px-2.5 py-0.5 rounded-full border border-[#EA741D]/30 inline-block mb-1.5">
                        Artizen Portal Access
                    </span>
                    <h1 class="font-heading font-extrabold text-xl md:text-2xl uppercase tracking-tight text-[#1E1E24] dark:text-white mb-0.5">
                        Sign In to Artizen
                    </h1>
                    <p class="text-[11px] text-gray-500 dark:text-gray-400 font-medium">
                        Welcome back! Enter your credentials to access your dashboard.
                    </p>
                </div>

                <!-- Session Status Alerts -->
                @if (session('status'))
                    <div class="mb-4 p-3 rounded-xl bg-emerald-50 dark:bg-emerald-500/10 border border-emerald-200 dark:border-emerald-500/30 text-emerald-700 dark:text-emerald-400 text-[11px] font-semibold flex items-center gap-2">
                        <i class="fa-solid fa-circle-check text-xs shrink-0"></i>
                        <span>{{ session('status') }}</span>
                    </div>
                @endif

                @if (session('error'))
                    <div class="mb-4 p-3 rounded-xl bg-red-50 dark:bg-red-500/10 border border-red-200 dark:border-red-500/30 text-red-700 dark:text-red-400 text-[11px] font-semibold flex items-center gap-2">
                        <i class="fa-solid fa-triangle-exclamation text-xs shrink-0"></i>
                        <span>{{ session('error') }}</span>
                    </div>
                @endif

                @if ($errors->any())
                    <div class="mb-4 p-3 rounded-xl bg-red-50 dark:bg-red-500/10 border border-red-200 dark:border-red-500/30 text-red-700 dark:text-red-400 text-[11px] font-semibold">
                        <div class="flex items-center gap-1.5 font-bold mb-1">
                            <i class="fa-solid fa-circle-exclamation"></i>
                            <span>Authentication Failed</span>
                        </div>
                        <ul class="list-disc list-inside space-y-0.5 text-[10px]">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <!-- Login Form Card -->
                <div class="bg-white dark:bg-[#121214] border border-gray-200 dark:border-white/10 p-5 rounded-2xl shadow-sm dark:shadow-xl relative text-left">
                    
                    <!-- Social Google Sign-In Button -->
                    <button type="button" onclick="alert('Google Sign-In: Authenticating account...')" class="w-full py-2.5 bg-white dark:bg-white/5 hover:bg-gray-50 dark:hover:bg-white/10 text-gray-700 dark:text-gray-200 border border-gray-200 dark:border-white/10 rounded-xl text-xs font-semibold flex items-center justify-center gap-2.5 transition-all cursor-pointer shadow-sm mb-3">
                        <svg class="w-4 h-4" viewBox="0 0 24 24">
                            <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                            <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                            <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.06H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.94l2.85-2.22.81-.63z"/>
                            <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.06l3.66 2.84c.87-2.6 3.3-4.52 6.16-4.52z"/>
                        </svg>
                        <span>Continue with Google</span>
                    </button>

                    <!-- Divider -->
                    <div class="relative flex items-center justify-center my-3">
                        <div class="border-t border-gray-200 dark:border-white/10 w-full"></div>
                        <span class="bg-white dark:bg-[#121214] px-2.5 text-[9px] font-heading uppercase tracking-wider text-gray-400 shrink-0">Or sign in with email</span>
                        <div class="border-t border-gray-200 dark:border-white/10 w-full"></div>
                    </div>

                    <form action="{{ route('admin.login.submit') }}" method="POST" class="space-y-3.5" novalidate>
                        @csrf

                        <!-- Email Input -->
                        <div>
                            <div class="flex justify-between items-center mb-1">
                                <label for="admin-email" class="block text-[11px] font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300">
                                    Email Address <span class="text-[#EA741D]">*</span>
                                </label>
                                @error('email')
                                    <span class="text-[9px] text-red-500 dark:text-red-400 font-semibold">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-gray-400 dark:text-gray-500">
                                    <i class="fa-solid fa-envelope text-[10px]"></i>
                                </span>
                                <input type="email" id="admin-email" name="email" value="{{ old('email') }}" required autofocus placeholder="admin@artizen.com"
                                    class="w-full pl-8 pr-3 py-2 bg-gray-50 dark:bg-[#1A1A1E] border @error('email') border-red-500/60 bg-red-500/5 @else border-gray-200 dark:border-white/10 @enderror rounded-xl text-xs text-[#1E1E24] dark:text-white placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:border-[#EA741D] focus:ring-1 focus:ring-[#EA741D] transition-all">
                            </div>
                        </div>

                        <!-- Password Input with Eye Toggle Button -->
                        <div>
                            <div class="flex justify-between items-center mb-1">
                                <label for="admin-password" class="block text-[11px] font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300">
                                    Password <span class="text-[#EA741D]">*</span>
                                </label>
                                @error('password')
                                    <span class="text-[9px] text-red-500 dark:text-red-400 font-semibold">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-gray-400 dark:text-gray-500">
                                    <i class="fa-solid fa-lock text-[10px]"></i>
                                </span>
                                <input type="password" id="admin-password" name="password" required placeholder="••••••••••••"
                                    class="w-full pl-8 pr-10 py-2 bg-gray-50 dark:bg-[#1A1A1E] border @error('password') border-red-500/60 bg-red-500/5 @else border-gray-200 dark:border-white/10 @enderror rounded-xl text-xs text-[#1E1E24] dark:text-white placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:border-[#EA741D] focus:ring-1 focus:ring-[#EA741D] transition-all">
                                
                                <!-- Password Eye Icon Toggle -->
                                <button type="button" onclick="togglePasswordVisibility()" aria-label="Toggle password visibility"
                                    class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400 dark:text-gray-500 hover:text-[#EA741D] focus:outline-none transition-colors cursor-pointer">
                                    <i id="eye-icon" class="fa-solid fa-eye text-[10px]"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Remember Me Checkbox -->
                        <div class="flex items-center justify-between pt-0.5">
                            <label class="inline-flex items-center gap-1.5 cursor-pointer group">
                                <input type="checkbox" name="remember" class="w-3.5 h-3.5 rounded border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-[#1A1A1E] text-[#EA741D] focus:ring-[#EA741D] cursor-pointer">
                                <span class="text-[11px] text-gray-600 dark:text-gray-400 font-medium select-none group-hover:text-gray-900 dark:group-hover:text-white transition-colors">Remember Me</span>
                            </label>
                        </div>

                        <!-- Submit Button -->
                        <button type="submit" class="w-full py-2.5 bg-[#EA741D] hover:bg-[#D6630F] text-[#1E1E24] font-heading font-extrabold text-xs uppercase tracking-widest rounded-xl shadow-md hover:shadow-lg transition-all flex items-center justify-center gap-2 cursor-pointer mt-1">
                            <i class="fa-solid fa-right-to-bracket text-[10px]"></i> Sign In to Account
                        </button>
                    </form>

                    <!-- Create Account Link for New Users -->
                    <div class="mt-4 pt-3 border-t border-gray-200 dark:border-white/10 text-center">
                        <span class="text-[11px] text-gray-500 dark:text-gray-400 font-medium">New to Artizen?</span>
                        <a href="{{ route('register') }}" class="text-[11px] font-bold text-[#EA741D] hover:underline ml-1">Create an Account</a>
                    </div>
                </div>

                <!-- Footer Return Link -->
                <div class="mt-5 text-center">
                    <a href="{{ route('home') }}" class="text-[11px] text-gray-500 hover:text-[#EA741D] transition-colors font-medium inline-flex items-center gap-1.5">
                        <i class="fa-solid fa-arrow-left text-[9px]"></i> Return to Artizen Public Site
                    </a>
                </div>

            </div>
        </div>

    </div>
</div>

<!-- Password Visibility Toggle JavaScript -->
<script>
    function togglePasswordVisibility() {
        const passwordInput = document.getElementById('admin-password');
        const eyeIcon = document.getElementById('eye-icon');

        if (passwordInput && eyeIcon) {
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                eyeIcon.classList.remove('fa-eye');
                eyeIcon.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                eyeIcon.classList.remove('fa-eye-slash');
                eyeIcon.classList.add('fa-eye');
            }
        }
    }
</script>
@endsection
