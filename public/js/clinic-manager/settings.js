// UI helpers
const $ = (s, el=document) => el.querySelector(s);
const $$ = (s, el=document) => Array.from(el.querySelectorAll(s));
const toast = (msg) => { const t = $('#toast'); t.textContent = msg; t.classList.add('show'); setTimeout(()=>t.classList.remove('show'), 2000); };

// Image previewers
function bindImagePreview(inputId, previewContainerId) {
  const input = document.getElementById(inputId);
  const preview = document.getElementById(previewContainerId);
  if (!input || !preview) return;
  input.addEventListener('change', (e) => {
    const files = Array.from(e.target.files || []);
    if (!files.length) return;
    preview.innerHTML = '';
    files.slice(0, 1).forEach(file => {
      const url = URL.createObjectURL(file);
      const item = document.createElement('div');
      item.className = 'image-preview-item' + (preview.classList.contains('cover') ? ' hero' : '');
      const img = document.createElement('img');
      img.src = url;
      item.appendChild(img);
      preview.appendChild(item);
    });
  });
}

function initHoursToggles() {
  $$('.hours-row').forEach(row => {
    const toggle = $('.open-toggle', row);
    const start = $('.time-start', row);
    const end = $('.time-end', row);
    toggle.addEventListener('change', () => {
      const on = toggle.checked;
      start.disabled = !on; end.disabled = !on;
    });
  });
}

function initHolidays() {
  const list = $('#holidayList');
  const addBtn = $('#btnAddHoliday');
  const newInput = $('#newHoliday');
  if (!list || !addBtn || !newInput) return;

  addBtn.addEventListener('click', () => {
    if (!newInput.value) { toast('Pick a date'); return; }
  const div = document.createElement('div');
  div.className = 'holiday-item';
  div.innerHTML = `<input type="date" value="${newInput.value}" />\
<button type="button" class="icon-btn remove" aria-label="Remove holiday" title="Remove">×</button>`;
    list.appendChild(div);
    newInput.value = '';
  });
  list.addEventListener('click', (e) => {
    if (e.target.classList.contains('remove')) {
      e.target.closest('.holiday-item').remove();
    }
  });
}

function wireForms() {
  $('#formManager')?.addEventListener('submit', (e) => { e.preventDefault(); toast('Manager profile saved (UI only)'); });
  $('#formClinic')?.addEventListener('submit', (e) => { e.preventDefault(); toast('Clinic profile saved (UI only)'); });
  $('#formHours')?.addEventListener('submit', (e) => { e.preventDefault(); toast('Hours & policies saved (UI only)'); });
}

// Init
bindImagePreview('mgrAvatar', 'mgrAvatarPreview');
bindImagePreview('clinicLogo', 'clinicLogoPreview');
bindImagePreview('clinicCover', 'clinicCoverPreview');
initHoursToggles();
initHolidays();
wireForms();

// Hook custom uploader buttons to hidden inputs
document.addEventListener('click', (e) => {
  const btn = e.target.closest('button[data-for]');
  if (!btn) return;
  const id = btn.getAttribute('data-for');
  const input = document.getElementById(id);
  if (input) input.click();
});

// Active quick-nav highlighting (IntersectionObserver)
const links = Array.from(document.querySelectorAll('.quick-nav a'));
const sectionEls = links.map(a => document.querySelector(a.getAttribute('href'))).filter(Boolean);

if ('IntersectionObserver' in window) {
  const io = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        const id = entry.target.id;
        links.forEach(l => l.classList.toggle('active', l.getAttribute('href') === `#${id}`));
      }
    });
  }, {rootMargin: '-45% 0px -45% 0px', threshold:[0,1]});
  sectionEls.forEach(sec => io.observe(sec));
} else {
  // Fallback: mark first section
  if (sectionEls[0]) links[0].classList.add('active');
}

// Reveal animations for cards
const cmCards = Array.from(document.querySelectorAll('.settings-grid > section.card'));
if('IntersectionObserver' in window){
  const revealObs = new IntersectionObserver(es=>{
    es.forEach(en=>{ if(en.isIntersecting){ en.target.classList.add('reveal-in'); revealObs.unobserve(en.target);} });
  },{threshold:.2});
  cmCards.forEach(c=>{c.classList.add('reveal-ready'); revealObs.observe(c);});
}
