<!-- ARTIZEN — Clean Look Frequently Asked Questions (FAQ) Section -->
<section id="faq" class="w-full bg-white dark:bg-[#0B0B0E] py-14 sm:py-18 border-t border-gray-200/80 dark:border-white/10 transition-colors select-none">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">

        <!-- Section Header -->
        <div class="text-center mb-10 sm:mb-12">
            <h2 class="text-2xl sm:text-3xl md:text-4xl font-heading font-extrabold text-gray-950 dark:text-white tracking-tight">
                Frequently Asked Questions
            </h2>
            <p class="text-xs sm:text-sm text-gray-500 dark:text-gray-400 mt-2 max-w-xl mx-auto leading-relaxed font-normal">
                Everything you need to know about event setup bookings, arrival timings, package customization, and offline payments in Indore.
            </p>
        </div>

        @php
            if (isset($faqs) && ($faqs instanceof \Illuminate\Database\Eloquent\Collection || is_array($faqs))) {
                $faqList = $faqs;
            } else {
                $faqList = \App\Models\Faq::active()->forHomepage()->ordered()->get();
            }
        @endphp

        <!-- Clean Accordion Container (All Closed by Default) -->
        <div class="space-y-3" id="artizen-faq-accordion">
            @forelse ($faqList as $index => $faq)
                @php
                    $faqQ = is_object($faq) ? $faq->question : ($faq['q'] ?? $faq['question'] ?? '');
                    $faqA = is_object($faq) ? $faq->answer : ($faq['a'] ?? $faq['answer'] ?? '');
                @endphp
                <div class="faq-accordion-item rounded-2xl bg-[#FAF9F6] dark:bg-[#121217] border border-gray-200/80 dark:border-white/10 hover:border-gray-300 dark:hover:border-white/20 transition-all duration-200 overflow-hidden shadow-xs">
                    
                    <!-- Question Header Trigger (Closed by Default) -->
                    <button type="button" 
                            class="faq-accordion-trigger w-full text-left px-5 sm:px-6 py-4 sm:py-4.5 flex items-center justify-between gap-4 cursor-pointer select-none focus:outline-none group"
                            aria-expanded="false"
                            aria-controls="faq-answer-{{ $index }}"
                            onclick="toggleArtizenFaq({{ $index }})">
                        <span class="text-sm sm:text-[15px] font-heading font-bold text-gray-900 dark:text-white group-hover:text-amber-800 dark:group-hover:text-amber-400 transition-colors leading-snug">
                            {{ $faqQ }}
                        </span>
                        <div class="w-7 h-7 sm:w-8 sm:h-8 rounded-full bg-white dark:bg-[#1C1C22] border border-gray-200 dark:border-white/10 flex items-center justify-center shrink-0 text-gray-500 dark:text-gray-400 group-hover:text-gray-900 dark:group-hover:text-white group-hover:border-gray-300 dark:group-hover:border-white/20 transition-all">
                            <i id="faq-chevron-{{ $index }}" class="fa-solid fa-chevron-down text-[11px] transition-transform duration-300"></i>
                        </div>
                    </button>

                    <!-- Answer Body (Hidden by Default) -->
                    <div id="faq-answer-{{ $index }}" 
                         class="faq-accordion-body transition-all duration-300 ease-in-out hidden">
                        <div class="px-5 sm:px-6 pb-4 sm:pb-5 pt-1 text-xs sm:text-[13px] text-gray-600 dark:text-gray-300 leading-relaxed font-normal border-t border-gray-200/50 dark:border-white/5">
                            {{ $faqA }}
                        </div>
                    </div>

                </div>
            @empty
                <div class="text-center py-10 px-6 rounded-2xl bg-[#FAF9F6] dark:bg-[#121217] border border-gray-200/80 dark:border-white/10">
                    <p class="text-xs sm:text-sm text-gray-500 dark:text-gray-400">Have questions about event packages, setups, or payments in Indore? Reach out to our event coordinator directly or explore our Help Center.</p>
                </div>
            @endforelse
        </div>

        <!-- Clean Prompt Box: Haven't Found Your Answer? Go to FAQ Page -->
        <div class="mt-8 sm:mt-10 p-5 sm:p-6 rounded-2xl bg-[#FAF7F2] dark:bg-[#141419] border border-gray-200/80 dark:border-white/10 flex flex-col sm:flex-row items-center justify-between gap-4 text-center sm:text-left transition-colors">
            <div class="flex items-center gap-3.5">
                <div class="w-10 h-10 rounded-full bg-amber-500/10 dark:bg-amber-400/10 text-amber-800 dark:text-amber-400 flex items-center justify-center text-lg shrink-0">
                    <i class="fa-solid fa-circle-question"></i>
                </div>
                <div>
                    <h4 class="text-xs sm:text-sm font-heading font-bold text-gray-900 dark:text-white">
                        Haven't found your answer?
                    </h4>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                        Visit our complete Help Center or connect directly with our Indore event team.
                    </p>
                </div>
            </div>

            <div class="flex flex-wrap items-center justify-center sm:justify-end gap-2.5 shrink-0">
                <a href="{{ route('faq.index') }}" 
                   class="inline-flex items-center justify-center bg-black dark:bg-white text-white dark:text-black hover:bg-gray-800 dark:hover:bg-gray-100 px-4 py-2.5 rounded-xl text-xs font-bold transition-all shadow-xs">
                    <span>Go to FAQ Page</span>
                </a>
                <a href="{{ route('contact.index') }}" 
                   class="inline-flex items-center justify-center bg-white dark:bg-[#1F1F26] border border-gray-200 dark:border-white/10 text-gray-700 dark:text-gray-300 hover:text-black dark:hover:text-white px-4 py-2.5 rounded-xl text-xs font-bold transition-colors">
                    <span>Contact Us</span>
                </a>
            </div>
        </div>

    </div>
</section>

<!-- Accordion Toggle JavaScript -->
<script>
    function toggleArtizenFaq(index) {
        const body = document.getElementById(`faq-answer-${index}`);
        const chevron = document.getElementById(`faq-chevron-${index}`);
        const trigger = chevron?.closest('button');

        if (!body || !chevron) return;

        const isCurrentlyOpen = !body.classList.contains('hidden');

        // Optional: Close other FAQs for single-open accordion behavior
        const allBodies = document.querySelectorAll('.faq-accordion-body');
        const allChevrons = document.querySelectorAll('[id^="faq-chevron-"]');

        allBodies.forEach(b => b.classList.add('hidden'));
        allChevrons.forEach(c => {
            c.classList.remove('rotate-180', 'text-amber-600', 'dark:text-amber-400');
            c.closest('button')?.setAttribute('aria-expanded', 'false');
        });

        // Toggle clicked item
        if (!isCurrentlyOpen) {
            body.classList.remove('hidden');
            chevron.classList.add('rotate-180', 'text-amber-600', 'dark:text-amber-400');
            trigger?.setAttribute('aria-expanded', 'true');
        }
    }
</script>