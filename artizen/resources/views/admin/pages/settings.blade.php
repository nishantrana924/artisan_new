@extends('layouts.admin')

@section('page_title', 'Admin Profile & Security Settings')
@section('page_heading', 'Settings')
@section('page_subheading', 'Manage your administrator profile, credentials, and account settings')

@push('head_scripts')
<style>
    .admin-input-eye {
        padding-right: 2.75rem !important;
    }
</style>
@endpush

@section('content')
@php
    $defaultTab = 'profile';
    if ($errors->has('current_password') || $errors->has('password') || $errors->has('password_confirmation')) {
        $defaultTab = 'security';
    } elseif ($errors->has('otp') || $errors->has('new_password') || $errors->has('new_password_confirmation') || session('otp_sent')) {
        $defaultTab = 'forgot';
    } elseif ($errors->has('name') || $errors->has('email') || $errors->has('avatar_file')) {
        $defaultTab = 'profile';
    }
@endphp

<div class="max-w-4xl mx-auto space-y-6">

    <!-- Header Section -->
    <div class="pb-2">
        <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Profile & Security Settings</h1>
        <p class="text-xs text-gray-500 mt-1">Manage your account information, security credentials, and platform preferences.</p>
    </div>

    <!-- Feedback Alerts -->
    @if (session('success'))
        <div class="p-3.5 rounded-xl bg-gray-50 border border-gray-200 text-gray-900 text-xs font-medium flex items-center justify-between">
            <div class="flex items-center gap-2">
                <i class="fa-solid fa-circle-check text-emerald-600 text-sm"></i>
                <span>{{ session('success') }}</span>
            </div>
            <button type="button" onclick="this.parentElement.remove()" class="text-gray-400 hover:text-gray-600 cursor-pointer">
                <i class="fa-solid fa-xmark text-xs"></i>
            </button>
        </div>
    @endif

    @if (session('error'))
        <div class="p-3.5 rounded-xl bg-gray-50 border border-rose-200 text-rose-800 text-xs font-medium flex items-center justify-between">
            <div class="flex items-center gap-2">
                <i class="fa-solid fa-circle-exclamation text-rose-600 text-sm"></i>
                <span>{{ session('error') }}</span>
            </div>
            <button type="button" onclick="this.parentElement.remove()" class="text-gray-400 hover:text-gray-600 cursor-pointer">
                <i class="fa-solid fa-xmark text-xs"></i>
            </button>
        </div>
    @endif

    @if ($errors->any())
        <div class="p-3.5 rounded-xl bg-gray-50 border border-rose-200 text-rose-800 text-xs">
            <div class="flex items-center gap-2 font-semibold mb-1">
                <i class="fa-solid fa-circle-exclamation text-rose-600 text-sm"></i>
                <span>Please correct the errors below:</span>
            </div>
            <ul class="list-disc list-inside space-y-0.5 text-rose-700 pl-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Clean Tab Navigation -->
    <div class="border-b border-gray-200">
        <nav class="flex gap-6 -mb-px overflow-x-auto text-xs font-medium" aria-label="Settings Tabs">
            <button type="button" 
                onclick="switchSettingsTab('profile')" 
                id="tab-btn-profile" 
                class="settings-tab-btn pb-3 border-b-2 transition-colors whitespace-nowrap cursor-pointer">
                Profile
            </button>
            <button type="button" 
                onclick="switchSettingsTab('security')" 
                id="tab-btn-security" 
                class="settings-tab-btn pb-3 border-b-2 transition-colors whitespace-nowrap cursor-pointer">
                Security & Passwords
            </button>
            <button type="button" 
                onclick="switchSettingsTab('forgot')" 
                id="tab-btn-forgot" 
                class="settings-tab-btn pb-3 border-b-2 transition-colors whitespace-nowrap cursor-pointer">
                Forgot Password
            </button>
            <button type="button" 
                onclick="switchSettingsTab('general')" 
                id="tab-btn-general" 
                class="settings-tab-btn pb-3 border-b-2 transition-colors whitespace-nowrap cursor-pointer">
                General
            </button>
        </nav>
    </div>

    <!-- Settings Content Card -->
    <div class="bg-white border border-gray-200 rounded-2xl p-6 sm:p-8">

        <!-- ================= TAB 1: PROFILE ================= -->
        <div id="tab-panel-profile" class="settings-tab-panel">
            <div class="mb-6 pb-4 border-b border-gray-100">
                <h2 class="text-base font-bold text-gray-900 tracking-tight">Profile Details</h2>
                <p class="text-xs text-gray-500 mt-0.5">Update your administrative name, email address, and avatar image.</p>
            </div>

            <form action="{{ route('admin.settings.profile') }}" method="POST" enctype="multipart/form-data" class="space-y-5 max-w-xl">
                @csrf

                <!-- Avatar Upload Section -->
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-2">Avatar</label>
                    <div class="flex items-center gap-4">
                        <div class="w-16 h-16 rounded-full bg-gray-100 border border-gray-200 flex items-center justify-center overflow-hidden shrink-0">
                            @if(!empty($adminUser->avatar))
                                <img id="profile-avatar-img" src="{{ asset($adminUser->avatar) }}" alt="Admin Avatar" class="w-full h-full object-cover">
                                <span id="profile-avatar-initial" class="text-xl font-bold text-gray-700 hidden">
                                    {{ strtoupper(substr($adminUser->name ?? 'A', 0, 1)) }}
                                </span>
                            @else
                                <span id="profile-avatar-initial" class="text-xl font-bold text-gray-700">
                                    {{ strtoupper(substr($adminUser->name ?? 'A', 0, 1)) }}
                                </span>
                                <img id="profile-avatar-img" src="" alt="Admin Avatar" class="w-full h-full object-cover hidden">
                            @endif
                        </div>
                        <div class="space-y-1.5">
                            <input type="file" id="avatar_file" name="avatar_file" accept="image/jpeg,image/png,image/webp,image/gif" onchange="previewSelectedAvatar(this)" class="hidden">
                            <input type="hidden" id="remove_avatar" name="remove_avatar" value="0">
                            <div class="flex items-center gap-3">
                                <button type="button" onclick="document.getElementById('avatar_file').click()" class="px-3 py-1.5 bg-white hover:bg-gray-50 border border-gray-300 text-gray-700 text-xs font-medium rounded-lg transition-colors cursor-pointer">
                                    Upload photo
                                </button>
                                <button type="button" id="remove-avatar-btn" onclick="removeAvatarAction()" class="text-xs text-gray-500 hover:text-gray-900 transition-colors cursor-pointer {{ empty($adminUser->avatar) ? 'hidden' : '' }}">
                                    Remove
                                </button>
                            </div>
                            <p id="avatar-filename-text" class="text-[11px] text-gray-400">JPG, PNG, or WEBP</p>
                        </div>
                    </div>
                    @error('avatar_file')
                        <p class="text-[11px] text-rose-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Full Name -->
                <div>
                    <label for="admin-name" class="block text-xs font-semibold text-gray-700 mb-1.5">
                        Full Name
                    </label>
                    <input type="text" id="admin-name" name="name" 
                        value="{{ old('name', $adminUser->name ?? 'Artizen Admin') }}" 
                        required 
                        class="w-full px-3 py-2 text-xs font-medium text-gray-900 bg-white border border-gray-300 rounded-lg focus:border-gray-900 focus:ring-1 focus:ring-gray-900 outline-none transition-colors">
                    @error('name')
                        <p class="text-[11px] text-rose-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Email Address -->
                <div>
                    <label for="admin-email-input" class="block text-xs font-semibold text-gray-700 mb-1.5">
                        Email Address
                    </label>
                    <input type="email" id="admin-email-input" name="email" 
                        value="{{ old('email', $adminUser->email ?? 'admin@artizen.com') }}" 
                        required 
                        class="w-full px-3 py-2 text-xs font-medium text-gray-900 bg-white border border-gray-300 rounded-lg focus:border-gray-900 focus:ring-1 focus:ring-gray-900 outline-none transition-colors">
                    @error('email')
                        <p class="text-[11px] text-rose-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Submit Button -->
                <div class="pt-2">
                    <button type="submit" class="px-5 py-2.5 bg-gray-900 hover:bg-black text-white text-xs font-semibold rounded-lg transition-colors cursor-pointer">
                        Save Changes
                    </button>
                </div>
            </form>
        </div>

        <!-- ================= TAB 2: SECURITY & PASSWORDS ================= -->
        <div id="tab-panel-security" class="settings-tab-panel hidden">
            <div class="mb-6 pb-4 border-b border-gray-100">
                <h2 class="text-base font-bold text-gray-900 tracking-tight">Change Account Password</h2>
                <p class="text-xs text-gray-500 mt-0.5">Ensure your account is using a secure password of at least 8 characters.</p>
            </div>

            <form action="{{ route('admin.settings.password') }}" method="POST" class="space-y-5 max-w-xl">
                @csrf

                <!-- Current Password -->
                <div>
                    <div class="flex items-center justify-between mb-1.5">
                        <label for="current_password" class="block text-xs font-semibold text-gray-700">
                            Current Password
                        </label>
                        <button type="button" onclick="switchSettingsTab('forgot')" class="text-[11px] text-gray-500 hover:text-gray-900 underline cursor-pointer">
                            Forgot current password?
                        </button>
                    </div>
                    <div class="relative">
                        <input type="password" id="current_password" name="current_password" required placeholder="Enter current password"
                            class="w-full px-3 admin-input-eye py-2 text-xs font-medium text-gray-900 bg-white border @error('current_password') border-rose-500 @else border-gray-300 @enderror rounded-lg focus:border-gray-900 focus:ring-1 focus:ring-gray-900 outline-none transition-colors">
                        <button type="button" onclick="togglePasswordVisibility('current_password', 'eye-curr')" class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400 hover:text-gray-700 cursor-pointer">
                            <i id="eye-curr" class="fa-solid fa-eye text-xs"></i>
                        </button>
                    </div>
                    @error('current_password')
                        <p class="text-[11px] text-rose-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- New Password -->
                <div>
                    <label for="new_password" class="block text-xs font-semibold text-gray-700 mb-1.5">
                        New Password
                    </label>
                    <div class="relative">
                        <input type="password" id="new_password" name="password" required minlength="8" placeholder="Minimum 8 characters"
                            oninput="checkPasswordStrength(this.value)"
                            class="w-full px-3 admin-input-eye py-2 text-xs font-medium text-gray-900 bg-white border @error('password') border-rose-500 @else border-gray-300 @enderror rounded-lg focus:border-gray-900 focus:ring-1 focus:ring-gray-900 outline-none transition-colors">
                        <button type="button" onclick="togglePasswordVisibility('new_password', 'eye-new')" class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400 hover:text-gray-700 cursor-pointer">
                            <i id="eye-new" class="fa-solid fa-eye text-xs"></i>
                        </button>
                    </div>
                    @error('password')
                        <p class="text-[11px] text-rose-500 mt-1">{{ $message }}</p>
                    @enderror

                    <!-- Password Strength Meter -->
                    <div class="mt-2 space-y-1">
                        <div class="flex items-center justify-between text-[11px] text-gray-500">
                            <span>Password strength</span>
                            <span id="strength-label">Enter password</span>
                        </div>
                        <div class="w-full bg-gray-100 rounded-full h-1 overflow-hidden">
                            <div id="strength-bar" class="h-1 w-0 bg-gray-300 transition-all duration-200"></div>
                        </div>
                    </div>
                </div>

                <!-- Confirm New Password -->
                <div>
                    <label for="password_confirmation" class="block text-xs font-semibold text-gray-700 mb-1.5">
                        Confirm New Password
                    </label>
                    <div class="relative">
                        <input type="password" id="password_confirmation" name="password_confirmation" required minlength="8" placeholder="Re-enter new password"
                            class="w-full px-3 admin-input-eye py-2 text-xs font-medium text-gray-900 bg-white border border-gray-300 rounded-lg focus:border-gray-900 focus:ring-1 focus:ring-gray-900 outline-none transition-colors">
                        <button type="button" onclick="togglePasswordVisibility('password_confirmation', 'eye-conf')" class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400 hover:text-gray-700 cursor-pointer">
                            <i id="eye-conf" class="fa-solid fa-eye text-xs"></i>
                        </button>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="pt-2">
                    <button type="submit" class="px-5 py-2.5 bg-gray-900 hover:bg-black text-white text-xs font-semibold rounded-lg transition-colors cursor-pointer">
                        Update Password
                    </button>
                </div>
            </form>
        </div>

        <!-- ================= TAB 3: FORGOT PASSWORD ================= -->
        @php
            $otpSent = session('otp_sent') || session('admin_reset_otp') || $errors->has('otp');
            $otpVerified = session('admin_reset_otp_verified') === true;
        @endphp
        <div id="tab-panel-forgot" class="settings-tab-panel hidden">
            <div class="mb-6 pb-4 border-b border-gray-100">
                <h2 class="text-base font-bold text-gray-900 tracking-tight">Forgot Password? Emergency Reset</h2>
                <p class="text-xs text-gray-500 mt-0.5">Request a 6-digit OTP to your registered email to verify your identity and set a new password.</p>
            </div>

            <div class="space-y-5 max-w-xl">
                <!-- STEP 1: Connected Admin Email & Send OTP Action -->
                <div class="p-4 rounded-xl border border-gray-200 bg-gray-50/70 space-y-3">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                        <div>
                            <span class="block text-[11px] font-semibold text-gray-500 uppercase tracking-wider">Connected Admin Email</span>
                            <span class="text-xs font-bold text-gray-900">{{ $adminUser->email ?? 'admin@artizen.com' }}</span>
                        </div>
                        <button type="button" 
                            id="send-otp-btn"
                            onclick="sendResetOtpEmail()" 
                            class="px-4 py-2 bg-gray-900 hover:bg-black text-white text-xs font-semibold rounded-lg transition-colors cursor-pointer shrink-0">
                            {{ $otpSent ? 'Resend OTP Code' : 'Send OTP Code' }}
                        </button>
                    </div>
                    <p id="otp-feedback-msg" class="text-[11px] text-gray-500">
                        @if($otpSent)
                            A 6-digit OTP code was sent to your email. Enter it below to proceed.
                        @else
                            Click "Send OTP Code" to receive a 6-digit verification code.
                        @endif
                    </p>
                </div>

                <!-- STEP 2: Enter & Verify OTP (Only visible after clicking Send OTP) -->
                <div id="step-otp-section" class="p-4 rounded-xl border border-gray-200 bg-white space-y-3 {{ $otpSent || $otpVerified ? '' : 'hidden' }}">
                    <div class="flex items-center justify-between">
                        <label for="em_otp" class="block text-xs font-semibold text-gray-700">
                            6-Digit OTP Verification Code
                        </label>
                        <span class="text-[10px] text-gray-400">Valid for 10 minutes</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <input type="text" id="em_otp" maxlength="6" pattern="[0-9]{6}" inputmode="numeric"
                            value="{{ old('otp') }}"
                            placeholder="Enter 6-digit OTP"
                            {{ $otpVerified ? 'disabled' : '' }}
                            class="flex-1 px-3 py-2 text-xs font-semibold tracking-wider text-gray-900 bg-white border border-gray-300 rounded-lg focus:border-gray-900 focus:ring-1 focus:ring-gray-900 outline-none transition-colors">
                        <button type="button" 
                            id="verify-otp-btn"
                            onclick="verifyResetOtpAction()" 
                            {{ $otpVerified ? 'disabled' : '' }}
                            class="px-4 py-2 bg-gray-900 hover:bg-black text-white text-xs font-semibold rounded-lg transition-colors cursor-pointer shrink-0 disabled:bg-emerald-600 disabled:cursor-default">
                            {{ $otpVerified ? 'Verified ✓' : 'Verify OTP' }}
                        </button>
                    </div>
                    <p id="otp-verify-feedback" class="text-[11px] {{ $otpVerified ? 'text-emerald-600 font-medium' : 'text-gray-500' }}">
                        @if($otpVerified)
                            OTP verified successfully. You can now set your new password below.
                        @else
                            Enter the code from your email and click "Verify OTP".
                        @endif
                    </p>
                    @error('otp')
                        <p class="text-[11px] text-rose-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- STEP 3: Set New Password (Only visible after OTP is successfully verified) -->
                <div id="step-password-section" class="{{ $otpVerified ? '' : 'hidden' }}">
                    <form action="{{ route('admin.settings.emergency-reset') }}" method="POST" class="space-y-4">
                        @csrf
                        <input type="hidden" name="otp" id="hidden_verified_otp" value="{{ old('otp') }}">

                        <div class="p-3 rounded-lg bg-emerald-50 border border-emerald-200 text-xs text-emerald-800 font-medium flex items-center gap-2">
                            <i class="fa-solid fa-circle-check text-emerald-600"></i>
                            <span>OTP verification successful. Please enter your new password below.</span>
                        </div>

                        <!-- New Password Field -->
                        <div>
                            <label for="em_password" class="block text-xs font-semibold text-gray-700 mb-1.5">
                                New Password <span class="text-rose-500">*</span>
                            </label>
                            <div class="relative">
                                <input type="password" id="em_password" name="new_password" required minlength="8" placeholder="Minimum 8 characters"
                                    class="w-full px-3 admin-input-eye py-2 text-xs font-medium text-gray-900 bg-white border @error('new_password') border-rose-500 @else border-gray-300 @enderror rounded-lg focus:border-gray-900 focus:ring-1 focus:ring-gray-900 outline-none transition-colors">
                                <button type="button" onclick="togglePasswordVisibility('em_password', 'eye-em-1')" class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400 hover:text-gray-700 cursor-pointer">
                                    <i id="eye-em-1" class="fa-solid fa-eye text-xs"></i>
                                </button>
                            </div>
                            @error('new_password')
                                <p class="text-[11px] text-rose-500 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Confirm New Password Field -->
                        <div>
                            <label for="em_password_confirmation" class="block text-xs font-semibold text-gray-700 mb-1.5">
                                Confirm New Password <span class="text-rose-500">*</span>
                            </label>
                            <div class="relative">
                                <input type="password" id="em_password_confirmation" name="new_password_confirmation" required minlength="8" placeholder="Re-enter new password"
                                    class="w-full px-3 admin-input-eye py-2 text-xs font-medium text-gray-900 bg-white border border-gray-300 rounded-lg focus:border-gray-900 focus:ring-1 focus:ring-gray-900 outline-none transition-colors">
                                <button type="button" onclick="togglePasswordVisibility('em_password_confirmation', 'eye-em-2')" class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400 hover:text-gray-700 cursor-pointer">
                                    <i id="eye-em-2" class="fa-solid fa-eye text-xs"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="pt-2 flex items-center justify-between">
                            <button type="submit" class="px-5 py-2.5 bg-gray-900 hover:bg-black text-white text-xs font-semibold rounded-lg transition-colors cursor-pointer">
                                Save New Password
                            </button>
                            <a href="{{ route('admin.password.request') }}" class="text-xs text-gray-500 hover:text-gray-900 underline">
                                Public reset page
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- ================= TAB 4: GENERAL ================= -->
        <div id="tab-panel-general" class="settings-tab-panel hidden">
            <div class="mb-6 pb-4 border-b border-gray-100">
                <h2 class="text-base font-bold text-gray-900 tracking-tight">General Information</h2>
                <p class="text-xs text-gray-500 mt-0.5">Platform configuration and administrator session details.</p>
            </div>

            <div class="max-w-xl space-y-4">
                <div class="divide-y divide-gray-100 border border-gray-200 rounded-xl overflow-hidden">
                    <div class="p-4 flex items-center justify-between bg-white text-xs">
                        <span class="text-gray-500 font-medium">Administrator Role</span>
                        <span class="text-gray-900 font-semibold">Master Administrator</span>
                    </div>
                    <div class="p-4 flex items-center justify-between bg-white text-xs">
                        <span class="text-gray-500 font-medium">Account Email</span>
                        <span class="text-gray-900 font-semibold">{{ $adminUser->email ?? 'admin@artizen.com' }}</span>
                    </div>
                    <div class="p-4 flex items-center justify-between bg-white text-xs">
                        <span class="text-gray-500 font-medium">Primary Hub</span>
                        <span class="text-gray-900 font-semibold">Indore, Madhya Pradesh</span>
                    </div>
                    <div class="p-4 flex items-center justify-between bg-white text-xs">
                        <span class="text-gray-500 font-medium">Account Created</span>
                        <span class="text-gray-900 font-semibold">{{ $adminUser->created_at ? $adminUser->created_at->format('M d, Y') : 'Oct 2026' }}</span>
                    </div>
                    <div class="p-4 flex items-center justify-between bg-white text-xs">
                        <span class="text-gray-500 font-medium">Payment Protocol</span>
                        <span class="text-gray-900 font-semibold">Offline Consultation & Confirmation</span>
                    </div>
                    <div class="p-4 flex items-center justify-between bg-white text-xs">
                        <span class="text-gray-500 font-medium">Session Status</span>
                        <span class="text-gray-900 font-semibold">Authenticated</span>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<script>
