<div id="whatsapp-floating-widget" class="fixed bottom-20 sm:bottom-24 right-5 md:right-7 z-50 flex flex-col items-end gap-2.5 pointer-events-none select-none">
    
    <!-- Proactive Chat Bubble ("Need any help?") -->
    <div id="wa-chat-bubble" 
         class="pointer-events-auto opacity-0 translate-y-3 transition-all duration-500 ease-out bg-white dark:bg-[#18181B] text-[#18181B] dark:text-white border border-gray-200 dark:border-white/10 p-3.5 sm:p-4 rounded-2xl shadow-2xl max-w-[240px] sm:max-w-[260px] relative hidden">
        
        <!-- Close Button -->
        <button type="button" onclick="dismissWaBubble(event)" aria-label="Close message"
                class="absolute top-2.5 right-2.5 text-gray-400 hover:text-gray-700 dark:hover:text-white p-1 rounded-full text-xs transition-colors cursor-pointer leading-none">
            <i class="fa-solid fa-xmark"></i>
        </button>

        <!-- Message Body -->
        <a href="https://wa.me/919131668156?text=Hi%20Artizen%2C%20I%20have%20an%20inquiry%20regarding%20an%20event%20booking."
           target="_blank" 
           class="block text-left group cursor-pointer pr-3">
            <span class="text-[11px] font-heading font-extrabold uppercase tracking-wider text-[#B89700] dark:text-[#FFD600] block mb-1">
                Artizen Planner
            </span>
            <p class="text-xs font-medium text-gray-700 dark:text-gray-200 leading-snug">
                Need any help planning your event in Indore?
            </p>
            <span class="inline-flex items-center gap-1 text-[11px] font-bold text-[#25D366] mt-2 group-hover:underline">
                <span>Chat with us</span>
                <i class="fa-solid fa-arrow-right text-[9px]"></i>
            </span>
        </a>

        <!-- Tail pointer pointing down to the button -->
        <div class="absolute -bottom-1.5 right-6 w-3.5 h-3.5 bg-white dark:bg-[#18181B] border-r border-b border-gray-200 dark:border-white/10 rotate-45"></div>
    </div>

    <!-- Clean WhatsApp Button (Solid, No animations, No hover expansion) -->
    <a href="https://wa.me/919131668156?text=Hi%20Artizen%2C%20I%20have%20an%20inquiry%20regarding%20an%20event%20booking."
       target="_blank"
       aria-label="Chat with Artizen on WhatsApp"
       class="pointer-events-auto w-14 h-14 rounded-full bg-[#25D366] text-white flex items-center justify-center shadow-xl hover:bg-[#20ba5a] transition-colors duration-200 cursor-pointer focus:outline-none"
    >
        <i class="fa-brands fa-whatsapp text-3xl"></i>
    </a>
</div>

<script>
    (function () {
        // Trigger chat bubble after 3.5 seconds on page
        setTimeout(function () {
            const bubble = document.getElementById('wa-chat-bubble');
            if (!bubble) return;
            
            // Check if dismissed in this session
            if (sessionStorage.getItem('artizen_wa_bubble_dismissed') === 'true') {
                return;
            }

            bubble.classList.remove('hidden');
            requestAnimationFrame(() => {
                bubble.classList.remove('opacity-0', 'translate-y-3');
                bubble.classList.add('opacity-100', 'translate-y-0');
            });
        }, 3500);

        window.dismissWaBubble = function (e) {
            if (e) {
                e.preventDefault();
                e.stopPropagation();
            }
            const bubble = document.getElementById('wa-chat-bubble');
            if (bubble) {
                bubble.classList.add('opacity-0', 'translate-y-3');
                setTimeout(() => {
                    bubble.classList.add('hidden');
                }, 400);
            }
            sessionStorage.setItem('artizen_wa_bubble_dismissed', 'true');
        };
    })();
</script>
