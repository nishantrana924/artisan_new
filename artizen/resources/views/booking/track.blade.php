@extends('layouts.app')

@section('content')
<div class="py-8 md:py-12 bg-white dark:bg-[#0C0C0E] text-[#1E1E24] dark:text-white min-h-[75vh] flex flex-col justify-center relative font-body selection:bg-[#FFD600] selection:text-[#171719] transition-colors duration-300">

    <!-- Background Glow Accents -->
    <div class="absolute top-0 left-1/2 -translate-x-1/2 w-96 h-96 bg-gold/10 rounded-full blur-3xl pointer-events-none"></div>

    <div class="max-w-4xl mx-auto px-4 w-full relative z-10">

        <!-- Header Section -->
        <div class="text-center mb-8">
            <span class="text-[9px] font-heading font-extrabold uppercase tracking-widest text-gold bg-gold/10 px-3 py-1 rounded-full border border-gold/30 inline-block mb-3">
                Real-Time Request Tracker
            </span>
            <h1 class="font-heading font-black text-2xl md:text-4xl uppercase tracking-tight text-[#1E1E24] dark:text-white mb-2">
                Track Event Booking Status
            </h1>
            <p class="text-xs md:text-sm text-gray-600 dark:text-gray-400 max-w-lg mx-auto font-medium">
                Enter your unique Booking Reference ID and registered Mobile Number to check your event setup progress.
            </p>
        </div>

        <!-- Search Form Card -->
        <div class="bg-white dark:bg-[#121214] border border-gray-200 dark:border-white/10 p-5 md:p-7 rounded-2xl shadow-lg mb-8 max-w-xl mx-auto">
            <form action="{{ route('booking.track.search') }}" method="POST" class="space-y-4" novalidate>
                @csrf

                @if (session('error'))
                    <div class="p-3.5 rounded-xl bg-red-50 dark:bg-red-500/10 border border-red-200 dark:border-red-500/30 text-red-700 dark:text-red-400 text-xs font-semibold flex items-start gap-2.5">
                        <i class="fa-solid fa-triangle-exclamation text-sm shrink-0 mt-0.5"></i>
                        <span>{{ session('error') }}</span>
                    </div>
                @endif

                @if ($errors->any())
                    <div class="p-3.5 rounded-xl bg-red-50 dark:bg-red-500/10 border border-red-200 dark:border-red-500/30 text-red-700 dark:text-red-400 text-xs font-semibold">
                        <ul class="list-disc list-inside space-y-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <!-- Booking ID Input -->
                    <div>
                        <label for="track-booking-id" class="block text-[11px] font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-1.5">
                            Booking ID <span class="text-gold">*</span>
                        </label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-gray-400 dark:text-gray-500">
                                <i class="fa-solid fa-hashtag text-[11px]"></i>
                            </span>
                            <input type="text" id="track-booking-id" name="booking_id" value="{{ old('booking_id', $bookingId ?? '') }}" required placeholder="BK-8844"
                                class="w-full pl-8 pr-3 py-2.5 bg-gray-50 dark:bg-[#1A1A1E] border border-gray-200 dark:border-white/10 rounded-xl text-xs uppercase font-bold text-[#1E1E24] dark:text-white placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:border-gold focus:ring-1 focus:ring-gold transition-all">
                        </div>
                    </div>

                    <!-- Mobile Input -->
                    <div>
                        <label for="track-mobile" class="block text-[11px] font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-1.5">
                            Mobile Number <span class="text-gold">*</span>
                        </label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-gray-400 dark:text-gray-500">
                                <i class="fa-solid fa-phone text-[11px]"></i>
                            </span>
                            <input type="tel" id="track-mobile" name="mobile" value="{{ old('mobile') }}" required placeholder="9131668156"
                                class="w-full pl-8 pr-3 py-2.5 bg-gray-50 dark:bg-[#1A1A1E] border border-gray-200 dark:border-white/10 rounded-xl text-xs text-[#1E1E24] dark:text-white placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:border-gold focus:ring-1 focus:ring-gold transition-all">
                        </div>
                    </div>
                </div>

                <button type="submit" class="w-full py-3 bg-[#FFD600] hover:bg-[#E6C200] text-[#171719] font-heading font-extrabold text-xs uppercase tracking-widest rounded-xl shadow-md hover:shadow-lg transition-all flex items-center justify-center gap-2 cursor-pointer mt-2">
                    <i class="fa-solid fa-magnifying-glass text-xs"></i> Check Booking Status
                </button>
            </form>
        </div>

        <!-- Result View Card (If Booking Match Found) -->
        @if (isset($booking) && $booking)
            @php
                $status = $booking['status'] ?? 'Pending';
                $statusClasses = [
                    'Pending' => 'bg-amber-500/15 text-amber-600 dark:text-amber-400 border-amber-500/30',
                    'Contacted' => 'bg-blue-500/15 text-blue-600 dark:text-blue-400 border-blue-500/30',
                    'Confirmed' => 'bg-emerald-500/15 text-emerald-600 dark:text-emerald-400 border-emerald-500/30',
                    'Completed' => 'bg-purple-500/15 text-purple-600 dark:text-purple-400 border-purple-500/30',
                    'Cancelled' => 'bg-red-500/15 text-red-600 dark:text-red-400 border-red-500/30',
                ];
                $currentStatusClass = $statusClasses[$status] ?? $statusClasses['Pending'];

                // Timeline Stepper Progress Index
                $stepIndex = match(strtolower($status)) {
                    'pending' => 1,
                    'contacted' => 2,
                    'confirmed' => 3,
                    'completed' => 4,
                    'cancelled' => -1,
                    default => 1
                };
            @endphp

            <div class="bg-white dark:bg-[#121214] border border-gray-200 dark:border-white/10 rounded-2xl shadow-xl overflow-hidden transition-all duration-300">
                
                <!-- Card Header -->
                <div class="p-5 md:p-6 border-b border-gray-200 dark:border-white/10 bg-gray-50/50 dark:bg-white/[0.02] flex flex-wrap items-center justify-between gap-4">
                    <div>
                        <div class="flex items-center gap-2.5 mb-1">
                            <span class="font-heading font-black text-xl md:text-2xl tracking-tight text-[#1E1E24] dark:text-white">
                                {{ $booking['id'] }}
                            </span>
                            <span class="text-xs font-bold px-3 py-1 rounded-full border {{ $currentStatusClass }}">
                                {{ strtoupper($status) }}
                            </span>
                        </div>
                        <p class="text-xs text-gray-500 dark:text-gray-400 font-medium">
                            Submitted on {{ $booking['created_at'] ?? 'N/A' }}
                        </p>
                    </div>

                    <a href="https://wa.me/919131668156?text={{ urlencode('Hello Artizen Team, inquiring about my booking ID: ' . $booking['id']) }}" target="_blank"
                        class="px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold flex items-center gap-2 transition-all shadow-sm shrink-0">
                        <i class="fa-brands fa-whatsapp text-sm"></i> Chat Support
                    </a>
                </div>

                <!-- Booking Status Progress Timeline -->
                <div class="p-5 md:p-8 border-b border-gray-200 dark:border-white/10 bg-gray-50 dark:bg-[#0C0C0E]/50">
                    <h3 class="text-xs font-heading font-extrabold uppercase tracking-wider text-gray-500 dark:text-gray-400 mb-6 text-center">
                        Event Processing Timeline
                    </h3>

                    @if ($status === 'Cancelled')
                        <div class="p-4 rounded-xl bg-red-50 dark:bg-red-500/10 border border-red-200 dark:border-red-500/30 text-center">
                            <div class="w-10 h-10 rounded-full bg-red-500/20 text-red-500 flex items-center justify-center mx-auto mb-2 text-lg">
                                <i class="fa-solid fa-xmark"></i>
                            </div>
                            <h4 class="font-heading font-bold text-sm text-red-600 dark:text-red-400 uppercase tracking-tight">Booking Cancelled</h4>
                            <p class="text-xs text-gray-600 dark:text-gray-400 mt-1 max-w-md mx-auto">
                                This booking request has been cancelled. If you believe this is an error or wish to reschedule, please contact our support team.
                            </p>
                        </div>
                    @else
                        <!-- 4 Step Stepper -->
                        <div class="grid grid-cols-1 sm:grid-cols-4 gap-4 relative">
                            <!-- Step 1: Submitted -->
                            <div class="flex sm:flex-col items-center gap-3 sm:text-center relative z-10">
                                <div class="w-10 h-10 rounded-full flex items-center justify-center font-bold text-xs shrink-0 {{ $stepIndex >= 1 ? 'bg-[#FFD600] text-[#171719] shadow-md' : 'bg-gray-200 dark:bg-white/10 text-gray-400' }}">
                                    <i class="fa-solid fa-paper-plane text-xs"></i>
                                </div>
                                <div>
                                    <h4 class="font-heading font-bold text-xs uppercase tracking-tight {{ $stepIndex >= 1 ? 'text-[#1E1E24] dark:text-white' : 'text-gray-400' }}">1. Request Received</h4>
                                    <p class="text-[10px] text-gray-500 dark:text-gray-400 mt-0.5">Booking saved in queue</p>
                                </div>
                            </div>

                            <!-- Step 2: Contacted -->
                            <div class="flex sm:flex-col items-center gap-3 sm:text-center relative z-10">
                                <div class="w-10 h-10 rounded-full flex items-center justify-center font-bold text-xs shrink-0 {{ $stepIndex >= 2 ? 'bg-[#FFD600] text-[#171719] shadow-md' : 'bg-gray-200 dark:bg-white/10 text-gray-400' }}">
                                    <i class="fa-solid fa-phone-volume text-xs"></i>
                                </div>
                                <div>
                                    <h4 class="font-heading font-bold text-xs uppercase tracking-tight {{ $stepIndex >= 2 ? 'text-[#1E1E24] dark:text-white' : 'text-gray-400' }}">2. Planner Contact</h4>
                                    <p class="text-[10px] text-gray-500 dark:text-gray-400 mt-0.5">Venue details verified</p>
                                </div>
                            </div>

                            <!-- Step 3: Confirmed -->
                            <div class="flex sm:flex-col items-center gap-3 sm:text-center relative z-10">
                                <div class="w-10 h-10 rounded-full flex items-center justify-center font-bold text-xs shrink-0 {{ $stepIndex >= 3 ? 'bg-[#FFD600] text-[#171719] shadow-md' : 'bg-gray-200 dark:bg-white/10 text-gray-400' }}">
                                    <i class="fa-solid fa-calendar-check text-xs"></i>
                                </div>
                                <div>
                                    <h4 class="font-heading font-bold text-xs uppercase tracking-tight {{ $stepIndex >= 3 ? 'text-[#1E1E24] dark:text-white' : 'text-gray-400' }}">3. Event Confirmed</h4>
                                    <p class="text-[10px] text-gray-500 dark:text-gray-400 mt-0.5">Team & inventory assigned</p>
                                </div>
                            </div>

                            <!-- Step 4: Completed -->
                            <div class="flex sm:flex-col items-center gap-3 sm:text-center relative z-10">
                                <div class="w-10 h-10 rounded-full flex items-center justify-center font-bold text-xs shrink-0 {{ $stepIndex >= 4 ? 'bg-emerald-500 text-white shadow-md' : 'bg-gray-200 dark:bg-white/10 text-gray-400' }}">
                                    <i class="fa-solid fa-champagne-glasses text-xs"></i>
                                </div>
                                <div>
                                    <h4 class="font-heading font-bold text-xs uppercase tracking-tight {{ $stepIndex >= 4 ? 'text-emerald-500' : 'text-gray-400' }}">4. Execution Complete</h4>
                                    <p class="text-[10px] text-gray-500 dark:text-gray-400 mt-0.5">Celebration delivered</p>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Booking Details Grid -->
                <div class="p-5 md:p-8 grid grid-cols-1 md:grid-cols-2 gap-6 text-left">
                    
                    <!-- Left Details Column -->
                    <div class="space-y-4">
                        <div>
                            <span class="text-[10px] font-heading font-bold uppercase tracking-wider text-gray-400 block mb-1">Customer Information</span>
                            <div class="bg-gray-50 dark:bg-[#1A1A1E] p-3.5 rounded-xl border border-gray-200 dark:border-white/10">
                                <p class="text-xs font-bold text-[#1E1E24] dark:text-white mb-0.5">{{ $booking['name'] }}</p>
                                <p class="text-[11px] text-gray-500 dark:text-gray-400 flex items-center gap-1.5">
                                    <i class="fa-solid fa-phone text-[9px] text-gold"></i> {{ $booking['mobile'] }}
                                </p>
                                @if(!empty($booking['email']))
                                    <p class="text-[11px] text-gray-500 dark:text-gray-400 flex items-center gap-1.5 mt-0.5">
                                        <i class="fa-solid fa-envelope text-[9px] text-gold"></i> {{ $booking['email'] }}
                                    </p>
                                @endif
                            </div>
                        </div>

                        <div>
                            <span class="text-[10px] font-heading font-bold uppercase tracking-wider text-gray-400 block mb-1">Event Specs & Schedule</span>
                            <div class="bg-gray-50 dark:bg-[#1A1A1E] p-3.5 rounded-xl border border-gray-200 dark:border-white/10 space-y-1.5">
                                <div class="flex justify-between items-center text-xs">
                                    <span class="text-gray-500 dark:text-gray-400">Package:</span>
                                    <span class="font-bold text-[#1E1E24] dark:text-white">{{ $booking['package'] }}</span>
                                </div>
                                <div class="flex justify-between items-center text-xs">
                                    <span class="text-gray-500 dark:text-gray-400">Category / Tier:</span>
                                    <span class="font-semibold text-gold">{{ $booking['category'] ?? 'Event Setup' }} ({{ $booking['tier'] ?? 'Standard' }})</span>
                                </div>
                                <div class="flex justify-between items-center text-xs">
                                    <span class="text-gray-500 dark:text-gray-400">Event Date & Time:</span>
                                    <span class="font-bold text-[#1E1E24] dark:text-white">{{ $booking['date'] }} at {{ $booking['time'] }}</span>
                                </div>
                                <div class="flex justify-between items-center text-xs">
                                    <span class="text-gray-500 dark:text-gray-400">Guest Count:</span>
                                    <span class="font-bold text-[#1E1E24] dark:text-white">{{ $booking['guest_count'] ?? 25 }} Guests</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Right Venue & Payment Column -->
                    <div class="space-y-4">
                        <div>
                            <span class="text-[10px] font-heading font-bold uppercase tracking-wider text-gray-400 block mb-1">Venue Address</span>
                            <div class="bg-gray-50 dark:bg-[#1A1A1E] p-3.5 rounded-xl border border-gray-200 dark:border-white/10 text-xs">
                                <p class="font-medium text-[#1E1E24] dark:text-white leading-relaxed">
                                    {{ $booking['address'] }}
                                </p>
                                <p class="text-gray-500 dark:text-gray-400 text-[11px] mt-1">
                                    {{ $booking['area'] }}, {{ $booking['city'] ?? 'Indore' }} - {{ $booking['pincode'] }}
                                </p>
                                @if(!empty($booking['landmark']))
                                    <p class="text-[10px] text-gold mt-1 font-semibold">
                                        Landmark: {{ $booking['landmark'] }}
                                    </p>
                                @endif
                            </div>
                        </div>

                        <div>
                            <span class="text-[10px] font-heading font-bold uppercase tracking-wider text-gray-400 block mb-1">Payment & Pricing</span>
                            <div class="bg-gray-50 dark:bg-[#1A1A1E] p-3.5 rounded-xl border border-gray-200 dark:border-white/10 flex items-center justify-between">
                                <div>
                                    <span class="text-[10px] text-gray-500 dark:text-gray-400 uppercase tracking-wider block">Total Estimated Amount</span>
                                    <span class="font-heading font-black text-lg text-[#1E1E24] dark:text-white">₹{{ number_format($booking['total'] ?? $booking['package_price'] ?? 4999) }}</span>
                                </div>
                                <span class="text-[9px] font-heading font-extrabold uppercase tracking-widest text-emerald-600 dark:text-emerald-400 bg-emerald-500/10 px-2.5 py-1 rounded-full border border-emerald-500/20">
                                    100% Offline Payment
                                </span>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        @endif

        <!-- Action Links -->
        <div class="mt-8 text-center flex flex-wrap items-center justify-center gap-4 text-xs font-medium">
            <a href="{{ route('home') }}" class="text-gray-500 hover:text-gold transition-colors inline-flex items-center gap-1.5">
                <i class="fa-solid fa-arrow-left text-[10px]"></i> Return to Home
            </a>
            <span class="text-gray-300 dark:text-gray-700">•</span>
            <a href="{{ route('contact.index') }}" class="text-gray-500 hover:text-gold transition-colors inline-flex items-center gap-1.5">
                <i class="fa-solid fa-headset text-[10px]"></i> Contact Artizen Planner
            </a>
        </div>

    </div>
</div>
@endsection
