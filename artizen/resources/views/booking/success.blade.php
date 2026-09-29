@extends('layouts.app')

@section('content')
<div class="min-h-screen pt-8 pb-16 px-4 md:px-12 bg-white dark:bg-[#0c0c0c] text-black dark:text-white relative flex items-center justify-center">
    <div class="max-w-3xl w-full mx-auto">

        <div class="bg-white dark:bg-[#121212] border border-gray-200 dark:border-white/10 p-8 md:p-12 rounded-3xl shadow-xl text-center flex flex-col items-center gap-6">
            
            <!-- Success Icon -->
            <div class="w-20 h-20 rounded-full bg-emerald-50 dark:bg-emerald-950/50 border border-emerald-200 dark:border-emerald-900/50 text-emerald-600 dark:text-emerald-400 flex items-center justify-center text-4xl shadow-sm">
                <i class="fa-solid fa-circle-check"></i>
            </div>

            <!-- Header Title -->
            <div>
                <span class="text-[10px] font-heading font-extrabold uppercase tracking-widest text-amber-600 dark:text-amber-400 mb-2 inline-flex items-center gap-1.5 bg-amber-50 dark:bg-amber-950/40 px-3 py-1 rounded-full border border-amber-200 dark:border-amber-900/50">
                    BOOKING REQUEST SUBMITTED
                </span>
                <h1 class="font-heading font-extrabold text-3xl md:text-4xl uppercase tracking-tight text-gray-900 dark:text-white leading-tight">
                    Thank You, {{ $booking['name'] ?? 'Customer' }}!
                </h1>
                <p class="text-sm text-gray-600 dark:text-gray-300 max-w-lg mx-auto mt-2">
                    Your booking request has been registered in the Artizen system. Our team is currently reviewing your event requirements and date availability.
                </p>
            </div>

            <!-- Booking Details Card -->
            <div class="w-full bg-gray-50 dark:bg-white/[0.03] border border-gray-200 dark:border-white/10 p-6 rounded-2xl text-left space-y-4">
                <div class="flex items-center justify-between border-b border-gray-200 dark:border-white/10 pb-3">
                    <div>
                        <span class="text-[10px] font-bold text-gray-500 uppercase tracking-wider block">Booking Reference ID</span>
                        <span class="font-heading font-black text-2xl text-amber-600 dark:text-amber-400">{{ $booking['id'] ?? 'BK-8844' }}</span>
                    </div>
                    <div class="text-right">
                        <span class="text-[10px] font-bold text-gray-500 uppercase tracking-wider block mb-1">Current Status</span>
                        <span class="px-3 py-1 bg-amber-100 dark:bg-amber-950/60 text-amber-800 dark:text-amber-300 text-xs font-black uppercase tracking-wider rounded-full border border-amber-300 dark:border-amber-800">
                            {{ $booking['status'] ?? 'PENDING' }}
                        </span>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-xs font-medium text-gray-700 dark:text-gray-300">
                    <div>
                        <span class="text-gray-500 block">Package Selected:</span>
                        <span class="font-bold text-gray-900 dark:text-white">{{ $booking['package'] ?? 'Celebration Setup' }}</span>
                    </div>

                    <div>
                        <span class="text-gray-500 block">Event Date & Time:</span>
                        <span class="font-bold text-gray-900 dark:text-white">{{ $booking['date'] ?? 'Upcoming' }} ({{ $booking['time'] ?? 'Evening' }})</span>
                    </div>

                    <div>
                        <span class="text-gray-500 block">Venue Location:</span>
                        <span class="font-bold text-gray-900 dark:text-white">{{ $booking['area'] ?? '' }}, {{ $booking['city'] ?? 'Indore' }}</span>
                    </div>

                    <div>
                        <span class="text-gray-500 block">Total Amount (Offline Pay):</span>
                        <span class="font-bold text-emerald-600 dark:text-emerald-400 text-sm">₹{{ number_format($booking['total'] ?? 4999) }}</span>
                    </div>
                </div>
            </div>

            <!-- WhatsApp Action Callout -->
            @php
                $imgUrl = $booking['image'] ?? '/assets/images/hero/1.jpg';
                if (!str_starts_with($imgUrl, 'http')) {
                    $imgUrl = url($imgUrl);
                }

                $msgLines = [
                    "Hi Artizen Team! I just submitted an event booking request on the portal.",
                    "",
                    "📌 Booking ID: " . ($booking['id'] ?? 'BK-XXXX'),
                    "🎉 Package: " . ($booking['package'] ?? 'Setup Package'),
                    "📅 Date & Time: " . ($booking['date'] ?? '') . " (" . ($booking['time'] ?? 'Evening') . ")",
                    "📍 Location: " . ($booking['area'] ?? '') . ", " . ($booking['city'] ?? 'Indore'),
                    "💰 Amount: ₹" . number_format($booking['total'] ?? 4999),
                    "📷 Setup Photo: " . $imgUrl,
                    "",
                    "Please confirm availability for my date."
                ];

                $waText = urlencode(implode("\n", $msgLines));
                $waLink = "https://wa.me/919131668156?text=" . $waText;
            @endphp
            
            <div class="w-full flex flex-col sm:flex-row items-center justify-center gap-3 pt-2">
                <a href="{{ $waLink }}" target="_blank" class="w-full sm:w-auto px-5 py-3.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-extrabold uppercase tracking-wider rounded-xl shadow-lg transition-all flex items-center justify-center gap-2">
                    <i class="fa-brands fa-whatsapp text-base"></i> Instant WhatsApp Confirmation
                </a>

                <a href="{{ route('booking.track', ['id' => $booking['id'] ?? '']) }}" class="w-full sm:w-auto px-5 py-3.5 bg-[#EA741D] hover:bg-[#D6630F] text-white text-xs font-extrabold uppercase tracking-wider rounded-xl shadow-md transition-all flex items-center justify-center gap-2">
                    <i class="fa-solid fa-magnifying-glass text-xs"></i> Track Live Status
                </a>

                <a href="{{ route('events.index') }}" class="w-full sm:w-auto px-5 py-3.5 bg-gray-100 hover:bg-gray-200 text-gray-900 dark:bg-white/10 dark:hover:bg-white/15 dark:text-white border border-gray-200 dark:border-white/10 text-xs font-extrabold uppercase tracking-wider rounded-xl transition-all">
                    Explore Packages
                </a>
            </div>

        </div>

    </div>
</div>
@endsection
