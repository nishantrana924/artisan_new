@extends('layouts.app')

@section('title', 'Create Your Account | ARTIZEN')

@section('content')
<div class="py-6 md:py-10 bg-[#FAF9F6] dark:bg-[#080808] text-[#171719] dark:text-white flex flex-col justify-center relative overflow-hidden font-body selection:bg-[#FFD600] selection:text-[#171719] transition-colors duration-300">

    <!-- Background Ambient Glow Accents -->
    <div class="absolute top-0 left-0 w-72 h-72 bg-[#FFD600]/15 dark:bg-[#FFD600]/10 rounded-full blur-3xl pointer-events-none -translate-x-1/2 -translate-y-1/2"></div>
    <div class="absolute bottom-0 right-0 w-[20rem] h-[20rem] bg-[#FFD600]/10 dark:bg-[#FFD600]/5 rounded-full blur-3xl pointer-events-none translate-x-1/3 translate-y-1/3"></div>

    <!-- Main Grid Container -->
    <div class="w-full grid grid-cols-1 lg:grid-cols-2 gap-6 lg:gap-8 items-stretch relative z-10 max-w-6xl mx-auto px-4 sm:px-6">

        <!-- Left Showcase Side (Desktop Only) -->
        <div class="hidden lg:flex bg-[#171719] border border-[#E6E2D8] dark:border-white/10 p-8 xl:p-10 flex-col justify-between relative overflow-hidden rounded-2xl text-white">
            <!-- Background Image with High Contrast Overlay -->
            <div class="absolute inset-0 z-0 opacity-30 bg-cover bg-center" style="background-image: url('https://images.unsplash.com/photo-1511795409834-ef04bbd61622?q=80&w=1200&auto=format&fit=crop');"></div>
            <div class="absolute inset-0 z-0 bg-gradient-to-t from-[#171719] via-[#171719]/80 to-[#171719]/40"></div>

            <!-- Top Brand Title -->
            <div class="relative z-10 flex items-center gap-2">
                <a href="{{ route('home') }}" class="font-heading font-black text-2xl tracking-tighter" style="color: #ffffff !important;">
                    ARTIZEN<span style="color: #FFD600 !important;">.</span>
                </a>
            </div>

            <!-- Center Content: Clean, Minimal, No Clutter -->
            <div class="relative z-10 my-auto py-10 text-left">
                <h2 class="font-heading font-extrabold text-3xl xl:text-4xl uppercase tracking-tight leading-snug" style="color: #ffffff !important;">
                    Book Complete Event Packages <br>
                    In <span style="color: #FFD600 !important;">Under 2 Minutes.</span>
                </h2>
                <p class="text-xs font-medium max-w-sm mt-3 leading-relaxed" style="color: #E4E4E7 !important;">
                    Create your free Artizen account to track reservations, manage booking slips, and explore curated celebrations in Indore.
                </p>
            </div>

            <!-- Bottom Copyright Footer -->
            <div class="relative z-10 text-[11px] font-medium" style="color: #A1A1AA !important;">
                © {{ date('Y') }} ARTIZEN. Instant Event Booking Indore.
            </div>
        </div>

        <!-- Right Registration Form Side -->
        <div class="bg-white dark:bg-[#121215] border border-[#E6E2D8] dark:border-white/10 rounded-2xl p-6 sm:p-10 flex flex-col justify-center items-center relative w-full">
            <div class="w-full max-w-sm mx-auto">

                <!-- Form Header -->
                <div class="mb-5 text-left">
                    <h1 class="font-heading font-extrabold text-2xl uppercase tracking-tight text-[#171719] dark:text-white mb-1">
                        Create Account
                    </h1>
                    <p class="text-xs text-[#666666] dark:text-gray-400 font-medium">
                        Fill in your details to create your free Artizen account.
                    </p>
                </div>

                @if ($errors->any())
                    <div class="mb-4 p-3.5 rounded-xl bg-red-50 dark:bg-red-500/10 border border-red-200 dark:border-red-500/30 text-red-700 dark:text-red-400 text-xs font-semibold text-left">
                        <div class="flex items-center gap-1.5 font-bold mb-1">
                            <i class="fa-solid fa-circle-exclamation"></i>
                            <span>Registration Error</span>
                        </div>
                        <ul class="list-disc list-inside space-y-0.5 text-[11px]">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <!-- One-Click Google Signup -->
                <div class="mb-4">
                    <a href="{{ route('auth.google', request('redirect', $redirect ?? '') ? ['redirect' => request('redirect', $redirect ?? '')] : []) }}"
                       class="w-full py-2.5 px-4 rounded-xl border border-[#E6E2D8] dark:border-white/15 bg-white dark:bg-[#1E1E24] hover:bg-gray-50 dark:hover:bg-white/5 text-[#171719] dark:text-white font-heading font-bold text-xs flex items-center justify-center gap-2.5 transition-all shadow-2xs hover:shadow-xs hover:border-gray-400 group cursor-pointer"
                       title="Sign up with your Google Account">
                        <svg class="w-4 h-4 shrink-0" viewBox="0 0 24 24">
                            <path fill="#4285F4" d="M23.745 12.27c0-.7-.06-1.4-.19-2.07H12v4.51h6.6c-.29 1.52-1.14 2.8-2.4 3.66v3.05h3.87c2.27-2.09 3.67-5.17 3.67-9.15z"/>
                            <path fill="#34A853" d="M12 24c3.24 0 5.95-1.08 7.93-2.91l-3.87-3.05c-1.08.72-2.45 1.16-4.06 1.16-3.13 0-5.78-2.11-6.73-4.96H1.25v3.15C3.25 21.36 7.35 24 12 24z"/>
                            <path fill="#FBBC05" d="M5.27 14.24c-.25-.72-.39-1.5-.39-2.24s.14-1.52.39-2.24V6.61H1.25C.45 8.2 0 10.04 0 12s.45 3.8 1.25 5.39l4.02-3.15z"/>
                            <path fill="#EA4335" d="M12 4.75c1.77 0 3.35.61 4.6 1.8l3.42-3.42C17.95 1.19 15.24 0 12 0 7.35 0 3.25 2.64 1.25 6.61l4.02 3.15c.95-2.85 3.6-4.96 6.73-4.96z"/>
                        </svg>
                        <span>Sign up with Google</span>
                    </a>

                    <div class="relative my-3.5">
                        <div class="absolute inset-0 flex items-center">
                            <div class="w-full border-t border-[#E6E2D8] dark:border-white/10"></div>
                        </div>
                        <div class="relative flex justify-center text-[10px] uppercase font-bold tracking-wider">
                            <span class="bg-white dark:bg-[#121215] px-3 text-gray-400 dark:text-gray-500">or register with email</span>
                        </div>
                    </div>
                </div>

                <form action="{{ route('register.submit') }}" method="POST" class="space-y-3.5" novalidate id="register-form">
                    @csrf
                    @if(request('redirect', $redirect ?? ''))
                        <input type="hidden" name="redirect" value="{{ request('redirect', $redirect ?? '') }}">
                    @endif

                    <!-- Full Name -->
                    <div>
                        <label for="reg-name" class="block text-xs font-bold uppercase tracking-wider text-[#171719] dark:text-gray-300 mb-1.5">
                            Full Name <span class="text-[#FFD600]">*</span>
                        </label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none text-gray-400 dark:text-gray-500">
                                <i class="fa-solid fa-user text-xs"></i>
                            </span>
                            <input type="text" id="reg-name" name="name" value="{{ old('name') }}" required autofocus placeholder="Nishant Rana"
                                class="w-full pl-9 pr-3.5 py-2.5 bg-[#FAF9F6] dark:bg-[#1E1E24] border border-[#E6E2D8] dark:border-white/10 rounded-xl text-xs text-[#171719] dark:text-white placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:border-[#FFD600] focus:ring-1 focus:ring-[#FFD600] transition-all">
                        </div>
                    </div>

                    <!-- Email Address -->
                    <div>
                        <label for="reg-email" class="block text-xs font-bold uppercase tracking-wider text-[#171719] dark:text-gray-300 mb-1.5">
                            Email Address <span class="text-[#FFD600]">*</span>
                        </label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none text-gray-400 dark:text-gray-500">
                                <i class="fa-solid fa-envelope text-xs"></i>
                            </span>
                            <input type="email" id="reg-email" name="email" value="{{ old('email') }}" required placeholder="you@example.com"
                                class="w-full pl-9 pr-3.5 py-2.5 bg-[#FAF9F6] dark:bg-[#1E1E24] border border-[#E6E2D8] dark:border-white/10 rounded-xl text-xs text-[#171719] dark:text-white placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:border-[#FFD600] focus:ring-1 focus:ring-[#FFD600] transition-all">
                        </div>
                    </div>

                    <!-- Password -->
                    <div>
                        <label for="reg-password" class="block text-xs font-bold uppercase tracking-wider text-[#171719] dark:text-gray-300 mb-1.5">
                            Password <span class="text-[#FFD600]">*</span>
                        </label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none text-gray-400 dark:text-gray-500">
                                <i class="fa-solid fa-lock text-xs"></i>
                            </span>
                            <input type="password" id="reg-password" name="password" required placeholder="Minimum 8 characters"
                                class="w-full pl-9 pr-10 py-2.5 bg-[#FAF9F6] dark:bg-[#1E1E24] border border-[#E6E2D8] dark:border-white/10 rounded-xl text-xs text-[#171719] dark:text-white placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:border-[#FFD600] focus:ring-1 focus:ring-[#FFD600] transition-all">
                            <button type="button" onclick="toggleVisibility('reg-password', 'reg-eye-1')" class="absolute inset-y-0 right-0 flex items-center pr-3.5 text-gray-400 hover:text-[#FFD600] cursor-pointer">
                                <i id="reg-eye-1" class="fa-solid fa-eye text-xs"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Confirm Password -->
                    <div>
                        <label for="reg-password-confirm" class="block text-xs font-bold uppercase tracking-wider text-[#171719] dark:text-gray-300 mb-1.5">
                            Confirm Password <span class="text-[#FFD600]">*</span>
                        </label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none text-gray-400 dark:text-gray-500">
                                <i class="fa-solid fa-lock text-xs"></i>
                            </span>
                            <input type="password" id="reg-password-confirm" name="password_confirmation" required placeholder="Re-enter password"
                                class="w-full pl-9 pr-10 py-2.5 bg-[#FAF9F6] dark:bg-[#1E1E24] border border-[#E6E2D8] dark:border-white/10 rounded-xl text-xs text-[#171719] dark:text-white placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:border-[#FFD600] focus:ring-1 focus:ring-[#FFD600] transition-all">
                            <button type="button" onclick="toggleVisibility('reg-password-confirm', 'reg-eye-2')" class="absolute inset-y-0 right-0 flex items-center pr-3.5 text-gray-400 hover:text-[#FFD600] cursor-pointer">
                                <i id="reg-eye-2" class="fa-solid fa-eye text-xs"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Submit CTA Button -->
                    <button type="submit" class="w-full py-3 bg-[#FFD600] hover:bg-[#E6C200] text-[#171719] font-heading font-extrabold text-xs uppercase tracking-wider rounded-xl shadow-md hover:shadow-lg transition-all flex items-center justify-center gap-2 cursor-pointer mt-2">
                        <i class="fa-solid fa-user-plus text-xs"></i> Create My Account
                    </button>
                </form>

                <!-- Already Have Account Link -->
                <div class="mt-5 pt-4 border-t border-[#E6E2D8] dark:border-white/10 text-center">
                    <span class="text-xs text-[#666666] dark:text-gray-400 font-medium">Already have an account?</span>
                    <a href="{{ route('login', request('redirect', $redirect ?? '') ? ['redirect' => request('redirect', $redirect ?? '')] : []) }}" class="text-xs font-bold text-[#171719] dark:text-[#FFD600] hover:underline ml-1">Sign In to Account</a>
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
