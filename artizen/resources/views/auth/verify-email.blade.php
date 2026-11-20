@extends('layouts.app')

@section('title', 'Verify Your Email Address | ARTIZEN')

@section('content')
<div class="py-10 md:py-16 bg-[#FAF9F6] dark:bg-[#080808] text-[#171719] dark:text-white flex flex-col justify-center relative overflow-hidden font-body selection:bg-[#FFD600] selection:text-[#171719] transition-colors duration-300">

    <!-- Ambient Glow Accents -->
    <div class="absolute top-0 left-0 w-72 h-72 bg-[#FFD600]/15 dark:bg-[#FFD600]/10 rounded-full blur-3xl pointer-events-none -translate-x-1/2 -translate-y-1/2"></div>
    <div class="absolute bottom-0 right-0 w-[20rem] h-[20rem] bg-[#FFD600]/10 dark:bg-[#FFD600]/5 rounded-full blur-3xl pointer-events-none translate-x-1/3 translate-y-1/3"></div>

    <div class="max-w-md mx-auto px-4 sm:px-6 relative z-10 w-full">

        <div class="bg-white dark:bg-[#121215] border border-[#E6E2D8] dark:border-white/10 rounded-2xl p-6 sm:p-10 shadow-xs text-center">

            <!-- Mail Icon Header -->
            <div class="w-14 h-14 rounded-2xl bg-[#FFF9D2] dark:bg-[#2A2610] text-[#9E8200] dark:text-[#FFD600] flex items-center justify-center mx-auto mb-5 text-2xl shadow-2xs">
                <i class="fa-solid fa-envelope-open-text"></i>
            </div>

            <h1 class="font-heading font-extrabold text-2xl uppercase tracking-tight text-[#171719] dark:text-white mb-2">
                Verify Your Email
            </h1>

            <p class="text-xs text-gray-600 dark:text-gray-300 leading-relaxed mb-6">
                We have sent a 6-digit verification code to<br>
                <strong class="font-bold text-gray-900 dark:text-[#FFD600] text-sm break-all">{{ $email ?? session('verify_email', 'your email') }}</strong>
            </p>

            @if (session('success'))
                <div class="mb-5 p-3.5 rounded-xl bg-emerald-50 dark:bg-emerald-500/10 border border-emerald-200 dark:border-emerald-500/30 text-emerald-700 dark:text-emerald-400 text-xs font-semibold text-left flex items-start gap-2.5">
                    <i class="fa-solid fa-circle-check text-xs mt-0.5 shrink-0"></i>
                    <span class="leading-relaxed">{{ session('success') }}</span>
                </div>
            @endif

            @if (session('warning'))
                <div class="mb-5 p-3.5 rounded-xl bg-amber-50 dark:bg-amber-500/10 border border-amber-200 dark:border-amber-500/30 text-amber-800 dark:text-amber-300 text-xs font-semibold text-left flex items-start gap-2.5">
                    <i class="fa-solid fa-triangle-exclamation text-xs mt-0.5 shrink-0"></i>
                    <span class="leading-relaxed">{{ session('warning') }}</span>
                </div>
            @endif

            @if ($errors->any())
                <div class="mb-5 p-3.5 rounded-xl bg-red-50 dark:bg-red-500/10 border border-red-200 dark:border-red-500/30 text-red-700 dark:text-red-400 text-xs font-semibold text-left flex items-start gap-2.5">
                    <i class="fa-solid fa-circle-exclamation text-xs mt-0.5 shrink-0"></i>
                    <span class="leading-relaxed">{{ $errors->first() }}</span>
                </div>
            @endif

            <!-- OTP Verification Form -->
            <form action="{{ route('verification.verify') }}" method="POST" class="space-y-4" novalidate id="otp-form">
                @csrf
                <input type="hidden" name="email" value="{{ $email ?? session('verify_email') }}">
                @if(request('redirect', $redirect ?? session('verify_redirect')))
                    <input type="hidden" name="redirect" value="{{ request('redirect', $redirect ?? session('verify_redirect')) }}">
                @endif

                <div>
                    <label for="otp-input" class="block text-[11px] font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400 mb-2">
                        Enter 6-Digit Code
                    </label>
                    <div class="relative">
                        <input type="text" 
                               id="otp-input" 
                               name="otp" 
                               inputmode="numeric" 
                               pattern="[0-9]*" 
                               maxlength="6" 
                               autocomplete="one-time-code"
                               required 
                               autofocus 
                               placeholder="••••••"
                               class="w-full text-center text-3xl font-mono font-bold tracking-[0.5em] py-3.5 px-4 bg-[#FAF9F6] dark:bg-[#1E1E24] border border-[#E6E2D8] dark:border-white/10 rounded-xl text-[#171719] dark:text-white placeholder-gray-300 dark:placeholder-gray-600 focus:outline-none focus:border-[#FFD600] focus:ring-2 focus:ring-[#FFD600]/30 transition-all">
                    </div>
                    <span class="text-[11px] text-gray-400 dark:text-gray-500 mt-1.5 block">
                        Code expires in 10 minutes
                    </span>
                </div>

                <button type="submit" 
                        id="verify-submit-btn" 
                        class="w-full py-3.5 bg-[#FFD600] hover:bg-[#E6C200] text-[#171719] font-heading font-extrabold text-xs uppercase tracking-wider rounded-xl shadow-md hover:shadow-lg transition-all flex items-center justify-center gap-2 cursor-pointer mt-3">
                    <i class="fa-solid fa-shield-check text-xs"></i>
                    <span>Verify & Continue</span>
                </button>
            </form>

            <!-- Resend OTP Section -->
            <div class="mt-6 pt-5 border-t border-[#E6E2D8] dark:border-white/10 flex flex-col items-center gap-3">
                <form action="{{ route('verification.resend') }}" method="POST" id="resend-otp-form" class="w-full">
                    @csrf
                    <input type="hidden" name="email" value="{{ $email ?? session('verify_email') }}">
                    <button type="submit" 
                            id="resend-otp-btn" 
                            class="text-xs font-bold text-gray-700 dark:text-gray-300 hover:text-[#171719] dark:hover:text-[#FFD600] transition-colors inline-flex items-center gap-1.5 cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed">
                        <i class="fa-solid fa-arrow-rotate-right text-[11px]" id="resend-icon"></i>
                        <span id="resend-btn-text">Resend Code</span>
                    </button>
                </form>

                <div class="flex items-center justify-center gap-4 text-xs text-gray-500">
                    <a href="{{ route('register') }}" class="hover:underline">Wrong Email? Sign Up Again</a>
                    <span>•</span>
                    <a href="{{ route('login') }}" class="hover:underline">Back to Login</a>
                </div>
            </div>

        </div>

    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const otpInput = document.getElementById('otp-input');
        const resendBtn = document.getElementById('resend-otp-btn');
        const resendText = document.getElementById('resend-btn-text');

        // Only allow numbers
        if (otpInput) {
            otpInput.addEventListener('input', function () {
                this.value = this.value.replace(/[^0-9]/g, '');
                if (this.value.length === 6) {
                    document.getElementById('otp-form').submit();
                }
            });
        }

        // 60-Second Cooldown Timer for Resend
        let cooldown = {{ session('resend_cooldown', 0) > 0 ? session('resend_cooldown') : (session('resent') ? 60 : 0) }};
        
        function updateTimer() {
            if (cooldown > 0) {
                resendBtn.disabled = true;
                resendText.textContent = `Resend Code in ${cooldown}s`;
                cooldown--;
                setTimeout(updateTimer, 1000);
            } else {
                resendBtn.disabled = false;
                resendText.textContent = 'Resend Code';
            }
        }

        if (cooldown > 0) {
            updateTimer();
        }
    });
</script>
@endsection