// Tab Switching
function switchSettingsTab(tabName) {
    const tabs = ['profile', 'security', 'forgot', 'general'];
    if (!tabs.includes(tabName)) tabName = 'profile';

    tabs.forEach(t => {
        const panel = document.getElementById('tab-panel-' + t);
        const btn = document.getElementById('tab-btn-' + t);
        if (panel) {
            if (t === tabName) {
                panel.classList.remove('hidden');
            } else {
                panel.classList.add('hidden');
            }
        }
        if (btn) {
            if (t === tabName) {
                btn.className = 'settings-tab-btn pb-3 border-b-2 border-gray-900 text-gray-900 font-semibold transition-colors whitespace-nowrap cursor-pointer';
            } else {
                btn.className = 'settings-tab-btn pb-3 border-b-2 border-transparent text-gray-500 hover:text-gray-800 hover:border-gray-300 font-medium transition-colors whitespace-nowrap cursor-pointer';
            }
        }
    });

    try {
        if (window.history && window.history.replaceState) {
            window.history.replaceState(null, null, '#' + tabName);
        }
    } catch (e) {}
}

// Initial Tab Setup on Page Load
document.addEventListener('DOMContentLoaded', function() {
    let initialTab = '{{ $defaultTab }}';
    const hash = window.location.hash ? window.location.hash.substring(1) : '';
    if (['profile', 'security', 'forgot', 'general'].includes(hash)) {
        initialTab = hash;
    }
    switchSettingsTab(initialTab);
});

