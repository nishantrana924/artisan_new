@extends('layouts.app')

@section('content')
<div class="min-h-screen pt-8 pb-16 px-4 md:px-12 bg-white dark:bg-[#0C0C0E] text-[#1E1E24] dark:text-white relative">
    <div class="max-w-7xl mx-auto">

        <!-- Breadcrumb Navigation -->
        <nav class="flex items-center gap-2 text-xs text-gray-500 mb-6 font-medium">
            <a href="{{ route('home') }}" class="hover:text-[#EA741D] transition-colors">Home</a>
            <i class="fa-solid fa-chevron-right text-[10px]"></i>
            <a href="{{ route('events.index') }}" class="hover:text-[#EA741D] transition-colors">Packages</a>
            <i class="fa-solid fa-chevron-right text-[10px]"></i>
            <span class="text-[#1E1E24] dark:text-white font-bold">Booking Request</span>
        </nav>

        <!-- Page Header Banner -->
        <div class="border-b border-gray-200 dark:border-white/10 pb-6 mb-10 text-left">
            <span class="text-[10px] font-heading font-extrabold uppercase tracking-widest text-[#EA741D] mb-2 inline-flex items-center gap-1.5 bg-[#EA741D]/10 px-3 py-1 rounded-full border border-[#EA741D]/30">
                <i class="fa-solid fa-calendar-check"></i> NO ONLINE PAYMENT REQUIRED
            </span>
            <h1 class="font-heading font-extrabold text-3xl md:text-5xl uppercase tracking-tight text-[#1E1E24] dark:text-white leading-tight">
                Customize & Book Package
            </h1>
            <p class="text-sm md:text-base text-gray-600 dark:text-gray-300 max-w-2xl mt-2 leading-relaxed">
                Submit your celebration requirements below. Our Artizen event manager will contact you on WhatsApp/Phone to confirm venue logistics and offline payment.
            </p>
        </div>

        <!-- Validation Errors Alert -->
        @if ($errors->any())
            <div class="mb-8 p-4 rounded-2xl bg-red-50 dark:bg-red-950/40 border border-red-200 dark:border-red-900/50 text-red-700 dark:text-red-300 text-xs font-semibold">
                <div class="flex items-center gap-2 font-bold mb-1">
                    <i class="fa-solid fa-triangle-exclamation"></i>
                    <span>Please correct the errors in the form below:</span>
                </div>
                <ul class="list-disc list-inside space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Main 2-Column Form Layout -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 items-start text-left">
            
            <!-- Left Side Form (7 Cols) -->
            <div class="lg:col-span-7 bg-white dark:bg-[#121214] border border-gray-200 dark:border-white/10 p-6 md:p-8 rounded-3xl shadow-sm">
                <form action="{{ route('booking.save') }}" method="POST" id="main-booking-form" class="space-y-8">
                    @csrf

                    <!-- Hidden fields for pricing calculation -->
                    <input type="hidden" name="price" id="form-price-input" value="{{ $selectedPrice > 0 ? $selectedPrice : 4999 }}">
                    <input type="hidden" name="total_price" id="form-total-price-input" value="{{ $selectedPrice > 0 ? $selectedPrice : 4999 }}">

                    <!-- Step 1: Customer Information -->
                    <div>
                        <h3 class="font-heading font-extrabold text-lg uppercase tracking-tight text-[#1E1E24] dark:text-white mb-4 flex items-center gap-2">
                            <span class="w-7 h-7 rounded-full bg-[#EA741D] text-white text-xs font-black flex items-center justify-center shadow-sm">1</span>
                            Customer Contact Information
                        </h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-1.5">Full Name *</label>
                                <input type="text" name="name" value="{{ old('name') }}" required placeholder="e.g. Rahul Sharma" class="w-full px-4 py-3 bg-gray-50 dark:bg-white/5 border border-gray-200 dark:border-white/10 rounded-xl text-xs text-[#1E1E24] dark:text-white focus:outline-none focus:border-[#EA741D] transition-colors">
                            </div>

                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-1.5">Mobile Number *</label>
                                <input type="tel" name="mobile" value="{{ old('mobile') }}" required placeholder="e.g. 9826012345" class="w-full px-4 py-3 bg-gray-50 dark:bg-white/5 border border-gray-200 dark:border-white/10 rounded-xl text-xs text-[#1E1E24] dark:text-white focus:outline-none focus:border-[#EA741D] transition-colors">
                            </div>

                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-1.5">WhatsApp Number *</label>
                                <input type="tel" name="whatsapp" value="{{ old('whatsapp') }}" required placeholder="e.g. 9826012345" class="w-full px-4 py-3 bg-gray-50 dark:bg-white/5 border border-gray-200 dark:border-white/10 rounded-xl text-xs text-[#1E1E24] dark:text-white focus:outline-none focus:border-[#EA741D] transition-colors">
                            </div>

                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-1.5">Email Address</label>
                                <input type="email" name="email" value="{{ old('email') }}" placeholder="e.g. rahul@example.com" class="w-full px-4 py-3 bg-gray-50 dark:bg-white/5 border border-gray-200 dark:border-white/10 rounded-xl text-xs text-[#1E1E24] dark:text-white focus:outline-none focus:border-[#EA741D] transition-colors">
                            </div>
                        </div>
                    </div>

                    <hr class="border-gray-100 dark:border-white/5">

                    <!-- Step 2: Event Details -->
                    <div>
                        <h3 class="font-heading font-extrabold text-lg uppercase tracking-tight text-[#1E1E24] dark:text-white mb-4 flex items-center gap-2">
                            <span class="w-7 h-7 rounded-full bg-[#EA741D] text-white text-xs font-black flex items-center justify-center shadow-sm">2</span>
                            Event & Package Selection
                        </h3>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-1.5">Event Category *</label>
                                <select name="category" id="booking-category-select" onchange="updatePackageOptions()" class="w-full px-4 py-3 bg-gray-50 dark:bg-[#1A1A1E] border border-gray-200 dark:border-white/10 rounded-xl text-xs text-[#1E1E24] dark:text-white focus:outline-none focus:border-[#EA741D] transition-colors">
                                    @foreach($categoriesList as $cat)
                                        @if($cat['active'] ?? true)
                                            <option value="{{ $cat['title'] }}" {{ ($selectedCat === $cat['title']) ? 'selected' : '' }}>{{ $cat['title'] }}</option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-1.5">Package & Tier *</label>
                                <select name="package" id="booking-package-select" onchange="updateSummaryPricing()" class="w-full px-4 py-3 bg-gray-50 dark:bg-[#1A1A1E] border border-gray-200 dark:border-white/10 rounded-xl text-xs text-[#1E1E24] dark:text-white focus:outline-none focus:border-[#EA741D] transition-colors">
                                    <!-- Populated dynamically by JS -->
                                </select>
                            </div>

                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-1.5">Event Date *</label>
                                <input type="date" name="event_date" value="{{ old('event_date', date('Y-m-d', strtotime('+2 days'))) }}" min="{{ date('Y-m-d') }}" required class="w-full px-4 py-3 bg-gray-50 dark:bg-white/5 border border-gray-200 dark:border-white/10 rounded-xl text-xs text-[#1E1E24] dark:text-white focus:outline-none focus:border-[#EA741D] transition-colors">
                            </div>

                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-1.5">Event Setup Time *</label>
                                <select name="event_time" required class="w-full px-4 py-3 bg-gray-50 dark:bg-[#1A1A1E] border border-gray-200 dark:border-white/10 rounded-xl text-xs text-[#1E1E24] dark:text-white focus:outline-none focus:border-[#EA741D] transition-colors">
                                    <option value="Morning (09:00 AM - 12:00 PM)">Morning (09:00 AM - 12:00 PM)</option>
                                    <option value="Afternoon (12:00 PM - 04:00 PM)">Afternoon (12:00 PM - 04:00 PM)</option>
                                    <option value="Evening (04:00 PM - 08:00 PM)" selected>Evening (04:00 PM - 08:00 PM)</option>
                                    <option value="Night (08:00 PM - Midnight)">Night (08:00 PM - Midnight)</option>
                                </select>
                            </div>

                            <div class="md:col-span-2">
                                <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-1.5">Expected Guest Count *</label>
                                <input type="number" name="guest_count" value="{{ old('guest_count', 30) }}" min="1" max="1000" required placeholder="e.g. 30 Guests" class="w-full px-4 py-3 bg-gray-50 dark:bg-white/5 border border-gray-200 dark:border-white/10 rounded-xl text-xs text-[#1E1E24] dark:text-white focus:outline-none focus:border-[#EA741D] transition-colors">
                            </div>
                        </div>
                    </div>

                    <hr class="border-gray-100 dark:border-white/5">

                    <!-- Step 3: Venue Details -->
                    <div>
                        <h3 class="font-heading font-extrabold text-lg uppercase tracking-tight text-[#1E1E24] dark:text-white mb-4 flex items-center gap-2">
                            <span class="w-7 h-7 rounded-full bg-[#EA741D] text-white text-xs font-black flex items-center justify-center shadow-sm">3</span>
                            Venue Location (Indore)
                        </h3>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="md:col-span-2">
                                <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-1.5">Full Venue Address *</label>
                                <textarea name="address" rows="2" required placeholder="e.g. House No. 42, Green Park Colony..." class="w-full px-4 py-3 bg-gray-50 dark:bg-white/5 border border-gray-200 dark:border-white/10 rounded-xl text-xs text-[#1E1E24] dark:text-white focus:outline-none focus:border-[#EA741D] transition-colors">{{ old('address') }}</textarea>
                            </div>

                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-1.5">Area / Colony *</label>
                                <input type="text" name="area" value="{{ old('area') }}" required placeholder="e.g. Vijay Nagar / Palasia / Saket" class="w-full px-4 py-3 bg-gray-50 dark:bg-white/5 border border-gray-200 dark:border-white/10 rounded-xl text-xs text-[#1E1E24] dark:text-white focus:outline-none focus:border-[#EA741D] transition-colors">
                            </div>

                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-1.5">Landmark</label>
                                <input type="text" name="landmark" value="{{ old('landmark') }}" placeholder="e.g. Near C21 Mall" class="w-full px-4 py-3 bg-gray-50 dark:bg-white/5 border border-gray-200 dark:border-white/10 rounded-xl text-xs text-[#1E1E24] dark:text-white focus:outline-none focus:border-[#EA741D] transition-colors">
                            </div>

                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-1.5">City</label>
                                <input type="text" name="city" value="Indore" readonly class="w-full px-4 py-3 bg-gray-100 dark:bg-white/10 border border-gray-200 dark:border-white/10 rounded-xl text-xs text-[#1E1E24] dark:text-white font-bold cursor-not-allowed">
                            </div>

                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-1.5">Pincode *</label>
                                <input type="text" name="pincode" value="{{ old('pincode', '452010') }}" required placeholder="e.g. 452010" class="w-full px-4 py-3 bg-gray-50 dark:bg-white/5 border border-gray-200 dark:border-white/10 rounded-xl text-xs text-[#1E1E24] dark:text-white focus:outline-none focus:border-[#EA741D] transition-colors">
                            </div>
                        </div>
                    </div>

                    <hr class="border-gray-100 dark:border-white/5">

                    <!-- Step 4: Special Notes -->
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-1.5">Special Instructions / Customization Notes</label>
                        <textarea name="notes" rows="2" placeholder="e.g. Preferred color theme: Red & Gold. Need extra mic." class="w-full px-4 py-3 bg-gray-50 dark:bg-white/5 border border-gray-200 dark:border-white/10 rounded-xl text-xs text-[#1E1E24] dark:text-white focus:outline-none focus:border-[#EA741D] transition-colors">{{ old('notes') }}</textarea>
                    </div>

                    <!-- Submit Primary CTA Button (Solid Gold) -->
                    <button type="submit" class="w-full py-4 bg-[#EA741D] hover:bg-[#D6630F] text-white font-heading font-extrabold text-sm uppercase tracking-widest rounded-2xl shadow-lg hover:shadow-xl transition-all flex items-center justify-center gap-2 cursor-pointer">
                        <i class="fa-solid fa-paper-plane text-[#1E1E24]"></i> Submit Booking Request
                    </button>

                    <p class="text-[11px] text-gray-500 text-center font-medium">
                        No payment is collected now. Pay offline after admin contacts and confirms your date.
                    </p>
                </form>
            </div>

            <!-- Right Side Sticky Summary Card (5 Cols) -->
            <div class="lg:col-span-5 lg:sticky lg:top-28">
                <div class="bg-white dark:bg-[#121214] border border-[#EA741D]/30 dark:border-white/10 p-6 rounded-3xl shadow-sm text-left flex flex-col gap-6">
                    <div>
                        <span class="text-[9px] font-heading font-extrabold text-[#EA741D] uppercase tracking-widest block mb-1">
                            REALTIME BOOKING SUMMARY
                        </span>
                        <h3 id="summary-package-title" class="font-heading font-extrabold text-xl text-[#1E1E24] dark:text-white leading-tight">
                            Select Package
                        </h3>
                        <span id="summary-category-badge" class="inline-block mt-2 text-[10px] font-bold uppercase tracking-wider bg-[#EA741D]/10 text-[#EA741D] px-2.5 py-1 rounded-md border border-[#EA741D]/30">
                            Event Setup
                        </span>
                    </div>

                    <div class="space-y-3 text-xs text-gray-600 dark:text-gray-300 border-t border-b border-gray-100 dark:border-white/5 py-4 font-medium">
                        <div class="flex items-center justify-between">
                            <span class="text-gray-500">City / Coverage:</span>
                            <span class="font-bold text-[#1E1E24] dark:text-white">Indore, MP</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-gray-500">Payment Mode:</span>
                            <span class="font-bold text-emerald-600 dark:text-emerald-400">Offline Post Confirmation</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-gray-500">Initial Status:</span>
                            <span class="font-bold text-[#EA741D] uppercase">PENDING REVIEW</span>
                        </div>
                    </div>

                    <!-- Price Breakdown -->
                    <div class="space-y-2 text-xs">
                        <div class="flex items-center justify-between text-gray-600 dark:text-gray-400 font-medium">
                            <span>Base Package Price:</span>
                            <span id="summary-base-price" class="font-bold text-[#1E1E24] dark:text-white">₹4,999</span>
                        </div>
                        <div class="flex items-center justify-between text-gray-600 dark:text-gray-400 font-medium">
                            <span>Logistics & Setup Fee:</span>
                            <span class="font-bold text-emerald-600">INCLUDED (Free)</span>
                        </div>
                        <div class="flex items-center justify-between text-gray-600 dark:text-gray-400 font-medium">
                            <span>Online Gate Fee:</span>
                            <span class="font-bold text-emerald-600">₹0 (Zero Fee)</span>
                        </div>
                        <div class="pt-3 border-t border-gray-100 dark:border-white/5 flex items-baseline justify-between">
                            <span class="font-heading font-extrabold text-sm text-[#1E1E24] dark:text-white uppercase">Total Booking Amount:</span>
                            <span id="summary-total-price" class="font-heading font-extrabold text-2xl text-[#EA741D]">₹4,999</span>
                        </div>
                    </div>

                    <div class="p-4 rounded-2xl bg-[#EA741D]/10 border border-[#EA741D]/30 text-xs text-[#1E1E24] dark:text-gray-200 font-medium leading-relaxed">
                        <i class="fa-solid fa-circle-info text-[#EA741D] mr-1"></i>
                        After submission, your booking status becomes <strong class="font-bold">PENDING</strong>. An Artizen manager will contact you on WhatsApp/Phone within 15 minutes.
                    </div>
                </div>
            </div>

        </div>

    </div>
