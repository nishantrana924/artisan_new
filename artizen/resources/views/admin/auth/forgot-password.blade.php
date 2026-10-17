@extends('layouts.app')

@section('content')
<div class="py-6 md:py-10 bg-[#FAF9F6] dark:bg-[#080808] text-[#171719] dark:text-white flex flex-col justify-center relative font-body selection:bg-[#FFD600] selection:text-[#171719] transition-colors duration-300 min-h-[85vh]">

    <!-- Background Ambient Glow Accents -->
    <div class="absolute top-0 left-0 w-72 h-72 bg-[#FFD600]/15 dark:bg-[#FFD600]/10 rounded-full blur-3xl pointer-events-none -translate-x-1/2 -translate-y-1/2"></div>
    <div class="absolute bottom-0 right-0 w-[20rem] h-[20rem] bg-[#FFD600]/10 dark:bg-[#FFD600]/5 rounded-full blur-3xl pointer-events-none translate-x-1/3 translate-y-1/3"></div>

    <div class="w-full max-w-md mx-auto px-4 relative z-10">

        <!-- Brand Header -->
        <div class="text-center mb-6">
            <a href="{{ route('home') }}" class="inline-block mb-2">
                <span class="font-heading font-black text-2xl tracking-tighter text-[#171719] dark:text-white">
                    ARTIZEN<span class="text-[#FFD600]">.</span>
                </span>
            </a>
            <div class="flex items-center justify-center gap-2 mt-1">
                <span class="text-[9px] font-heading font-extrabold uppercase tracking-widest text-[#171719] dark:text-[#FFD600] bg-[#FFD600]/25 dark:bg-[#FFD600]/15 px-3 py-1 rounded-full border border-[#FFD600]/40">
                    Administrator Security Portal
                </span>
            </div>
            <h1 class="text-xl font-extrabold text-gray-900 dark:text-white mt-4 tracking-tight">Forgot Password?</h1>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Enter your registered administrator email to set a new password</p>
        </div>

        @if (session('success'))
            <div class="mb-4 p-3 rounded-xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 text-emerald-800 dark:text-emerald-300 text-xs font-semibold flex items-center gap-2">
                <i class="fa-solid fa-circle-check text-emerald-600"></i>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @if ($errors->any())
            <div class="mb-4 p-3 rounded-xl bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-800 text-rose-800 dark:text-rose-300 text-xs font-semibold">
                <div class="flex items-center gap-1.5 font-bold mb-1">
                    <i class="fa-solid fa-circle-exclamation text-rose-600"></i>
                    <span>Please correct the errors below</span>
                </div>
                <ul class="list-disc list-inside space-y-0.5 text-[11px] text-rose-700 dark:text-rose-300">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Card Container -->
        <div class="bg-white dark:bg-[#171719] border border-[#E6E2D8] dark:border-white/10 p-6 rounded-2xl shadow-sm dark:shadow-xl relative">
            <form action="{{ route('admin.password.reset') }}" method="POST" class="space-y-4">
                @csrf

                <!-- Admin Email -->
                <div>
                    <label for="reset-email" class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-1">
                        Admin Email Address <span class="text-[#FFD600]">*</span>
                    </label>
                    <div class="flex gap-2">
                        <div class="relative flex-1">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-gray-400">
                                <i class="fa-solid fa-envelope text-xs"></i>
                            </span>
                            <input type="email" id="reset-email" name="email" value="{{ old('email', session('reset_email', 'admin@artizen.com')) }}" required autofocus
                                placeholder="admin@artizen.com"
                                class="w-full pl-9 pr-3 py-2.5 bg-[#FAF9F6] dark:bg-[#292929] border @error('email') border-red-500 @else border-gray-200 dark:border-white/10 @enderror rounded-xl text-xs text-gray-900 dark:text-white placeholder-gray-400 focus:outline-none focus:border-[#FFD600] focus:ring-1 focus:ring-[#FFD600]">
                        </div>
                        <button type="button" id="send-otp-btn" onclick="sendPublicResetOtp()"
                            class="px-3 py-2 bg-gray-900 hover:bg-black dark:bg-[#222224] dark:hover:bg-[#2d2d30] text-white border border-gray-300 dark:border-white/15 text-xs font-bold rounded-xl transition-colors cursor-pointer shrink-0">
                            Send OTP
                        </button>
                    </div>
                    <p id="otp-status-msg" class="text-[11px] text-gray-500 dark:text-gray-400 mt-1">
                        @if(session('otp_sent'))
                            A 6-digit verification code has been dispatched to your email.
                        @else
                            Click "Send OTP" to receive a 6-digit verification code.
                        @endif
                    </p>
                    @error('email')
                        <p class="text-[11px] text-rose-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- 6-digit OTP Code Field -->
                <div>
                    <div class="flex items-center justify-between mb-1">
                        <label for="reset-otp" class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300">
                            Verification Code (OTP) <span class="text-[#FFD600]">*</span>
                        </label>
                        <span class="text-[10px] text-gray-400">Valid for 10 min</span>
                    </div>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-gray-400">
                            <i class="fa-solid fa-key text-xs"></i>
                        </span>
                        <input type="text" id="reset-otp" name="otp" value="{{ old('otp') }}" required maxlength="6" pattern="[0-9]{6}" inputmode="numeric"
                            placeholder="Enter 6-digit OTP"
                            class="w-full pl-9 pr-3 py-2.5 bg-[#FAF9F6] dark:bg-[#292929] border @error('otp') border-red-500 @else border-gray-200 dark:border-white/10 @enderror rounded-xl text-xs font-mono tracking-widest text-gray-900 dark:text-white placeholder-gray-400 focus:outline-none focus:border-[#FFD600] focus:ring-1 focus:ring-[#FFD600]">
                    </div>
                    @error('otp')
                        <p class="text-[11px] text-rose-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- New Password -->
                <div>
                    <label for="new-password" class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-1">
                        New Password <span class="text-[#FFD600]">*</span>
                    </label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-gray-400">
                            <i class="fa-solid fa-lock text-xs"></i>
                        </span>
                        <input type="password" id="new-password" name="password" required minlength="8" placeholder="Minimum 8 characters"
                            class="w-full pl-9 pr-10 py-2.5 bg-[#FAF9F6] dark:bg-[#292929] border @error('password') border-red-500 @else border-gray-200 dark:border-white/10 @enderror rounded-xl text-xs text-gray-900 dark:text-white placeholder-gray-400 focus:outline-none focus:border-[#FFD600] focus:ring-1 focus:ring-[#FFD600]">
                        <button type="button" onclick="togglePasswordVisibility('new-password', 'eye-icon-1')" class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 cursor-pointer">
                            <i id="eye-icon-1" class="fa-solid fa-eye text-xs"></i>
                        </button>
                    </div>
                </div>

                <!-- Confirm Password -->
                <div>
                    <label for="password-confirm" class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-1">
                        Confirm New Password <span class="text-[#FFD600]">*</span>
                    </label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-gray-400">
                            <i class="fa-solid fa-shield-check text-xs"></i>
                        </span>
                        <input type="password" id="password-confirm" name="password_confirmation" required minlength="8" placeholder="Repeat new password"
                            class="w-full pl-9 pr-10 py-2.5 bg-[#FAF9F6] dark:bg-[#292929] border border-gray-200 dark:border-white/10 rounded-xl text-xs text-gray-900 dark:text-white placeholder-gray-400 focus:outline-none focus:border-[#FFD600] focus:ring-1 focus:ring-[#FFD600]">
                        <button type="button" onclick="togglePasswordVisibility('password-confirm', 'eye-icon-2')" class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 cursor-pointer">
                            <i id="eye-icon-2" class="fa-solid fa-eye text-xs"></i>
                        </button>
                    </div>
                </div>

                <!-- Submit Button -->
                <button type="submit" class="w-full py-2.5 bg-[#FFD600] hover:bg-[#E6C200] text-[#171719] font-heading font-extrabold text-xs uppercase tracking-widest rounded-xl shadow-md hover:shadow-lg transition-all flex items-center justify-center gap-2 cursor-pointer mt-2">
                    <i class="fa-solid fa-key text-xs"></i> Reset & Update Password
                </button>
            </form>

            <div class="mt-4 pt-4 border-t border-gray-100 dark:border-white/10 text-center">
                <a href="{{ route('login') }}" class="text-xs font-bold text-gray-700 dark:text-gray-300 hover:text-gray-900 dark:hover:text-[#FFD600] inline-flex items-center gap-1.5 transition-colors">
                    <i class="fa-solid fa-arrow-left text-[10px]"></i> Back to Sign In
                </a>
            </div>
        </div>
    </div>
