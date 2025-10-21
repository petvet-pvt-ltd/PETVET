// Groomer Settings JavaScript
document.addEventListener('DOMContentLoaded', function() {
    const toast = document.getElementById('toast');

    // Quick navigation smooth scroll
    document.querySelectorAll('.quick-nav a').forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const targetId = this.getAttribute('href');
            const targetSection = document.querySelector(targetId);
            
            if (targetSection) {
                targetSection.scrollIntoView({ behavior: 'smooth', block: 'start' });
                
                // Update active state
                document.querySelectorAll('.quick-nav a').forEach(l => l.classList.remove('active'));
                this.classList.add('active');
            }
        });
    });

    // Update active nav on scroll
    const observerOptions = {
        root: null,
        rootMargin: '-100px 0px -50% 0px',
        threshold: 0
    };

    const observer = new IntersectionObserver(entries => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const id = entry.target.getAttribute('id');
                document.querySelectorAll('.quick-nav a').forEach(link => {
                    link.classList.remove('active');
                    if (link.getAttribute('href') === `#${id}`) {
                        link.classList.add('active');
                    }
                });
            }
        });
    }, observerOptions);

    document.querySelectorAll('[data-section]').forEach(section => {
        observer.observe(section);
    });

    // File upload handlers
    document.querySelectorAll('button[data-for]').forEach(btn => {
        btn.addEventListener('click', function() {
            const inputId = this.dataset.for;
            const input = document.getElementById(inputId);
            if (input) input.click();
        });
    });

    // Avatar preview
    const avatarInput = document.getElementById('groomerAvatar');
    if (avatarInput) {
        avatarInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(event) {
                    const preview = document.querySelector('#groomerAvatarPreview img');
                    if (preview) {
                        preview.src = event.target.result;
                        showToast('Avatar updated (not saved yet)');
                    }
                };
                reader.readAsDataURL(file);
            }
        });
    }

    // Cover photo preview
    const coverPhotoInput = document.getElementById('coverPhoto');
    if (coverPhotoInput) {
        coverPhotoInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(event) {
                    const preview = document.querySelector('#coverPhotoPreview img');
                    if (preview) {
                        preview.src = event.target.result;
                        showToast('Cover photo updated (not saved yet)');
                    }
                };
                reader.readAsDataURL(file);
            }
        });
    }

    // Profile form submission
    const profileForm = document.getElementById('formProfile');
    if (profileForm) {
        profileForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            
            // In real app, send to server
            console.log('Profile data:', Object.fromEntries(formData));
            
            showToast('Profile updated successfully');
            
            // Simulate server response
            setTimeout(() => {
                // Update UI or reload
            }, 1000);
        });
    }

    // Password form submission
    const passwordForm = document.getElementById('formPassword');
    if (passwordForm) {
        passwordForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const newPassword = this.querySelector('input[name="new_password"]').value;
            const confirmPassword = this.querySelector('input[name="confirm_password"]').value;
            
            if (newPassword !== confirmPassword) {
                showToast('Passwords do not match!');
                return;
            }
            
            const formData = new FormData(this);
            
            // In real app, send to server
            console.log('Password change requested');
            
            showToast('Password updated successfully');
            this.reset();
        });
    }

    // Preferences form submission
    const prefsForm = document.getElementById('formPrefs');
    if (prefsForm) {
        prefsForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            
            // In real app, send to server
            console.log('Preferences:', Object.fromEntries(formData));
            
            showToast('Preferences saved successfully');
        });
    }

    // Role form submission - NO CONFIRMATION for groomer
    const roleForm = document.getElementById('formRole');
    const roleMap = {
        'pet_owner': '/PETVET/index.php?module=pet-owner&page=my-pets',
        'trainer': '/PETVET/index.php?module=trainer&page=dashboard',
        'sitter': '/PETVET/index.php?module=sitter&page=dashboard',
        'breeder': '/PETVET/index.php?module=breeder&page=dashboard',
        'groomer': '/PETVET/index.php?module=groomer&page=services',
        'vet': '/PETVET/index.php?module=vet&page=dashboard',
        'clinic_manager': '/PETVET/index.php?module=clinic-manager&page=overview',
        'receptionist': '/PETVET/index.php?module=receptionist&page=dashboard',
        'admin': '/PETVET/index.php?module=admin&page=dashboard'
    };
    
    if (roleForm) {
        roleForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const selectedRole = this.querySelector('input[name="active_role"]:checked');
            if (selectedRole) {
                const roleName = selectedRole.nextElementSibling.querySelector('.role-name').textContent;
                const roleValue = selectedRole.value;
                const redirectUrl = roleMap[roleValue];
                
                if (redirectUrl) {
                    try {
                        console.log('Switching to role:', roleValue);
                        showToast(`Switching to ${roleName}...`);
                        
                        // Call API to switch role in session
                        const response = await fetch('/PETVET/api/switch-role.php', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json' },
                            body: JSON.stringify({ role: roleValue })
                        });
                        
                        const result = await response.json();
                        
                        if (result.success) {
                            // Redirect to the new role's dashboard
                            window.location.href = redirectUrl;
                        } else {
                            showToast(result.message || 'Failed to switch role', 'error');
                        }
                    } catch (error) {
                        showToast('Error switching role', 'error');
                        console.error('Role switch error:', error);
                    }
                }
            }
        });
    }

    // Role option click (make entire card clickable)
    document.querySelectorAll('.role-option').forEach(option => {
        option.addEventListener('click', function() {
            const radio = this.querySelector('input[type="radio"]');
            if (radio) {
                radio.checked = true;
                
                // Update active state
                document.querySelectorAll('.role-option').forEach(opt => {
                    opt.classList.remove('active');
                });
                this.classList.add('active');
            }
        });
    });

    // Toast notification
    function showToast(message) {
        toast.textContent = message;
        toast.classList.add('show');
        setTimeout(() => {
            toast.classList.remove('show');
        }, 3000);
    }

    // Toggle animations
    document.querySelectorAll('label.toggle input[type="checkbox"]').forEach(toggle => {
        toggle.addEventListener('change', function() {
            const track = this.nextElementSibling;
            track.style.transform = 'scale(0.95)';
            setTimeout(() => {
                track.style.transform = 'scale(1)';
            }, 150);
        });
    });
});
