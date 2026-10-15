@extends('layouts.app')

@section('title', 'My Orders & Bookings - ARTIZEN Indore')
@section('meta_description', 'View and track all your celebration bookings, event dates, offline invoices and confirmation status with Artizen in Indore.')

@section('content')
<div class="min-h-screen pt-4 pb-16 px-4 md:px-8 lg:px-12 bg-[#FAF9F5] text-main-text select-none">
    <div class="max-w-5xl mx-auto">

        <!-- Breadcrumbs -->
        <nav aria-label="Breadcrumb" class="flex items-center gap-2 text-xs text-[#777777] mb-4 font-medium text-left">
            <a href="{{ route('home') }}" class="hover:text-black transition-colors">Home</a>
            <i class="fa-solid fa-chevron-right text-[9px] text-[#CCCCCC]"></i>
            <span class="text-main-text font-bold">My Orders &amp; Bookings</span>
        </nav>

        <!-- Page Header -->
        <div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-[#E8E5DF] pb-4 text-left">
            <div>
                <h1 class="font-heading font-extrabold text-2xl sm:text-3xl text-main-text tracking-tight">
                    My Bookings &amp; Orders
                </h1>
                <p class="text-xs text-[#777777] mt-1">
                    Manage your celebration reservations, view setup schedules &amp; download offline slips for Indore.
                </p>
            </div>
            
            <div>
                <a href="{{ route('events.index') }}"
                    class="px-4 py-2 rounded-full bg-[#FFD600] hover:bg-[#ffe033] text-neutral-900 text-xs font-heading font-extrabold uppercase tracking-wider transition-colors shadow-2xs inline-flex items-center gap-1.5">
                    <i class="fa-solid fa-plus text-[10px]"></i>
                    <span>Book New Setup</span>
                </a>
            </div>
        </div>

        <!-- Orders List Container -->
        @if(count($filteredBookings) > 0)
            <div class="space-y-4 text-left">
                @foreach($filteredBookings as $b)
                    @php
                        $bStatus = trim($b['status'] ?? 'Pending');
                        $lowerStatus = strtolower($bStatus);
                        $isPending = in_array($lowerStatus, ['pending', 'under review']);
                        $isConfirmed = in_array($lowerStatus, ['confirmed', 'contacted', 'approved']);
                        $isCompleted = ($lowerStatus === 'completed');
                        $isCancelled = in_array($lowerStatus, ['cancelled', 'rejected']);

                        $badgeClass = 'bg-amber-50 text-amber-800 border-amber-200';
                        $statusDisplay = 'Pending';
                        if ($isConfirmed) {
                            $badgeClass = 'bg-emerald-50 text-emerald-800 border-emerald-200';
                            $statusDisplay = 'Approved';
                        } elseif ($isCompleted) {
                            $badgeClass = 'bg-neutral-900 text-white border-neutral-900';
                            $statusDisplay = 'Completed';
                        } elseif ($isCancelled) {
                            if ($lowerStatus === 'rejected') {
                                $badgeClass = 'bg-rose-50 text-rose-800 border-rose-200';
                                $statusDisplay = 'Rejected';
                            } else {
                                $badgeClass = 'bg-neutral-100 text-neutral-700 border-neutral-200';
                                $statusDisplay = 'Cancelled';
                            }
                        }

                        $waOrderMsg = "Hi Artizen! I have an inquiry about my booking ID: " . ($b['id'] ?? 'BK-XXXX') . " (" . ($b['package'] ?? 'Package') . ")";
                        $waOrderUrl = "https://wa.me/919131668156?text=" . urlencode($waOrderMsg);
                    @endphp

                    <div class="border border-[#E8E5DF] bg-white rounded-2xl p-5 sm:p-6 shadow-2xs hover:shadow-xs transition-shadow">
                        <!-- Top Header: ID, Date, Status -->
                        <div class="flex flex-wrap items-center justify-between gap-3 pb-4 border-b border-[#E8E5DF]">
                            <div class="flex items-center gap-2.5">
                                <span class="font-mono font-extrabold text-sm sm:text-base text-main-text">
                                    {{ $b['id'] ?? 'BK-XXXX' }}
                                </span>
                                <button type="button" onclick="copyBookingId('{{ $b['id'] ?? '' }}', this)"
                                    class="text-[10px] text-neutral-400 hover:text-black transition-colors cursor-pointer px-1.5 py-0.5 rounded border border-[#E8E5DF] hover:border-black"
                                    title="Copy Booking ID">
                                    <i class="fa-regular fa-copy"></i>
                                </button>
                                <span class="text-[11px] text-[#777777] hidden sm:inline">•</span>
                                <span class="text-[11px] text-[#777777] hidden sm:inline">Placed on {{ $b['created_at'] ?? 'Recently' }}</span>
                            </div>

                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold border {{ $badgeClass }}">
                                {{ $statusDisplay }}
                            </span>
                        </div>

                        <!-- Card Body: Package Details & Venue Info -->
                        <div class="py-4 grid grid-cols-1 md:grid-cols-12 gap-4 items-center">
                            
                            <!-- Left: Package Info (7 cols) -->
                            <div class="md:col-span-7 flex items-start gap-3.5">
                                <div class="w-16 h-16 rounded-xl overflow-hidden bg-neutral-100 border border-[#E8E5DF] shrink-0">
                                    <img src="{{ $b['image'] ?? '/assets/images/hero/1.jpg' }}" alt="{{ $b['package'] ?? 'Package' }}" class="w-full h-full object-cover">
                                </div>
                                <div class="min-w-0">
                                    <h2 class="font-heading font-extrabold text-sm sm:text-base text-main-text leading-snug truncate">
                                        {{ $b['package'] ?? 'Event Celebration' }}
                                    </h2>
                                    <div class="flex flex-wrap items-center gap-2 mt-1 text-xs text-[#555555]">
                                        <span class="font-semibold text-neutral-800">{{ $b['tier'] ?? 'Standard Setup' }}</span>
                                        <span>•</span>
                                        <span>{{ $b['guest_count'] ?? 25 }} Guests</span>
                                        <span>•</span>
                                        <span>{{ $b['category'] ?? 'Celebrations' }}</span>
                                    </div>

                                    @if(!empty($b['addons']) && is_array($b['addons']) && count($b['addons']) > 0)
                                        <div class="mt-2 text-[11px] text-[#777777]">
                                            <span class="font-semibold text-neutral-800">{{ count($b['addons']) }} Add-on(s):</span>
                                            {{ implode(', ', array_map(function($a) { return is_array($a) ? ($a['name'] ?? 'Add-on') : $a; }, $b['addons'])) }}
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <!-- Right: Event Date, Time & Location (5 cols) -->
                            <div class="md:col-span-5 border-t md:border-t-0 md:border-l border-[#E8E5DF] pt-3 md:pt-0 md:pl-4 space-y-1.5 text-xs text-[#555555]">
                                <div class="flex items-center gap-2">
                                    <i class="fa-regular fa-calendar text-neutral-700 text-xs w-4"></i>
                                    <span class="font-bold text-main-text">
                                        {{ !empty($b['date']) ? date('D, d M Y', strtotime($b['date'])) : 'Date TBD' }}
                                    </span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <i class="fa-regular fa-clock text-neutral-700 text-xs w-4"></i>
                                    <span>{{ $b['time'] ?? 'Setup Window Pending' }}</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <i class="fa-solid fa-location-dot text-neutral-700 text-xs w-4"></i>
                                    <span class="truncate">{{ $b['area'] ?? 'Indore' }}, {{ $b['city'] ?? 'Indore' }}</span>
                                </div>
                            </div>

                        </div>

                        <!-- Card Footer: Total Price & Actions -->
                        <div class="pt-4 border-t border-[#E8E5DF] flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                            <div class="flex items-baseline gap-2">
                                <span class="text-xs text-[#777777]">Total Amount:</span>
                                <span class="text-base sm:text-lg font-heading font-extrabold text-main-text">
                                    ₹{{ number_format($b['total'] ?? ($b['package_price'] ?? 0)) }}
                                </span>
                                <span class="text-[11px] font-medium text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded border border-emerald-200">
                                    Offline Settlement
                                </span>
                            </div>

                            <div class="flex items-center gap-2">
                                <a href="{{ route('booking.success', ['id' => $b['id']]) }}"
                                    class="px-3.5 py-2 rounded-xl bg-white hover:bg-neutral-50 text-neutral-800 border border-[#D4D4D8] text-xs font-bold transition-colors">
                                    View Receipt
                                </a>
                                <button type="button" onclick="generateOrderPDF(@json($b))"
                                    class="px-3.5 py-2 rounded-xl bg-neutral-900 hover:bg-black text-white text-xs font-bold transition-colors cursor-pointer">
                                    Download PDF
                                </button>
                                <a href="{{ $waOrderUrl }}" target="_blank" rel="noopener"
                                    class="px-3.5 py-2 rounded-xl bg-[#25D366] hover:bg-[#20bd5a] text-white text-xs font-bold transition-colors inline-flex items-center gap-1.5 cursor-pointer">
                                    <i class="fa-brands fa-whatsapp text-sm"></i> WhatsApp Support
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <!-- Empty State -->
            <div class="border border-[#E8E5DF] bg-white rounded-2xl p-10 sm:p-14 text-center flex flex-col items-center justify-center max-w-lg mx-auto shadow-2xs">
                <div class="w-14 h-14 rounded-2xl bg-[#FAF9F5] border border-[#E8E5DF] flex items-center justify-center text-neutral-600 mb-4 text-xl">
                    <i class="fa-solid fa-bag-shopping"></i>
                </div>
                <h2 class="font-heading font-extrabold text-lg text-main-text mb-1">
                    No Bookings Found
                </h2>
                <p class="text-xs text-[#777777] max-w-sm mb-6 leading-relaxed">
                    You haven't submitted any event bookings yet. Choose a curated setup package in Indore to get started!
                </p>
                <div class="flex items-center gap-3">
                    <a href="{{ route('events.index') }}"
                        class="px-5 py-2.5 rounded-xl bg-[#FFD600] hover:bg-[#ffe033] text-neutral-900 text-xs font-heading font-extrabold uppercase tracking-wider transition-colors shadow-2xs">
                        Explore Celebrations &rarr;
                    </a>
                </div>
            </div>
        @endif

    </div>
</div>

<!-- jsPDF Vector PDF Generator for Client Download -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script>
function copyBookingId(id, btn) {
    if (!id) return;
    navigator.clipboard.writeText(id).then(() => {
        const origHtml = btn.innerHTML;
        btn.innerHTML = '<i class="fa-solid fa-check text-emerald-600"></i>';
        setTimeout(() => { btn.innerHTML = origHtml; }, 1800);
    });
}

function generateOrderPDF(b) {
    if (typeof window.jspdf === 'undefined') {
        window.location.href = `/booking/success?id=${b.id}`;
        return;
    }

    try {
        const { jsPDF } = window.jspdf;
        const doc = new jsPDF({ unit: 'pt', format: 'a4' });

        const black = [24, 24, 27];
        const gray = [110, 110, 115];
        const lightLine = [225, 222, 218];
        const gold = [217, 119, 6];

        // Header
        doc.setFillColor(24, 24, 27);
        doc.rect(40, 40, 515, 65, 'F');

        doc.setTextColor(255, 255, 255);
        doc.setFont('helvetica', 'bold');
        doc.setFontSize(22);
        doc.text('ARTIZEN', 55, 75);

        doc.setFont('helvetica', 'normal');
        doc.setFontSize(10);
        doc.text('Event Booking Slip - Indore, Madhya Pradesh', 55, 92);

        doc.setFont('helvetica', 'bold');
        doc.setFontSize(14);
        doc.text(String(b.id || 'BK-XXXX'), 535, 75, { align: 'right' });

        doc.setFont('helvetica', 'normal');
        doc.setFontSize(9);
        doc.text('Status: ' + String(b.status || 'Pending'), 535, 92, { align: 'right' });

        // Meta Box
        let y = 135;
        doc.setDrawColor(...lightLine);
        doc.setLineWidth(1);
        doc.line(40, y, 555, y);

        y += 20;
        doc.setTextColor(...gray);
        doc.setFontSize(9);
        doc.text('Customer Name:', 45, y);
        doc.text('Contact Mobile:', 210, y);
        doc.text('Event Date:', 390, y);

        y += 15;
        doc.setTextColor(...black);
        doc.setFont('helvetica', 'bold');
        doc.setFontSize(11);
        doc.text(String(b.name || 'Valued Customer'), 45, y);
        doc.text(String(b.mobile || 'Registered Phone'), 210, y);
        doc.text(String(b.date || 'TBD'), 390, y);

        y += 25;
        doc.setDrawColor(...lightLine);
        doc.line(40, y, 555, y);

        // Package Details
        y += 25;
        doc.setFont('helvetica', 'bold');
        doc.setFontSize(12);
        doc.setTextColor(...black);
        doc.text('Celebration Setup Details', 45, y);

        y += 20;
        doc.setFont('helvetica', 'normal');
        doc.setFontSize(10);
        doc.setTextColor(...gray);
        doc.text('Package:', 45, y);
        doc.setTextColor(...black);
        doc.setFont('helvetica', 'bold');
        doc.text(String(b.package || 'Event Package'), 140, y);

        y += 18;
        doc.setFont('helvetica', 'normal');
        doc.setTextColor(...gray);
        doc.text('Tier Selected:', 45, y);
        doc.setTextColor(...black);
        doc.text(String(b.tier || 'Standard Setup'), 140, y);

        y += 18;
        doc.setTextColor(...gray);
        doc.text('Setup Slot:', 45, y);
        doc.setTextColor(...black);
        doc.text(String(b.time || 'Standard Slot'), 140, y);

        y += 18;
        doc.setTextColor(...gray);
        doc.text('Venue Address:', 45, y);
        doc.setTextColor(...black);
        doc.text(String((b.address || '') + ', ' + (b.area || '') + ', Indore'), 140, y);

        // Pricing Box
        y += 35;
        doc.setFillColor(250, 249, 245);
        doc.roundedRect(40, y, 515, 65, 6, 6, 'F');
        doc.setDrawColor(...lightLine);
        doc.roundedRect(40, y, 515, 65, 6, 6, 'D');

        doc.setFont('helvetica', 'normal');
        doc.setFontSize(10);
        doc.setTextColor(...gray);
        doc.text('Total Settlement Amount (Offline Payment):', 55, y + 28);

        doc.setFont('helvetica', 'bold');
        doc.setFontSize(18);
        doc.setTextColor(...black);
        doc.text('INR ' + Number(b.total || 0).toLocaleString('en-IN'), 535, y + 32, { align: 'right' });

        doc.setFontSize(9);
        doc.setTextColor(...gold);
        doc.text('Payment collected offline after setup verification in Indore', 55, y + 48);

        // Footer
        doc.setFont('helvetica', 'normal');
        doc.setFontSize(9);
        doc.setTextColor(...gray);
        doc.text('Support & Coordinator Helpline: +91 91316 68156 | support@artizenevents.com', 297, 780, { align: 'center' });

        doc.save(`Artizen-${b.id || 'Booking'}.pdf`);
    } catch(e) {
        window.location.href = `/booking/success?id=${b.id}`;
    }
}
</script>
@endsection