</div>

<script>
    const packagesData = @json($packagesDb);
    const initialSelectedPkg = @json($selectedPkgName);

    function updatePackageOptions() {
        const catSelect = document.getElementById('booking-category-select');
        const pkgSelect = document.getElementById('booking-package-select');
        const selectedCatTitle = catSelect.value;

        pkgSelect.innerHTML = '';
        let foundTiers = [];

        Object.values(packagesData).forEach(cat => {
            if (cat.title === selectedCatTitle || cat.title.toLowerCase().includes(selectedCatTitle.toLowerCase())) {
                (cat.tiers || []).forEach(tier => {
                    foundTiers.push({
                        name: `${cat.title} - ${tier.name}`,
                        price: tier.price || 0,
                        category: cat.title
                    });
                });
            }
        });

        if (foundTiers.length === 0) {
            foundTiers.push({
                name: `${selectedCatTitle} Setup Package`,
                price: 4999,
                category: selectedCatTitle
            });
        }

        foundTiers.forEach((t, idx) => {
            const opt = document.createElement('option');
            opt.value = t.name;
            opt.dataset.price = t.price;
            opt.dataset.category = t.category;
            opt.textContent = `${t.name} (₹${t.price.toLocaleString('en-IN')})`;
            if (initialSelectedPkg && t.name.toLowerCase().includes(initialSelectedPkg.toLowerCase())) {
                opt.selected = true;
            }
            pkgSelect.appendChild(opt);
        });

        updateSummaryPricing();
    }

    function updateSummaryPricing() {
        const pkgSelect = document.getElementById('booking-package-select');
        const selectedOpt = pkgSelect.options[pkgSelect.selectedIndex];
        if (!selectedOpt) return;

        const price = parseInt(selectedOpt.dataset.price) || 4999;
        const title = selectedOpt.value;
        const category = selectedOpt.dataset.category || 'Event Setup';

        document.getElementById('form-price-input').value = price;
        document.getElementById('form-total-price-input').value = price;
        document.getElementById('summary-package-title').innerText = title;
        document.getElementById('summary-category-badge').innerText = category;
        document.getElementById('summary-base-price').innerText = `₹${price.toLocaleString('en-IN')}`;
        document.getElementById('summary-total-price').innerText = `₹${price.toLocaleString('en-IN')}`;
    }

    document.addEventListener('DOMContentLoaded', () => {
        updatePackageOptions();
    });
</script>
@endsection
