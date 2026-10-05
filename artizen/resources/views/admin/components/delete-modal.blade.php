{{-- =========================================================================
     GLOBAL DELETE CONFIRMATION MODAL
     Clean, minimalist, zero-clutter confirmation dialog for all admin deletes.
     ========================================================================= --}}
<div id="global-delete-modal"
     class="fixed inset-0 z-[120] flex items-center justify-center bg-black/50 backdrop-blur-xs hidden opacity-0 transition-opacity duration-200 pointer-events-none p-4 select-none"
     role="dialog"
     aria-modal="true"
     aria-labelledby="global-delete-title">
    
    <div class="modal-card bg-white dark:bg-[#1C1C20] rounded-2xl w-full max-w-sm p-6 shadow-2xl border border-gray-100 dark:border-white/10 text-center transform scale-95 opacity-0 transition-all duration-200 pointer-events-auto">
        {{-- Title --}}
        <h3 id="global-delete-title" class="text-[15px] font-bold text-gray-900 dark:text-white tracking-tight">
            Delete item?
        </h3>

        {{-- Subtitle / Notice --}}
        <p id="global-delete-message" class="text-xs text-gray-500 dark:text-gray-400 mt-1.5 mb-6 leading-relaxed">
            Are you sure? This action cannot be undone.
        </p>

        {{-- Action Buttons --}}
        <div class="flex items-center gap-2.5">
            <button type="button"
                    onclick="closeDeleteModal()"
                    class="flex-1 py-2.5 px-4 rounded-xl text-xs font-semibold text-gray-700 dark:text-gray-300 bg-gray-100 hover:bg-gray-200 dark:bg-white/5 dark:hover:bg-white/10 transition-colors cursor-pointer outline-none">
                Cancel
            </button>
            <button type="button"
                    id="global-delete-confirm-btn"
                    onclick="confirmDeleteAction()"
                    class="flex-1 py-2.5 px-4 rounded-xl text-xs font-bold text-white bg-red-600 hover:bg-red-700 active:bg-red-800 transition-colors cursor-pointer shadow-xs outline-none">
                Delete
            </button>
        </div>
    </div>
</div>

<script>
    (function () {
        let pendingAction = null;

        window.openDeleteModal = function (options = {}) {
            const modal = document.getElementById('global-delete-modal');
            const titleEl = document.getElementById('global-delete-title');
            const messageEl = document.getElementById('global-delete-message');
            if (!modal) return;

            const title = options.title || 'Delete item?';
            const message = options.message || 'Are you sure? This action cannot be undone.';

            if (titleEl) titleEl.textContent = title;
            if (messageEl) messageEl.textContent = message;

            if (options.onConfirm && typeof options.onConfirm === 'function') {
                pendingAction = options.onConfirm;
            } else if (options.form) {
                pendingAction = () => {
                    if (typeof options.form.submit === 'function') {
                        options.form.submit();
                    } else {
                        HTMLFormElement.prototype.submit.call(options.form);
                    }
                };
            } else {
                pendingAction = null;
            }

            modal.classList.remove('hidden', 'pointer-events-none');
            requestAnimationFrame(() => {
                modal.classList.remove('opacity-0');
                const card = modal.querySelector('.modal-card');
                if (card) {
                    card.classList.remove('scale-95', 'opacity-0');
                    card.classList.add('scale-100', 'opacity-100');
                }
            });
        };

        window.closeDeleteModal = function () {
            const modal = document.getElementById('global-delete-modal');
            if (!modal) return;
            const card = modal.querySelector('.modal-card');
            if (card) {
                card.classList.remove('scale-100', 'opacity-100');
                card.classList.add('scale-95', 'opacity-0');
            }
            modal.classList.add('opacity-0');
            setTimeout(() => {
                modal.classList.add('hidden', 'pointer-events-none');
                pendingAction = null;
            }, 180);
        };

        window.confirmDeleteAction = function () {
            const btn = document.getElementById('global-delete-confirm-btn');
            if (btn) {
                btn.disabled = true;
                btn.classList.add('opacity-75', 'cursor-not-allowed');
                btn.textContent = 'Deleting...';
            }

            const action = pendingAction;
            pendingAction = null;

            if (typeof action === 'function') {
                action();
            } else {
                window.closeDeleteModal();
            }
        };

        // Global shorthand for triggering delete on any form
        window.confirmDelete = function (formOrEvent, itemName = '') {
            let form = null;
            if (formOrEvent && typeof formOrEvent.preventDefault === 'function') {
                formOrEvent.preventDefault();
                formOrEvent.stopPropagation();
                form = formOrEvent.target.closest('form') || formOrEvent.currentTarget.closest('form');
            } else if (formOrEvent instanceof HTMLFormElement) {
                form = formOrEvent;
            } else if (typeof formOrEvent === 'string') {
                form = document.querySelector(formOrEvent);
            }

            const title = itemName ? `Delete "${itemName}"?` : 'Delete this item?';
            window.openDeleteModal({
                form: form,
                title: title,
                message: 'Are you sure? This action cannot be undone.'
            });
            return false;
        };

        // Delegated click handler for any [data-confirm-delete] element
        document.addEventListener('click', function (e) {
            const trigger = e.target.closest('[data-confirm-delete]');
            if (!trigger) return;
            e.preventDefault();
            e.stopPropagation();

            const form = trigger.closest('form');
            const customTitle = trigger.getAttribute('data-delete-title') || trigger.getAttribute('data-confirm-delete');
            const customMessage = trigger.getAttribute('data-delete-message');

            window.openDeleteModal({
                form: form,
                title: customTitle ? `Delete "${customTitle}"?` : 'Delete this item?',
                message: customMessage || 'Are you sure? This action cannot be undone.'
            });
        });

        // Close on backdrop click
        document.addEventListener('click', function (e) {
            const modal = document.getElementById('global-delete-modal');
            if (modal && e.target === modal) {
                window.closeDeleteModal();
            }
        });

        // Close on ESC
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') {
                const modal = document.getElementById('global-delete-modal');
                if (modal && !modal.classList.contains('hidden')) {
                    window.closeDeleteModal();
                }
            }
        });
    })();
</script>
