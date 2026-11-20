@extends('layouts.app')

@section('title', 'Order History & Booking Details - ARTIZEN Indore')

@push('styles')
<style>
@media print {
    @page {
        size: A4 portrait;
        margin: 5mm 8mm 5mm 8mm;
    }
    html, body {
        background-color: #ffffff !important;
        background: #ffffff !important;
        color: #111827 !important;
        font-size: 10pt !important;
        line-height: 1.35 !important;
        margin: 0 !important;
        padding: 0 !important;
        width: 100% !important;
        height: 100% !important;
        -webkit-print-color-adjust: exact !important;
        print-color-adjust: exact !important;
    }
    header, footer, nav, .site-header, .site-footer, #artizen-planner-widget, .no-print, [aria-label="Breadcrumb"], #artizen-cookie-consent, #receipt-content, #search-form-section {
        display: none !important;
    }
    .min-h-screen {
        min-height: auto !important;
        padding: 0 !important;
        background: #ffffff !important;
    }
    #artizen-print-page {
        display: flex !important;
        flex-direction: column !important;
        justify-content: space-between !important;
        width: 100% !important;
        height: 268mm !important;
        max-height: 268mm !important;
        margin: 0 !important;
        padding: 2mm 3mm !important;
        box-sizing: border-box !important;
        overflow: hidden !important;
        page-break-inside: avoid !important;
        page-break-after: avoid !important;
        break-inside: avoid !important;
    }
}
@media screen {
    #artizen-print-page {
        display: none !important;
    }
}
</style>
@endpush

@section('content')
<style>
@media screen {
    #artizen-print-page {
        display: none !important;
    }
}
@media print {
    #artizen-print-page {
        display: flex !important;
    }
}
</style>

<!-- Artizen PDF Engines (Standalone html2canvas + jsPDF - direct 1-page download) -->
<script src="{{ asset('js/html2canvas.min.js') }}"></script>
<script src="{{ asset('js/jspdf.umd.min.js') }}"></script>
<script src="{{ asset('js/artizen-pdf.js') }}?v=17.0"></script>
<script>
window.downloadBookingPDF = function(btn) {
    const trigger = btn || document.getElementById('download-pdf-btn');
    const booking = @json($booking ?? []);
    if (window.ArtizenPDF && typeof window.ArtizenPDF.download === 'function') {
        window.ArtizenPDF.download(booking, trigger);
    } else {
        console.warn('ArtizenPDF is loading, retrying in 200ms...');
        setTimeout(function() {
            if (window.ArtizenPDF && typeof window.ArtizenPDF.download === 'function') {
                window.ArtizenPDF.download(booking, trigger);
            }
        }, 200);
    }
};
var downloadBookingPDF = window.downloadBookingPDF;

