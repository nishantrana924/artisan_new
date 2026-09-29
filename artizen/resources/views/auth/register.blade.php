@extends('layouts.app')

@section('content')
<div class="py-4 md:py-6 bg-white dark:bg-[#0C0C0E] text-[#1E1E24] dark:text-white flex flex-col justify-center relative font-body selection:bg-[#EA741D] selection:text-white transition-colors duration-300">

    <!-- Background Ambient Glow Accents -->
    <div class="absolute top-0 left-0 w-72 h-72 bg-[#EA741D]/15 dark:bg-[#EA741D]/10 rounded-full blur-3xl pointer-events-none -translate-x-1/2 -translate-y-1/2"></div>
    <div class="absolute bottom-0 right-0 w-[20rem] h-[20rem] bg-[#EA741D]/10 dark:bg-[#EA741D]/5 rounded-full blur-3xl pointer-events-none translate-x-1/3 translate-y-1/3"></div>

    <!-- Main Grid Container (Full Width Edge-to-Edge) -->
    <div class="w-full grid grid-cols-1 lg:grid-cols-12 items-stretch relative z-10">

        <!-- Left Showcase Side (Desktop Only) -->
        <div class="hidden lg:flex lg:col-span-6 xl:col-span-7 bg-[#121214] border-r border-t border-b border-gray-300 dark:border-white/10 p-6 xl:p-8 flex-col justify-between relative overflow-hidden rounded-none text-white">
            <!-- Background Image with High Contrast Overlay -->
            <div class="absolute inset-0 z-0 opacity-40 bg-cover bg-center" style="background-image: url('https://images.unsplash.com/photo-1511795409834-ef04bbd61622?q=80&w=1200&auto=format&fit=crop');"></div>
            <div class="absolute inset-0 z-0 bg-gradient-to-t from-[#121214] via-[#121214]/75 to-[#121214]/40"></div>

            <!-- Top Brand Badge -->
            <div class="relative z-10 flex items-center gap-2 mb-2">
                <a href="{{ route('home') }}" class="font-heading font-black text-2xl tracking-tighter text-white">
                    ARTIZEN<span class="text-[#EA741D]">.</span>
                </a>
                <span class="text-[8px] font-heading font-extrabold uppercase tracking-widest text-[#EA741D] bg-[#EA741D]/20 px-2.5 py-0.5 rounded-full border border-[#EA741D]/40 backdrop-blur-md">
                    Join Artizen
                </span>
            </div>

            <!-- Center Content: Benefits -->
            <div class="relative z-10 my-auto py-4 text-left">
                <h2 class="font-heading font-extrabold text-xl xl:text-2xl uppercase tracking-tight leading-snug mb-2 drop-shadow-md" style="color: #ffffff !important;">
                    Book Complete Event Packages <br>
                    In <span style="color: #EA741D !important;">Under 2 Minutes</span> in Indore.
                </h2>
                <p class="text-[11px] xl:text-xs max-w-lg leading-normal mb-5 font-medium" style="color: #E4E4E7 !important;">
                    Create your free Artizen account to manage event reservations, save your custom venue details, and track your celebration requests in real-time.
                </p>

                <!-- Key Feature Highlights Grid -->
                <div class="grid grid-cols-2 gap-3 max-w-lg mb-4">
                    <div class="p-3.5 rounded-xl border border-white/20 backdrop-blur-md flex items-start gap-3 shadow-xl" style="background-color: rgba(18, 18, 20, 0.85) !important;">
                        <div class="w-7 h-7 rounded-lg border flex items-center justify-center shrink-0 mt-0.5" style="background-color: rgba(212, 163, 115, 0.2) !important; border-color: rgba(212, 163, 115, 0.4) !important; color: #EA741D !important;">
                            <i class="fa-solid fa-bolt text-[11px]"></i>
                        </div>
                        <div>
                            <h3 class="font-heading font-bold text-xs uppercase tracking-wide mb-0.5" style="color: #ffffff !important;">Instant Bookings</h3>
                            <p class="text-[10px] leading-tight" style="color: #D4D4D8 !important;">Select birthday, acoustic & party packages fast.</p>
                        </div>
                    </div>

                    <div class="p-3.5 rounded-xl border border-white/20 backdrop-blur-md flex items-start gap-3 shadow-xl" style="background-color: rgba(18, 18, 20, 0.85) !important;">
                        <div class="w-7 h-7 rounded-lg border flex items-center justify-center shrink-0 mt-0.5" style="background-color: rgba(212, 163, 115, 0.2) !important; border-color: rgba(212, 163, 115, 0.4) !important; color: #EA741D !important;">
                            <i class="fa-solid fa-shield-halved text-[11px]"></i>
                        </div>
                        <div>
                            <h3 class="font-heading font-bold text-xs uppercase tracking-wide mb-0.5" style="color: #ffffff !important;">Offline Payment</h3>
                            <p class="text-[10px] leading-tight" style="color: #D4D4D8 !important;">Zero upfront online payment required.</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Bottom Copyright Footer -->
            <div class="relative z-10 text-[10px] font-medium pt-2" style="color: #A1A1AA !important;">
                © {{ date('Y') }} ARTIZEN Event Booking Platform. All rights reserved.
            </div>
        </div>

        <!-- Right Registration Form Side -->
        <div class="lg:col-span-6 xl:col-span-5 border-t border-b border-gray-200 dark:border-white/10 bg-white dark:bg-[#0C0C0E] py-6 px-4 md:px-8 flex flex-col justify-center items-center relative w-full">
            <div class="w-full max-w-sm mx-auto">

                <!-- Form Header -->
                <div class="mb-4 text-left">
                    <span class="text-[8px] font-heading font-extrabold uppercase tracking-widest text-[#EA741D] bg-[#EA741D]/15 px-2.5 py-0.5 rounded-full border border-[#EA741D]/30 inline-block mb-1.5">
                        New Customer Registration
                    </span>
                    <h1 class="font-heading font-extrabold text-xl md:text-2xl uppercase tracking-tight text-[#1E1E24] dark:text-white mb-0.5">
                        Create Account
                    </h1>
                    <p class="text-[11px] text-gray-500 dark:text-gray-400 font-medium">
                        Fill in your details to create your free Artizen account.
                    </p>
                </div>

                @if ($errors->any())
                    <div class="mb-4 p-3 rounded-xl bg-red-50 dark:bg-red-500/10 border border-red-200 dark:border-red-500/30 text-red-700 dark:text-red-400 text-[11px] font-semibold">
                        <div class="flex items-center gap-1.5 font-bold mb-1">
                            <i class="fa-solid fa-circle-exclamation"></i>
                            <span>Registration Error</span>
                        </div>
                        <ul class="list-disc list-inside space-y-0.5 text-[10px]">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <!-- Form Card -->
                <div class="bg-white dark:bg-[#121214] border border-gray-200 dark:border-white/10 p-5 rounded-2xl shadow-sm dark:shadow-xl relative text-left">
                    <form action="{{ route('register.submit') }}" method="POST" class="space-y-3" novalidate>
                        @csrf

                        <!-- Full Name -->
                        <div>
                            <label for="reg-name" class="block text-[11px] font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-1">
                                Full Name <span class="text-[#EA741D]">*</span>
                            </label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-gray-400 dark:text-gray-500">
                                    <i class="fa-solid fa-user text-[10px]"></i>
                                </span>
                                <input type="text" id="reg-name" name="name" value="{{ old('name') }}" required autofocus placeholder="Nishant Rana"
                                    class="w-full pl-8 pr-3 py-2 bg-gray-50 dark:bg-[#1A1A1E] border border-gray-200 dark:border-white/10 rounded-xl text-xs text-[#1E1E24] dark:text-white placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:border-[#EA741D] focus:ring-1 focus:ring-[#EA741D] transition-all">
                            </div>
                        </div>

                        <!-- Email Address -->
                        <div>
                            <label for="reg-email" class="block text-[11px] font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-1">
                                Email Address <span class="text-[#EA741D]">*</span>
                            </label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-gray-400 dark:text-gray-500">
                                    <i class="fa-solid fa-envelope text-[10px]"></i>
                                </span>
                                <input type="email" id="reg-email" name="email" value="{{ old('email') }}" required placeholder="you@example.com"
                                    class="w-full pl-8 pr-3 py-2 bg-gray-50 dark:bg-[#1A1A1E] border border-gray-200 dark:border-white/10 rounded-xl text-xs text-[#1E1E24] dark:text-white placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:border-[#EA741D] focus:ring-1 focus:ring-[#EA741D] transition-all">
                            </div>
                        </div>

                        <!-- Password -->
                        <div>
                            <label for="reg-password" class="block text-[11px] font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-1">
                                Password <span class="text-[#EA741D]">*</span>
                            </label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-gray-400 dark:text-gray-500">
                                    <i class="fa-solid fa-lock text-[10px]"></i>
                                </span>
                                <input type="password" id="reg-password" name="password" required placeholder="Minimum 8 characters"
                                    class="w-full pl-8 pr-10 py-2 bg-gray-50 dark:bg-[#1A1A1E] border border-gray-200 dark:border-white/10 rounded-xl text-xs text-[#1E1E24] dark:text-white placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:border-[#EA741D] focus:ring-1 focus:ring-[#EA741D] transition-all">
                                <button type="button" onclick="toggleVisibility('reg-password', 'reg-eye-1')" class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400 hover:text-[#EA741D]">
                                    <i id="reg-eye-1" class="fa-solid fa-eye text-[10px]"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Confirm Password -->
                        <div>
                            <label for="reg-password-confirm" class="block text-[11px] font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-1">
                                Confirm Password <span class="text-[#EA741D]">*</span>
                            </label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-gray-400 dark:text-gray-500">
                                    <i class="fa-solid fa-lock text-[10px]"></i>
                                </span>
                                <input type="password" id="reg-password-confirm" name="password_confirmation" required placeholder="Re-enter password"
                                    class="w-full pl-8 pr-10 py-2 bg-gray-50 dark:bg-[#1A1A1E] border border-gray-200 dark:border-white/10 rounded-xl text-xs text-[#1E1E24] dark:text-white placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:border-[#EA741D] focus:ring-1 focus:ring-[#EA741D] transition-all">
                                <button type="button" onclick="toggleVisibility('reg-password-confirm', 'reg-eye-2')" class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400 hover:text-[#EA741D]">
                                    <i id="reg-eye-2" class="fa-solid fa-eye text-[10px]"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Submit CTA Button -->
                        <button type="submit" class="w-full py-2.5 bg-[#EA741D] hover:bg-[#D6630F] text-[#1E1E24] font-heading font-extrabold text-xs uppercase tracking-widest rounded-xl shadow-md hover:shadow-lg transition-all flex items-center justify-center gap-2 cursor-pointer mt-2">
                            <i class="fa-solid fa-user-plus text-[10px]"></i> Create My Account
                        </button>
                    </form>

                    <!-- Already Have Account Link -->
                    <div class="mt-4 pt-3 border-t border-gray-200 dark:border-white/10 text-center">
                        <span class="text-[11px] text-gray-500 dark:text-gray-400 font-medium">Already have an account?</span>
                        <a href="{{ route('admin.login') }}" class="text-[11px] font-bold text-[#EA741D] hover:underline ml-1">Sign In to Account</a>
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

<script>
    function toggleVisibility(inputId, eyeId) {
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
