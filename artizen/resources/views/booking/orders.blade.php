@extends('layouts.app')

@section('title', 'My Orders & Bookings - ARTIZEN Indore')
@section('meta_description', 'View and track all your celebration bookings, event dates, offline invoices and confirmation status with Artizen in Indore.')

@section('content')
<div class="min-h-screen pt-4 sm:pt-6 pb-16 sm:pb-20 bg-[#FAF9F5] text-main-text select-none">
    <div class="w-full max-w-7xl mx-auto px-3 sm:px-6 lg:px-8">

        <!-- Breadcrumbs -->
        <nav aria-label="Breadcrumb" class="flex items-center gap-2 text-xs text-[#777777] mb-3 sm:mb-4 font-medium text-left">
            <a href="{{ route('home') }}" class="hover:text-black transition-colors">Home</a>
            <i class="fa-solid fa-chevron-right text-[9px] text-[#CCCCCC]"></i>
            <span class="text-main-text font-bold truncate">My Orders &amp; Bookings</span>
        </nav>

        <!-- Page Header (Fully responsive on mobile, tablet, laptop) -->
        <div class="mb-5 sm:mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-3 sm:gap-4 border-b border-[#E8E5DF] pb-4 sm:pb-5 text-left">
            <div>
                <h1 class="font-heading font-extrabold text-xl sm:text-2xl md:text-3xl lg:text-4xl text-main-text tracking-tight">
                    My Bookings &amp; Orders
                </h1>
                <p class="text-xs sm:text-sm text-[#777777] mt-1 sm:mt-1.5">
                    Manage your celebration reservations, view setup schedules &amp; download offline slips for Indore.
                </p>
            </div>
            
            <div class="shrink-0">
                <a href="{{ route('events.index') }}"
                    class="w-full sm:w-auto justify-center px-4 sm:px-5 py-2 sm:py-2.5 rounded-full bg-[#FFD600] hover:bg-[#ffe033] text-neutral-900 text-xs font-heading font-extrabold uppercase tracking-wider transition-all shadow-2xs hover:shadow-xs inline-flex items-center gap-2">
                    <i class="fa-solid fa-plus text-[10px]"></i>
                    <span>Book New Setup</span>
                </a>
            </div>
        </div>

        <!-- Status Filter Tabs (Urban Company / Airbnb style with smooth mobile scroll) -->
        <div class="mb-5 sm:mb-6 flex items-center gap-2 overflow-x-auto pb-2 scrollbar-none text-left -mx-3 px-3 sm:mx-0 sm:px-0">
            @php
                $tabs = [
                    'all' => ['label' => 'All Bookings', 'count' => $counts['all'] ?? 0],
                    'pending' => ['label' => 'Pending Review', 'count' => $counts['pending'] ?? 0],
                    'confirmed' => ['label' => 'Approved / Confirmed', 'count' => $counts['confirmed'] ?? 0],
                    'completed' => ['label' => 'Completed', 'count' => $counts['completed'] ?? 0],
                    'cancelled' => ['label' => 'Cancelled / Rejected', 'count' => $counts['cancelled'] ?? 0],
                ];
            @endphp
            @foreach($tabs as $key => $tab)
                @php
                    $isActive = ($activeTab === $key);
                @endphp
                <a href="{{ route('orders.index', ['status' => $key]) }}"
                    class="px-3.5 sm:px-4 py-1.5 sm:py-2 rounded-full text-xs font-bold whitespace-nowrap transition-all flex items-center gap-1.5 sm:gap-2 {{ $isActive ? 'bg-neutral-900 text-white shadow-xs' : 'bg-white text-neutral-600 hover:text-black hover:bg-neutral-100 border border-[#E8E5DF]' }}">
                    <span>{{ $tab['label'] }}</span>
                    <span class="text-[10px] px-1.5 py-0.5 rounded-full {{ $isActive ? 'bg-white/20 text-white' : 'bg-neutral-100 text-neutral-600' }}">
                        {{ $tab['count'] }}
                    </span>
                </a>
            @endforeach
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

                        $orderIdVal = $b['id'] ?? 'BK-XXXX';
                        $bTotalVal = (float)($b['total'] ?? ($b['total_amount'] ?? ($b['package_price'] ?? 0)));
                        $pdfUrlOrder = route('booking.success', ['id' => $orderIdVal, 'download_pdf' => 1]);
                        $waOrderMsg = "BOOKING INQUIRY - ARTIZEN\n"
                            . "----------------------------------------\n"
                            . "Booking ID: #" . $orderIdVal . "\n"
                            . "Customer: " . strip_tags($b['name'] ?? 'Customer') . "\n"
                            . "Package: " . strip_tags($b['package'] ?? 'Package') . "\n"
                            . "Total: Rs. " . number_format($bTotalVal) . "\n"
                            . "Invoice PDF: " . $pdfUrlOrder . "\n"
                            . "----------------------------------------\n"
                            . "Hi Artizen team! I have an inquiry regarding my booking above.";
                        $waOrderUrl = "https://wa.me/919131668156?text=" . rawurlencode($waOrderMsg);
                    @endphp

                    <div class="border border-[#E8E5DF] bg-white rounded-2xl p-4 sm:p-6 lg:p-7 shadow-2xs hover:shadow-xs transition-all">
                        <!-- Top Header: ID, Date, Status -->
                        <div class="flex flex-wrap items-center justify-between gap-2.5 pb-3.5 sm:pb-4 border-b border-[#E8E5DF]">
                            <div class="flex flex-wrap items-center gap-2 sm:gap-2.5">
                                <span class="font-mono font-extrabold text-xs sm:text-sm md:text-base text-main-text">
                                    {{ $b['id'] ?? 'BK-XXXX' }}
                                </span>
                                <button type="button" onclick="copyBookingId('{{ $b['id'] ?? '' }}', this)"
                                    class="text-[10px] text-neutral-400 hover:text-black transition-colors cursor-pointer px-1.5 py-0.5 rounded border border-[#E8E5DF] hover:border-black"
                                    title="Copy Booking ID">
                                    <i class="fa-regular fa-copy"></i>
                                </button>
                                <span class="text-[11px] text-[#777777] hidden sm:inline">•</span>
                                <span class="text-[10.5px] sm:text-[11px] text-[#777777] block sm:inline">Placed on {{ $b['created_at'] ?? 'Recently' }}</span>
                            </div>

                            <span class="inline-flex items-center px-3 sm:px-3.5 py-0.5 sm:py-1 rounded-full text-[11px] sm:text-xs font-bold border {{ $badgeClass }} shrink-0">
                                {{ $statusDisplay }}
                            </span>
                        </div>

                        <!-- Card Body: Package Details & Venue Info (Responsive Grid) -->
                        <div class="py-4 sm:py-5 grid grid-cols-1 lg:grid-cols-12 gap-4 sm:gap-6 items-start lg:items-center">
                            
                            <!-- Left: Package Info (8 cols on large screens, stacked on mobile) -->
                            <div class="lg:col-span-8 flex items-start gap-3.5 sm:gap-4 md:gap-5">
                                <div class="w-16 h-16 sm:w-20 sm:h-20 md:w-24 md:h-24 rounded-xl sm:rounded-2xl overflow-hidden bg-neutral-100 border border-[#E8E5DF] shrink-0 shadow-2xs">
                                    <img src="{{ $b['image'] ?? '/assets/images/hero/1.jpg' }}" alt="{{ $b['package'] ?? 'Package' }}" class="w-full h-full object-cover">
                                </div>
                                <div class="min-w-0 flex-1">
                                    <h2 class="font-heading font-extrabold text-sm sm:text-base md:text-lg text-main-text leading-snug">
                                        {{ $b['package'] ?? 'Event Celebration' }}
                                    </h2>
                                    <div class="flex flex-wrap items-center gap-1.5 sm:gap-2 mt-1 sm:mt-1.5 text-xs text-[#555555]">
                                        <span class="font-semibold text-neutral-800 bg-neutral-100 px-2 py-0.5 rounded text-[11px] sm:text-xs">{{ $b['tier'] ?? 'Standard Setup' }}</span>
                                        <span>•</span>
                                        <span class="font-medium text-neutral-700 text-[11px] sm:text-xs">{{ $b['guest_count'] ?? 25 }} Guests</span>
                                        <span>•</span>
                                        <span class="font-medium text-neutral-600 text-[11px] sm:text-xs">{{ $b['category'] ?? 'Celebrations' }}</span>
                                    </div>

                                    @if(!empty($b['addons']) && is_array($b['addons']) && count($b['addons']) > 0)
                                        <div class="mt-2 sm:mt-2.5 flex flex-wrap gap-1 sm:gap-1.5 items-center">
                                            <span class="text-[10.5px] sm:text-[11px] font-bold text-neutral-700">{{ count($b['addons']) }} Add-on(s):</span>
                                            @foreach($b['addons'] as $addonItem)
                                                @php
                                                    $addonName = is_array($addonItem) ? ($addonItem['name'] ?? ($addonItem['title'] ?? 'Add-on')) : $addonItem;
                                                @endphp
                                                <span class="text-[10px] sm:text-[10.5px] bg-[#FAF9F5] text-neutral-700 border border-[#E8E5DF] px-1.5 sm:px-2 py-0.5 rounded-md font-medium">
                                                    + {{ $addonName }}
                                                </span>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <!-- Right: Event Date, Time & Location (4 cols on large screens) -->
                            <div class="lg:col-span-4 border-t lg:border-t-0 lg:border-l border-[#E8E5DF] pt-3 sm:pt-4 lg:pt-0 lg:pl-6 space-y-1.5 sm:space-y-2 text-xs sm:text-[13px] text-[#555555] w-full">
                                <div class="flex items-center gap-2 sm:gap-2.5">
                                    <i class="fa-regular fa-calendar text-neutral-800 text-xs sm:text-sm w-4 shrink-0"></i>
                                    <span class="font-bold text-main-text">
                                        {{ !empty($b['date']) ? date('D, d M Y', strtotime($b['date'])) : 'Date TBD' }}
                                    </span>
                                </div>
                                <div class="flex items-center gap-2 sm:gap-2.5">
                                    <i class="fa-regular fa-clock text-neutral-800 text-xs sm:text-sm w-4 shrink-0"></i>
                                    <span class="font-medium text-neutral-700">{{ $b['time'] ?? 'Setup Window Pending' }}</span>
                                </div>
                                <div class="flex items-start gap-2 sm:gap-2.5">
                                    <i class="fa-solid fa-location-dot text-neutral-800 text-xs sm:text-sm w-4 shrink-0 mt-0.5"></i>
                                    <span class="font-medium text-neutral-700 leading-snug">
                                        {{ array_filter([$b['landmark'] ?? '', $b['area'] ?? '', $b['city'] ?? 'Indore']) ? implode(', ', array_filter([$b['landmark'] ?? '', $b['area'] ?? '', $b['city'] ?? 'Indore'])) : 'Indore, Madhya Pradesh' }}
                                    </span>
                                </div>
                            </div>

                        </div>

                        <!-- Card Footer: Total Price & Actions (Adaptive Mobile Grid + Desktop Flex) -->
                        <div class="pt-3.5 sm:pt-4 border-t border-[#E8E5DF] flex flex-col sm:flex-row sm:items-center justify-between gap-3 sm:gap-4">
                            <div class="flex flex-wrap items-center gap-2 sm:gap-2.5">
                                <span class="text-xs text-[#777777]">Total Settlement:</span>
                                <span class="text-base sm:text-lg md:text-xl font-heading font-extrabold text-main-text">
                                    ₹{{ number_format($b['total'] ?? ($b['package_price'] ?? 0)) }}
                                </span>
                                <span class="text-[10.5px] sm:text-[11px] font-semibold text-emerald-800 bg-emerald-50 px-2 sm:px-2.5 py-0.5 rounded-full border border-emerald-200">
                                    Offline Settlement
                                </span>
                            </div>

                            <div class="grid grid-cols-2 sm:flex sm:flex-wrap sm:items-center gap-2 w-full sm:w-auto">
                                <a href="{{ route('booking.success', ['id' => $b['id']]) }}"
                                    class="col-span-1 px-3 sm:px-4 py-2 sm:py-2.5 rounded-xl bg-white hover:bg-neutral-50 text-neutral-800 border border-[#D4D4D8] text-xs font-bold transition-all shadow-2xs hover:shadow-xs text-center inline-flex items-center justify-center">
                                    View Receipt
                                </a>
                                <button type="button" onclick="generateOrderPDF('{{ $b['id'] }}', this)"
                                    class="col-span-1 px-3 sm:px-4 py-2 sm:py-2.5 rounded-xl bg-neutral-900 hover:bg-black text-white text-xs font-bold transition-all cursor-pointer inline-flex items-center justify-center gap-1.5 shadow-2xs hover:shadow-xs text-center">
                                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
                                    <span>Download PDF</span>
                                </button>
                                <a href="{{ $waOrderUrl }}" target="_blank" rel="noopener"
                                    class="col-span-2 sm:col-span-1 px-3 sm:px-4 py-2 sm:py-2.5 rounded-xl bg-[#25D366] hover:bg-[#20bd5a] text-white text-xs font-bold transition-all inline-flex items-center justify-center gap-1.5 cursor-pointer shadow-2xs hover:shadow-xs text-center">
                                    <i class="fa-brands fa-whatsapp text-sm"></i> WhatsApp Support
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <!-- Empty State -->
            <div class="border border-[#E8E5DF] bg-white rounded-2xl sm:rounded-3xl p-8 sm:p-14 md:p-16 text-center flex flex-col items-center justify-center max-w-xl mx-auto shadow-2xs my-8">
                <div class="w-14 h-14 sm:w-16 sm:h-16 rounded-2xl bg-[#FAF9F5] border border-[#E8E5DF] flex items-center justify-center text-neutral-600 mb-4 text-xl sm:text-2xl">
                    <i class="fa-solid fa-bag-shopping"></i>
                </div>
                <h2 class="font-heading font-extrabold text-lg sm:text-xl text-main-text mb-1.5">
                    No Bookings Found
                </h2>
                <p class="text-xs sm:text-sm text-[#777777] max-w-md mb-6 leading-relaxed">
                    You haven't submitted any event celebration requests yet. Browse our handcrafted decor, sound, and party setups in Indore to get started!
                </p>
                <div class="flex items-center gap-3">
                    <a href="{{ route('events.index') }}"
                        class="px-5 sm:px-6 py-2.5 sm:py-3 rounded-full bg-[#FFD600] hover:bg-[#ffe033] text-neutral-900 text-xs font-heading font-extrabold uppercase tracking-wider transition-all shadow-2xs hover:shadow-xs">
                        Explore Celebrations &rarr;
                    </a>
                </div>
            </div>
        @endif

    </div>