document.addEventListener('DOMContentLoaded', function() {
    const btn = document.getElementById('download-pdf-btn');
    if (btn) {
        btn.onclick = function(e) {
            e.preventDefault();
            window.downloadBookingPDF(btn);
        };
    }
});
</script>
<div class="min-h-screen pt-8 pb-20 px-4 md:px-8 bg-[#FAF9F5] text-main-text">
    <div class="max-w-xl mx-auto">

        <!-- Page Header -->
        <div class="no-print text-left mb-6">
            <span class="text-[11px] font-bold tracking-widest uppercase text-[#888888] block">ARTIZEN INDORE</span>
            <h1 class="font-heading font-extrabold text-xl sm:text-2xl text-main-text tracking-tight mt-0.5">
                Order History &amp; Booking Details
            </h1>
            <p class="text-xs text-[#666666] mt-0.5">
                Check your event setup status, review order specifics, or download your receipt.
            </p>
        </div>

        @if (session('error'))
            <div class="no-print mb-5 p-3.5 rounded-xl bg-red-50 border border-red-200 text-red-800 text-xs font-medium">
                {{ session('error') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="no-print mb-5 p-3.5 rounded-xl bg-red-50 border border-red-200 text-red-800 text-xs font-medium">
                <ul class="list-disc list-inside space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if (isset($booking) && $booking)
            @php
                $rawStatus = strtolower(trim($booking['status'] ?? 'pending'));
                if (in_array($rawStatus, ['approved', 'confirmed', 'completed'])) {
                    $displayStatus = 'Approved';
                    $statusBadgeClass = 'bg-emerald-50 text-emerald-800 border border-emerald-200';
                    $printStatusBg = 'bg-[#ECFDF5] border-[#A7F3D0] text-[#065F46]';
                    $printStatusSub = 'text-[#047857]';
                    $printStatusIcon = '<svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#059669" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6L9 17l-5-5"></path></svg>';
                } elseif (in_array($rawStatus, ['rejected', 'cancelled', 'canceled'])) {
                    $displayStatus = 'Rejected';
                    $statusBadgeClass = 'bg-red-50 text-red-800 border border-red-200';
                    $printStatusBg = 'bg-[#FEF2F2] border-[#FECACA] text-[#991B1B]';
                    $printStatusSub = 'text-[#B91C1C]';
                    $printStatusIcon = '<svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#DC2626" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>';
                } else {
                    $displayStatus = 'Pending Review';
                    $statusBadgeClass = 'bg-neutral-100 text-neutral-800 border border-[#D4D4D8]';
                    $printStatusBg = 'bg-white border-[1.5px] border-[#E5A93C] text-[#78350F]';
                    $printStatusSub = 'text-[#92400E]';
                    $printStatusIcon = '<svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#D97706" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 22h14"></path><path d="M5 2h14"></path><path d="M17 22v-4.172a2 2 0 0 0-.586-1.414L12 12l-4.414 4.414A2 2 0 0 0 7 17.828V22"></path><path d="M7 2v4.172a2 2 0 0 0 .586 1.414L12 12l4.414-4.414A2 2 0 0 0 17 6.172V2"></path></svg>';
                }

                $basePrice = (float)($booking['package_price'] ?? $booking['price'] ?? $booking['total'] ?? 4999);
                $totalAmount = (float)($booking['total'] ?? $booking['total_amount'] ?? $basePrice);
                $surcharge = (float)($booking['surcharge'] ?? $booking['surcharge_amount'] ?? max(0, $totalAmount - $basePrice));

                $rawDate = $booking['created_at'] ?? now();
                $formattedBookingDate = is_numeric($rawDate) ? date('d M Y', $rawDate) : (strtotime($rawDate) ? date('d M Y', strtotime($rawDate)) : (string)$rawDate);

                $rawEventDate = $booking['date'] ?? date('Y-m-d');
                $formattedEventDate = strtotime($rawEventDate) ? date('d M Y', strtotime($rawEventDate)) : (string)$rawEventDate;
                $eventTime = $booking['time'] ?? '18:00';
                $guestCount = $booking['guest_count'] ?? 25;

                $address = $booking['address'] ?? 'Indore, Madhya Pradesh';
                $landmark = $booking['landmark'] ?? '';
                $area = $booking['area'] ?? '';
                $pincode = $booking['pincode'] ?? '';
                $city = $booking['city'] ?? 'Indore';
                $state = $booking['state'] ?? 'Madhya Pradesh';
                
                $venueParts = array_filter([$address, $landmark ? 'Near ' . $landmark : '', $area, $city . ', ' . $state, $pincode]);
                $fullVenue = implode(', ', $venueParts) ?: 'Indore, Madhya Pradesh';

                $customerMobile = $booking['mobile'] ?? ($booking['customer_mobile'] ?? 'N/A');
                $customerWhatsapp = $booking['whatsapp'] ?? ($booking['customer_whatsapp'] ?? $customerMobile);
                $customerEmail = $booking['email'] ?? ($booking['customer_email'] ?? 'Not provided');
            @endphp

            <!-- Found Booking Details (On-Screen Receipt Style) -->
            <div id="receipt-content" class="print-container border border-[#E8E5DF] bg-white p-6 sm:p-8 rounded-2xl shadow-sm text-left">
                
                <!-- Receipt Header -->
                <div class="pb-5 border-b border-[#E8E5DF] flex items-start justify-between gap-4">
                    <div>
                        <span class="text-[10px] uppercase font-bold text-[#888888] block">Order / Booking ID</span>
                        <span class="font-mono font-bold text-lg sm:text-xl text-main-text tracking-wide">{{ $booking['id'] }}</span>
                        <p class="text-xs text-[#666666] mt-0.5">Booked on {{ $formattedBookingDate }}</p>
                    </div>

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
                            <span class="text-sm font-bold text-main-text">₹{{ number_format($basePrice) }}</span>
                        </div>
                    </div>

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
                            <div><span class="text-[#888888]">Date:</span> <strong class="text-main-text">{{ $formattedEventDate }}</strong></div>
                            <div><span class="text-[#888888]">Slot:</span> <strong class="text-main-text">{{ $eventTime }}</strong></div>
                            <div><span class="text-[#888888]">Guests:</span> <strong class="text-main-text">{{ $guestCount }} People</strong></div>
                        </div>
                    </div>

                    <!-- Venue & Customer -->
                    <div>
                        <span class="text-[10px] uppercase font-bold text-[#888888] block mb-1.5">Venue &amp; Customer</span>
                        <div class="space-y-1 text-neutral-700">
                            <div><span class="text-[#888888]">Name:</span> <strong class="text-main-text">{{ $booking['name'] ?? 'Customer' }}</strong></div>
                            <div><span class="text-[#888888]">Phone:</span> <strong class="text-main-text">+91 {{ $customerMobile }}</strong></div>
                            <div><span class="text-[#888888]">Address:</span> <strong class="text-main-text">{{ $booking['address'] ?? 'Indore' }}, {{ $booking['area'] ?? '' }}</strong></div>
                            @if(!empty($booking['pincode']))
                                <div><span class="text-[#888888]">Pincode:</span> <strong class="text-main-text">{{ $booking['pincode'] }}, {{ $city }}</strong></div>
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
                            ₹{{ number_format($totalAmount) }}
                        </span>
                    </div>
                </div>

            </div>

            <!-- Action Buttons (Hidden from Print / PDF) -->
            @php
                $trackIdVal = $booking['id'] ?? 'BK-XXXX';
                $pdfUrlTrack = route('booking.success', ['id' => $trackIdVal, 'download_pdf' => 1]);
                $waMsgTrack = "👋 *Hi Artizen!* I have an update/inquiry regarding my booking:\n"
                    . "📋 *Booking ID:* #" . $trackIdVal . "\n"
                    . "👤 *Customer:* " . ($booking['name'] ?? 'Customer') . "\n"
                    . "🎉 *Package:* " . ($booking['package'] ?? 'Package') . "\n"
                    . "📅 *Date:* " . $formattedEventDate . " • " . trim(trim($eventTime), '()') . "\n"
                    . "💰 *Total:* ₹" . number_format($totalAmount) . "\n"
                    . "📄 *Invoice PDF:* " . $pdfUrlTrack;
                $waUrlTrack = "https://wa.me/919131668156?text=" . urlencode($waMsgTrack);
            @endphp
            <div class="no-print mt-5 flex flex-wrap items-center justify-between gap-3">
                <div class="flex items-center gap-2 w-full sm:w-auto">
                    <button type="button" onclick="window.downloadBookingPDF(this)" id="download-pdf-btn"
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

            <!-- Print-Only 1-Page A4 Invoice (Strictly hidden on screen) -->
            <div id="artizen-print-page" style="display: none !important;" class="bg-white text-[#111827]">
                <div>
                    <!-- 1. Header (Official Logo on Left, Invoice Details on Right) -->
                    <div class="flex justify-between items-start">
                        <div class="flex items-center">
                            <img src="{{ asset('assets/images/logo/artizen.png') }}" alt="Artizen Logo" class="h-20 w-auto object-contain shrink-0">
                        </div>

                        <!-- Block flow to eliminate flex collision -->
                        <div class="text-right min-w-[260px]">
                            <div class="text-2xl font-black text-[#111827] leading-tight mb-2 tracking-tight">Booking Invoice</div>
                            <div class="text-[12.5px] text-[#4B5563] mb-1.5 leading-normal">
                                Invoice No: <span class="font-extrabold text-[#111827] ml-1">{{ $booking['id'] }}</span>
                            </div>
                            <div class="text-[12.5px] text-[#4B5563] leading-normal">Booking Date: <span class="font-bold text-[#111827]">{{ $formattedBookingDate }}</span></div>
                        </div>
                    </div>

                    <div class="border-t border-[#E5E7EB] my-3.5"></div>

                    <!-- 2. Middle 3-Column Section -->
                    <div class="flex gap-4 items-stretch text-[12.5px]">
                        <div class="flex-[1.05] pr-2">
                            <span class="text-[11px] font-bold text-[#4B5563] uppercase tracking-wider block mb-1">Billed To</span>
                            <div class="text-[16.5px] font-black text-[#111827] mb-2 leading-tight">{{ $booking['name'] ?? 'Valued Customer' }}</div>
                            <div class="space-y-1.5 text-[#1F2937] font-semibold text-[12.5px]">
                                <div>📞 &nbsp;+91 {{ $customerMobile }}</div>
                                <div>💬 &nbsp;+91 {{ $customerWhatsapp }}</div>
                                <div class="font-medium text-[#374151]">✉ &nbsp;{{ $customerEmail }}</div>
                                <div>📍 &nbsp;{{ $city }}, {{ $state }}</div>
                            </div>
                        </div>

                        <div class="w-[1px] bg-[#E5E7EB] shrink-0"></div>

                        <div class="flex-[1.35] px-2.5">
                            <span class="text-[11px] font-bold text-[#4B5563] uppercase tracking-wider block mb-1.5">Event Details</span>
                            <div class="space-y-1.5 text-[#1F2937] font-medium text-[12.5px]">
                                <div>📅 &nbsp;<strong class="text-[#111827] font-black text-[13px]">{{ $formattedEventDate }} &bull; {{ trim(trim($eventTime), '()') }}</strong></div>
                                <div>👥 &nbsp;Guest Count: <strong class="text-[#111827] font-extrabold">{{ $guestCount }} Guests</strong></div>
                                <div style="line-height: 1.45;" class="text-xs">
                                    📍 &nbsp;<strong class="text-[#111827] font-extrabold">Venue:</strong> {{ $fullVenue }}
                                </div>
                            </div>
                        </div>

                        <div class="w-[1px] bg-[#E5E7EB] shrink-0"></div>

                        <div class="flex-[1.1] flex items-center justify-end pl-1">
                            <div class="w-full border rounded-xl p-3.5 flex items-center gap-3.5 {{ $printStatusBg }}">
                                <div class="shrink-0">{!! $printStatusIcon !!}</div>
                                <div>
                                    <span class="text-[11px] font-bold uppercase tracking-wider block {{ $printStatusSub }}">Booking Status</span>
                                    <span class="font-black text-[16.5px] block mt-0.5 whitespace-nowrap">{{ $displayStatus }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- 3. Table Section -->
                    <div class="mt-4 border border-[#E5E7EB] rounded-xl overflow-hidden">
                        <div class="bg-[#F4F4F5] p-2.5 flex items-center text-xs font-black text-[#111827] uppercase tracking-wider border-b border-[#E5E7EB]">
                            <div class="w-9 text-center">#</div>
                            <div class="flex-1 pl-3 text-left">Service &amp; Description</div>
                            <div class="w-44 text-left">Tier / Specification</div>
                            <div class="w-28 text-right pr-2">Amount (INR)</div>
                        </div>
                        <div class="p-3 flex items-center border-b border-[#F3F4F6]">
                            <div class="w-9 text-center text-[#4B5563] font-extrabold text-[12.5px]">1</div>
                            <div class="flex-1 pl-3">
                                <div class="font-black text-[14.5px] text-[#111827] leading-tight">{{ $booking['package'] ?? 'Celebration Package' }}</div>
                                <div class="text-xs text-[#4B5563] font-medium mt-0.5">Category: {{ $booking['category'] ?? 'Event Setup' }}</div>
                            </div>
                            <div class="w-44 text-[#1F2937] font-semibold text-xs">{{ $booking['tier'] ?? 'Standard Setup' }}</div>
                            <div class="w-28 text-right pr-2 font-black text-[14.5px] text-[#111827]">₹{{ number_format($basePrice) }}</div>
                        </div>
                        @if(!empty($booking['addons']) && is_array($booking['addons']))
                            @foreach($booking['addons'] as $idx => $addon)
                                <div class="p-2.5 flex items-center text-xs bg-[#FAFAFA] border-b border-[#F3F4F6]">
                                    <div class="w-9 text-center text-[#6B7280] font-bold">{{ $idx + 2 }}</div>
                                    <div class="flex-1 pl-3 font-bold text-[#111827]">+ {{ $addon['name'] ?? 'Add-on' }}</div>
                                    <div class="w-44 text-[#4B5563] text-xs font-medium">Custom Add-on</div>
                                    <div class="w-28 text-right pr-2 font-black text-[13.5px] text-[#111827]">₹{{ number_format($addon['price'] ?? 0) }}</div>
                                </div>
                            @endforeach
                        @endif
                    </div>

                    <!-- 4. Totals Summary -->
                    <div class="mt-3 pt-1 flex justify-end">
                        <div class="w-80 text-xs space-y-1">
                            <div class="flex justify-between px-2 text-[#374151] font-medium leading-normal text-[12.5px]">
                                <span>Base Package Subtotal</span>
                                <span class="font-bold text-[#111827]">₹{{ number_format($basePrice) }}</span>
                            </div>
                            <div class="flex justify-between px-2 text-[#374151] font-medium leading-normal text-[12.5px]">
                                <span>Service &amp; Setup Coordination</span>
                                <span class="font-semibold text-[#374151]">Included</span>
                            </div>
                            @if($surcharge > 0)
                                <div class="flex justify-between px-2 text-[#374151] font-medium leading-normal text-[12.5px]">
                                    <span>Selected Add-ons Surcharge</span>
                                    <span class="font-bold text-[#111827]">₹{{ number_format($surcharge) }}</span>
                                </div>
                            @endif
                            <div class="bg-white border-[1.5px] border-[#E5A93C] rounded-xl p-2.5 flex justify-between items-center mt-1.5">
                                <span class="font-black text-[15px] text-[#111827]">Total Amount</span>
                                <span class="font-black text-[22px] text-[#9A3412]">₹{{ number_format($totalAmount) }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- 5. 100% Offline Payment Banner -->
                    <div class="mt-3.5 bg-white border-[1.5px] border-[#E5A93C] rounded-xl p-3.5 flex items-start gap-3.5">
                        <svg width="22" height="22" viewBox="0 0 22 22" style="flex-shrink: 0; min-width: 22px; width: 22px; height: 22px; margin-top: 2px;">
                            <circle cx="11" cy="11" r="10" fill="#FFFFFF" stroke="#E5A93C" stroke-width="1.6"/>
                            <text x="11" y="15.5" font-family="'Times New Roman', Georgia, serif" font-size="14" font-weight="bold" font-style="italic" fill="#D97706" text-anchor="middle">i</text>
                        </svg>
                        <div>
                            <div class="text-[13px] font-black text-[#111827] uppercase tracking-wide">100% Offline Payment Model &nbsp;|&nbsp; Zero Advance Mandate</div>
                            <div class="text-xs text-[#1F2937] font-medium leading-relaxed mt-1">At Artizen, we believe in complete celebration perfection before payment. No online payment is collected on this portal. Please inspect your completed venue setup and settle the total amount directly with our event manager at your venue via Cash, UPI (Google Pay, PhonePe, Paytm), or Bank Transfer.</div>
                        </div>
                    </div>

                    <!-- 6. Event Day Guidelines -->
                    <div class="mt-3.5">
                        <div class="text-[12.5px] font-black text-[#111827] pb-1 border-b border-[#E5E7EB] uppercase tracking-wide flex items-center gap-2">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#111827" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" class="shrink-0"><path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"></path><rect x="8" y="2" width="8" height="4" rx="1" ry="1"></rect><line x1="8" y1="11" x2="16" y2="11"></line><line x1="8" y1="15" x2="12" y2="15"></line></svg>
                            <span>Event Day Guidelines &amp; Protocol</span>
                        </div>
                        <div class="mt-2 space-y-2 text-xs text-[#1F2937]">
                            <div class="flex items-start gap-2.5">
                                <svg width="20" height="20" viewBox="0 0 20 20" style="flex-shrink: 0; min-width: 20px; width: 20px; height: 20px; margin-top: 1px;">
                                    <circle cx="10" cy="10" r="9" fill="#FFFFFF" stroke="#E5A93C" stroke-width="1.3"/>
                                    <text x="10" y="14" font-family="-apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif" font-size="11" font-weight="800" fill="#92400E" text-anchor="middle">1</text>
                                </svg>
                                <span class="leading-relaxed"><strong class="text-[#111827] font-black">Punctual Crew Arrival:</strong> Our setup artists will reach the venue 60–90 minutes prior to the booked event time.</span>
                            </div>
                            <div class="flex items-start gap-2.5">
                                <svg width="20" height="20" viewBox="0 0 20 20" style="flex-shrink: 0; min-width: 20px; width: 20px; height: 20px; margin-top: 1px;">
                                    <circle cx="10" cy="10" r="9" fill="#FFFFFF" stroke="#E5A93C" stroke-width="1.3"/>
                                    <text x="10" y="14" font-family="-apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif" font-size="11" font-weight="800" fill="#92400E" text-anchor="middle">2</text>
                                </svg>
                                <span class="leading-relaxed"><strong class="text-[#111827] font-black">Venue Access &amp; Power:</strong> Ensure standard electrical power sockets and venue access permission are ready on arrival.</span>
                            </div>
                            <div class="flex items-start gap-2.5">
                                <svg width="20" height="20" viewBox="0 0 20 20" style="flex-shrink: 0; min-width: 20px; width: 20px; height: 20px; margin-top: 1px;">
                                    <circle cx="10" cy="10" r="9" fill="#FFFFFF" stroke="#E5A93C" stroke-width="1.3"/>
                                    <text x="10" y="14" font-family="-apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif" font-size="11" font-weight="800" fill="#92400E" text-anchor="middle">3</text>
                                </svg>
                                <span class="leading-relaxed"><strong class="text-[#111827] font-black">Dedicated Coordination:</strong> For live updates, theme customization, or timing adjustments, connect with our concierge.</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 7. Footer (Clean Single-Line Official Corporate Footer) -->
                <div class="pt-3 border-t border-[#E5E7EB] flex items-center justify-between text-[11px] text-[#4B5563] w-full">
                    <div class="flex items-center shrink-0">
                        <img src="{{ asset('assets/images/logo/artizen.png') }}" alt="Artizen Logo" class="h-10 w-auto object-contain shrink-0">
                    </div>

                    <div class="flex items-center gap-2 text-[10.5px] font-medium whitespace-nowrap text-right">
                        <span>Call &amp; WhatsApp: <strong class="text-[#111827] font-bold">+91 9131668156</strong></span>
                        <span class="text-[#9CA3AF] mx-1">&bull;</span>
                        <span>support@artizenevents.com</span>
                        <span class="text-[#9CA3AF] mx-1">&bull;</span>
                        <span>Indore, MP - 452001</span>
                        <span class="text-[#9CA3AF] mx-1">&bull;</span>
                        <span class="text-[#111827] font-bold">www.artizens.in</span>
                    </div>
                </div>
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
                    <input type="text" id="track-booking-id" name="booking_id" value="{{ old('booking_id', $bookingId ?? '') }}" required placeholder="e.g. BK-C4KC1H"
                        class="w-full px-3.5 py-2.5 bg-[#FAF9F5] border border-[#E8E5DF] rounded-xl text-xs font-mono font-bold text-main-text placeholder-neutral-400 focus:outline-none focus:border-black focus:bg-white transition-colors">
                </div>

                <div>
                    <label for="track-mobile" class="block text-xs font-semibold text-main-text mb-1">
                        Registered Mobile Number *
                    </label>
                    <input type="tel" id="track-mobile" name="mobile" value="{{ old('mobile') }}" required placeholder="e.g. 8817724275"
                        class="w-full px-3.5 py-2.5 bg-[#FAF9F5] border border-[#E8E5DF] rounded-xl text-xs text-main-text placeholder-neutral-400 focus:outline-none focus:border-black focus:bg-white transition-colors">
                </div>

                <button type="submit" class="w-full py-2.5 bg-neutral-900 hover:bg-black text-white text-xs font-bold rounded-xl transition-colors cursor-pointer mt-2 shadow-sm">
                    Search Booking Details
                </button>
            </form>
        </div>

    </div>
</div>
@endsection