// Password visibility toggle
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

// Live preview selected file and update avatar state
function previewSelectedAvatar(input) {
    if (input.files && input.files[0]) {
        const file = input.files[0];
        const reader = new FileReader();
        reader.onload = function(e) {
            const imgEl = document.getElementById('profile-avatar-img');
            const initialEl = document.getElementById('profile-avatar-initial');
            const removeBtn = document.getElementById('remove-avatar-btn');
            const filenameText = document.getElementById('avatar-filename-text');
            const removeInput = document.getElementById('remove_avatar');

            if (imgEl) {
                imgEl.src = e.target.result;
                imgEl.classList.remove('hidden');
            }
            if (initialEl) initialEl.classList.add('hidden');
            if (removeBtn) removeBtn.classList.remove('hidden');
            if (removeInput) removeInput.value = '0';
            if (filenameText) {
                filenameText.textContent = file.name + ' (' + Math.round(file.size / 1024) + ' KB) — ready to upload';
                filenameText.classList.add('text-gray-700');
                filenameText.classList.remove('text-gray-400');
            }
        };
        reader.readAsDataURL(file);
    }
}

// Remove avatar
function removeAvatarAction() {
    const fileInput = document.getElementById('avatar_file');
    const removeInput = document.getElementById('remove_avatar');
    const imgEl = document.getElementById('profile-avatar-img');
    const initialEl = document.getElementById('profile-avatar-initial');
    const removeBtn = document.getElementById('remove-avatar-btn');
    const filenameText = document.getElementById('avatar-filename-text');

    if (fileInput) fileInput.value = '';
    if (removeInput) removeInput.value = '1';
    if (imgEl) {
        imgEl.src = '';
        imgEl.classList.add('hidden');
    }
    if (initialEl) initialEl.classList.remove('hidden');
    if (removeBtn) removeBtn.classList.add('hidden');
    if (filenameText) {
        filenameText.textContent = 'Avatar will be removed upon saving';
        filenameText.classList.remove('text-gray-700');
        filenameText.classList.add('text-gray-400');
    }
}

