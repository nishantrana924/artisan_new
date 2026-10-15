@extends('layouts.admin')

@section('page_title', 'Admin Profile & Security Settings')
@section('page_heading', 'Account & Security Settings')
@section('page_subheading', 'Manage your administrator profile, credentials, and password security')

@push('head_scripts')
<style>
    .admin-input-icon {
        padding-left: 2.85rem !important;
    }
    .admin-input-eye {
        padding-right: 2.85rem !important;
    }
</style>
@endpush

@section('content')
<div class="space-y-6 max-w-7xl mx-auto">

    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 pb-2 border-b border-gray-200">
        <div>
            <h1 class="text-2xl font-extrabold text-gray-900 tracking-tight flex items-center gap-2.5">
                <i class="fa-solid fa-user-shield text-[#EA741D]"></i>
                <span>Profile & Security Settings</span>
            </h1>
            <p class="text-xs text-gray-500 mt-1">Manage your administrator details, update your password, or perform emergency account resets</p>
        </div>
        <div class="flex items-center gap-2">
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200 shadow-2xs">
                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                Active Admin Session
            </span>
        </div>
    </div>

    <!-- Error / Validation Alert Notifications -->

    @if (session('error'))
        <div class="p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-sm font-semibold flex items-center justify-between shadow-2xs">
            <div class="flex items-center gap-2.5">
                <i class="fa-solid fa-circle-exclamation text-rose-600 text-base"></i>
                <span>{{ session('error') }}</span>
            </div>
            <button type="button" onclick="this.parentElement.remove()" class="text-rose-600 hover:text-rose-800 cursor-pointer text-xs">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
    @endif

    @if ($errors->any())
        <div class="p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-sm shadow-2xs">
            <div class="flex items-center gap-2 font-bold mb-1.5">
                <i class="fa-solid fa-circle-exclamation text-rose-600"></i>
                <span>Please resolve the following errors:</span>
            </div>
            <ul class="list-disc list-inside space-y-1 text-xs text-rose-700 font-medium pl-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Main Content 2-Column Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">

        <!-- =================== LEFT COLUMN: PROFILE CARD (Spans 5 cols) =================== -->
        <div class="lg:col-span-5 flex flex-col gap-6">

            <!-- Profile Overview & Form Card -->
            <div class="bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden">
                <!-- Top Decorative Banner -->
                <div class="h-28 bg-gradient-to-r from-gray-900 via-gray-800 to-amber-700 relative p-4 flex items-start justify-between">
                    <span class="px-2.5 py-1 rounded-md bg-white/20 backdrop-blur-md text-white text-[10px] font-extrabold uppercase tracking-widest border border-white/20">
                        Artizen Admin
                    </span>
                    <span class="text-white/40 text-3xl font-black">
                        <i class="fa-solid fa-shield-halved"></i>
                    </span>
                </div>

                <!-- Avatar and Profile Header -->
                <div class="px-6 pb-6 pt-0 relative">
                    <!-- Avatar circle overlapping the banner -->
                    <div class="flex items-end justify-between -mt-12 mb-4">
                        <div class="relative group">
                            <div class="w-24 h-24 rounded-2xl bg-gray-900 text-white border-4 border-white shadow-md flex items-center justify-center overflow-hidden shrink-0">
                                @if(!empty($adminUser->avatar))
                                    <img id="profile-avatar-img" src="{{ $adminUser->avatar }}" alt="Admin Avatar" class="w-full h-full object-cover">
                                @else
                                    <span id="profile-avatar-initial" class="text-3xl font-extrabold text-amber-400">
                                        {{ strtoupper(substr($adminUser->name ?? 'A', 0, 1)) }}
                                    </span>
                                    <img id="profile-avatar-img" src="" alt="Admin Avatar" class="w-full h-full object-cover hidden">
                                @endif
                            </div>
                            <span class="absolute bottom-0 right-0 w-6 h-6 rounded-full bg-emerald-500 border-2 border-white flex items-center justify-center text-white text-[10px]" title="Active">
                                <i class="fa-solid fa-check"></i>
                            </span>
                        </div>

                        <div class="text-right">
                            <span class="inline-block px-2.5 py-1 rounded-full text-[11px] font-bold bg-amber-50 text-amber-800 border border-amber-200">
                                <i class="fa-solid fa-crown text-amber-500 mr-1"></i> Super Admin
                            </span>
                        </div>
                    </div>

                    <!-- User Name & Title -->
                    <div class="mb-5 pb-4 border-b border-gray-100">
                        <h2 class="text-lg font-extrabold text-gray-900 tracking-tight">{{ $adminUser->name ?? 'Artizen Admin' }}</h2>
                        <p class="text-xs text-gray-500 font-medium">{{ $adminUser->email ?? 'admin@artizen.com' }}</p>
                        <div class="flex items-center gap-2 mt-2">
                            <span class="text-[11px] font-medium text-gray-400 flex items-center gap-1">
                                <i class="fa-solid fa-location-dot text-gray-400"></i> Indore Hub Central
                            </span>
                            <span class="text-gray-300">•</span>
                            <span class="text-[11px] font-medium text-gray-400 flex items-center gap-1">
                                <i class="fa-solid fa-calendar-check text-gray-400"></i> Joined {{ $adminUser->created_at ? $adminUser->created_at->format('M Y') : 'Oct 2026' }}
                            </span>
                        </div>
                    </div>

                    <!-- Profile Edit Form -->
                    <form action="{{ route('admin.settings.profile') }}" method="POST" class="space-y-4">
                        @csrf

                        <!-- Name Field -->
                        <div>
                            <label for="admin-name" class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">
                                Full Name <span class="text-rose-500">*</span>
                            </label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-gray-400">
                                    <i class="fa-solid fa-user text-xs"></i>
                                </span>
                                <input type="text" id="admin-name" name="name" 
                                    value="{{ old('name', $adminUser->name ?? 'Artizen Admin') }}" 
                                    required 
                                    class="w-full admin-input-icon pr-3 py-2 text-xs font-semibold text-gray-900 bg-white border border-gray-300 rounded-xl focus:border-gray-900 focus:ring-1 focus:ring-gray-900 outline-none transition-all">
                            </div>
                            @error('name')
                                <p class="text-[11px] text-rose-500 font-medium mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Email Address Field -->
                        <div>
                            <label for="admin-email-input" class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">
                                Email Address <span class="text-rose-500">*</span>
                            </label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-gray-400">
                                    <i class="fa-solid fa-envelope text-xs"></i>
                                </span>
                                <input type="email" id="admin-email-input" name="email" 
                                    value="{{ old('email', $adminUser->email ?? 'admin@artizen.com') }}" 
                                    required 
                                    class="w-full admin-input-icon pr-3 py-2 text-xs font-semibold text-gray-900 bg-white border border-gray-300 rounded-xl focus:border-gray-900 focus:ring-1 focus:ring-gray-900 outline-none transition-all">
                            </div>
                            @error('email')
                                <p class="text-[11px] text-rose-500 font-medium mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Avatar URL Field -->
                        <div>
                            <div class="flex items-center justify-between mb-1">
                                <label for="admin-avatar-input" class="block text-xs font-bold uppercase tracking-wider text-gray-700">
                                    Avatar Image URL
                                </label>
                                <span class="text-[10px] text-gray-400">Optional</span>
                            </div>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-gray-400">
                                    <i class="fa-solid fa-image text-xs"></i>
                                </span>
                                <input type="url" id="admin-avatar-input" name="avatar" 
                                    value="{{ old('avatar', $adminUser->avatar ?? '') }}" 
                                    placeholder="https://images.unsplash.com/... or UI Avatar"
                                    oninput="updateAvatarPreview(this.value)"
                                    class="w-full admin-input-icon pr-3 py-2 text-xs font-semibold text-gray-900 bg-white border border-gray-300 rounded-xl focus:border-gray-900 focus:ring-1 focus:ring-gray-900 outline-none transition-all">
                            </div>
                            @error('avatar')
                                <p class="text-[11px] text-rose-500 font-medium mt-1">{{ $message }}</p>
                            @enderror

                            <!-- Quick Preset Avatars -->
                            <div class="flex items-center gap-1.5 mt-2">
                                <span class="text-[10px] text-gray-400 font-medium">Quick presets:</span>
                                <button type="button" onclick="setPresetAvatar('https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=200&h=200&fit=crop&crop=face')" class="text-[10px] px-2 py-0.5 bg-gray-100 hover:bg-gray-200 rounded text-gray-700 font-semibold cursor-pointer">
                                    Photo 1
                                </button>
                                <button type="button" onclick="setPresetAvatar('https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=200&h=200&fit=crop&crop=face')" class="text-[10px] px-2 py-0.5 bg-gray-100 hover:bg-gray-200 rounded text-gray-700 font-semibold cursor-pointer">
                                    Photo 2
                                </button>
                                <button type="button" onclick="setPresetAvatar('')" class="text-[10px] px-2 py-0.5 bg-gray-100 hover:bg-gray-200 rounded text-gray-700 font-semibold cursor-pointer">
                                    Initials
                                </button>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="pt-3">
                            <button type="submit" class="w-full py-2.5 bg-gray-900 hover:bg-black text-white text-xs font-bold uppercase tracking-wider rounded-xl transition-all shadow-sm flex items-center justify-center gap-2 cursor-pointer">
                                <i class="fa-solid fa-floppy-disk text-xs"></i> Save Profile Changes
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Security Highlights Box -->
            <div class="bg-gray-50 border border-gray-200/80 rounded-2xl p-5 space-y-3">
                <h3 class="text-xs font-bold text-gray-900 uppercase tracking-wider flex items-center gap-2">
                    <i class="fa-solid fa-shield-halved text-amber-500"></i> Platform Security Highlights
                </h3>
                <ul class="text-xs text-gray-600 space-y-2">
                    <li class="flex items-start gap-2">
                        <i class="fa-solid fa-circle-check text-emerald-500 text-xs mt-0.5"></i>
                        <span><strong>100% Offline Payments:</strong> No payment gateway credentials or credit cards stored in database.</span>
                    </li>
                    <li class="flex items-start gap-2">
                        <i class="fa-solid fa-circle-check text-emerald-500 text-xs mt-0.5"></i>
                        <span><strong>Strict Session Isolation:</strong> Authenticated sessions protected with anti-CSRF token rotation.</span>
                    </li>
                    <li class="flex items-start gap-2">
                        <i class="fa-solid fa-circle-check text-emerald-500 text-xs mt-0.5"></i>
                        <span><strong>Indore Hub Architecture:</strong> Geo-targeted event booking system with role-based administrative control.</span>
                    </li>
                </ul>
            </div>
        </div>

        <!-- =================== RIGHT COLUMN: PASSWORD & RECOVERY (Spans 7 cols) =================== -->
        <div class="lg:col-span-7 flex flex-col gap-6">

            <!-- Card 1: Change Password -->
            <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm">
                <div class="flex items-center justify-between pb-4 mb-5 border-b border-gray-100">
                    <div>
                        <h2 class="text-base font-extrabold text-gray-900 tracking-tight flex items-center gap-2">
                            <i class="fa-solid fa-key text-gray-800"></i>
                            <span>Change Account Password</span>
                        </h2>
                        <p class="text-xs text-gray-500 mt-0.5">Keep your administrative credentials updated and protected</p>
                    </div>
                    <span class="text-[11px] font-bold text-gray-400 bg-gray-100 px-2.5 py-1 rounded-lg">
                        Standard Update
                    </span>
                </div>

                <form action="{{ route('admin.settings.password') }}" method="POST" class="space-y-4">
                    @csrf

                    <!-- Current Password -->
                    <div>
                        <div class="flex items-center justify-between mb-1">
                            <label for="current_password" class="block text-xs font-bold uppercase tracking-wider text-gray-700">
                                Current Password <span class="text-rose-500">*</span>
                            </label>
                            <a href="#forgot-password-card" onclick="highlightForgotCard()" class="text-[11px] font-bold text-[#EA741D] hover:underline cursor-pointer">
                                Forgot current password?
                            </a>
                        </div>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-gray-400">
                                <i class="fa-solid fa-lock text-xs"></i>
                            </span>
                            <input type="password" id="current_password" name="current_password" required placeholder="Enter your existing password"
                                class="w-full admin-input-icon admin-input-eye py-2.5 text-xs font-semibold text-gray-900 bg-white border @error('current_password') border-rose-500 @else border-gray-300 @enderror rounded-xl focus:border-gray-900 focus:ring-1 focus:ring-gray-900 outline-none transition-all">
                            <button type="button" onclick="togglePasswordVisibility('current_password', 'eye-curr')" class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400 hover:text-gray-700 cursor-pointer">
                                <i id="eye-curr" class="fa-solid fa-eye text-xs"></i>
                            </button>
                        </div>
                        @error('current_password')
                            <p class="text-[11px] text-rose-500 font-medium mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- New Password -->
                    <div>
                        <label for="new_password" class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">
                            New Password <span class="text-rose-500">*</span>
                        </label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-gray-400">
                                <i class="fa-solid fa-shield text-xs"></i>
                            </span>
                            <input type="password" id="new_password" name="password" required minlength="8" placeholder="Minimum 8 characters"
                                oninput="checkPasswordStrength(this.value)"
                                class="w-full admin-input-icon admin-input-eye py-2.5 text-xs font-semibold text-gray-900 bg-white border @error('password') border-rose-500 @else border-gray-300 @enderror rounded-xl focus:border-gray-900 focus:ring-1 focus:ring-gray-900 outline-none transition-all">
                            <button type="button" onclick="togglePasswordVisibility('new_password', 'eye-new')" class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400 hover:text-gray-700 cursor-pointer">
                                <i id="eye-new" class="fa-solid fa-eye text-xs"></i>
                            </button>
                        </div>
                        @error('password')
                            <p class="text-[11px] text-rose-500 font-medium mt-1">{{ $message }}</p>
                        @enderror

                        <!-- Dynamic Password Strength Indicator -->
                        <div class="mt-2 space-y-1">
                            <div class="flex items-center justify-between text-[10px] font-bold text-gray-500">
                                <span>Password Strength</span>
                                <span id="strength-label">Enter password</span>
                            </div>
                            <div class="w-full bg-gray-100 rounded-full h-1.5 overflow-hidden">
                                <div id="strength-bar" class="h-1.5 w-0 bg-gray-300 transition-all duration-300"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Confirm New Password -->
                    <div>
                        <label for="password_confirmation" class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">
                            Confirm New Password <span class="text-rose-500">*</span>
                        </label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-gray-400">
                                <i class="fa-solid fa-circle-check text-xs"></i>
                            </span>
                            <input type="password" id="password_confirmation" name="password_confirmation" required minlength="8" placeholder="Re-type new password"
                                class="w-full admin-input-icon admin-input-eye py-2.5 text-xs font-semibold text-gray-900 bg-white border border-gray-300 rounded-xl focus:border-gray-900 focus:ring-1 focus:ring-gray-900 outline-none transition-all">
                            <button type="button" onclick="togglePasswordVisibility('password_confirmation', 'eye-conf')" class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400 hover:text-gray-700 cursor-pointer">
                                <i id="eye-conf" class="fa-solid fa-eye text-xs"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="pt-2">
                        <button type="submit" class="px-6 py-2.5 bg-gray-900 hover:bg-black text-white text-xs font-bold uppercase tracking-wider rounded-xl transition-all shadow-sm flex items-center justify-center gap-2 cursor-pointer">
                            <i class="fa-solid fa-lock text-xs"></i> Update Password
                        </button>
                    </div>
                </form>
            </div>

            <!-- Card 2: Forgot Password / Emergency Recovery ("fogote") -->
            <div id="forgot-password-card" class="bg-gradient-to-br from-amber-50/60 via-white to-amber-50/30 border-2 border-amber-200/80 rounded-2xl p-6 shadow-sm transition-all duration-300">
                <div class="flex items-start justify-between pb-4 mb-4 border-b border-amber-100">
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="w-8 h-8 rounded-lg bg-amber-500 text-white flex items-center justify-center text-sm shadow-2xs">
                                <i class="fa-solid fa-life-ring"></i>
                            </span>
                            <div>
                                <h2 class="text-base font-extrabold text-gray-900 tracking-tight">Forgot Password? Emergency Reset</h2>
                                <p class="text-xs text-amber-800/80 font-medium mt-0.5">Direct recovery assistance for active administrators</p>
                            </div>
                        </div>
                    </div>
                    <span class="text-[10px] font-extrabold uppercase tracking-wider px-2 py-0.5 rounded bg-amber-200 text-amber-900">
                        In-Session Reset
                    </span>
                </div>

                <div class="mb-4 p-3 rounded-xl bg-white border border-amber-200/80 text-xs text-gray-700 flex items-start gap-2.5">
                    <i class="fa-solid fa-triangle-exclamation text-amber-600 text-sm mt-0.5 shrink-0"></i>
                    <div>
                        <p class="font-semibold text-gray-900">Lost or don't know your current password?</p>
                        <p class="text-gray-600 mt-0.5">As an authenticated administrator, you can override and set a brand-new password right now without knowing your old password. Type your admin email or <strong>RESET</strong> to authorize.</p>
                    </div>
                </div>

                <form action="{{ route('admin.settings.emergency-reset') }}" method="POST" class="space-y-4">
                    @csrf

                    <!-- Confirmation Security Field -->
                    <div>
                        <label for="admin_confirmation" class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">
                            Type Admin Email (<span class="text-amber-700 font-extrabold">{{ $adminUser->email ?? 'admin@artizen.com' }}</span>) or "RESET" <span class="text-rose-500">*</span>
                        </label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-gray-400">
                                <i class="fa-solid fa-user-check text-xs"></i>
                            </span>
                            <input type="text" id="admin_confirmation" name="admin_confirmation" required 
                                placeholder="Type {{ $adminUser->email ?? 'admin@artizen.com' }} or RESET"
                                class="w-full admin-input-icon pr-3 py-2.5 text-xs font-semibold text-gray-900 bg-white border @error('admin_confirmation') border-rose-500 @else border-gray-300 @enderror rounded-xl focus:border-amber-600 focus:ring-1 focus:ring-amber-600 outline-none transition-all">
                        </div>
                        @error('admin_confirmation')
                            <p class="text-[11px] text-rose-500 font-medium mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- New Password for Emergency Reset -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="em_password" class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">
                                New Password <span class="text-rose-500">*</span>
                            </label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-gray-400">
                                    <i class="fa-solid fa-lock text-xs"></i>
                                </span>
                                <input type="password" id="em_password" name="new_password" required minlength="8" placeholder="Minimum 8 chars"
                                    class="w-full admin-input-icon admin-input-eye py-2.5 text-xs font-semibold text-gray-900 bg-white border @error('new_password') border-rose-500 @else border-gray-300 @enderror rounded-xl focus:border-amber-600 focus:ring-1 focus:ring-amber-600 outline-none transition-all">
                                <button type="button" onclick="togglePasswordVisibility('em_password', 'eye-em-1')" class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400 hover:text-gray-700 cursor-pointer">
                                    <i id="eye-em-1" class="fa-solid fa-eye text-xs"></i>
                                </button>
                            </div>
                            @error('new_password')
                                <p class="text-[11px] text-rose-500 font-medium mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="em_password_confirmation" class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">
                                Repeat New Password <span class="text-rose-500">*</span>
                            </label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-gray-400">
                                    <i class="fa-solid fa-check text-xs"></i>
                                </span>
                                <input type="password" id="em_password_confirmation" name="new_password_confirmation" required minlength="8" placeholder="Re-type new password"
                                    class="w-full admin-input-icon admin-input-eye py-2.5 text-xs font-semibold text-gray-900 bg-white border border-gray-300 rounded-xl focus:border-amber-600 focus:ring-1 focus:ring-amber-600 outline-none transition-all">
                                <button type="button" onclick="togglePasswordVisibility('em_password_confirmation', 'eye-em-2')" class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400 hover:text-gray-700 cursor-pointer">
                                    <i id="eye-em-2" class="fa-solid fa-eye text-xs"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Emergency Reset CTA -->
                    <div class="pt-2 flex flex-col sm:flex-row items-center justify-between gap-3">
                        <button type="submit" class="w-full sm:w-auto px-6 py-2.5 bg-amber-600 hover:bg-amber-700 text-white text-xs font-bold uppercase tracking-wider rounded-xl transition-all shadow-sm flex items-center justify-center gap-2 cursor-pointer">
                            <i class="fa-solid fa-shield-cat text-xs"></i> Authorize & Set New Password
                        </button>
                        <a href="{{ route('admin.password.request') }}" class="text-xs font-bold text-gray-600 hover:text-gray-900 inline-flex items-center gap-1.5 underline">
                            <i class="fa-solid fa-arrow-up-right-from-square text-[10px]"></i> Open Public Reset Page
                        </a>
                    </div>
                </form>
            </div>

        </div>
    </div>