</div>

<script>
function sendPublicResetOtp() {
    const emailInput = document.getElementById('reset-email');
    const btn = document.getElementById('send-otp-btn');
    const statusMsg = document.getElementById('otp-status-msg');

    if (!emailInput || !emailInput.value.trim()) {
        if (statusMsg) {
            statusMsg.textContent = 'Please enter your administrator email first.';
            statusMsg.className = 'text-[11px] text-rose-500 font-medium mt-1';
        }
        emailInput?.focus();
        return;
    }

    const email = emailInput.value.trim();
    const origText = btn.textContent;
    btn.disabled = true;
    btn.textContent = 'Sending...';

    fetch('{{ route("admin.password.send-otp") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ email: email })
    })
    .then(res => res.json())
    .then(data => {
        btn.disabled = false;
        btn.textContent = 'Resend OTP';
        if (statusMsg) {
            statusMsg.textContent = data.message || 'Verification code dispatched to your email.';
            statusMsg.className = 'text-[11px] text-emerald-600 dark:text-emerald-400 font-medium mt-1';
        }
        const otpInput = document.getElementById('reset-otp');
        if (otpInput) {
            otpInput.focus();
        }
    })
    .catch(err => {
        btn.disabled = false;
        btn.textContent = origText;
        if (statusMsg) {
            statusMsg.textContent = 'Failed to dispatch verification code. Please try again.';
            statusMsg.className = 'text-[11px] text-rose-500 font-medium mt-1';
        }
    });
}

function togglePasswordVisibility(fieldId, iconId) {
    const input = document.getElementById(fieldId);
    const icon = document.getElementById(iconId);
    if (!input || !icon) return;
    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
    } else {
        input.type = 'password';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
    }
}
</script>
@endsection
