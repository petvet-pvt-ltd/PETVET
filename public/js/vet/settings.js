/**
 * Vet Settings Page JavaScript
 * Handles profile updates, password changes, and preferences
 */

(function() {
    'use strict';

    const MODULE = 'vet';
    const $ = (sel) => document.querySelector(sel);
    const $$ = (sel) => document.querySelectorAll(sel);

    // Toast notification
    function showToast(message, type = 'success') {
        const toast = document.getElementById('toast');
        if (!toast) return;
        
        toast.textContent = message;
        toast.className = `toast ${type} show`;
        
        setTimeout(() => {
            toast.classList.remove('show');
        }, 3000);
    }

    // Quick nav highlighting on scroll using IntersectionObserver
    function setupScrollSpy() {
        const navLinks = $$('.quick-nav a');
        const sections = $$('[id^="section"]');
        
        if (navLinks.length && sections.length) {
            const sectionMap = new Map();
            navLinks.forEach(link => {
                const href = link.getAttribute('href');
                if (href.startsWith('#')) {
                    const sectionId = href.substring(1);
                    const section = $(`#${sectionId}`);
                    if (section) {
                        sectionMap.set(section, link);
                    }
                }
            });

            const observerOptions = {
                rootMargin: '-40% 0px -50% 0px',
                threshold: [0, 0.25, 0.5, 1]
            };

            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        const link = sectionMap.get(entry.target);
                        if (link) {
                            navLinks.forEach(l => l.classList.remove('active'));
                            link.classList.add('active');
                        }
                    }
                });
            }, observerOptions);

            sections.forEach(section => observer.observe(section));
        }
    }

    // Handle avatar upload and preview
    function setupAvatarUpload() {
        const avatarInput = document.getElementById('vetAvatar');
        const changeBtn = document.querySelector('[data-for="vetAvatar"]');
        const preview = document.querySelector('#vetAvatarPreview img');

        if (changeBtn && avatarInput) {
            changeBtn.addEventListener('click', (e) => {
                e.preventDefault();
                avatarInput.click();
            });
        }

        if (avatarInput && preview) {
            avatarInput.addEventListener('change', (e) => {
                const file = e.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = (event) => {
                        preview.src = event.target.result;
                    };
                    reader.readAsDataURL(file);
                }
            });
        }
    }

    // Phone validation
    function setupPhoneValidation() {
        const phoneInput = document.getElementById('phoneInput');
        const phoneError = document.getElementById('phoneError');
        
        if (phoneInput) {
            phoneInput.addEventListener('input', () => {
                const value = phoneInput.value;
                const regex = /^07[0-9]{8}$/;
                
                if (value && !regex.test(value)) {
                    phoneError.textContent = 'Phone must be 10 digits starting with 07';
                    phoneError.style.display = 'block';
                    phoneInput.style.borderColor = '#ef4444';
                } else {
                    phoneError.style.display = 'none';
                    phoneInput.style.borderColor = '';
                }
            });
        }
    }

    // Button state management
    function updateButtonState(form, button) {
        if (form.dataset.clean === 'true') {
            button.disabled = true;
            button.style.opacity = '0.5';
            button.style.cursor = 'not-allowed';
            button.style.pointerEvents = 'none';
        } else {
            button.disabled = false;
            button.style.opacity = '1';
            button.style.cursor = 'pointer';
            button.style.pointerEvents = 'auto';
        }
    }

    function captureFormState(form) {
        const formData = new FormData(form);
        const state = {};
        for (let [key, value] of formData.entries()) {
            state[key] = value;
        }
        return JSON.stringify(state);
    }

    function hasFormChanged(form, originalState) {
        const currentState = captureFormState(form);
        return currentState !== originalState;
    }

    // Handle profile form submission
    function setupProfileForm() {
        const form = document.getElementById('formProfile');
        if (!form) return;

        form.addEventListener('submit', async (e) => {
            e.preventDefault();

            const formData = new FormData(form);
            const submitBtn = form.querySelector('button[type="submit"]');
            const originalText = submitBtn.textContent;
            
            submitBtn.disabled = true;
            submitBtn.textContent = 'Saving...';

            try {
                const response = await fetch(`/PETVET/api/${MODULE}/update-profile.php`, {
                    method: 'POST',
                    body: formData
                });

                const data = await response.json();

                if (data.success) {
                    showToast('Profile updated successfully!', 'success');
                    form.dataset.clean = 'true';
                    updateButtonState(form, submitBtn);
                    setTimeout(() => window.location.reload(), 1000);
                } else {
                    showToast(data.error || 'Failed to update profile', 'error');
                }
            } catch (error) {
                console.error('Profile update error:', error);
                showToast('An error occurred. Please try again.', 'error');
            } finally {
                submitBtn.disabled = false;
                submitBtn.textContent = originalText;
            }
        });
    }

    // Handle password form submission
    function setupPasswordForm() {
        const form = document.getElementById('formPassword');
        if (!form) return;

        form.addEventListener('submit', async (e) => {
            e.preventDefault();

            const formData = new FormData(form);
            const newPassword = formData.get('new_password');
            const confirmPassword = formData.get('confirm_password');

            if (newPassword !== confirmPassword) {
                showToast('Passwords do not match', 'error');
                return;
            }

            const submitBtn = form.querySelector('button[type="submit"]');
            const originalText = submitBtn.textContent;
            submitBtn.disabled = true;
            submitBtn.textContent = 'Updating...';

            try {
                const response = await fetch(`/PETVET/api/${MODULE}/update-password.php`, {
                    method: 'POST',
                    body: formData
                });

                const data = await response.json();

                if (data.success) {
                    showToast('Password updated successfully!', 'success');
                    form.reset();
                    form.dataset.clean = 'true';
                    updateButtonState(form, submitBtn);
                } else {
                    showToast(data.error || 'Failed to update password', 'error');
                }
            } catch (error) {
                console.error('Password update error:', error);
                showToast('An error occurred. Please try again.', 'error');
            } finally {
                submitBtn.disabled = false;
                submitBtn.textContent = originalText;
            }
        });
    }

    // Handle preferences form submission
    function setupPreferencesForm() {
        const form = document.getElementById('formPrefs');
        if (!form) return;

        form.addEventListener('submit', async (e) => {
            e.preventDefault();

            const formData = new FormData(form);
            const submitBtn = form.querySelector('button[type="submit"]');
            const originalText = submitBtn.textContent;
            
            submitBtn.disabled = true;
            submitBtn.textContent = 'Saving...';

            try {
                const response = await fetch(`/PETVET/api/${MODULE}/update-preferences.php`, {
                    method: 'POST',
                    body: formData
                });

                const data = await response.json();

                if (data.success) {
                    showToast('Preferences updated successfully!', 'success');
                    form.dataset.clean = 'true';
                    updateButtonState(form, submitBtn);
                } else {
                    showToast(data.error || 'Failed to update preferences', 'error');
                }
            } catch (error) {
                console.error('Preferences update error:', error);
                showToast('An error occurred. Please try again.', 'error');
            } finally {
                submitBtn.disabled = false;
                submitBtn.textContent = originalText;
            }
        });
    }

    // Initialize form change tracking
    function setupFormTracking() {
        setTimeout(() => {
            ['#formProfile', '#formPassword', '#formPrefs'].forEach(id => {
                const f = $(id);
                if (!f) return;
                
                // Capture original state
                const originalState = captureFormState(f);
                f.dataset.originalState = originalState;
                f.dataset.clean = 'true';
                
                const btn = f.querySelector('button[type="submit"]');
                if (btn) {
                    updateButtonState(f, btn);
                }
                
                f.addEventListener('input', () => {
                    const changed = hasFormChanged(f, f.dataset.originalState);
                    f.dataset.clean = changed ? 'false' : 'true';
                    if (btn) updateButtonState(f, btn);
                });
                
                // Also track file input changes for avatar
                const fileInputs = f.querySelectorAll('input[type="file"]');
                fileInputs.forEach(inp => {
                    inp.addEventListener('change', () => {
                        if (inp.files && inp.files.length > 0) {
                            f.dataset.clean = 'false';
                            if (btn) updateButtonState(f, btn);
                        }
                    });
                });
            });
        }, 100);

        // Warn before leaving page with unsaved changes
        window.addEventListener('beforeunload', e => {
            const dirty = ['#formProfile', '#formPassword', '#formPrefs'].some(id => {
                const f = $(id);
                return f && f.dataset.clean === 'false';
            });
            if (dirty) {
                e.preventDefault();
                e.returnValue = '';
            }
        });
    }

    // Initialize on DOM ready
    document.addEventListener('DOMContentLoaded', () => {
        setupScrollSpy();
        setupAvatarUpload();
        setupPhoneValidation();
        setupProfileForm();
        setupPasswordForm();
        setupPreferencesForm();
        setupFormTracking();
    });

})();
