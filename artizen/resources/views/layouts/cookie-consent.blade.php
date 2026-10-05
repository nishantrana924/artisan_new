<!-- ARTIZEN — Cookie & Terms Consent Banner / Card -->
<div id="artizen-cookie-consent" 
     class="fixed bottom-4 left-4 right-4 sm:left-6 sm:right-auto sm:max-w-[430px] z-[990] bg-white/95 dark:bg-[#18181B]/95 backdrop-blur-md rounded-2xl p-5 sm:p-6 shadow-[0_12px_40px_rgba(0,0,0,0.18)] border border-gray-200/80 dark:border-white/10 opacity-0 translate-y-6 pointer-events-none transition-all duration-400 ease-out select-none">
    
    <div class="flex items-start gap-3.5">
        <!-- Cookie / Privacy Badge Icon -->
        <div class="w-10 h-10 rounded-xl bg-[#FFF9D2] dark:bg-[#2A2610] text-[#9E8200] dark:text-[#FFD600] flex items-center justify-center shrink-0 text-base shadow-xs">
            <i class="fa-solid fa-shield-halved"></i>
        </div>

        <div class="flex-1 min-w-0">
            <h4 class="text-[15px] font-bold text-gray-900 dark:text-white font-heading mb-1 tracking-tight">
                Cookie & Privacy Choices
            </h4>
            <p class="text-xs sm:text-[13px] text-gray-600 dark:text-gray-300 leading-relaxed font-normal mb-4">
                We use cookies to personalize your event planning experience and analyze our traffic. By continuing, you agree to our 
                <a href="{{ route('contact.index') }}" class="font-semibold text-black dark:text-[#FFD600] underline hover:no-underline">Terms &amp; Conditions</a> 
                and 
                <a href="{{ route('contact.index') }}" class="font-semibold text-black dark:text-[#FFD600] underline hover:no-underline">Privacy Policy</a>.
            </p>

            <!-- Buttons -->
            <div class="flex items-center gap-2.5">
                <button type="button" 
                        onclick="dismissCookieConsent('declined')"
                        class="px-3.5 py-2 rounded-xl text-xs sm:text-[13px] font-medium text-gray-600 dark:text-gray-300 bg-gray-100 hover:bg-gray-200 dark:bg-[#27272A] dark:hover:bg-[#323236] transition-colors cursor-pointer focus:outline-none">
                    Decline
                </button>

                <button type="button" 
                        onclick="dismissCookieConsent('accepted')"
                        class="px-4 py-2 rounded-xl text-xs sm:text-[13px] font-semibold text-black bg-[#FFD600] hover:bg-[#E6C200] active:scale-[0.98] transition-all duration-150 shadow-xs cursor-pointer focus:outline-none">
                    Accept All
                </button>
            </div>
        </div>

        <!-- Quick Close Icon Button -->
        <button type="button" 
                onclick="dismissCookieConsent('closed')" 
                class="text-gray-400 hover:text-gray-700 dark:hover:text-white p-1 -mt-1 -mr-1 rounded-lg text-xs transition-colors cursor-pointer"
                aria-label="Close consent banner">
            <i class="fa-solid fa-xmark"></i>
        </button>
    </div>
</div>

<script>
    (function () {
        const LOCAL_STORAGE_KEY = 'artizen_cookie_consent_accepted';
        const SESSION_STORAGE_KEY = 'artizen_cookie_session_dismissed';

        function checkAndShowCookieBanner() {
            // If already accepted permanently, never show again
            if (localStorage.getItem(LOCAL_STORAGE_KEY) === 'true') {
                return;
            }

            // If dismissed in this current session, do not show until next visit/tab session
            if (sessionStorage.getItem(SESSION_STORAGE_KEY) === 'true') {
                return;
            }

            // Show after brief delay
            setTimeout(function () {
                const banner = document.getElementById('artizen-cookie-consent');
                if (banner) {
                    banner.classList.remove('opacity-0', 'translate-y-6', 'pointer-events-none');
                    banner.classList.add('opacity-100', 'translate-y-0', 'pointer-events-auto');
                }
            }, 1200);
        }

        window.dismissCookieConsent = function (action) {
            if (action === 'accepted') {
                // Permanently store in localStorage: Never show again
                localStorage.setItem(LOCAL_STORAGE_KEY, 'true');
            } else {
                // Declined or closed: Dismiss for this session only, will show again on next visit
                sessionStorage.setItem(SESSION_STORAGE_KEY, 'true');
            }

            const banner = document.getElementById('artizen-cookie-consent');
            if (banner) {
                banner.classList.remove('opacity-100', 'translate-y-0', 'pointer-events-auto');
                banner.classList.add('opacity-0', 'translate-y-6', 'pointer-events-none');
            }
        };

        document.addEventListener('DOMContentLoaded', checkAndShowCookieBanner);
    })();
</script>
