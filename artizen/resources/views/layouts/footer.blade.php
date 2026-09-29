<!-- Footer Footer Footer -->
    <footer class="border-t border-white/10 bg-[#0a0a0a] py-16 text-white relative">
        <div class="w-full px-4 md:px-12">

            <div class="grid grid-cols-1 md:grid-cols-12 gap-12 mb-12">

                <!-- Brand Details Column -->
                <div class="md:col-span-5 text-left">
                    <div class="flex flex-col sm:flex-row items-start gap-6 mb-6">
                        <a href="{{ route('home') }}" class="shrink-0">
                            <img src="{{ asset('assets/images/logo/artizen.png') }}" alt="Artizen Logo" class="h-32 w-auto object-contain rounded-lg">
                        </a>
                        <p class="font-body text-xs text-gray-400 leading-relaxed flex-1">
                            We do not just arrange vendors. We create celebration vibes. Ready-to-book event setups,
                            decorations, sound rentals, and live artist bookings reimagined for easy celebrations.
                        </p>
                    </div>

                    <!-- Social Icons (circular outline) -->
                    <div class="flex gap-3 mb-6">
                        <a href="https://www.instagram.com/vibifyeventsavenues?igsh=MThleThwMXpycmhoeg%3D%3D"
                            target="_blank" rel="noopener noreferrer"
                            class="w-8 h-8 rounded-full border border-white/15 flex items-center justify-center text-gray-400 hover:text-gold hover:border-gold transition-all"
                            aria-label="Instagram">
                            <i class="fa-brands fa-instagram text-sm" aria-hidden="true"></i>
                        </a>
                    </div>

                    <span class="text-[10px] text-gray-500 uppercase tracking-widest block mt-4">© 2026 ARTIZEN. ALL RIGHTS RESERVED.</span>
                </div>

                <!-- Links Column 1 -->
                <div class="md:col-span-2 text-left">
                    <h4 class="font-heading font-bold text-xs uppercase tracking-widest mb-6 text-white">LEGAL</h4>
                    <ul class="font-body text-xs text-gray-400 flex flex-col gap-3">
                        <li><a href="#" class="hover:text-gold transition-colors">TERMS OF SERVICE</a></li>
                        <li><a href="#" class="hover:text-gold transition-colors">PRIVACY CODE</a></li>
                        <li><a href="#" class="hover:text-gold transition-colors">TICKET REFUNDS</a></li>
                        <li><a href="#" class="hover:text-gold transition-colors">CANCELLATION POLICY</a></li>
                    </ul>
                </div>

                <!-- Links Column 2 -->
                <div class="md:col-span-2 text-left">
                    <h4 class="font-heading font-bold text-xs uppercase tracking-widest mb-6 text-white">CONNECT</h4>
                    <ul class="font-body text-xs text-gray-400 flex flex-col gap-3">
                        <li><a href="https://www.instagram.com/vibifyeventsavenues?igsh=MThleThwMXpycmhoeg%3D%3D"
                                target="_blank" rel="noopener noreferrer"
                                class="hover:text-gold transition-colors">INSTAGRAM</a></li>

                        <li><a href="#" class="hover:text-gold transition-colors">SOUNDCLOUD</a></li>
                        <li><a href="#" class="hover:text-gold transition-colors">DISCORD SERVER</a></li>
                    </ul>
                </div>

                <!-- Get in Touch (with icons) Column -->
                <div class="md:col-span-3 text-left">
                    <h4 class="font-heading font-bold text-xs uppercase tracking-widest mb-6 text-white">GET IN TOUCH
                    </h4>
                    <ul class="font-body text-xs text-gray-400 flex flex-col gap-4">
                        <li class="flex items-start gap-3">
                            <i class="fa-solid fa-phone text-gold shrink-0 mt-1 text-sm" aria-hidden="true"></i>
                            <div>
                                <span class="block text-white font-semibold">Phone Support</span>
                                <a href="tel:+919131668156" class="hover:text-gold transition-colors mt-0.5 block">+91
                                    91316 68156</a>
                            </div>
                        </li>
                        <li class="flex items-start gap-3">
                            <i class="fa-regular fa-envelope text-gold shrink-0 mt-1 text-sm" aria-hidden="true"></i>
                            <div>
                                <span class="block text-white font-semibold">Email Us</span>
                                <a href="mailto:support@artizen.events"
                                    class="hover:text-gold transition-colors mt-0.5 block">support@artizen.events</a>
                            </div>
                        </li>
                        <li class="flex items-start gap-3">
                            <i class="fa-solid fa-location-dot text-gold shrink-0 mt-1 text-sm" aria-hidden="true"></i>
                            <div>
                                <span class="block text-white font-semibold">Corporate Office</span>
                                <span class="block mt-0.5 text-gray-400">Artizen Events, Indore, MP, India</span>
                            </div>
                        </li>
                    </ul>
                </div>

            </div>

            <!-- Secure Payments Panel -->
            <div class="flex items-center gap-3 mb-8">
                <!-- Visa Card -->
                <div
                    class="bg-white px-2 py-1 rounded-md shadow-sm border border-white/5 flex items-center justify-center shrink-0 w-10 h-6">
                    <svg class="w-full h-full text-[#1A1F71]" viewBox="0 0 24 24" fill="currentColor">
                        <path
                            d="M17.06 5.61h-2.28c-.6 0-1.12.35-1.34.89l-4.73 11.36h2.87l.57-1.57h3.51l.33 1.57h2.53l-2.19-12.25zm-3.12 7.51l1.76-4.82.99 4.82h-2.75z M6.43 5.61H4.11L2.02 11.89c-.15.42-.23.57-.59.76-.36.21-.95.39-1.63.48v.25h3.99c.59 0 1.05-.38 1.18-.95l1.08-5.74 2.87 7.2h3.04l-4.49-12.28H6.43z M23.75 5.61h-2.21c-.46 0-.89.27-1.08.73l-5.11 11.52h2.88l.58-1.58h3.52l.33 1.58h2.54l-2.2-12.25z" />
                    </svg>
                </div>
                <!-- Mastercard Card -->
                <div
                    class="bg-white px-2 py-1 rounded-md shadow-sm border border-white/5 flex items-center justify-center shrink-0 w-10 h-6">
                    <svg class="w-full h-full" viewBox="0 0 24 15" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <circle cx="8" cy="7.5" r="6" fill="#EB001B" />
                        <circle cx="16" cy="7.5" r="6" fill="#F79E1B" fill-opacity="0.8" />
                    </svg>
                </div>
                <!-- UPI Card -->
                <div
                    class="bg-white px-2 py-1 rounded-md shadow-sm border border-white/5 flex items-center justify-center shrink-0 w-10 h-6">
                    <span
                        class="text-[8px] font-heading font-extrabold text-[#00669e] tracking-tight leading-none">UPI</span>
                </div>
                <!-- RuPay Card -->
                <div
                    class="bg-white px-2 py-1 rounded-md shadow-sm border border-white/5 flex items-center justify-center shrink-0 w-10 h-6">
                    <span
                        class="text-[8px] font-heading font-extrabold text-[#097939] tracking-tight leading-none">RUPAY</span>
                </div>
            </div>

            <!-- Footer Bottom with Back-To-Top button -->
            <div class="border-t border-white/10 pt-8 flex flex-col sm:flex-row justify-between items-center gap-6">
                <div class="text-[10px] text-gray-500 uppercase tracking-widest">
                    DESIGN & DEVELOPMENT BY <a href="https://brandsphereit.com/" target="_blank"
                        rel="noopener noreferrer"
                        class="text-white hover:text-gold font-bold transition-all">BRANDSPHERE</a>
                </div>

                <!-- Circular Orange Back-To-Top Button -->
                <button onclick="scrollToTop()"
                    class="w-12 h-12 rounded-full bg-gold text-white flex items-center justify-center hover:bg-gold/90 transition-all shadow-lg cursor-pointer focus:outline-none shrink-0"
                    aria-label="Back to Top">
                    <i class="fa-solid fa-arrow-up text-base text-white" aria-hidden="true"></i>
                </button>
            </div>

        </div>
    </footer>
</body>

</html>