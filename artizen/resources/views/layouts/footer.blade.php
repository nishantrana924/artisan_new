<!-- ARTIZEN — Luxury Event Platform Footer (E-Commerce Grade UI/UX) -->
<footer class="w-full bg-[#FAF9F6] dark:bg-[#0A0A0C] border-t border-[#E5E7EB] dark:border-[#1F1F23] text-[#111827] dark:text-[#F3F4F6] pt-14 pb-8 transition-colors select-none">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        <!-- Top Grid: 4 Clean Columns -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-12 gap-10 lg:gap-8 pb-12">

            <!-- Column 1: Brand & Direct Connect (4 Cols) -->
            <div class="lg:col-span-4 text-left">
                <a href="{{ route('home') }}" class="inline-block mb-4">
                    <img src="{{ asset('assets/images/logo/artizen.png') }}" alt="Artizen Events" class="h-14 sm:h-16 w-auto object-contain">
                </a>
                <p class="text-xs sm:text-[13px] leading-relaxed text-[#4B5563] dark:text-[#9CA3AF] max-w-sm mb-6 font-normal">
                    Indore's trusted event setup & celebration platform. Ready-to-book designer setups, luxury birthday decor, sound rigs, and live artists.
                </p>

                <!-- Social & Direct Action Circles -->
                <div class="flex items-center gap-2.5">
                    <a href="https://www.instagram.com/vibifyeventsavenues?igsh=MThleThwMXpycmhoeg%3D%3D"
                       target="_blank" rel="noopener noreferrer"
                       class="w-9 h-9 rounded-full bg-white dark:bg-[#16161A] border border-[#E5E7EB] dark:border-[#27272A] text-[#374151] dark:text-[#D1D5DB] hover:text-[#111827] dark:hover:text-white hover:border-[#111827] dark:hover:border-white transition-all flex items-center justify-center text-sm shadow-xs"
                       aria-label="Instagram">
                        <i class="fa-brands fa-instagram"></i>
                    </a>
                    <a href="https://wa.me/919131668156?text=Hi%20Artizen,%20I%20would%20like%20to%20inquire%20about%20an%20event%20setup"
                       target="_blank" rel="noopener noreferrer"
                       class="w-9 h-9 rounded-full bg-white dark:bg-[#16161A] border border-[#E5E7EB] dark:border-[#27272A] text-[#374151] dark:text-[#D1D5DB] hover:text-[#25D366] dark:hover:text-[#25D366] hover:border-[#25D366] dark:hover:border-[#25D366] transition-all flex items-center justify-center text-sm shadow-xs"
                       aria-label="WhatsApp">
                        <i class="fa-brands fa-whatsapp"></i>
                    </a>
                    <a href="tel:+919131668156"
                       class="w-9 h-9 rounded-full bg-white dark:bg-[#16161A] border border-[#E5E7EB] dark:border-[#27272A] text-[#374151] dark:text-[#D1D5DB] hover:text-[#111827] dark:hover:text-white hover:border-[#111827] dark:hover:border-white transition-all flex items-center justify-center text-xs shadow-xs"
                       aria-label="Call">
                        <i class="fa-solid fa-phone"></i>
                    </a>
                    <a href="mailto:support@artizen.events"
                       class="w-9 h-9 rounded-full bg-white dark:bg-[#16161A] border border-[#E5E7EB] dark:border-[#27272A] text-[#374151] dark:text-[#D1D5DB] hover:text-[#111827] dark:hover:text-white hover:border-[#111827] dark:hover:border-white transition-all flex items-center justify-center text-xs shadow-xs"
                       aria-label="Email">
                        <i class="fa-regular fa-envelope"></i>
                    </a>
                </div>
            </div>

            <!-- Column 2: Event Collections (3 Cols) -->
            <div class="lg:col-span-3 text-left">
                <h3 class="text-[11px] sm:text-xs font-heading font-extrabold uppercase tracking-widest text-[#111827] dark:text-white mb-4">
                    Event Setups
                </h3>
                <ul class="text-xs sm:text-[13px] text-[#4B5563] dark:text-[#9CA3AF] space-y-2.5">
                    <li>
                        <a href="{{ route('events.index', ['category' => 'cat-birthdays']) }}" class="hover:text-[#111827] dark:hover:text-[#FFD600] transition-colors block">
                            Birthday Celebrations
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('events.index', ['category' => 'cat-proposal-anniversary']) }}" class="hover:text-[#111827] dark:hover:text-[#FFD600] transition-colors block">
                            Proposals & Romantic
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('events.index', ['category' => 'cat-weddings-sangeet']) }}" class="hover:text-[#111827] dark:hover:text-[#FFD600] transition-colors block">
                            Weddings & Sangeet Stages
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('events.index', ['category' => 'cat-house-party']) }}" class="hover:text-[#111827] dark:hover:text-[#FFD600] transition-colors block">
                            House Party & DJ Sound
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('events.index', ['category' => 'cat-kids-cozy']) }}" class="hover:text-[#111827] dark:hover:text-[#FFD600] transition-colors block">
                            Baby Showers & Kids Theme
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('events.index', ['category' => 'cat-baby-corporate']) }}" class="hover:text-[#111827] dark:hover:text-[#FFD600] transition-colors block">
                            Corporate Events & Galas
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('events.index') }}" class="text-[#111827] dark:text-[#FFD600] font-bold hover:underline block pt-1">
                            Browse All Packages
                        </a>
                    </li>
                </ul>
            </div>

            <!-- Column 3: Platform Navigation (2 Cols) -->
            <div class="lg:col-span-2 text-left">
                <h3 class="text-[11px] sm:text-xs font-heading font-extrabold uppercase tracking-widest text-[#111827] dark:text-white mb-4">
                    Explore
                </h3>
                <ul class="text-xs sm:text-[13px] text-[#4B5563] dark:text-[#9CA3AF] space-y-2.5">
                    <li>
                        <a href="{{ route('home') }}" class="hover:text-[#111827] dark:hover:text-[#FFD600] transition-colors block">Home</a>
                    </li>
                    <li>
                        <a href="{{ route('about-us') }}" class="hover:text-[#111827] dark:hover:text-[#FFD600] transition-colors block">About ARTIZEN</a>
                    </li>
                    <li>
                        <a href="{{ route('gallery.index') }}" class="hover:text-[#111827] dark:hover:text-[#FFD600] transition-colors block">Event Gallery</a>
                    </li>
                    <li>
                        <a href="{{ route('booking.index') }}" class="hover:text-[#111827] dark:hover:text-[#FFD600] transition-colors block">Book Setup</a>
                    </li>
                    <li>
                        <a href="{{ route('booking.track') }}" class="hover:text-[#111827] dark:hover:text-[#FFD600] transition-colors block">Track Booking</a>
                    </li>
                    <li>
                        <a href="{{ route('contact.index') }}" class="hover:text-[#111827] dark:hover:text-[#FFD600] transition-colors block">Contact Us</a>
                    </li>
                    <li>
                        <a href="{{ route('admin.login') }}" class="text-[11px] text-[#6B7280] dark:text-[#6B7280] hover:text-[#111827] dark:hover:text-[#FFD600] transition-colors block pt-1">Partner Login</a>
                    </li>
                </ul>
            </div>

            <!-- Column 4: Contact & Office (3 Cols) -->
            <div class="lg:col-span-3 text-left">
                <h3 class="text-[11px] sm:text-xs font-heading font-extrabold uppercase tracking-widest text-[#111827] dark:text-white mb-4">
                    Get In Touch
                </h3>
                <ul class="text-xs sm:text-[13px] text-[#4B5563] dark:text-[#9CA3AF] space-y-3.5">
                    <li>
                        <span class="block text-[11px] uppercase tracking-wider text-[#9CA3AF] dark:text-[#6B7280] font-semibold mb-0.5">Phone Support</span>
                        <a href="tel:+919131668156" class="text-sm font-semibold text-[#111827] dark:text-white hover:text-black dark:hover:text-[#FFD600] transition-colors block">
                            +91 91316 68156
                        </a>
                    </li>
                    <li>
                        <span class="block text-[11px] uppercase tracking-wider text-[#9CA3AF] dark:text-[#6B7280] font-semibold mb-0.5">WhatsApp Assistance</span>
                        <a href="https://wa.me/919131668156" target="_blank" rel="noopener noreferrer" class="text-sm font-semibold text-[#111827] dark:text-white hover:text-[#25D366] dark:hover:text-[#25D366] transition-colors block">
                            +91 91316 68156 <span class="text-[11px] font-normal text-[#6B7280] dark:text-[#9CA3AF]">(24/7 Chat)</span>
                        </a>
                    </li>
                    <li>
                        <span class="block text-[11px] uppercase tracking-wider text-[#9CA3AF] dark:text-[#6B7280] font-semibold mb-0.5">Email Support</span>
                        <a href="mailto:support@artizen.events" class="text-xs sm:text-[13px] text-[#111827] dark:text-white hover:underline transition-colors block">
                            support@artizen.events
                        </a>
                    </li>
                    <li>
                        <span class="block text-[11px] uppercase tracking-wider text-[#9CA3AF] dark:text-[#6B7280] font-semibold mb-0.5">Indore Headquarters</span>
                        <span class="text-xs sm:text-[13px] text-[#4B5563] dark:text-[#9CA3AF] block leading-snug">
                            Vijay Nagar, Indore, MP 452010
                        </span>
                    </li>
                </ul>
            </div>

        </div>

        <!-- Bottom Bar: Copyright, Brand Attribution & Back-To-Top Button -->
        <div class="border-t border-[#E5E7EB] dark:border-[#1F1F23] pt-6 flex flex-col sm:flex-row justify-between items-center gap-4">
            <div class="text-xs text-[#6B7280] dark:text-[#9CA3AF] font-body tracking-normal">
                © {{ date('Y') }} ARTIZEN. ALL RIGHTS RESERVED.
            </div>

            <div class="flex items-center gap-4">
                <div class="text-[11px] text-[#6B7280] dark:text-[#9CA3AF] uppercase tracking-wider font-medium">
                    DESIGN & DEVELOPMENT BY <a href="https://brandsphereit.com/" target="_blank" rel="noopener noreferrer" class="text-[#111827] dark:text-white hover:text-black dark:hover:text-[#FFD600] font-bold transition-colors">BRANDSPHERE</a>
                </div>

                <!-- Back-To-Top Circular Button -->
                <button onclick="scrollToTop()"
                        class="w-8 h-8 sm:w-9 sm:h-9 rounded-full bg-[#FFD600] hover:bg-[#E6C200] text-[#111827] flex items-center justify-center transition-all shadow-xs cursor-pointer shrink-0"
                        aria-label="Back to Top">
                    <i class="fa-solid fa-arrow-up text-xs"></i>
                </button>
            </div>
        </div>

    </div>
</footer>

<script>
    function scrollToTop() {
        if (window.lenis) {
            window.lenis.scrollTo(0, { duration: 1.2 });
        } else {
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }
    }
</script>
</body>
</html>