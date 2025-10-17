/**
 * Custom Confirmation Modal - Vanilla JavaScript (NO LIBRARIES)
 * Replaces browser's alert() and confirm() with custom modal popups
 * Freezes background when open
 */

const ConfirmModal = {
    overlay: null,
    currentCallback: null,

    /**
     * Initialize the modal (call once on page load)
     */
    init() {
        if (this.overlay) return; // Already initialized

        // Create modal HTML
        const html = `
            <div class="confirm-modal-overlay" id="confirmModalOverlay">
                <div class="confirm-modal" id="confirmModal">
                    <div class="confirm-modal-header">
                        <div class="confirm-modal-icon" id="confirmModalIcon">⚠️</div>
                        <div class="confirm-modal-content">
                            <h3 class="confirm-modal-title" id="confirmModalTitle">Confirm Action</h3>
                            <p class="confirm-modal-message" id="confirmModalMessage">Are you sure?</p>
                        </div>
                    </div>
                    <div class="confirm-modal-footer">
                        <button class="confirm-modal-btn confirm-modal-btn-cancel" id="confirmModalCancel">Cancel</button>
                        <button class="confirm-modal-btn confirm-modal-btn-confirm" id="confirmModalConfirm">Confirm</button>
                    </div>
                </div>
            </div>
        `;

        // Append to body
        document.body.insertAdjacentHTML('beforeend', html);

        // Get references
        this.overlay = document.getElementById('confirmModalOverlay');
        this.modal = document.getElementById('confirmModal');
        this.icon = document.getElementById('confirmModalIcon');
        this.title = document.getElementById('confirmModalTitle');
        this.message = document.getElementById('confirmModalMessage');
        this.cancelBtn = document.getElementById('confirmModalCancel');
        this.confirmBtn = document.getElementById('confirmModalConfirm');

        // Event listeners
        this.cancelBtn.addEventListener('click', () => this.close(false));
        this.confirmBtn.addEventListener('click', () => this.close(true));

        // Close on overlay click
        this.overlay.addEventListener('click', (e) => {
            if (e.target === this.overlay) {
                this.close(false);
            }
        });

        // Close on Escape key
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && this.overlay.classList.contains('active')) {
                this.close(false);
            }
        });
    },

    /**
     * Show confirmation modal
     * @param {Object} options - Modal options
     * @returns {Promise<boolean>} - Resolves to true if confirmed, false if cancelled
     */
    show(options = {}) {
        if (!this.overlay) this.init();

        return new Promise((resolve) => {
            // Set options
            this.title.textContent = options.title || 'Confirm Action';
            this.message.textContent = options.message || 'Are you sure you want to proceed?';
            this.cancelBtn.textContent = options.cancelText || 'Cancel';
            this.confirmBtn.textContent = options.confirmText || 'Confirm';

            // Set icon type
            const type = options.type || 'warning'; // warning, danger, info, success
            this.icon.className = 'confirm-modal-icon ' + type;

            // Set icon emoji
            const icons = {
                warning: '⚠️',
                danger: '🗑️',
                info: 'ℹ️',
                success: '✓'
            };
            this.icon.textContent = icons[type] || icons.warning;

            // Set confirm button style
            this.confirmBtn.className = 'confirm-modal-btn confirm-modal-btn-confirm';
            if (type === 'danger') {
                this.confirmBtn.classList.add('danger');
            }

            // Store callback
            this.currentCallback = resolve;

            // Show modal
            this.overlay.classList.add('active');
            document.body.classList.add('modal-open');

            // Focus confirm button
            setTimeout(() => {
                this.confirmBtn.focus();
            }, 100);
        });
    },

    /**
     * Close modal
     * @param {boolean} confirmed - Whether user confirmed or cancelled
     */
    close(confirmed) {
        this.overlay.classList.remove('active');
        document.body.classList.remove('modal-open');

        if (this.currentCallback) {
            this.currentCallback(confirmed);
            this.currentCallback = null;
        }
    },

    /**
     * Show alert (replacement for window.alert)
     * @param {string} message - Alert message
     * @param {string} title - Alert title
     */
    async alert(message, title = 'Notice') {
        return this.show({
            title: title,
            message: message,
            type: 'info',
            confirmText: 'OK',
            cancelText: '' // Hide cancel button by setting empty text
        });
    },

    /**
     * Show confirmation (replacement for window.confirm)
     * @param {string} message - Confirmation message
     * @param {string} title - Confirmation title
     * @returns {Promise<boolean>} - True if confirmed, false if cancelled
     */
    async confirm(message, title = 'Confirm') {
        return this.show({
            title: title,
            message: message,
            type: 'warning',
            confirmText: 'Yes',
            cancelText: 'No'
        });
    },

    /**
     * Show delete confirmation
     * @param {string} itemName - Name of item being deleted
     * @returns {Promise<boolean>} - True if confirmed, false if cancelled
     */
    async confirmDelete(itemName = 'this item') {
        return this.show({
            title: 'Delete ' + itemName + '?',
            message: 'This action cannot be undone. Are you sure you want to delete ' + itemName + '?',
            type: 'danger',
            confirmText: 'Delete',
            cancelText: 'Cancel'
        });
    }
};

// Initialize on DOM ready
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => ConfirmModal.init());
} else {
    ConfirmModal.init();
}

// Export for use in other scripts
window.ConfirmModal = ConfirmModal;
