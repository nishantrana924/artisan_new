@extends('layouts.app')

@section('content')
<div class="min-h-screen pt-8 pb-16 px-4 md:px-12 bg-white dark:bg-[#0c0c0c] text-black dark:text-white relative">
    <div class="max-w-7xl mx-auto">

        <!-- Breadcrumb Navigation -->
        <nav class="flex items-center gap-2 text-xs text-gray-500 mb-6 font-medium">
            <a href="{{ route('home') }}" class="hover:text-black dark:hover:text-white transition-colors">Home</a>
            <i class="fa-solid fa-chevron-right text-[10px]"></i>
            <span class="text-black dark:text-white font-bold">Contact Us</span>
        </nav>

        <!-- Page Header Banner -->
        <div class="border-b border-gray-200 dark:border-white/10 pb-6 mb-10 text-left">
            <span class="text-[10px] font-heading font-extrabold uppercase tracking-widest text-gold mb-2 inline-flex items-center gap-1.5 bg-gold/10 px-3 py-1 rounded-full border border-gold/30">
                <i class="fa-solid fa-headset"></i> ARTIZEN EVENT SUPPORT INDORE
            </span>
            <h1 class="font-heading font-extrabold text-3xl md:text-5xl uppercase tracking-tight text-gray-900 dark:text-white leading-tight">
                Contact & Inquiries
            </h1>
            <p class="text-sm md:text-base text-gray-600 dark:text-gray-300 max-w-2xl mt-2">
                Have a custom event request, date availability query, or venue question? Connect with our Indore event team directly via phone, WhatsApp, or the enquiry form below.
            </p>
        </div>

        <!-- Success Flash Alert -->
        @if (session('success'))
            <div class="mb-8 p-5 rounded-2xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-900/50 text-emerald-800 dark:text-emerald-300 text-xs font-bold flex items-start gap-3">
                <i class="fa-solid fa-circle-check text-base text-emerald-600 dark:text-emerald-400 shrink-0 mt-0.5"></i>
                <div>
                    <span class="block text-sm font-extrabold">Inquiry Sent Successfully!</span>
                    <span class="font-medium text-emerald-700 dark:text-emerald-300/90 mt-0.5 block">{{ session('success') }}</span>
                </div>
            </div>
        @endif

        <!-- Main 2-Column Layout -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 items-start text-left">

            <!-- Left Column: Contact Cards (5 Cols) -->
            <div class="lg:col-span-5 flex flex-col gap-6">
                
                <!-- Quick WhatsApp Card -->
                <div class="bg-gradient-to-br from-emerald-900 to-emerald-950 text-white p-6 rounded-3xl shadow-md border border-emerald-800 flex flex-col gap-4">
                    <div class="w-12 h-12 rounded-2xl bg-emerald-800/60 border border-emerald-700 flex items-center justify-center text-2xl text-emerald-400">
                        <i class="fa-brands fa-whatsapp"></i>
                    </div>
                    <div>
                        <h3 class="font-heading font-extrabold text-lg uppercase tracking-tight">Instant WhatsApp Support</h3>
                        <p class="text-xs text-emerald-200/80 font-normal mt-1 leading-relaxed">
                            Need fast setup details or date checks? Chat directly with an Artizen event planner.
                        </p>
                    </div>
                    <a href="https://wa.me/{{ $contactInfo['whatsapp'] }}?text={{ urlencode('Hi Artizen! I have an event query.') }}" target="_blank" class="w-full py-3 bg-emerald-500 hover:bg-emerald-400 text-black font-extrabold text-xs uppercase tracking-wider rounded-xl transition-all text-center flex items-center justify-center gap-2">
                        <i class="fa-brands fa-whatsapp text-base"></i> Chat on WhatsApp
                    </a>
                </div>

                <!-- Contact Details List -->
                <div class="bg-white dark:bg-[#121212] border border-gray-200 dark:border-white/10 p-6 rounded-3xl shadow-sm flex flex-col gap-5">
                    
                    <!-- Phone Call -->
                    <div class="flex items-start gap-4">
                        <div class="w-10 h-10 rounded-xl bg-gold/10 border border-gold/20 text-gold flex items-center justify-center text-lg shrink-0">
                            <i class="fa-solid fa-phone"></i>
                        </div>
                        <div>
                            <span class="text-[10px] font-bold uppercase tracking-wider text-gray-500 block">Phone Support</span>
                            <a href="tel:{{ $contactInfo['phone'] }}" class="text-sm font-extrabold text-gray-900 dark:text-white hover:text-gold transition-colors">
                                {{ $contactInfo['phone'] }}
                            </a>
                        </div>
                    </div>

                    <!-- Email -->
                    <div class="flex items-start gap-4">
                        <div class="w-10 h-10 rounded-xl bg-gold/10 border border-gold/20 text-gold flex items-center justify-center text-lg shrink-0">
                            <i class="fa-solid fa-envelope"></i>
                        </div>
                        <div>
                            <span class="text-[10px] font-bold uppercase tracking-wider text-gray-500 block">Email Us</span>
                            <a href="mailto:{{ $contactInfo['email'] }}" class="text-sm font-extrabold text-gray-900 dark:text-white hover:text-gold transition-colors">
                                {{ $contactInfo['email'] }}
                            </a>
                        </div>
                    </div>

                    <!-- Address -->
                    <div class="flex items-start gap-4">
                        <div class="w-10 h-10 rounded-xl bg-gold/10 border border-gold/20 text-gold flex items-center justify-center text-lg shrink-0">
                            <i class="fa-solid fa-location-dot"></i>
                        </div>
                        <div>
                            <span class="text-[10px] font-bold uppercase tracking-wider text-gray-500 block">Indore Office Location</span>
                            <p class="text-xs font-bold text-gray-900 dark:text-white leading-relaxed mt-0.5">
                                {{ $contactInfo['address'] }}
                            </p>
                        </div>
                    </div>

                    <!-- Business Hours -->
                    <div class="flex items-start gap-4 border-t border-gray-100 dark:border-white/5 pt-4">
                        <div class="w-10 h-10 rounded-xl bg-gold/10 border border-gold/20 text-gold flex items-center justify-center text-lg shrink-0">
                            <i class="fa-solid fa-clock"></i>
                        </div>
                        <div>
                            <span class="text-[10px] font-bold uppercase tracking-wider text-gray-500 block">Operating Hours</span>
                            <p class="text-xs font-bold text-gray-900 dark:text-white mt-0.5">
                                {{ $contactInfo['hours'] }}
                            </p>
                        </div>
                    </div>

                </div>

            </div>

            <!-- Right Column: Interactive Form (7 Cols) -->
            <div class="lg:col-span-7 bg-white dark:bg-[#121212] border border-gray-200 dark:border-white/10 p-6 md:p-8 rounded-3xl shadow-sm">
                <h3 class="font-heading font-extrabold text-xl uppercase tracking-tight text-gray-900 dark:text-white mb-2">
                    Send Us a Message
                </h3>
                <p class="text-xs text-gray-500 dark:text-gray-400 font-normal mb-6">
                    Fill out the inquiry form below. We respond to all event inquiries within 15 minutes.
                </p>

                <form action="{{ route('contact.store') }}" method="POST" class="space-y-5">
                    @csrf

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-1.5">Your Name *</label>
                            <input type="text" name="name" value="{{ old('name') }}" required placeholder="e.g. Priyesh Patel" class="w-full px-4 py-3 bg-gray-50 dark:bg-white/5 border border-gray-200 dark:border-white/10 rounded-xl text-xs text-gray-900 dark:text-white focus:outline-none focus:border-gold focus:ring-1 focus:ring-gold">
                        </div>

                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-1.5">Phone / WhatsApp *</label>
                            <input type="tel" name="phone" value="{{ old('phone') }}" required placeholder="e.g. 9826012345" class="w-full px-4 py-3 bg-gray-50 dark:bg-white/5 border border-gray-200 dark:border-white/10 rounded-xl text-xs text-gray-900 dark:text-white focus:outline-none focus:border-gold focus:ring-1 focus:ring-gold">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-1.5">Email Address</label>
                            <input type="email" name="email" value="{{ old('email') }}" placeholder="e.g. priyesh@example.com" class="w-full px-4 py-3 bg-gray-50 dark:bg-white/5 border border-gray-200 dark:border-white/10 rounded-xl text-xs text-gray-900 dark:text-white focus:outline-none focus:border-gold focus:ring-1 focus:ring-gold">
                        </div>

                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-1.5">Event Subject / Type *</label>
                            <select name="subject" required class="w-full px-4 py-3 bg-gray-50 dark:bg-white/5 border border-gray-200 dark:border-white/10 rounded-xl text-xs text-gray-900 dark:text-white focus:outline-none focus:border-gold focus:ring-1 focus:ring-gold">
                                <option value="Birthday Party Setup">Birthday Party Setup</option>
                                <option value="House Party & DJ Rigs">House Party & DJ Rigs</option>
                                <option value="Proposal & Anniversary Decor">Proposal & Anniversary Decor</option>
                                <option value="Wedding & Sangeet Setup">Wedding & Sangeet Setup</option>
                                <option value="Baby Shower & Naming">Baby Shower & Naming</option>
                                <option value="Corporate Event Inquiry">Corporate Event Inquiry</option>
                                <option value="Custom Event Package Request">Custom Event Package Request</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-1.5">Message / Details *</label>
                        <textarea name="message" rows="4" required placeholder="Tell us about your event date, expected guest count, and any special decoration or sound system requirements..." class="w-full px-4 py-3 bg-gray-50 dark:bg-white/5 border border-gray-200 dark:border-white/10 rounded-xl text-xs text-gray-900 dark:text-white focus:outline-none focus:border-gold focus:ring-1 focus:ring-gold">{{ old('message') }}</textarea>
                    </div>

                    <button type="submit" class="w-full py-4 bg-[#FFD600] hover:bg-[#E6C200] text-[#171719] font-heading font-extrabold text-xs uppercase tracking-widest rounded-2xl shadow-lg transition-all flex items-center justify-center gap-2 cursor-pointer">
                        <i class="fa-solid fa-paper-plane text-[#171719]"></i> Send Message
                    </button>
                </form>
            </div>

        </div>

    </div>
</div>
@endsection