</div>

@php
    $logoPath = public_path('assets/images/logo/artizen.png');
    $logoBase64 = file_exists($logoPath) ? 'data:image/png;base64,' . base64_encode(file_get_contents($logoPath)) : asset('assets/images/logo/artizen.png');
@endphp

<!-- Artizen PDF Engines (Standalone html2canvas + jsPDF - zero blank bug) -->
<script>
    window.ARTIZEN_LOGO_BASE64 = @json($logoBase64);
    window.ALL_USER_BOOKINGS = @json(collect($filteredBookings)->keyBy('id'));
</script>
<script src="{{ asset('js/html2canvas.min.js') }}"></script>
<script src="{{ asset('js/jspdf.umd.min.js') }}"></script>
<script src="{{ asset('js/artizen-pdf.js') }}?v=21.0"></script>
<script>
function copyBookingId(id, btn) {
    if (!id) return;
    navigator.clipboard.writeText(id).then(() => {
        const origHtml = btn.innerHTML;
        btn.innerHTML = '<i class="fa-solid fa-check text-emerald-600"></i>';
        setTimeout(() => { btn.innerHTML = origHtml; }, 1800);
    });
}

function generateOrderPDF(bookingId, btn) {
    if (!bookingId) return;
    const trigger = btn || (typeof event !== 'undefined' ? event.currentTarget : null);
    const booking = (window.ALL_USER_BOOKINGS && window.ALL_USER_BOOKINGS[bookingId]) || { id: bookingId };
    
    if (window.ArtizenPDF && typeof window.ArtizenPDF.download === 'function') {
        window.ArtizenPDF.download(booking, trigger);
    } else {
        window.location.href = '/booking/success?id=' + encodeURIComponent(bookingId) + '&download_pdf=1';
    }
}
</script>
@endsection