// Dynamic Password Strength Meter
function checkPasswordStrength(password) {
    const label = document.getElementById('strength-label');
    const bar = document.getElementById('strength-bar');
    if (!label || !bar) return;

    if (!password || password.length === 0) {
        label.textContent = 'Enter password';
        label.className = 'text-gray-500';
        bar.className = 'h-1 w-0 bg-gray-300 transition-all duration-200';
        return;
    }

    let score = 0;
    if (password.length >= 8) score++;
    if (password.length >= 12) score++;
    if (/[A-Z]/.test(password)) score++;
    if (/[0-9]/.test(password)) score++;
    if (/[^A-Za-z0-9]/.test(password)) score++;

    if (score <= 2) {
        label.textContent = 'Weak';
        label.className = 'text-gray-600 font-medium';
        bar.className = 'h-1 w-1/3 bg-gray-400 transition-all duration-200';
    } else if (score <= 3) {
        label.textContent = 'Medium';
        label.className = 'text-gray-700 font-medium';
        bar.className = 'h-1 w-2/3 bg-gray-600 transition-all duration-200';
    } else {
        label.textContent = 'Strong';
        label.className = 'text-gray-900 font-medium';
        bar.className = 'h-1 w-full bg-gray-900 transition-all duration-200';
    }
}

