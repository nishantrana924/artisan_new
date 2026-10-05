@extends('layouts.app')

@section('title', 'Contact Artizen | Plan Your Celebration in Indore')
@section('meta_description', 'Get in touch with Artizen to plan your bespoke celebration in Indore. Inquire online or connect via WhatsApp and phone.')

@section('content')
<div class="min-h-screen bg-[#FBFBF9] dark:bg-[#0B0B0E] text-[#171719] dark:text-[#F3F4F6] py-8 sm:py-12 select-none">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Header: Minimal & Understated -->
        <div class="max-w-xl mb-6 sm:mb-8 text-left">
            <span class="text-xs uppercase tracking-widest text-[#9A7B38] dark:text-[#D4AF37] font-semibold">Artizen Concierge</span>
            <h1 class="text-2xl sm:text-3xl lg:text-4xl font-heading font-bold text-gray-950 dark:text-white tracking-tight mt-1">
                Let's plan your celebration.
            </h1>
            <p class="text-xs sm:text-sm text-gray-600 dark:text-gray-400 mt-1.5 font-normal">
                Tell us about your event vision, date, or venue in Indore. We'll be in touch shortly.
            </p>
        </div>

        @if (session('success'))
            <div class="mb-6 p-4 rounded-xl bg-emerald-50/80 dark:bg-emerald-950/30 border border-emerald-200 dark:border-emerald-800/40 text-emerald-900 dark:text-emerald-200 flex items-center justify-between gap-4 text-left">
                <div class="flex items-center gap-3">
                    <i class="fa-solid fa-check text-emerald-600 dark:text-emerald-400 text-sm"></i>
                    <p class="text-xs sm:text-sm font-medium">{{ session('success') }}</p>
                </div>
            </div>
        @endif

        <!-- Separate Standalone Contact Channels (Even padding, balanced vertical spacing) -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3.5 mb-6 text-left">
            <!-- Direct Call -->
            <a href="tel:+919131668156" 
               class="py-3 px-4 sm:py-3.5 sm:px-4 rounded-xl bg-white dark:bg-[#121216] border border-[#EBE7DF] dark:border-[#202026] hover:border-gray-900 dark:hover:border-[#FFD600] transition-all shadow-xs flex flex-col justify-center group">
                <span class="text-[10px] text-gray-400 uppercase tracking-widest font-semibold leading-tight">Direct Call</span>
                <span class="text-xs sm:text-sm font-bold text-gray-900 dark:text-white mt-1 leading-tight group-hover:text-amber-600 dark:group-hover:text-[#FFD600] transition-colors">
                    +91 91316 68156
                </span>
            </a>

            <!-- WhatsApp -->
            <a href="https://wa.me/919131668156?text={{ urlencode('Hi Artizen, I would like to inquire about an event celebration package in Indore.') }}" 
               target="_blank" 
               rel="noopener noreferrer" 
               class="py-3 px-4 sm:py-3.5 sm:px-4 rounded-xl bg-white dark:bg-[#121216] border border-[#EBE7DF] dark:border-[#202026] hover:border-emerald-500 transition-all shadow-xs flex flex-col justify-center group">
                <span class="text-[10px] text-gray-400 uppercase tracking-widest font-semibold leading-tight">WhatsApp</span>
                <span class="text-xs sm:text-sm font-bold text-emerald-600 dark:text-emerald-400 mt-1 leading-tight">
                    +91 91316 68156
                </span>
            </a>

            <!-- Email -->
            <a href="mailto:info@artizenevents.com" 
               class="py-3 px-4 sm:py-3.5 sm:px-4 rounded-xl bg-white dark:bg-[#121216] border border-[#EBE7DF] dark:border-[#202026] hover:border-gray-900 dark:hover:border-[#FFD600] transition-all shadow-xs flex flex-col justify-center group">
                <span class="text-[10px] text-gray-400 uppercase tracking-widest font-semibold leading-tight">Email Desk</span>
                <span class="text-xs sm:text-sm font-bold text-gray-900 dark:text-white mt-1 leading-tight truncate">
                    info@artizenevents.com
                </span>
            </a>
        </div>

        <!-- Main Split Card: Clean Shorter Lifestyle Image & Compact Form -->
        <div class="grid grid-cols-1 lg:grid-cols-12 rounded-2xl bg-white dark:bg-[#121216] border border-[#EBE7DF] dark:border-[#202026] shadow-sm overflow-hidden">
            
            <!-- Left Side: Clean Lifestyle Image (Shorter, controlled height) -->
            <div class="lg:col-span-5 relative h-[220px] sm:h-[260px] lg:h-full lg:max-h-[440px] overflow-hidden bg-gray-100 dark:bg-gray-900">
                <img src="{{ asset('images/contact/contact-hero.webp') }}" 
                     alt="Artizen Celebration" 
                     class="w-full h-full object-cover object-center transition-transform duration-700 hover:scale-105"
                     loading="eager">
            </div>

            <!-- Right Side: Clean Inquiry Form -->
            <div class="lg:col-span-7 p-5 sm:p-7 text-left">
                <div class="mb-4">
                    <span class="text-[11px] uppercase tracking-wider text-[#9A7B38] dark:text-[#D4AF37] font-semibold">Inquiry Form</span>
                    <h2 class="text-xl sm:text-2xl font-heading font-bold text-gray-950 dark:text-white tracking-tight mt-0.5">
                        Send An Inquiry
                    </h2>
                </div>

                <form action="{{ route('contact.store') }}" method="POST" class="space-y-3">
                    @csrf

                    <!-- Row 1: Name & Phone -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <input type="text" 
                                   name="name" 
                                   value="{{ old('name') }}" 
                                   required 
                                   placeholder="Your Full Name *" 
                                   class="w-full px-3.5 py-2.5 bg-[#FAF8F5] dark:bg-[#18181D] border border-[#E8E3DA] dark:border-[#282830] rounded-lg text-xs sm:text-sm text-gray-900 dark:text-white placeholder-gray-400 focus:outline-none focus:border-gray-950 dark:focus:border-[#FFD600] transition-colors">
                            @error('name')
                                <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span>
                            @enderror
                        </div>

                        <div>
                            <input type="tel" 
                                   name="phone" 
                                   value="{{ old('phone') }}" 
                                   required 
                                   placeholder="Phone / WhatsApp Number *" 
                                   class="w-full px-3.5 py-2.5 bg-[#FAF8F5] dark:bg-[#18181D] border border-[#E8E3DA] dark:border-[#282830] rounded-lg text-xs sm:text-sm text-gray-900 dark:text-white placeholder-gray-400 focus:outline-none focus:border-gray-950 dark:focus:border-[#FFD600] transition-colors">
                            @error('phone')
                                <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <!-- Row 2: Email & Celebration Type -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <input type="email" 
                                   name="email" 
                                   value="{{ old('email') }}" 
                                   required 
                                   placeholder="Email Address *" 
                                   class="w-full px-3.5 py-2.5 bg-[#FAF8F5] dark:bg-[#18181D] border border-[#E8E3DA] dark:border-[#282830] rounded-lg text-xs sm:text-sm text-gray-900 dark:text-white placeholder-gray-400 focus:outline-none focus:border-gray-950 dark:focus:border-[#FFD600] transition-colors">
                            @error('email')
                                <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="relative">
                            <select name="subject" 
                                    required 
                                    class="w-full px-3.5 py-2.5 pr-8 bg-[#FAF8F5] dark:bg-[#18181D] border border-[#E8E3DA] dark:border-[#282830] rounded-lg text-xs sm:text-sm text-gray-900 dark:text-white focus:outline-none focus:border-gray-950 dark:focus:border-[#FFD600] transition-colors appearance-none cursor-pointer">
                                <option value="" disabled {{ old('subject') ? '' : 'selected' }}>Select Celebration / Event Type *</option>
                                <option value="Birthday Celebration" {{ old('subject') == 'Birthday Celebration' ? 'selected' : '' }}>Birthday Celebration</option>
                                <option value="Romantic Proposal / Date" {{ old('subject') == 'Romantic Proposal / Date' ? 'selected' : '' }}>Romantic Proposal / Date</option>
                                <option value="Anniversary Setup" {{ old('subject') == 'Anniversary Setup' ? 'selected' : '' }}>Anniversary Setup</option>
                                <option value="House Party & DJ" {{ old('subject') == 'House Party & DJ' ? 'selected' : '' }}>House Party & DJ</option>
                                <option value="Wedding / Sangeet Decor" {{ old('subject') == 'Wedding / Sangeet Decor' ? 'selected' : '' }}>Wedding / Sangeet Decor</option>
                                <option value="Baby Shower / Kids" {{ old('subject') == 'Baby Shower / Kids' ? 'selected' : '' }}>Baby Shower / Kids</option>
                                <option value="Live Artist / Acoustic" {{ old('subject') == 'Live Artist / Acoustic' ? 'selected' : '' }}>Live Artist / Acoustic</option>
                                <option value="Corporate Event" {{ old('subject') == 'Corporate Event' ? 'selected' : '' }}>Corporate Event</option>
                                <option value="Custom Bespoke Celebration" {{ old('subject') == 'Custom Bespoke Celebration' ? 'selected' : '' }}>Custom Bespoke Celebration</option>
                            </select>
                            <span class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none text-gray-400 text-xs">
                                <i class="fa-solid fa-chevron-down text-[10px]"></i>
                            </span>
                            @error('subject')
                                <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <!-- Row 3: Message / Event Details -->
                    <div>
                        <textarea name="message" 
                                  rows="3" 
                                  required 
                                  placeholder="Tell us about your event (Date, venue in Indore, guest count, theme ideas)... *" 
                                  class="w-full px-3.5 py-2.5 bg-[#FAF8F5] dark:bg-[#18181D] border border-[#E8E3DA] dark:border-[#282830] rounded-lg text-xs sm:text-sm text-gray-900 dark:text-white placeholder-gray-400 focus:outline-none focus:border-gray-950 dark:focus:border-[#FFD600] transition-colors leading-relaxed">{{ old('message') }}</textarea>
                        @error('message')
                            <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Submit Button -->
                    <div class="pt-1">
                        <button type="submit" 
                                class="w-full sm:w-auto px-7 py-3 bg-gray-950 hover:bg-black dark:bg-[#FFD600] dark:hover:bg-[#E6C200] text-white dark:text-gray-950 font-semibold text-xs uppercase tracking-widest rounded-lg transition-all shadow hover:shadow-md cursor-pointer inline-flex items-center justify-center gap-2">
                            <span>Submit Inquiry</span>
                            <i class="fa-solid fa-arrow-right text-xs"></i>
                        </button>
                    </div>
                </form>
            </div>

        </div>

    </div>
</div>
@endsection
