@extends('layouts.app')

@section('title', 'Order History & Booking Details - ARTIZEN Indore')

@push('styles')
<style>
@media print {
    header, footer, nav, .site-header, .site-footer, #artizen-planner-widget, .no-print, [aria-label="Breadcrumb"] {
        display: none !important;
    }
    body {
        background-color: #ffffff !important;
        color: #111111 !important;
        font-size: 12pt !important;
        padding: 0 !important;
        margin: 0 !important;
    }
    .print-container {
        border: none !important;
        box-shadow: none !important;
        padding: 0 !important;
        margin: 0 !important;
        max-width: 100% !important;
        width: 100% !important;
    }
}
</style>
@endpush

@section('content')
<div class="min-h-screen pt-8 pb-20 px-4 md:px-8 bg-[#FAF9F5] text-main-text">
    <div class="max-w-xl mx-auto">

        <!-- Page Header -->
        <div class="text-left mb-6">
            <span class="text-[11px] font-bold tracking-widest uppercase text-[#888888] block">ARTIZEN INDORE</span>
            <h1 class="font-heading font-extrabold text-xl sm:text-2xl text-main-text tracking-tight mt-0.5">
                Order History &amp; Booking Details
            </h1>
            <p class="text-xs text-[#666666] mt-0.5">
                Check your event setup status, review order specifics, or download your receipt.
            </p>
        </div>

        @if (session('error'))
            <div class="mb-5 p-3.5 rounded-xl bg-red-50 border border-red-200 text-red-800 text-xs font-medium">
                {{ session('error') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="mb-5 p-3.5 rounded-xl bg-red-50 border border-red-200 text-red-800 text-xs font-medium">
                <ul class="list-disc list-inside space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if (isset($booking) && $booking)
            <!-- Found Booking Details (Clean Receipt Style) -->
            <div id="receipt-content" class="print-container border border-[#E8E5DF] bg-white p-6 sm:p-8 rounded-2xl shadow-sm text-left">
                
                <!-- Receipt Header -->
                <div class="pb-5 border-b border-[#E8E5DF] flex items-start justify-between gap-4">
                    <div>
                        <span class="text-[10px] uppercase font-bold text-[#888888] block">Order / Booking ID</span>
                        <span class="font-mono font-bold text-lg sm:text-xl text-main-text tracking-wide">{{ $booking['id'] }}</span>
                        <p class="text-xs text-[#666666] mt-0.5">Booked on {{ $booking['created_at'] ?? 'N/A' }}</p>
                    </div>

                    @php
                        $rawStatus = strtolower(trim($booking['status'] ?? 'pending'));
                        if (in_array($rawStatus, ['approved', 'confirmed', 'completed'])) {
                            $displayStatus = 'Approved';
                            $statusBadgeClass = 'bg-emerald-50 text-emerald-800 border border-emerald-200';
                        } elseif (in_array($rawStatus, ['rejected', 'cancelled', 'canceled'])) {
                            $displayStatus = 'Rejected';
                            $statusBadgeClass = 'bg-red-50 text-red-800 border border-red-200';
                        } else {
                            $displayStatus = 'Pending';
                            $statusBadgeClass = 'bg-neutral-100 text-neutral-800 border border-[#D4D4D8]';
                        }
                    @endphp
                    <div class="text-right shrink-0">
                        <span class="text-[10px] uppercase font-bold text-[#888888] block mb-1">Status</span>
                        <span class="inline-block px-3 py-0.5 rounded-full text-xs font-semibold {{ $statusBadgeClass }}">
                            {{ $displayStatus }}
                        </span>
                    </div>
                </div>

                <!-- Package Details -->
                <div class="py-4 border-b border-[#E8E5DF]">
                    <span class="text-[10px] uppercase font-bold text-[#888888] block mb-2">Package Details</span>
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <h2 class="font-heading font-bold text-base text-main-text leading-snug">
                                {{ $booking['package'] ?? 'Celebration Package' }}
                            </h2>
                            <span class="text-xs text-[#666666] block mt-0.5">
                                Tier: <strong class="text-main-text font-semibold">{{ $booking['tier'] ?? 'Standard Setup' }}</strong>
                                @if(!empty($booking['category']))
                                    &bull; {{ $booking['category'] }}
                                @endif
                            </span>
                        </div>
                        <div class="text-right shrink-0">
                            <span class="text-sm font-bold text-main-text">₹{{ number_format($booking['package_price'] ?? $booking['total'] ?? 4999) }}</span>
                        </div>
                    </div>

                    <!-- Add-ons itemized list (if any) -->
                    @if(!empty($booking['addons']) && is_array($booking['addons']) && count($booking['addons']) > 0)
                        <div class="mt-3 pt-3 border-t border-[#F0EFEB] space-y-1.5 text-xs">
                            <span class="text-[10px] font-bold text-[#888888] block uppercase">Selected Add-ons</span>
                            @foreach($booking['addons'] as $addon)
                                <div class="flex items-center justify-between text-neutral-700">
                                    <span>{{ $addon['name'] ?? 'Add-on' }}</span>
                                    <span class="font-medium text-main-text">₹{{ number_format($addon['price'] ?? 0) }}</span>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                <!-- Event Schedule & Venue Grid -->
                <div class="py-4 border-b border-[#E8E5DF] grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                    <!-- Schedule -->
                    <div>
                        <span class="text-[10px] uppercase font-bold text-[#888888] block mb-1.5">Event Schedule</span>
                        <div class="space-y-1 text-neutral-700">
                            <div><span class="text-[#888888]">Date:</span> <strong class="text-main-text">{{ date('l, d M Y', strtotime($booking['date'] ?? date('Y-m-d'))) }}</strong></div>
                            <div><span class="text-[#888888]">Slot:</span> <strong class="text-main-text">{{ $booking['time'] ?? 'Evening' }}</strong></div>
                            <div><span class="text-[#888888]">Guests:</span> <strong class="text-main-text">{{ $booking['guest_count'] ?? 30 }} People</strong></div>
                        </div>
                    </div>

                    <!-- Venue & Customer -->
                    <div>
                        <span class="text-[10px] uppercase font-bold text-[#888888] block mb-1.5">Venue &amp; Customer</span>
                        <div class="space-y-1 text-neutral-700">
                            <div><span class="text-[#888888]">Name:</span> <strong class="text-main-text">{{ $booking['name'] ?? 'Customer' }}</strong></div>
                            <div><span class="text-[#888888]">Phone:</span> <strong class="text-main-text">+91 {{ $booking['mobile'] ?? ($booking['customer_mobile'] ?? '') }}</strong></div>
                            <div><span class="text-[#888888]">Address:</span> <strong class="text-main-text">{{ $booking['address'] ?? 'Indore' }}, {{ $booking['area'] ?? '' }}</strong></div>
                            @if(!empty($booking['pincode']))
                                <div><span class="text-[#888888]">Pincode:</span> <strong class="text-main-text">{{ $booking['pincode'] }}, Indore MP</strong></div>
                            @endif
                        </div>
                    </div>
                </div>

                @if(!empty($booking['notes']))
                    <div class="py-3 border-b border-[#E8E5DF] text-xs">
                        <span class="text-[10px] uppercase font-bold text-[#888888] block mb-1">Customer Notes</span>
                        <p class="text-neutral-700 italic">{{ $booking['notes'] }}</p>
                    </div>
                @endif

                <!-- Settlement Total -->
                <div class="pt-4 flex items-baseline justify-between">
                    <div>
                        <span class="font-bold text-xs uppercase text-main-text block">Total Settlement</span>
                        <span class="text-[11px] text-[#777777]">Offline payment upon setup completion</span>
                    </div>
                    <div class="text-right">
                        <span class="font-heading font-extrabold text-xl sm:text-2xl text-main-text">
                            ₹{{ number_format($booking['total'] ?? 4999) }}
                        </span>
                    </div>
                </div>

            </div>

            <!-- Action Buttons (Hidden from Print / PDF) -->
            @php
                $waMsgTrack = "Hi Artizen! I have an inquiry about my booking ID: " . ($booking['id'] ?? 'BK-XXXX') . " (" . ($booking['package'] ?? 'Package') . ")";
                $waUrlTrack = "https://wa.me/919131668156?text=" . urlencode($waMsgTrack);
            @endphp
            <div class="no-print mt-5 flex flex-wrap items-center justify-between gap-3">
                <div class="flex items-center gap-2 w-full sm:w-auto">
                    <button type="button" onclick="downloadBookingPDF()" id="download-pdf-btn"
                        class="flex-1 sm:flex-initial px-5 py-2.5 bg-neutral-900 hover:bg-black text-white text-xs font-bold rounded-lg transition-colors text-center cursor-pointer shadow-sm">
                        Download PDF
                    </button>
                    <a href="{{ $waUrlTrack }}" target="_blank"
                        class="flex-1 sm:flex-initial px-4 py-2.5 bg-[#25D366] hover:bg-[#20bd5a] text-white text-xs font-bold rounded-lg transition-colors text-center inline-flex items-center justify-center gap-1.5 cursor-pointer shadow-sm">
                        <i class="fa-brands fa-whatsapp text-sm"></i> WhatsApp Support
                    </a>
                </div>

                <a href="{{ route('events.index') }}"
                    class="text-xs font-semibold text-neutral-600 hover:text-black transition-colors w-full sm:w-auto text-center sm:text-right">
                    Explore Packages &rarr;
                </a>
            </div>
        @endif

        <!-- Order Search Form Section -->
        <div id="search-form-section" class="{{ (isset($booking) && $booking) ? 'hidden mt-6' : '' }} border border-[#E8E5DF] bg-white p-6 sm:p-7 rounded-2xl shadow-sm text-left">
            <span class="text-[10px] uppercase font-bold text-[#888888] block mb-1">Lookup Booking</span>
            <h2 class="font-heading font-bold text-base text-main-text mb-4">
                Find Your Booking by Order ID &amp; Phone
            </h2>

            <form action="{{ route('booking.track.search') }}" method="POST" class="space-y-3.5" novalidate>
                @csrf

                <div>
                    <label for="track-booking-id" class="block text-xs font-semibold text-main-text mb-1">
                        Booking ID (Order ID) *
                    </label>
                    <input type="text" id="track-booking-id" name="booking_id" value="{{ old('booking_id', $bookingId ?? '') }}" required placeholder="e.g. BK-9366"
                        class="w-full px-3.5 py-2.5 bg-[#FAF9F5] border border-[#E8E5DF] rounded-xl text-xs font-mono font-bold text-main-text placeholder-neutral-400 focus:outline-none focus:border-black focus:bg-white transition-colors">
                </div>

                <div>
                    <label for="track-mobile" class="block text-xs font-semibold text-main-text mb-1">
                        Registered Mobile Number *
                    </label>
                    <input type="tel" id="track-mobile" name="mobile" value="{{ old('mobile') }}" required placeholder="e.g. 9811223344"
                        class="w-full px-3.5 py-2.5 bg-[#FAF9F5] border border-[#E8E5DF] rounded-xl text-xs text-main-text placeholder-neutral-400 focus:outline-none focus:border-black focus:bg-white transition-colors">
                </div>

                <button type="submit" class="w-full py-2.5 bg-neutral-900 hover:bg-black text-white text-xs font-bold rounded-xl transition-colors cursor-pointer mt-2 shadow-sm">
                    Search Booking Details
                </button>
            </form>
        </div>

    </div>
</div>

<!-- jsPDF for instant, 100% reliable PDF download -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script>
function downloadBookingPDF() {
    const btn = document.getElementById('download-pdf-btn');
    const origText = btn ? btn.innerText : 'Download PDF';
    if (btn) btn.innerText = 'Generating PDF...';

    try {
        if (typeof window.jspdf === 'undefined') {
            window.print();
            if (btn) btn.innerText = origText;
            return;
        }

        const { jsPDF } = window.jspdf;
        const doc = new jsPDF({ unit: 'pt', format: 'a4' });

        const black = [24, 24, 27];
        const gray = [110, 110, 115];
        const lightLine = [225, 222, 218];

        let y = 50;

        // Header
        doc.setTextColor(...gray);
        doc.setFont('helvetica', 'bold');
        doc.setFontSize(9);
        doc.text('ARTIZEN INDORE  |  EVENT BOOKING RECEIPT', 40, y);
        y += 20;

        doc.setTextColor(...black);
        doc.setFontSize(18);
        doc.text('Booking Confirmation', 40, y);
        y += 15;

        // Divider
        doc.setDrawColor(...lightLine);
        doc.setLineWidth(1);
        doc.line(40, y, 555, y);
        y += 25;

        // Order ID & Status
        doc.setFontSize(10);
        doc.setFont('helvetica', 'bold');
        doc.text('Order ID:', 40, y);
        doc.setFont('helvetica', 'normal');
        doc.text('{{ $booking["id"] ?? "BK-XXXX" }}', 120, y);

        doc.setFont('helvetica', 'bold');
        doc.text('Status:', 350, y);
        doc.setFont('helvetica', 'normal');
        doc.text('{{ $displayStatus ?? "Pending" }}', 410, y);
        y += 18;

        doc.setFont('helvetica', 'bold');
        doc.text('Booking Date:', 40, y);
        doc.setFont('helvetica', 'normal');
        doc.text('{{ $booking["created_at"] ?? date("d M Y") }}', 120, y);
        y += 25;

        // Divider
        doc.line(40, y, 555, y);
        y += 25;

        // Package Details
        doc.setFont('helvetica', 'bold');
        doc.setFontSize(12);
        doc.text('Package Details', 40, y);
        y += 18;

        doc.setFontSize(10);
        doc.setFont('helvetica', 'normal');
        doc.text('{{ addslashes($booking["package"] ?? "Celebration Package") }} ({{ addslashes($booking["tier"] ?? "Standard Setup") }})', 40, y);
        doc.text('Rs. {{ number_format($booking["package_price"] ?? $booking["total"] ?? 4999) }}', 555, y, { align: 'right' });
        y += 20;

        @if(!empty($booking['addons']) && is_array($booking['addons']) && count($booking['addons']) > 0)
            doc.setFont('helvetica', 'bold');
            doc.text('Selected Add-ons:', 40, y);
            y += 16;
            doc.setFont('helvetica', 'normal');
            @foreach($booking['addons'] as $addon)
                doc.text('+ {{ addslashes($addon["name"] ?? "Add-on") }}', 55, y);
                doc.text('Rs. {{ number_format($addon["price"] ?? 0) }}', 555, y, { align: 'right' });
                y += 16;
            @endforeach
            y += 8;
        @endif

        // Schedule & Venue
        doc.line(40, y, 555, y);
        y += 25;

        doc.setFont('helvetica', 'bold');
        doc.setFontSize(12);
        doc.text('Event Schedule & Venue', 40, y);
        y += 18;

        doc.setFontSize(10);
        doc.setFont('helvetica', 'normal');
        doc.text('Event Date: {{ date("l, d M Y", strtotime($booking["date"] ?? date("Y-m-d"))) }} ({{ $booking["time"] ?? "Evening" }})', 40, y);
        y += 16;
        doc.text('Expected Guests: {{ $booking["guest_count"] ?? 30 }} People', 40, y);
        y += 16;
        doc.text('Customer: {{ addslashes($booking["name"] ?? "Customer") }} (+91 {{ $booking["mobile"] ?? "" }})', 40, y);
        y += 16;
        doc.text('Venue Address: {{ addslashes($booking["address"] ?? "Indore") }}, {{ addslashes($booking["area"] ?? "") }}, Indore MP {{ $booking["pincode"] ?? "" }}', 40, y);
        y += 25;

        // Total
        doc.line(40, y, 555, y);
        y += 25;

        doc.setFont('helvetica', 'bold');
        doc.setFontSize(13);
        doc.text('TOTAL SETTLEMENT:', 40, y);
        doc.text('Rs. {{ number_format($booking["total"] ?? 4999) }}', 555, y, { align: 'right' });
        y += 18;

        doc.setFont('helvetica', 'normal');
        doc.setFontSize(9);
        doc.setTextColor(...gray);
        doc.text('Payment terms: Settle offline upon event setup completion. No advance payment required.', 40, y);
        y += 30;

        // Footer
        doc.line(40, y, 555, y);
        y += 18;
        doc.text('ARTIZEN Indore | Support & Hotline: +91 91316 68156 | support@artizen.events', 40, y);

        // Save file directly
        doc.save('Artizen-{{ $booking["id"] ?? "Booking" }}.pdf');
    } catch (err) {
        console.error('PDF export fallback:', err);
        window.print();
    } finally {
        if (btn) btn.innerText = origText;
    }
}
</script>
@endsection
