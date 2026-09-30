<section id="faq" class="py-24 w-full px-4 md:px-12 border-b border-primary-border scroll-mt-20 md:scroll-mt-24">
        <!-- Background decorative glow wrapper (preserves parent visible overflow for sticky sidebar) -->
        <div class="absolute inset-0 overflow-hidden pointer-events-none z-0" aria-hidden="true">
            <div class="faq-glow-bg"></div>
        </div>

        <div class="faq-container w-full">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-start">

                <!-- Left Sidebar Column (Spans 4 columns on desktop) -->
                <div class="lg:col-span-4 faq-sticky-sidebar text-left">
                    <span
                        class="inline-flex items-center gap-1.5 text-[10px] md:text-xs font-bold text-gold uppercase tracking-wider mb-3">
                        HELP CENTER
                    </span>
                    <h2
                        class="font-heading font-extrabold text-3xl md:text-5xl uppercase tracking-tight leading-none text-black dark:text-white">
                        Frequently<br>Asked<br><span class="faq-gradient-text">Questions</span>
                    </h2>
                    <p class="font-body text-gray-500 text-xs md:text-sm mt-4 leading-relaxed max-w-sm">
                        Everything you need to know about our event setups, confirmations, cancellation policies, and
                        transparent pricing.
                    </p>

                    <!-- Sticky Support Card -->
                    <div class="faq-support-card mt-8">
                        <h3 class="font-heading font-bold text-base text-black mb-2 uppercase">Still have questions?
                        </h3>
                        <p class="font-body text-xs text-gray-500 mb-6 leading-relaxed">
                            Can't find what you are looking for? Send us a message on WhatsApp and our team will assist
                            you within minutes.
                        </p>
                        <a href="https://wa.me/919131668156" target="_blank" rel="noopener noreferrer"
                            class="faq-support-btn flex items-center justify-center gap-2">
                            <i class="fa-brands fa-whatsapp text-lg shrink-0"></i>
                            <span>Chat with Us</span>
                        </a>
                    </div>
                </div>

                <!-- Right Accordion Column (Spans 8 columns on desktop) -->
                <div class="lg:col-span-8 faq-accordion-stack text-left">

                    <!-- FAQ Item 1 -->
                    <div class="faq-item">
                        <button onclick="toggleFaq(1)" class="faq-item-trigger">
                            <span class="faq-question">How fast will my booking be confirmed?</span>
                            <div class="faq-toggle-icon" id="faq-icon-1"></div>
                        </button>
                        <div id="faq-ans-1" class="faq-answer-container">
                            <p class="faq-answer">
                                Your booking is confirmed instantly online! Within seconds of completing checkout, you
                                will receive a secure confirmation barcode and summary voucher on screen and via email.
                                Our logistics coordinator will reach out to you within 2 hours to coordinate timing.
                            </p>
                        </div>
                    </div>

                    <!-- FAQ Item 2 -->
                    <div class="faq-item">
                        <button onclick="toggleFaq(2)" class="faq-item-trigger">
                            <span class="faq-question">Can I reschedule or cancel my booking?</span>
                            <div class="faq-toggle-icon" id="faq-icon-2"></div>
                        </button>
                        <div id="faq-ans-2" class="faq-answer-container">
                            <p class="faq-answer">
                                Yes, we provide flexible rescheduling free of charge up to 48 hours before the event
                                setup. Cancellations made more than 48 hours in advance are eligible for a 100% refund,
                                automatically credited to your original payment method.
                            </p>
                        </div>
                    </div>

                    <!-- FAQ Item 3 -->
                    <div class="faq-item">
                        <button onclick="toggleFaq(3)" class="faq-item-trigger">
                            <span class="faq-question">Do you customize event packages?</span>
                            <div class="faq-toggle-icon" id="faq-icon-3"></div>
                        </button>
                        <div id="faq-ans-3" class="faq-answer-container">
                            <p class="faq-answer">
                                Absolutely! If our standard package parameters do not fit your vision, you can fill out
                                our custom inquiry form or reach out directly on WhatsApp. We can add more artists,
                                premium decor backdrops, or larger sound setups.
                            </p>
                        </div>
                    </div>

                    <!-- FAQ Item 4 -->
                    <div class="faq-item">
                        <button onclick="toggleFaq(4)" class="faq-item-trigger">
                            <span class="faq-question">Which cities do you operate in?</span>
                            <div class="faq-toggle-icon" id="faq-icon-4"></div>
                        </button>
                        <div id="faq-ans-4" class="faq-answer-container">
                            <p class="faq-answer">
                                Artizen is currently operational across Indore, Madhya Pradesh. We
                                partner directly with vetted local decorators, DJs, and live acoustic artists in Indore
                                to ensure prompt, high-quality setup delivery.
                            </p>
                        </div>
                    </div>

                    <!-- FAQ Item 5 -->
                    <div class="faq-item">
                        <button onclick="toggleFaq(5)" class="faq-item-trigger">
                            <span class="faq-question">How do you verify your artists and decorators?</span>
                            <div class="faq-toggle-icon" id="faq-icon-5"></div>
                        </button>
                        <div id="faq-ans-5" class="faq-answer-container">
                            <p class="faq-answer">
                                Every vendor, decorator, and artist undergoes a thorough portfolio verification,
                                background check, and sound check process. We review setup speeds, instrument qualities,
                                and customer reviews to ensure consistent quality.
                            </p>
                        </div>
                    </div>

                    <!-- FAQ Item 6 -->
                    <div class="faq-item">
                        <button onclick="toggleFaq(6)" class="faq-item-trigger">
                            <span class="faq-question">Are there any hidden costs?</span>
                            <div class="faq-toggle-icon" id="faq-icon-6"></div>
                        </button>
                        <div id="faq-ans-6" class="faq-answer-container">
                            <p class="faq-answer">
                                No hidden costs! The price of the package includes transport, rigging, soundcheck, live
                                performance (for artists/DJs), and clean tear-down after the event within city limits.
                                What you see is exactly what you pay.
                            </p>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </section>



    <!-- E-commerce Sidebar Drawers: Shopping Cart Panel -->
    <div id="cart-drawer" class="fixed inset-0 z-50 pointer-events-none">
        <!-- Overlay Background -->
        <div id="cart-overlay" onclick="toggleCartDrawer()"
            class="absolute inset-0 bg-black/60 opacity-0 transition-opacity duration-300 pointer-events-auto hidden">
        </div>

        <!-- Drawer Drawer Body -->
        <div id="cart-body"
            class="absolute top-0 right-0 w-full sm:w-[480px] h-full bg-main-bg border-l border-primary-border shadow-2xl flex flex-col justify-between translate-x-full transition-transform duration-300 pointer-events-auto z-10">

            <!-- Cart Header -->
            <div class="p-6 border-b border-primary-border flex items-center justify-between">
                <div>
                    <span class="font-heading text-xs text-muted-text uppercase tracking-widest font-semibold"> MY
                        TICKETS</span>
                    <h3 class="font-heading font-bold text-xl uppercase tracking-tight mt-1">CART DETAILS</h3>
                </div>
                <button onclick="toggleCartDrawer()"
                    class="border border-primary-border p-2 hover:border-main-text text-muted-text hover:text-main-text transition-colors cursor-pointer flex items-center justify-center"
                    aria-label="Close Cart">
                    <i class="fa-solid fa-xmark text-sm" aria-hidden="true"></i>
                </button>
            </div>

            <!-- Cart Item List Container -->
            <div id="cart-items-container" class="flex-grow overflow-y-auto p-6 flex flex-col gap-6">
                <!-- Empty cart message -->
                <div id="empty-cart-message" class="h-full flex flex-col items-center justify-center text-center">
                    <i class="fa-solid fa-bag-shopping text-3xl text-muted-text mb-4" aria-hidden="true"></i>
                    <p class="font-heading text-sm font-bold uppercase tracking-wider text-muted-text">Your cart is
                        empty</p>
                    <p class="text-xs text-muted-text mt-1 max-w-[200px]">Go add some live concert events from the feed.
                    </p>
                </div>

                <!-- Cart Items populate here dynamically -->
            </div>

            <!-- Cart Pricing summary & Checkout actions -->
            <div class="p-6 border-t border-primary-border bg-surface-bg flex flex-col gap-4">
                <div class="flex items-center justify-between text-xs text-muted-text">
                    <span>SUBTOTAL</span>
                    <span id="cart-subtotal" class="font-heading font-semibold text-main-text">$0.00</span>
                </div>
                <div class="flex items-center justify-between text-xs text-muted-text">
                    <span>TAX & SERVICE FEES (10%)</span>
                    <span id="cart-tax" class="font-heading font-semibold text-main-text">$0.00</span>
                </div>
                <div class="flex items-center justify-between text-sm border-t border-primary-border/40 pt-4">
                    <span class="font-heading font-bold uppercase tracking-wider">TOTAL AMOUNT</span>
                    <span id="cart-total" class="font-heading font-bold text-lg text-main-text">$0.00</span>
                </div>

                <button onclick="triggerCheckout()" id="checkout-button"
                    class="w-full brutalist-button py-4 text-center text-xs uppercase tracking-widest mt-4 cursor-pointer"
                    disabled>
                    CHECKOUT SECURELY
                </button>

                <div
                    class="flex items-center justify-center gap-4 text-[10px] text-muted-text mt-2 uppercase tracking-widest">
                    <span>SSL ENCRYPTED</span> • <span>E-TICKETS BY EMAIL</span>
                </div>
            </div>

        </div>
    </div>

    <!-- CHECKOUT DETAILS FORM MODAL -->
    <div id="checkout-form-modal" class="fixed inset-0 bg-black/75 backdrop-blur-sm flex items-center justify-center z-[60] hidden opacity-0 transition-opacity duration-300 pointer-events-none">
        <div class="bg-[#121212] border border-primary-border w-full max-w-lg rounded-2xl shadow-2xl p-6 flex flex-col gap-5 text-left pointer-events-auto transform scale-95 transition-transform duration-300">
            <div class="flex justify-between items-center border-b border-primary-border pb-3">
                <div>
                    <span class="text-[9px] text-gold font-bold uppercase tracking-widest font-heading">Confirm Request</span>
                    <h3 class="text-white font-bold text-base uppercase tracking-wider font-heading mt-0.5">Booking Checkout Details</h3>
                </div>
                <button type="button" onclick="closeCheckoutModal()" class="text-muted-text hover:text-white transition-colors text-base bg-transparent border-0 cursor-pointer"><i class="fa-solid fa-xmark"></i></button>
            </div>
            
            <form id="checkout-details-form" onsubmit="submitBookingRequest(event)" class="flex flex-col gap-4">
                <div class="grid grid-cols-2 gap-4">
                    <div class="flex flex-col gap-1.5 text-left">
                        <label class="text-[8px] text-muted-text font-bold uppercase tracking-wider font-heading">Full Name</label>
                        <input type="text" id="checkout-name" required placeholder="e.g. Nishant Patel" class="w-full bg-[#151515] border border-primary-border p-2.5 text-xs font-semibold text-white rounded-lg outline-none focus:border-gold">
                    </div>
                    <div class="flex flex-col gap-1.5 text-left">
                        <label class="text-[8px] text-muted-text font-bold uppercase tracking-wider font-heading">Email Address</label>
                        <input type="email" id="checkout-email" required placeholder="e.g. nishant@example.com" class="w-full bg-[#151515] border border-primary-border p-2.5 text-xs font-semibold text-white rounded-lg outline-none focus:border-gold">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div class="flex flex-col gap-1.5 text-left">
                        <label class="text-[8px] text-muted-text font-bold uppercase tracking-wider font-heading">Mobile Number</label>
                        <input type="tel" id="checkout-mobile" required placeholder="10 digit number" class="w-full bg-[#151515] border border-primary-border p-2.5 text-xs font-semibold text-white rounded-lg outline-none focus:border-gold">
                    </div>
                    <div class="flex flex-col gap-1.5 text-left">
                        <label class="text-[8px] text-muted-text font-bold uppercase tracking-wider font-heading">WhatsApp Number</label>
                        <input type="tel" id="checkout-whatsapp" required placeholder="WhatsApp contact" class="w-full bg-[#151515] border border-primary-border p-2.5 text-xs font-semibold text-white rounded-lg outline-none focus:border-gold">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div class="flex flex-col gap-1.5 text-left">
                        <label class="text-[8px] text-muted-text font-bold uppercase tracking-wider font-heading">Operating City</label>
                        <select id="checkout-city" required class="w-full bg-[#151515] border border-primary-border p-2.5 text-xs font-semibold text-white rounded-lg outline-none focus:border-gold cursor-pointer">
                            <option value="Indore, MP">Indore, MP</option>
                        </select>
                    </div>
                    <div class="flex flex-col gap-1.5 text-left">
                        <label class="text-[8px] text-muted-text font-bold uppercase tracking-wider font-heading">Area Pincode</label>
                        <input type="text" id="checkout-pincode" required placeholder="e.g. 452001" class="w-full bg-[#151515] border border-primary-border p-2.5 text-xs font-semibold text-white rounded-lg outline-none focus:border-gold">
                    </div>
                </div>

                <div class="flex flex-col gap-1.5 text-left">
                    <label class="text-[8px] text-muted-text font-bold uppercase tracking-wider font-heading font-semibold">Full Venue Address & Landmark</label>
                    <textarea id="checkout-address" rows="2" required placeholder="e.g. Flat 302, Royal Residency, Near C21 Mall" class="w-full bg-[#151515] border border-primary-border p-2.5 text-xs font-body font-semibold text-white rounded-lg outline-none focus:border-gold"></textarea>
                </div>

                <div class="flex flex-col gap-1.5 text-left">
                    <label class="text-[8px] text-muted-text font-bold uppercase tracking-wider font-heading font-semibold">Special Notes / Requests</label>
                    <textarea id="checkout-notes" rows="1" placeholder="e.g. Theme preference, timing details" class="w-full bg-[#151515] border border-primary-border p-2.5 text-xs font-body font-semibold text-white rounded-lg outline-none focus:border-gold"></textarea>
                </div>

                <button type="submit" class="w-full bg-[#FFD600] hover:bg-[#E6C200] text-[#171719] py-3.5 text-center text-xs font-heading font-extrabold uppercase tracking-widest cursor-pointer rounded-lg border-none mt-2 font-bold shadow-md">
                    Confirm Booking Request
                </button>
            </form>
        </div>
    </div>

    <!-- Custom Event Details Lightbox / Modal -->
    <div id="details-lightbox" class="fixed inset-0 z-50 pointer-events-none hidden items-center justify-center p-4">
        <!-- Lightbox backdrop -->
        <div id="lightbox-backdrop" onclick="closeEventLightbox()"
            class="absolute inset-0 bg-black/85 pointer-events-auto cursor-pointer"></div>

        <!-- Lightbox Main Body Box -->
        <div id="lightbox-body"
            class="relative bg-main-bg border border-primary-border max-w-5xl w-full max-h-[90vh] overflow-y-auto pointer-events-auto flex flex-col md:grid md:grid-cols-12 z-10">

            <!-- Close button on lightbox -->
            <button onclick="closeEventLightbox()"
                class="absolute top-4 right-4 z-30 border border-primary-border p-2 bg-black/80 hover:border-main-text text-muted-text hover:text-white transition-colors cursor-pointer"
                aria-label="Close Lightbox">
                <i class="fa-solid fa-xmark text-sm" aria-hidden="true"></i>
            </button>

            <!-- Left Visual (col 7) -->
            <div
                class="md:col-span-7 border-b md:border-b-0 md:border-r border-primary-border relative aspect-video md:aspect-auto md:h-full flex items-center justify-center bg-surface-bg min-h-[300px]">
                <img id="lightbox-image" src="" alt="Event Detail Image"
                    class="absolute inset-0 w-full h-full object-cover grayscale">
                <div class="absolute inset-0 bg-gradient-to-t from-black via-black/20 to-transparent"></div>
                <div class="absolute bottom-6 left-6 right-6">
                    <span id="lightbox-tag"
                        class="inline-block bg-white text-black text-[9px] font-heading font-bold uppercase tracking-widest px-2.5 py-1 mb-2">CLUB
                        NIGHT</span>
                    <h2 id="lightbox-title"
                        class="font-heading font-bold text-2xl md:text-4xl uppercase tracking-tight leading-none text-white">
                        NEON
                        OBSIDIAN RAVE</h2>
                </div>
            </div>

            <!-- Right Panel Details & Booking Selector (col 5) -->
            <div class="md:col-span-5 p-6 md:p-8 flex flex-col justify-between overflow-y-auto">
                <div>
                    <span class="font-heading text-[10px] text-muted-text uppercase tracking-widest font-semibold">
                        SPECIFICATION</span>
                    <div class="grid grid-cols-2 gap-4 mt-3 mb-6 font-heading text-xs">
                        <div>
                            <div class="text-muted-text uppercase tracking-widest text-[9px]">LOCATION</div>
                            <div id="lightbox-location" class="font-semibold mt-1">Berlin, Germany</div>
                        </div>
                        <div>
                            <div class="text-muted-text uppercase tracking-widest text-[9px]">DATE</div>
                            <div id="lightbox-date" class="font-semibold mt-1">JUN 24, 2026</div>
                        </div>
                        <div>
                            <div class="text-muted-text uppercase tracking-widest text-[9px]">DOORS OPEN</div>
                            <div class="font-semibold mt-1">22:00 CET</div>
                        </div>
                        <div>
                            <div class="text-muted-text uppercase tracking-widest text-[9px]">AGE RESTRICTION</div>
                            <div class="font-semibold mt-1">18+ ONLY</div>
                        </div>
                    </div>

                    <div class="border-t border-primary-border pt-6">
                        <h4 class="font-heading font-bold text-xs uppercase tracking-widest mb-3 text-muted-text">LINEUP
                        </h4>
                        <div id="lightbox-lineup"
                            class="font-heading font-bold text-sm tracking-wider uppercase text-main-text flex flex-col gap-1 mb-6">
                            <!-- Dynamically populated -->
                        </div>
                        <p id="lightbox-desc" class="font-body text-xs text-muted-text leading-relaxed">
                            Event description...
                        </p>
                    </div>
                </div>

                <!-- Booking interaction box inside lightbox -->
                <div class="border-t border-primary-border pt-6 mt-8">
                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <div>
                            <label
                                class="block text-[10px] text-muted-text uppercase tracking-widest font-heading mb-2">SELECT
                                DATE</label>
                            <input type="date" id="lightbox-date-selector"
                                class="w-full bg-surface-bg border border-primary-border p-3 text-xs font-heading font-bold text-main-text rounded-lg outline-none focus:border-gold">
                        </div>
                        <div>
                            <label
                                class="block text-[10px] text-muted-text uppercase tracking-widest font-heading mb-2">SELECT
                                CITY</label>
                            <select id="lightbox-location-selector"
                                class="w-full bg-surface-bg border border-primary-border p-3 text-xs font-heading font-bold text-main-text tracking-widest rounded-lg">
                                <option value="Mumbai">Mumbai</option>
                                <option value="Bengaluru">Bengaluru</option>
                                <option value="Delhi">Delhi</option>
                                <option value="Pune">Pune</option>
                                <option value="Goa">Goa</option>
                            </select>
                        </div>
                    </div>

                    <label class="block text-[10px] text-muted-text uppercase tracking-widest font-heading mb-2">SELECT
                        PACKAGE
                        TIER</label>
                    <select id="lightbox-tier-selector" onchange="updateLightboxTierPrice()"
                        class="w-full bg-surface-bg border border-primary-border p-3 text-xs font-heading font-bold text-main-text tracking-widest rounded-lg mb-4">
                        <!-- Dynamic options -->
                    </select>

                    <div class="flex justify-between items-center mb-6">
                        <span class="text-xs text-muted-text uppercase tracking-widest">PACKAGE PRICE</span>
                        <span id="lightbox-tier-price"
                            class="font-heading font-bold text-xl text-main-text">₹0.00</span>
                    </div>

                    <div class="flex flex-col gap-2">
                        <button onclick="addTicketFromLightbox()"
                            class="w-full brutalist-button py-3.5 text-center text-xs uppercase tracking-widest cursor-pointer">
                            ADD TO CART
                        </button>
                        <button onclick="enquireOnWhatsApp()"
                            class="w-full border border-[#25D366] hover:bg-[#25D366]/10 text-[#25D366] transition-colors py-3.5 text-center text-xs font-heading font-bold uppercase tracking-widest cursor-pointer rounded-lg flex items-center justify-center gap-2 bg-transparent">
                            <i class="fa-brands fa-whatsapp text-base" aria-hidden="true"></i>
                            ENQUIRE ON WHATSAPP
                        </button>
                    </div>
                </div>

            </div>

        </div>
    </div>

    <!-- Checkout Success Modal -->
    <div id="success-modal" class="fixed inset-0 z-50 pointer-events-none hidden items-center justify-center p-4">
        <div class="absolute inset-0 bg-black/90 pointer-events-auto"></div>
        <div
            class="relative bg-surface-bg border border-primary-border max-w-md w-full p-8 text-center pointer-events-auto z-10">
            <div class="w-16 h-16 bg-white text-black flex items-center justify-center rounded-full mx-auto mb-6">
                <i class="fa-solid fa-check text-2xl" aria-hidden="true"></i>
            </div>

            <h3 class="font-heading font-bold text-2xl uppercase tracking-tight mb-2">BOOKING CONFIRMED</h3>
            <p class="font-body text-xs text-muted-text mb-6">
                Your payment was processed successfully. We've sent the e-tickets and receipt to your email address.
            </p>

            <!-- Mock barcode vector -->
            <div class="bg-white p-4 inline-block mb-8 border border-primary-border">
                <div class="w-48 h-12 flex justify-between bg-black">
                    <div class="w-1 h-full bg-white"></div>
                    <div class="w-3 h-full bg-white ml-1"></div>
                    <div class="w-2 h-full bg-white ml-0.5"></div>
                    <div class="w-1 h-full bg-white ml-2"></div>
                    <div class="w-4 h-full bg-white ml-1"></div>
                    <div class="w-1.5 h-full bg-white ml-0.5"></div>
                    <div class="w-2.5 h-full bg-white ml-1"></div>
                    <div class="w-1 h-full bg-white ml-0.5"></div>
                    <div class="w-3 h-full bg-white ml-2"></div>
                </div>
                <div class="text-[9px] font-heading tracking-widest text-black font-semibold mt-2">VIBE-59E6B245-C927
                </div>
            </div>

            <button onclick="closeSuccessModal()"
                class="w-full brutalist-button py-3 text-xs uppercase tracking-wider cursor-pointer">
                GO BACK TO GIGS
            </button>
        </div>
    </div>

    <!-- Footer Footer Footer -->
    