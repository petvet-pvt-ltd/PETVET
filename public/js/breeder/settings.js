// Pet Owner Settings JS
(function(){
  const $ = s=>document.querySelector(s);
  const $$ = s=>Array.from(document.querySelectorAll(s));
  const toastEl = $('#toast');
  function showToast(msg){
    if(!toastEl) return; toastEl.textContent = msg; toastEl.classList.add('show');
    clearTimeout(showToast._t); showToast._t = setTimeout(()=>toastEl.classList.remove('show'),2600);
  }

  // Scroll spy via IntersectionObserver for reliable highlighting
  const navLinks = $$('.quick-nav a');
  const sectionMap = new Map();
  navLinks.forEach(a=>{
    const id = a.getAttribute('href').replace('#','');
    const sec = document.getElementById(id);
    if(sec) sectionMap.set(sec, a);
  });
  const observer = new IntersectionObserver(entries=>{
    entries.forEach(entry=>{
      if(entry.isIntersecting){
        const link = sectionMap.get(entry.target);
        if(link){ navLinks.forEach(l=>l.classList.remove('active')); link.classList.add('active'); }
      }
    });
  }, {rootMargin:'-40% 0px -50% 0px', threshold:[0, .25, .5, 1]});
  sectionMap.forEach((_, sec)=>observer.observe(sec));
  // Smooth scroll assist: active state will be handled by observer
  // Smooth scroll for nav
  navLinks.forEach(a=>a.addEventListener('click', e=>{
    const id = a.getAttribute('href');
    if(id && id.startsWith('#')){
      e.preventDefault();
      const target = document.querySelector(id);
      if(target){ target.scrollIntoView({behavior:'smooth', block:'start'}); }
    }
  }));

  // Reveal animation for cards
  const cards = Array.from(document.querySelectorAll('.settings-grid > section.card'));
  if('IntersectionObserver' in window){
    const ro = new IntersectionObserver(entries=>{
      entries.forEach(en=>{
        if(en.isIntersecting){ en.target.classList.add('reveal-in'); ro.unobserve(en.target); }
      });
    }, {threshold:.2});
    cards.forEach(c=>{c.classList.add('reveal-ready'); ro.observe(c);});
  }

  // Avatar preview
  const avatarInput = $('#breederAvatar');
  const avatarPreview = $('#breederAvatarPreview .image-preview-item img');
  document.addEventListener('click', e=>{
    const btn = e.target.closest('[data-for="breederAvatar"]');
    if(btn && avatarInput){ avatarInput.click(); }
  });
  if(avatarInput){
    avatarInput.addEventListener('change', e=>{
      const file = avatarInput.files && avatarInput.files[0];
      if(!file) return;
      if(!file.type.startsWith('image/')){ showToast('Please select an image file'); return; }
      const url = URL.createObjectURL(file);
      if(avatarPreview){ avatarPreview.src = url; }
    });
  }

  // Cover photo preview
  const coverPhotoInput = document.getElementById('coverPhoto');
  document.addEventListener('click', e=>{
    const btn = e.target.closest('[data-for="coverPhoto"]');
    if(btn && coverPhotoInput){ coverPhotoInput.click(); }
  });
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

  // Generic form save simulation
  function handleFakeSubmit(form, label){
    if(!form) return;
    form.addEventListener('submit', e=>{
      e.preventDefault();
      // Basic validation example for password confirm
      if(form.id === 'formPassword'){
        const np = form.querySelector('input[name="new_password"]').value.trim();
        const cp = form.querySelector('input[name="confirm_password"]').value.trim();
        if(np.length < 6){ showToast('Password too short (min 6)'); return; }
        if(np !== cp){ showToast('Passwords do not match'); return; }
      }
      showToast(label + ' saved');
      form.dataset.clean = 'true';
    });
  }
  handleFakeSubmit($('#formProfile'),'Profile');
  handleFakeSubmit($('#formPassword'),'Password');
  handleFakeSubmit($('#formPrefs'),'Preferences');

  // Set current reminder value if select has value attribute (server injected)
  const reminderSelect = document.querySelector('select[name="reminder_appointments"]');
  if(reminderSelect){
    const val = reminderSelect.getAttribute('value');
    if(val && !reminderSelect.value) reminderSelect.value = val;
  }

  // Dirty form tracking
  ['#formProfile','#formPassword','#formPrefs'].forEach(id=>{
    const f = $(id);
    if(!f) return; f.dataset.clean='true';
    f.addEventListener('input', ()=>{ f.dataset.clean='false'; });
  });
  window.addEventListener('beforeunload', e=>{
    const dirty = ['#formProfile','#formPassword','#formPrefs'].some(id=>{
      const f=$(id); return f && f.dataset.clean==='false';
    });
    if(dirty){ e.preventDefault(); e.returnValue=''; }
  });

  // Role switching functionality
  const roleOptions = $$('.role-option');
  const roleForm = $('#formRole');
  const roleMap = {
    'pet-owner': '/PETVET/index.php?module=pet-owner&page=my-pets',
    'trainer': '/PETVET/index.php?module=trainer&page=dashboard',
    'sitter': '/PETVET/index.php?module=sitter&page=dashboard',
    'breeder': '/PETVET/index.php?module=breeder&page=dashboard',
    'groomer': '/PETVET/index.php?module=groomer&page=services'
  };
  
  roleOptions.forEach(opt=>{
    const radio = opt.querySelector('input[type=radio]');
    opt.addEventListener('click', ()=>{
      if(radio && !radio.checked){
        radio.checked = true;
        roleOptions.forEach(o=>o.classList.remove('active'));
        opt.classList.add('active');
      }
    });
  });
  
  if(roleForm){
    roleForm.addEventListener('submit', e=>{
      e.preventDefault();
      const selected = roleForm.querySelector('input[name="active_role"]:checked');
      if(selected){
        const roleValue = selected.value;
        const redirectUrl = roleMap[roleValue];
        if(redirectUrl){
          showToast('Switching to ' + roleValue.replace('-', ' ') + '...');
          setTimeout(()=>{ window.location.href = redirectUrl; }, 800);
        }
      }
    });
  }
})();