// Step 1: Send Reset OTP to connected email
function sendResetOtpEmail() {
    const btn = document.getElementById('send-otp-btn');
    const msg = document.getElementById('otp-feedback-msg');
    const otpSection = document.getElementById('step-otp-section');
    const otpInput = document.getElementById('em_otp');
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

    if (!btn) return;
    btn.disabled = true;
    const originalText = btn.textContent;
    btn.textContent = 'Sending...';

    fetch('{{ route("admin.settings.send-otp") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json'
        },
        body: JSON.stringify({})
    })
    .then(res => res.json())
    .then(data => {
        if (data.status === 'success') {
            if (msg) {
                msg.textContent = data.message || 'OTP verification code sent! Check your inbox.';
                msg.className = 'text-[11px] text-emerald-600 font-medium';
            }
            // Reveal Step 2 (OTP field)
            if (otpSection) {
                otpSection.classList.remove('hidden');
            }
            if (otpInput) {
                otpInput.disabled = false;
                otpInput.focus();
            }
            // 60-second cooldown timer
            let timeLeft = 60;
            btn.textContent = `Resend in ${timeLeft}s`;
            const timer = setInterval(() => {
                timeLeft--;
                if (timeLeft <= 0) {
                    clearInterval(timer);
                    btn.disabled = false;
                    btn.textContent = 'Resend OTP Code';
                } else {
                    btn.textContent = `Resend in ${timeLeft}s`;
                }
            }, 1000);
        } else {
            if (msg) {
                msg.textContent = data.message || 'Failed to send OTP code. Please try again.';
                msg.className = 'text-[11px] text-rose-500 font-medium';
            }
            btn.disabled = false;
            btn.textContent = originalText;
        }
    })
    .catch(err => {
        if (msg) {
            msg.textContent = 'Error sending OTP. Please try again.';
            msg.className = 'text-[11px] text-rose-500 font-medium';
        }
        btn.disabled = false;
        btn.textContent = originalText;
    });
}

