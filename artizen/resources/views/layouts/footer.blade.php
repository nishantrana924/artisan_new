<!-- ARTIZEN — Premium Event Platform Footer (Modern, Minimal & High-Conversion) -->
<footer class="w-full bg-[#FAF9F6] dark:bg-[#0A0A0D] border-t border-[#E5E7EB] dark:border-[#1F1F24] text-[#1F2937] dark:text-[#F3F4F6] pt-14 pb-8 transition-colors select-none">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        <!-- Main Footer Grid: 4 Clean, Focused Columns -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-12 gap-10 lg:gap-8 pb-12">

            <!-- Column 1: Brand, Mission, Service Badge & Social Media (4 Cols) -->
            <div class="lg:col-span-4 text-left flex flex-col justify-between">
                <div>
                    <!-- Brand Logo -->
                    <a href="{{ route('home') }}" class="inline-block mb-3.5 group">
                        <img src="{{ asset('assets/images/logo/artizen.png') }}" 
                             alt="ARTIZEN Events" 
                             class="h-12 sm:h-14 w-auto object-contain transition-transform duration-300 group-hover:scale-[1.02]">
                    </a>

                    <!-- Brand Bio -->
                    <p class="text-xs sm:text-[13px] leading-relaxed text-[#4B5563] dark:text-[#9CA3AF] max-w-sm mb-6 font-normal">
                        Indore's premier event booking platform. Handcrafted celebration decors, luxury birthday setups, sound rigs, and verified artists delivered with seamless execution.
                    </p>
                </div>

                <!-- Social Media Links (Minimal, Circular with Micro-Animations) -->
                <div>
                    <span class="block text-[11px] font-heading font-extrabold uppercase tracking-wider text-[#9CA3AF] dark:text-[#6B7280] mb-2.5">
                        Connect With Us
                    </span>
                    <div class="flex items-center gap-2.5">
                        <!-- Instagram -->
                        <a href="https://www.instagram.com/vibifyeventsavenues?igsh=MThleThwMXpycmhoeg%3D%3D"
                           target="_blank" rel="noopener noreferrer"
                           class="w-9 h-9 rounded-full bg-white dark:bg-[#151518] border border-[#E5E7EB] dark:border-[#27272A] text-[#374151] dark:text-[#D1D5DB] hover:text-[#E4405F] hover:border-[#E4405F] dark:hover:text-[#E4405F] dark:hover:border-[#E4405F] hover:shadow-sm transition-all duration-200 flex items-center justify-center text-sm"
                           title="Follow us on Instagram"
                           aria-label="Instagram">
                            <i class="fa-brands fa-instagram"></i>
                        </a>

                        <!-- WhatsApp -->
                        <a href="https://wa.me/919131668156?text=Hi%20Artizen,%20I%20would%20like%20to%20inquire%20about%20an%20event%20setup"
                           target="_blank" rel="noopener noreferrer"
                           class="w-9 h-9 rounded-full bg-white dark:bg-[#151518] border border-[#E5E7EB] dark:border-[#27272A] text-[#374151] dark:text-[#D1D5DB] hover:text-[#25D366] hover:border-[#25D366] dark:hover:text-[#25D366] dark:hover:border-[#25D366] hover:shadow-sm transition-all duration-200 flex items-center justify-center text-sm"
                           title="Chat on WhatsApp"
                           aria-label="WhatsApp">
                            <i class="fa-brands fa-whatsapp"></i>
                        </a>

                        <!-- Facebook -->
                        <a href="https://www.facebook.com"
                           target="_blank" rel="noopener noreferrer"
                           class="w-9 h-9 rounded-full bg-white dark:bg-[#151518] border border-[#E5E7EB] dark:border-[#27272A] text-[#374151] dark:text-[#D1D5DB] hover:text-[#1877F2] hover:border-[#1877F2] dark:hover:text-[#1877F2] dark:hover:border-[#1877F2] hover:shadow-sm transition-all duration-200 flex items-center justify-center text-sm"
                           title="Find us on Facebook"
                           aria-label="Facebook">
                            <i class="fa-brands fa-facebook-f"></i>
                        </a>

                        <!-- YouTube -->
                        <a href="https://www.youtube.com"
                           target="_blank" rel="noopener noreferrer"
                           class="w-9 h-9 rounded-full bg-white dark:bg-[#151518] border border-[#E5E7EB] dark:border-[#27272A] text-[#374151] dark:text-[#D1D5DB] hover:text-[#FF0000] hover:border-[#FF0000] dark:hover:text-[#FF0000] dark:hover:border-[#FF0000] hover:shadow-sm transition-all duration-200 flex items-center justify-center text-sm"
                           title="Watch our setups on YouTube"
                           aria-label="YouTube">
                            <i class="fa-brands fa-youtube"></i>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Column 2: Explore Links (3 Cols) -->
            <div class="lg:col-span-2 text-left">
                <h3 class="text-xs font-heading font-extrabold uppercase tracking-widest text-[#111827] dark:text-white mb-4">
                    Explore
                </h3>
                <ul class="text-xs sm:text-[13px] text-[#4B5563] dark:text-[#9CA3AF] space-y-2.5">
                    <li>
                        <a href="{{ route('home') }}" class="hover:text-[#111827] dark:hover:text-[#FFD600] transition-colors inline-flex items-center gap-1.5">
                            <span>Home</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('events.index') }}" class="hover:text-[#111827] dark:hover:text-[#FFD600] transition-colors inline-flex items-center gap-1.5">
                            <span>All Packages</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('gallery.index') }}" class="hover:text-[#111827] dark:hover:text-[#FFD600] transition-colors inline-flex items-center gap-1.5">
                            <span>Event Gallery</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('booking.index') }}" class="hover:text-[#111827] dark:hover:text-[#FFD600] transition-colors inline-flex items-center gap-1.5">
                            <span>Book Setup</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('booking.track') }}" class="hover:text-[#111827] dark:hover:text-[#FFD600] transition-colors inline-flex items-center gap-1.5">
                            <span>Track Booking</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('about-us') }}" class="hover:text-[#111827] dark:hover:text-[#FFD600] transition-colors inline-flex items-center gap-1.5">
                            <span>About Artizen</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('faq.index') }}" class="hover:text-[#111827] dark:hover:text-[#FFD600] transition-colors inline-flex items-center gap-1.5">
                            <span>FAQs & Help</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('contact.index') }}" class="hover:text-[#111827] dark:hover:text-[#FFD600] transition-colors inline-flex items-center gap-1.5">
                            <span>Contact Us</span>
                        </a>
                    </li>
                </ul>
            </div>

            <!-- Column 3: Legal & Trust Policies (3 Cols) -->
            <div class="lg:col-span-3 text-left">
                <h3 class="text-xs font-heading font-extrabold uppercase tracking-widest text-[#111827] dark:text-white mb-4">
                    Legal & Trust
                </h3>
                <ul class="text-xs sm:text-[13px] text-[#4B5563] dark:text-[#9CA3AF] space-y-2.5">
                    <li>
                        <a href="{{ route('legal.privacy') }}" class="hover:text-[#111827] dark:hover:text-[#FFD600] transition-colors inline-flex items-center gap-1.5">
                            <span>Privacy Policy</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('legal.terms') }}" class="hover:text-[#111827] dark:hover:text-[#FFD600] transition-colors inline-flex items-center gap-1.5">
                            <span>Terms & Conditions</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('legal.cancellation') }}" class="hover:text-[#111827] dark:hover:text-[#FFD600] transition-colors inline-flex items-center gap-1.5">
                            <span>Refund & Cancellation</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('legal.booking-policy') }}" class="hover:text-[#111827] dark:hover:text-[#FFD600] transition-colors inline-flex items-center gap-1.5">
                            <span>Booking & Payment Policy</span>
                        </a>
                    </li>
                </ul>

                <!-- Offline Payment Trust Note -->
                <div class="mt-4 pt-4 border-t border-[#E5E7EB] dark:border-[#222226] text-[11px] text-[#6B7280] dark:text-[#9CA3AF] leading-relaxed">
                    <div class="flex items-center gap-1.5 font-semibold text-[#111827] dark:text-white mb-1">
                        <i class="fa-solid fa-shield-halved text-emerald-600 dark:text-emerald-400"></i>
                        <span>Zero Online Risk</span>
                    </div>
                    <span>No online advance required. Pay offline only after your event booking is confirmed by our manager.</span>
                </div>
            </div>

            <!-- Column 4: Contact Details & Office (3 Cols) -->
            <div class="lg:col-span-3 text-left">
                <h3 class="text-xs font-heading font-extrabold uppercase tracking-widest text-[#111827] dark:text-white mb-4">
                    Contact Details
                </h3>

                <ul class="text-xs sm:text-[13px] text-[#4B5563] dark:text-[#9CA3AF] space-y-3.5">
                    <!-- Phone Support -->
                    <li class="flex items-start gap-2.5">
                        <div class="w-7 h-7 rounded-lg bg-white dark:bg-[#151518] border border-[#E5E7EB] dark:border-[#27272A] text-gray-700 dark:text-gray-300 flex items-center justify-center shrink-0 mt-0.5">
                            <i class="fa-solid fa-phone text-[11px]"></i>
                        </div>
                        <div>
                            <span class="block text-[10px] uppercase tracking-wider text-[#9CA3AF] dark:text-[#6B7280] font-semibold">Call Support</span>
                            <a href="tel:+919131668156" class="text-xs sm:text-[13px] font-semibold text-[#111827] dark:text-white hover:text-amber-600 dark:hover:text-[#FFD600] transition-colors">
                                +91 91316 68156
                            </a>
                        </div>
                    </li>

                    <!-- WhatsApp Assistance -->
                    <li class="flex items-start gap-2.5">
                        <div class="w-7 h-7 rounded-lg bg-white dark:bg-[#151518] border border-[#E5E7EB] dark:border-[#27272A] text-emerald-600 dark:text-emerald-400 flex items-center justify-center shrink-0 mt-0.5">
                            <i class="fa-brands fa-whatsapp text-xs"></i>
                        </div>
                        <div>
                            <span class="block text-[10px] uppercase tracking-wider text-[#9CA3AF] dark:text-[#6B7280] font-semibold">WhatsApp Assistance</span>
                            <a href="https://wa.me/919131668156?text=Hi%20Artizen,%20I%20would%20like%20to%20inquire%20about%20an%20event%20setup" 
                               target="_blank" rel="noopener noreferrer" 
                               class="text-xs sm:text-[13px] font-semibold text-[#111827] dark:text-white hover:text-emerald-600 dark:hover:text-[#25D366] transition-colors">
                                +91 91316 68156 <span class="text-[10px] font-normal text-emerald-600 dark:text-emerald-400">(Live Chat)</span>
                            </a>
                        </div>
                    </li>

                    <!-- Email Support -->
                    <li class="flex items-start gap-2.5">
                        <div class="w-7 h-7 rounded-lg bg-white dark:bg-[#151518] border border-[#E5E7EB] dark:border-[#27272A] text-gray-700 dark:text-gray-300 flex items-center justify-center shrink-0 mt-0.5">
                            <i class="fa-regular fa-envelope text-xs"></i>
                        </div>
                        <div>
                            <span class="block text-[10px] uppercase tracking-wider text-[#9CA3AF] dark:text-[#6B7280] font-semibold">Email Us</span>
                            <a href="mailto:support@artizen.events" class="text-xs sm:text-[13px] font-medium text-[#111827] dark:text-white hover:underline transition-colors">
                                support@artizen.events
                            </a>
                        </div>
                    </li>

                    <!-- Location / Address -->
                    <li class="flex items-start gap-2.5">
                        <div class="w-7 h-7 rounded-lg bg-white dark:bg-[#151518] border border-[#E5E7EB] dark:border-[#27272A] text-gray-700 dark:text-gray-300 flex items-center justify-center shrink-0 mt-0.5">
                            <i class="fa-solid fa-location-dot text-xs text-rose-500"></i>
                        </div>
                        <div>
                            <span class="block text-[10px] uppercase tracking-wider text-[#9CA3AF] dark:text-[#6B7280] font-semibold">Indore Hub</span>
                            <span class="text-xs sm:text-[13px] text-[#4B5563] dark:text-[#9CA3AF] leading-snug block">
                                Vijay Nagar, Indore, MP 452010
                            </span>
                        </div>
                    </li>
                </ul>
            </div>

        </div>

        <!-- Bottom Bar: Copyright, Brand Attribution & Back-To-Top Button -->
        <div class="border-t border-[#E5E7EB] dark:border-[#1F1F24] pt-6 flex flex-col sm:flex-row justify-between items-center gap-4 text-center sm:text-left">
            <!-- Copyright -->
            <div class="text-xs text-[#6B7280] dark:text-[#9CA3AF] font-body tracking-normal">
                © {{ date('Y') }} <span class="font-bold text-[#111827] dark:text-white">ARTIZEN</span>. All rights reserved.
            </div>

            <!-- Attribution & Back To Top -->
            <div class="flex items-center gap-4">
                <div class="text-xs text-[#6B7280] dark:text-[#9CA3AF] tracking-wide font-medium">
                    Developed by <a href="https://brandsphereit.com/" target="_blank" rel="noopener noreferrer" class="text-[#111827] dark:text-white hover:text-black dark:hover:text-[#FFD600] font-bold underline decoration-[#FFD600]/60 underline-offset-2 transition-colors">BrandSphere</a>
                </div>

                <!-- Back-To-Top Button -->
                <button onclick="scrollToTop()"
                        class="w-8 h-8 rounded-full bg-white dark:bg-[#1A1A1E] hover:bg-[#FFD600] hover:text-[#111827] dark:hover:bg-[#FFD600] dark:hover:text-[#111827] border border-[#E5E7EB] dark:border-[#2E2E34] text-[#4B5563] dark:text-[#D1D5DB] flex items-center justify-center transition-all duration-200 shadow-xs cursor-pointer shrink-0"
                        title="Scroll to top"
                        aria-label="Back to Top">
                    <i class="fa-solid fa-arrow-up text-xs"></i>
                </button>
            </div>
        </div>

    </div>
</footer>

<script>
    function scrollToTop() {
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }
</script>