@extends('layouts.app')

@section('title', 'Frequently Asked Questions & Help Center | ARTIZEN Indore')
@section('meta_description', 'Find answers to common questions regarding event package bookings, setup times, customization, and offline payments in Indore with Artizen.')

@section('content')
<div class="min-h-screen pt-8 pb-16 px-4 md:px-12 bg-white dark:bg-[#0c0c0c] text-black dark:text-white relative select-none">
    <div class="max-w-5xl mx-auto">

        <!-- Breadcrumb Navigation -->
        <nav class="flex items-center gap-2 text-xs text-gray-500 mb-6 font-medium">
            <a href="{{ route('home') }}" class="hover:text-black dark:hover:text-white transition-colors">Home</a>
            <i class="fa-solid fa-chevron-right text-[10px]"></i>
            <span class="text-black dark:text-white font-bold">Help Center & FAQs</span>
        </nav>

        <!-- FAQ Hero Header -->
        <div class="text-center max-w-2xl mx-auto mb-10">
            <span class="text-[10px] font-heading font-extrabold uppercase tracking-widest text-amber-700 dark:text-amber-400 mb-2.5 inline-flex items-center gap-1.5 bg-amber-500/10 px-3.5 py-1 rounded-full border border-amber-500/30">
                <i class="fa-solid fa-circle-question text-xs"></i> ARTIZEN HELP CENTER
            </span>
            <h1 class="font-heading font-extrabold text-3xl sm:text-4xl md:text-5xl uppercase tracking-tight text-gray-900 dark:text-white leading-tight">
                Frequently Asked Questions
            </h1>
            <p class="text-xs sm:text-sm text-gray-500 dark:text-gray-400 mt-3 leading-relaxed font-normal">
                Everything you need to know about booking celebration setups, venue arrival timings, package customization, and offline payments in Indore.
            </p>

            <!-- Search Input Bar -->
            <div class="relative mt-6 max-w-lg mx-auto">
                <i class="fa-solid fa-magnifying-glass absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                <input type="text" 
                       id="faq-search-input" 
                       placeholder="Search your question (e.g. payment, arrival, custom theme)..." 
                       class="w-full pl-11 pr-4 py-3 bg-[#FAF9F6] dark:bg-[#121217] border border-gray-200 dark:border-white/10 rounded-2xl text-xs sm:text-sm text-gray-900 dark:text-white placeholder-gray-400 focus:outline-none focus:border-gray-400 dark:focus:border-white/30 transition-all shadow-2xs">
            </div>
        </div>

        @php
            $categoryPills = [
                'booking'       => 'Booking',
                'payments'      => 'Offline Payments',
                'setup'         => 'Setup & Timings',
                'customization' => 'Themes & Custom',
                'policy'        => 'Cancellations',
                'general'       => 'General',
            ];
            $activeCats = array_unique(array_column($faqList, 'category'));
        @endphp
        <!-- Category Filter Pills -->
        <div class="flex items-center justify-center gap-2 overflow-x-auto pb-4 mb-8 scrollbar-hide">
            <button onclick="filterFaqCategory('all', this)" class="faq-category-btn active px-4 py-2 rounded-xl text-xs font-bold shrink-0 transition-all bg-black text-white dark:bg-white dark:text-black shadow-sm cursor-pointer">
                All ({{ count($faqList) }})
            </button>
            @foreach($categoryPills as $catKey => $catLabel)
                @if(in_array($catKey, $activeCats))
                    <button onclick="filterFaqCategory('{{ $catKey }}', this)" class="faq-category-btn px-4 py-2 rounded-xl text-xs font-medium shrink-0 transition-all bg-gray-100 dark:bg-[#15151A] text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-white/10 cursor-pointer">
                        {{ $catLabel }}
                    </button>
                @endif
            @endforeach
        </div>

        <!-- FAQs Accordion List (ALL CLOSED BY DEFAULT) -->
        <div class="space-y-3" id="faq-list-container">
            @forelse ($faqList as $index => $faq)
                <div class="faq-page-item rounded-2xl bg-[#FAF9F6] dark:bg-[#121217] border border-gray-200/80 dark:border-white/10 hover:border-gray-300 dark:hover:border-white/20 transition-all duration-200 overflow-hidden shadow-xs"
                     data-category="{{ $faq['category'] ?? 'general' }}"
                     data-question="{{ strtolower($faq['q']) }}"
                     data-answer="{{ strtolower(strip_tags($faq['a'])) }}">
                    
                    <!-- Question Header Trigger (Closed by default) -->
                    <button type="button" 
                            class="faq-page-trigger w-full text-left px-5 sm:px-6 py-4 sm:py-4.5 flex items-center justify-between gap-4 cursor-pointer select-none focus:outline-none group"
                            aria-expanded="false"
                            aria-controls="faq-full-answer-{{ $index }}"
                            onclick="toggleFaqPageItem({{ $index }})">
                        <div class="min-w-0 pr-2">
                            <span class="text-sm sm:text-[15px] font-heading font-bold text-gray-900 dark:text-white group-hover:text-amber-800 dark:group-hover:text-amber-400 transition-colors leading-snug block">
                                {{ $faq['q'] }}
                            </span>
                            @if(!empty($faq['category_label']))
                                <span class="text-[10px] text-gray-400 dark:text-gray-500 font-medium uppercase tracking-wider block mt-0.5">
                                    {{ $faq['category_label'] }}
                                </span>
                            @endif
                        </div>
                        <div class="w-8 h-8 rounded-full bg-white dark:bg-[#1C1C22] border border-gray-200 dark:border-white/10 flex items-center justify-center shrink-0 text-gray-500 dark:text-gray-400 group-hover:text-gray-900 dark:group-hover:text-white group-hover:border-gray-300 dark:group-hover:border-white/20 transition-all">
                            <i id="faq-page-chevron-{{ $index }}" class="fa-solid fa-chevron-down text-[11px] transition-transform duration-300"></i>
                        </div>
                    </button>

                    <!-- Answer Body (Hidden by default) -->
                    <div id="faq-full-answer-{{ $index }}" 
                         class="faq-page-body transition-all duration-300 ease-in-out hidden">
                        <div class="px-5 sm:px-6 pb-4 sm:pb-5 pt-1 text-xs sm:text-[13px] text-gray-600 dark:text-gray-300 leading-relaxed font-normal border-t border-gray-200/50 dark:border-white/5">
                            {!! nl2br(e($faq['a'])) !!}
                        </div>
                    </div>

                </div>
            @empty
                <div class="text-center py-16 bg-[#FAF9F6] dark:bg-[#121217] rounded-3xl border border-dashed border-gray-200 dark:border-white/10">
                    <div class="w-12 h-12 rounded-full bg-gray-100 dark:bg-white/10 text-gray-400 flex items-center justify-center mx-auto mb-3">
                        <i class="fa-solid fa-circle-question text-base"></i>
                    </div>
                    <h3 class="font-heading font-bold text-base text-gray-900 dark:text-white">Help Center Articles are Being Prepared</h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1 max-w-sm mx-auto">
                        Our team is currently updating frequently asked questions. For immediate assistance with celebration packages in Indore, please contact our team.
                    </p>
                </div>
            @endforelse
        </div>

        <!-- No Search Results Found Message -->
        <div id="faq-empty-state" class="hidden text-center py-16 bg-[#FAF9F6] dark:bg-[#121217] rounded-3xl border border-dashed border-gray-200 dark:border-white/10 mt-4">
            <div class="w-12 h-12 rounded-full bg-gray-100 dark:bg-white/10 text-gray-400 flex items-center justify-center mx-auto mb-3">
                <i class="fa-solid fa-magnifying-glass text-base"></i>
            </div>
            <h3 class="font-heading font-bold text-base text-gray-900 dark:text-white">No matching answers found</h3>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1 max-w-sm mx-auto">
                Try searching with different keywords or connect with our Indore event team directly on WhatsApp.
            </p>
            <button onclick="document.getElementById('faq-search-input').value=''; filterFaqCategory('all', document.querySelector('.faq-category-btn'));" 
                    class="mt-4 px-4 py-2 bg-black text-white dark:bg-white dark:text-black rounded-xl text-xs font-bold cursor-pointer">
                Clear Search
            </button>
        </div>

        <!-- Still Have Questions Prompt Box -->
        <div class="mt-12 sm:mt-16 p-6 sm:p-8 rounded-3xl bg-[#FAF7F2] dark:bg-[#141419] border border-gray-200/80 dark:border-white/10 flex flex-col sm:flex-row items-center justify-between gap-6 text-center sm:text-left transition-colors">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-full bg-amber-500/10 dark:bg-amber-400/10 text-amber-800 dark:text-amber-400 flex items-center justify-center text-xl shrink-0">
                    <i class="fa-solid fa-headset"></i>
                </div>
                <div>
                    <h3 class="text-sm sm:text-base font-heading font-bold text-gray-900 dark:text-white">
                        Still haven't found your answer?
                    </h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                        Our Indore event managers are on call to answer questions, discuss custom decor concepts, and reserve dates.
                    </p>
                </div>
            </div>

            <div class="flex flex-wrap items-center justify-center sm:justify-end gap-3 shrink-0">
                <a href="https://wa.me/919131668156?text=Hi%20Artizen,%20I%20have%20a%20question%20about%20event%20setup%20in%20Indore" 
                   target="_blank" 
                   rel="noopener noreferrer" 
                   class="inline-flex items-center gap-2 bg-[#25D366] hover:bg-[#20BD5A] text-white px-5 py-3 rounded-2xl text-xs font-bold transition-all shadow-xs hover:shadow-sm"
                   aria-label="Chat on WhatsApp">
                    <i class="fa-brands fa-whatsapp text-sm"></i>
                    <span>WhatsApp Event Manager</span>
                </a>
                <a href="{{ route('contact.index') }}" 
                   class="inline-flex items-center gap-2 bg-white dark:bg-[#1F1F26] border border-gray-200 dark:border-white/10 text-gray-800 dark:text-gray-200 hover:text-black dark:hover:text-white px-5 py-3 rounded-2xl text-xs font-bold transition-colors">
                    <span>Send Message</span>
                </a>
            </div>
        </div>

    </div>