// Step 2: Verify OTP and reveal new password fields
function verifyResetOtpAction() {
    const otpInput = document.getElementById('em_otp');
    const verifyBtn = document.getElementById('verify-otp-btn');
    const verifyMsg = document.getElementById('otp-verify-feedback');
    const passwordSection = document.getElementById('step-password-section');
    const hiddenOtp = document.getElementById('hidden_verified_otp');
    const newPassInput = document.getElementById('em_password');
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

    const otpValue = otpInput ? otpInput.value.trim() : '';
    if (!otpValue || otpValue.length !== 6) {
        if (verifyMsg) {
            verifyMsg.textContent = 'Please enter a valid 6-digit OTP code.';
            verifyMsg.className = 'text-[11px] text-rose-500 font-medium';
        }
        if (otpInput) otpInput.focus();
        return;
    }

    if (verifyBtn) {
        verifyBtn.disabled = true;
        verifyBtn.textContent = 'Verifying...';
    }

    fetch('{{ route("admin.settings.verify-otp") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json'
        },
        body: JSON.stringify({ otp: otpValue })
    })
    .then(res => res.json())
    .then(data => {
        if (data.status === 'success') {
            if (verifyMsg) {
                verifyMsg.textContent = 'OTP verified successfully! You can now set your new password below.';
                verifyMsg.className = 'text-[11px] text-emerald-600 font-medium';
            }
            if (verifyBtn) {
                verifyBtn.textContent = 'Verified ✓';
                verifyBtn.className = 'px-4 py-2 bg-emerald-600 text-white text-xs font-semibold rounded-lg cursor-default shrink-0';
            }
            if (otpInput) {
                otpInput.disabled = true;
            }
            if (hiddenOtp) {
                hiddenOtp.value = otpValue;
            }
            // Reveal Step 3 (New Password Fields)
            if (passwordSection) {
                passwordSection.classList.remove('hidden');
            }
            if (newPassInput) {
                newPassInput.focus();
            }
        } else {
            if (verifyMsg) {
                verifyMsg.textContent = data.message || 'Incorrect OTP code. Please check your email.';
                verifyMsg.className = 'text-[11px] text-rose-500 font-medium';
            }
            if (verifyBtn) {
                verifyBtn.disabled = false;
                verifyBtn.textContent = 'Verify OTP';
            }
        }
    })
    .catch(err => {
        if (verifyMsg) {
            verifyMsg.textContent = 'Error verifying OTP. Please try again.';
            verifyMsg.className = 'text-[11px] text-rose-500 font-medium';
        }
        if (verifyBtn) {
            verifyBtn.disabled = false;
            verifyBtn.textContent = 'Verify OTP';
        }
    });
}
</script>
@endsection