</div>

<script>
// Eye Icon Password Toggle
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

// Live Avatar Image Preview
function updateAvatarPreview(url) {
    const imgEl = document.getElementById('profile-avatar-img');
    const initialEl = document.getElementById('profile-avatar-initial');
    if (!imgEl) return;

    if (url && url.trim().length > 5) {
        imgEl.src = url.trim();
        imgEl.classList.remove('hidden');
        if (initialEl) initialEl.classList.add('hidden');
    } else {
        imgEl.src = '';
        imgEl.classList.add('hidden');
        if (initialEl) initialEl.classList.remove('hidden');
    }
}

// Preset Avatar selection helper
function setPresetAvatar(url) {
    const input = document.getElementById('admin-avatar-input');
    if (input) {
        input.value = url;
        updateAvatarPreview(url);
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
        bar.className = 'h-1.5 w-0 bg-gray-300 transition-all duration-300';
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
        label.className = 'text-rose-600 font-bold';
        bar.className = 'h-1.5 w-1/3 bg-rose-500 transition-all duration-300';
    } else if (score <= 3) {
        label.textContent = 'Medium';
        label.className = 'text-amber-600 font-bold';
        bar.className = 'h-1.5 w-2/3 bg-amber-500 transition-all duration-300';
    } else {
        label.textContent = 'Strong & Secure';
        label.className = 'text-emerald-600 font-bold';
        bar.className = 'h-1.5 w-full bg-emerald-500 transition-all duration-300';
    }
}

// Highlight Emergency Reset Card on Click
function highlightForgotCard() {
    const card = document.getElementById('forgot-password-card');
    if (card) {
        card.scrollIntoView({ behavior: 'smooth', block: 'center' });
        card.classList.add('ring-4', 'ring-amber-400');
        setTimeout(() => {
            card.classList.remove('ring-4', 'ring-amber-400');
        }, 1500);
    }
}
</script>
@endsection
