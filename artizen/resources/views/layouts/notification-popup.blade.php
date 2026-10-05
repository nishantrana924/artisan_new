<!-- ARTIZEN — Notification Permission Popup Modal -->
<div id="artizen-notification-modal" 
     class="fixed inset-0 z-[999] flex items-center justify-center p-4 bg-black/40 backdrop-blur-[2px] opacity-0 pointer-events-none transition-all duration-300 ease-out select-none"
     aria-modal="true" 
     role="dialog"
     aria-labelledby="notif-modal-title">

    <!-- Modal Card Box -->
    <div id="artizen-notification-card" 
         class="w-full max-w-[390px] bg-white dark:bg-[#18181B] rounded-[24px] shadow-2xl p-6 sm:p-8 transform scale-95 transition-all duration-300 ease-out border border-black/5 dark:border-white/10 text-center">
        
        <!-- Header Title -->
        <h3 id="notif-modal-title" class="text-2xl sm:text-[26px] font-bold text-[#27272A] dark:text-white tracking-tight mb-2.5 font-heading">
            Stay Updated!
        </h3>

        <!-- Description -->
        <p class="text-[14px] sm:text-[15px] text-[#6B7280] dark:text-[#A1A1AA] leading-relaxed font-normal mb-7 px-2">
            Allow notifications to receive the latest offers and updates.
        </p>

        <!-- Action Buttons -->
        <div class="grid grid-cols-2 gap-3 sm:gap-4">
            <!-- No, Thanks Button -->
            <button type="button" 
                    id="btn-notif-deny"
                    onclick="dismissNotificationModal(false)"
                    class="w-full py-3 px-4 rounded-xl bg-[#EBEBEB] hover:bg-[#E0E0E0] active:scale-[0.98] dark:bg-[#27272A] dark:hover:bg-[#323236] text-[#71717A] dark:text-[#D4D4D8] font-medium text-[15px] sm:text-base transition-all duration-150 cursor-pointer focus:outline-none">
                No, Thanks
            </button>

            <!-- Allow Button (Olive Green/Gold Theme Accent matching screenshot) -->
            <button type="button" 
                    id="btn-notif-allow"
                    onclick="acceptNotificationModal()"
                    class="w-full py-3 px-4 rounded-xl bg-[#717436] hover:bg-[#63662F] active:scale-[0.98] text-white font-medium text-[15px] sm:text-base shadow-sm hover:shadow transition-all duration-150 cursor-pointer focus:outline-none">
                Allow
            </button>
        </div>
    </div>
</div>

<script>
    (function () {
        const LOCAL_STORAGE_KEY = 'artizen_notification_accepted';
        const SESSION_STORAGE_KEY = 'artizen_notif_session_dismissed';

        function shouldShowNotificationPrompt() {
            // If browser does not support notifications
            if (!('Notification' in window)) return false;

            // If already accepted permanently, never show again
            if (localStorage.getItem(LOCAL_STORAGE_KEY) === 'true') {
                return false;
            }

            // If browser already granted permission, save in localStorage and don't show
            if (Notification.permission === 'granted') {
                localStorage.setItem(LOCAL_STORAGE_KEY, 'true');
                return false;
            }

            // If dismissed in this current session, do not show until next visit/session
            if (sessionStorage.getItem(SESSION_STORAGE_KEY) === 'true') {
                return false;
            }

            return true;
        }

        function openNotificationModal() {
            const modal = document.getElementById('artizen-notification-modal');
            const card = document.getElementById('artizen-notification-card');
            if (!modal || !card) return;

            modal.classList.remove('opacity-0', 'pointer-events-none');
            modal.classList.add('opacity-100', 'pointer-events-auto');

            card.classList.remove('scale-95');
            card.classList.add('scale-100');
        }

        window.closeNotificationModal = function () {
            const modal = document.getElementById('artizen-notification-modal');
            const card = document.getElementById('artizen-notification-card');
            if (!modal || !card) return;

            modal.classList.remove('opacity-100', 'pointer-events-auto');
            modal.classList.add('opacity-0', 'pointer-events-none');

            card.classList.remove('scale-100');
            card.classList.add('scale-95');
        };

        window.dismissNotificationModal = function () {
            // Only dismiss for this session — will show again on next visit!
            sessionStorage.setItem(SESSION_STORAGE_KEY, 'true');
            window.closeNotificationModal();
        };

        window.acceptNotificationModal = async function () {
            window.closeNotificationModal();
            // Permanently store in localStorage: Never show again
            localStorage.setItem(LOCAL_STORAGE_KEY, 'true');

            try {
                if ('Notification' in window && Notification.permission !== 'denied') {
                    const permission = await Notification.requestPermission();
                    if (permission === 'granted') {
                        try {
                            new Notification('Artizen Notifications Enabled 🎉', {
                                body: 'You will now receive special offers and updates for events in Indore!',
                                icon: '/assets/images/logo/artizen.png'
                            });
                        } catch (err) {}
                    }
                }
            } catch (e) {
                console.warn('Notification permission error:', e);
            }
        };

        // Trigger notification modal 2.5s after load if applicable
        document.addEventListener('DOMContentLoaded', function () {
            if (shouldShowNotificationPrompt()) {
                setTimeout(openNotificationModal, 2500);
            }
        });
    })();
</script>