</div>

<script>
    let currentCategory = 'all';

    function toggleFaqPageItem(index) {
        const body = document.getElementById(`faq-full-answer-${index}`);
        const chevron = document.getElementById(`faq-page-chevron-${index}`);
        const trigger = chevron?.closest('button');

        if (!body || !chevron) return;

        const isCurrentlyOpen = !body.classList.contains('hidden');

        // Close all other accordion items
        const allBodies = document.querySelectorAll('.faq-page-body');
        const allChevrons = document.querySelectorAll('[id^="faq-page-chevron-"]');

        allBodies.forEach(b => b.classList.add('hidden'));
        allChevrons.forEach(c => {
            c.classList.remove('rotate-180', 'text-amber-600', 'dark:text-amber-400');
            c.closest('button')?.setAttribute('aria-expanded', 'false');
        });

        // Toggle selected item
        if (!isCurrentlyOpen) {
            body.classList.remove('hidden');
            chevron.classList.add('rotate-180', 'text-amber-600', 'dark:text-amber-400');
            trigger?.setAttribute('aria-expanded', 'true');
        }
    }

    function filterFaqCategory(category, buttonEl) {
        currentCategory = category;

        // Update active filter pill style
        document.querySelectorAll('.faq-category-btn').forEach(btn => {
            btn.classList.remove('active', 'bg-black', 'text-white', 'dark:bg-white', 'dark:text-black', 'font-bold');
            btn.classList.add('bg-gray-100', 'dark:bg-[#15151A]', 'text-gray-700', 'dark:text-gray-300', 'font-medium');
        });

        if (buttonEl) {
            buttonEl.classList.add('active', 'bg-black', 'text-white', 'dark:bg-white', 'dark:text-black', 'font-bold');
            buttonEl.classList.remove('bg-gray-100', 'dark:bg-[#15151A]', 'text-gray-700', 'dark:text-gray-300', 'font-medium');
        }

        applyFaqFilters();
    }

    function applyFaqFilters() {
        const query = (document.getElementById('faq-search-input')?.value || '').trim().toLowerCase();
        const items = document.querySelectorAll('.faq-page-item');
        let visibleCount = 0;

        items.forEach(item => {
            const itemCat = item.getAttribute('data-category') || '';
            const question = item.getAttribute('data-question') || '';
            const answer = item.getAttribute('data-answer') || '';

            const matchesCategory = (currentCategory === 'all') || (itemCat === currentCategory);
            const matchesQuery = !query || question.includes(query) || answer.includes(query);

            if (matchesCategory && matchesQuery) {
                item.classList.remove('hidden');
                visibleCount++;
            } else {
                item.classList.add('hidden');
            }
        });

        const emptyState = document.getElementById('faq-empty-state');
        if (emptyState) {
            if (visibleCount === 0) {
                emptyState.classList.remove('hidden');
            } else {
                emptyState.classList.add('hidden');
            }
        }
    }

    document.getElementById('faq-search-input')?.addEventListener('input', applyFaqFilters);
</script>
@endsection
