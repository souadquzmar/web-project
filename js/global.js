
function initNavbar() {
  const nav = document.getElementById('navbar');
  if (!nav) return;
  if (!nav.classList.contains('navbar-hero')) return;
  let ticking = false;
  const brand = nav.querySelector('.site-brand');
  const onScroll = () => {
    if (ticking) return;
    ticking = true;
    requestAnimationFrame(() => {
      const scrolled = window.scrollY > 80;
      nav.classList.toggle('scrolled', scrolled);
      if (brand) brand.style.color = scrolled ? 'var(--dark)' : '#fff';
      nav.querySelectorAll('.nav-link:not(.active)').forEach(l => {
        l.style.color = scrolled ? 'var(--text)' : 'rgba(255,255,255,0.92)';
      });
      ticking = false;
    });
  };
  window.addEventListener('scroll', onScroll, { passive: true });
  onScroll();
}

function initBackTop() {
  const btn = document.getElementById('backTop');
  if (!btn) return;
  window.addEventListener('scroll', () => {
    btn.classList.toggle('visible', window.scrollY > 300);
  }, { passive: true });
  btn.addEventListener('click', () => window.scrollTo({ top: 0, behavior: 'smooth' }));
}

function initReveal() {
  const obs = new IntersectionObserver((entries) => {
    entries.forEach(e => {
      if (e.isIntersecting) { e.target.classList.add('visible'); obs.unobserve(e.target); }
    });
  }, { threshold: 0.1, rootMargin: '0px 0px -40px 0px' });
  document.querySelectorAll('.reveal').forEach(el => obs.observe(el));
}

function initMobileNav() {
  const toggle = document.querySelector('.navbar-toggle');
  const drawer = document.getElementById('mobileDrawer');
  const overlay = document.getElementById('mobileOverlay');
  const closeBtn = document.getElementById('mobileClose');
  if (!toggle || !drawer) return null;

  const open = () => { drawer.classList.add('open'); overlay?.classList.add('open'); toggle.classList.add('is-open'); document.body.style.overflow = 'hidden'; };
  const close = () => { drawer.classList.remove('open'); overlay?.classList.remove('open'); toggle.classList.remove('is-open'); document.body.style.overflow = ''; };

  toggle.addEventListener('click', open);
  closeBtn?.addEventListener('click', close);
  overlay?.addEventListener('click', close);

  const current = location.pathname.split('/').pop() || 'index.html';
  drawer.querySelectorAll('.mobile-nav-link').forEach(link => {
    if ((link.getAttribute('href') || '').includes(current)) link.classList.add('active');
  });

  return close;
}

function initModals(closeDrawer) {
  document.querySelectorAll('[data-modal]').forEach(trigger => {
    trigger.addEventListener('click', e => {
      e.preventDefault();
      const m = document.getElementById(trigger.dataset.modal);
      if (m) m.classList.add('open');
    });
  });

  document.querySelectorAll('.modal-overlay').forEach(overlay => {
    overlay.addEventListener('click', e => {
      if (e.target === overlay) overlay.classList.remove('open');
    });
    overlay.querySelectorAll('[data-close]').forEach(btn => {
      btn.addEventListener('click', () => overlay.classList.remove('open'));
    });
  });

  document.addEventListener('keydown', e => {
    if (e.key !== 'Escape') return;
    document.querySelectorAll('.modal-overlay.open').forEach(m => m.classList.remove('open'));
    if (closeDrawer) closeDrawer();
  });

  document.querySelectorAll('.modal-tab').forEach(tab => {
    tab.addEventListener('click', () => {
      const box = tab.closest('.modal-body, .modal-box');
      box.querySelectorAll('.modal-tab').forEach(t => t.classList.remove('active'));
      box.querySelectorAll('.modal-pane').forEach(p => p.classList.remove('active'));
      tab.classList.add('active');
      const pane = document.getElementById(tab.dataset.target);
      if (pane) pane.classList.add('active');
    });
  });
}

function initSearchTabs() {
  document.querySelectorAll('.tab-pill').forEach(pill => {
    pill.addEventListener('click', () => {
      const tabs = pill.closest('.search-tabs')?.querySelectorAll('.tab-pill');
      if (!tabs) return;
      tabs.forEach(p => { p.classList.remove('active'); p.style.background = 'rgba(255,255,255,0.92)'; p.style.color = '#1a1c23'; });
      pill.classList.add('active'); pill.style.background = '#ff385c'; pill.style.color = '#fff';
    });
  });
}


function initRanges() {
  document.querySelectorAll('input[type="range"][data-out]').forEach(input => {
    const out = document.getElementById(input.dataset.out);
    const prefix = input.dataset.prefix || '';
    const suffix = input.dataset.suffix || '';
    const fmt = v => prefix + Number(v).toLocaleString() + suffix;
    const update = () => { if (out) out.textContent = fmt(input.value); };
    input.addEventListener('input', update);
    update();
  });
}

function initFavs() {
  document.querySelectorAll('.prop-fav').forEach(btn => {
    btn.addEventListener('click', e => {
      e.stopPropagation();
      /* If the button is inside a real form, let the form submit */
      const form = btn.closest('form');
      if (form && form.getAttribute('action')) {
        /* The form will submit naturally — just let it go */
        return;
      }
      /* No form — just toggle the icon (static demo mode or not-logged-in) */
      btn.classList.toggle('active');
      const icon = btn.querySelector('i');
      if (icon) {
        icon.classList.toggle('fa-regular', !btn.classList.contains('active'));
        icon.classList.toggle('fa-solid', btn.classList.contains('active'));
      }
    });
  });
}

function initNewsletter() {
  document.querySelectorAll('.footer-email-row').forEach(row => {
    const btn = row.querySelector('.footer-subscribe');
    const input = row.querySelector('.footer-email-input');
    if (!btn || !input) return;
    /* The form already has action= for PHP — just add visual feedback */
    const form = row.closest('form') || row;
    if (form.tagName === 'FORM' && form.getAttribute('action')) return; /* Let the HTML form handle it */
    btn.addEventListener('click', () => {
      if (!input.value.includes('@')) return;
      const orig = btn.textContent;
      btn.textContent = '✓ Done!';
      btn.style.background = '#18a65c';
      input.value = '';
      setTimeout(() => { btn.textContent = orig; btn.style.background = ''; }, 3000);
    });
  });
}

function initCounters() {
  const section = document.querySelector('.section-stats, .stats-section');
  if (!section) return;
  const obs = new IntersectionObserver(entries => {
    entries.forEach(e => {
      if (!e.isIntersecting) return;
      e.target.querySelectorAll('.stat-number[data-target]').forEach(el => {
        const target = +el.dataset.target;
        let count = 0;
        const step = target / 60;
        const timer = setInterval(() => {
          count = Math.min(count + step, target);
          el.textContent = Math.floor(count).toLocaleString();
          if (count >= target) { el.textContent = target.toLocaleString(); clearInterval(timer); }
        }, 25);
      });
      obs.unobserve(e.target);
    });
  }, { threshold: 0.4 });
  obs.observe(section);
}

function initTyped() {
  const el = document.getElementById('typed-word');
  if (!el) return;

  const words = ['House', 'Apartment', 'Villa', 'Condo', 'Plaza'];
  let wi = 0, ci = 0, deleting = false;
  function lockWidth() {
    const h1 = el.closest('h1') || el.parentElement;
    const prevPos = h1.style.position;
    h1.style.position = 'relative';

    const probe = document.createElement('span');
    probe.setAttribute('aria-hidden', 'true');
    probe.style.cssText =
      'position:absolute;visibility:hidden;pointer-events:none;' +
      'white-space:nowrap;min-width:0;width:auto;' +
      'font-size:inherit;font-weight:inherit;' +
      'letter-spacing:inherit;font-family:inherit;';
    h1.appendChild(probe);

    let max = 0;
    words.forEach(w => {
      probe.textContent = w;
      max = Math.max(max, probe.offsetWidth);
    });

    h1.removeChild(probe);
    h1.style.position = prevPos;

    el.style.minWidth = max + 'px';
  }
  (document.fonts ? document.fonts.ready : Promise.resolve()).then(lockWidth);


  function tick() {
    const word = words[wi];

    if (!deleting) {
      ci++;
      el.textContent = word.slice(0, ci);

      if (ci < word.length) {
        setTimeout(tick, 75 + Math.random() * 55);
      } else {
        setTimeout(() => {
          deleting = true;
          setTimeout(tick, 40 + Math.random() * 20);
        }, 1700);
      }
    } else {
      ci--;
      el.textContent = word.slice(0, ci);

      if (ci > 0) {
        setTimeout(tick, 40 + Math.random() * 20);
      } else {
        deleting = false;
        wi = (wi + 1) % words.length;
        setTimeout(tick, 350);
      }
    }
  }
  el.textContent = '';
  setTimeout(tick, 800);
}


function initUpload() {
  const area = document.getElementById('uploadArea');
  const input = document.getElementById('fileInput');
  const preview = document.getElementById('uploadPreview');
  if (!area || !input) return;

  input.addEventListener('click', e => e.stopPropagation());
  input.addEventListener('change', () => showFiles(input.files));
  area.addEventListener('dragover', e => { e.preventDefault(); area.classList.add('drag-over'); });
  area.addEventListener('dragleave', () => area.classList.remove('drag-over'));
  area.addEventListener('drop', e => { e.preventDefault(); area.classList.remove('drag-over'); showFiles(e.dataTransfer.files); });

  function showFiles(files) {
    if (!preview) return;
    preview.innerHTML = '';
    Array.from(files).forEach(file => {
      const chip = document.createElement('div');
      chip.className = 'upload-chip';
      chip.innerHTML = `<i class="fa-solid fa-file-image"></i> ${file.name} <span class="upload-chip-size">${(file.size / 1024).toFixed(0)} KB</span>`;
      preview.appendChild(chip);
    });
    const label = area.querySelector('p');
    if (label && files.length) label.textContent = `${files.length} file${files.length > 1 ? 's' : ''} selected`;
  }
}

function initScrollHint() {
  const hint = document.querySelector('.scroll-hint');
  if (!hint) return;
  const hide = () => { if (window.scrollY > 80) hint.classList.add('hidden'); };
  window.addEventListener('scroll', hide, { passive: true });
  hide();
}

function validateField(input) {
  const val = input.value.trim();
  const type = input.type;
  let ok = true, msg = '';
  if (input.required && !val) { ok = false; msg = 'This field is required.'; }
  else if (type === 'email' && val && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(val)) { ok = false; msg = 'Enter a valid email address.'; }
  else if (type === 'tel' && val && !/^[\d\s\+\-\(\)]{7,}$/.test(val)) { ok = false; msg = 'Enter a valid phone number.'; }
  input.classList.toggle('error', !ok);
  input.classList.toggle('success', ok && !!val);
  let err = input.parentElement.querySelector('.field-error');
  if (!err) { err = document.createElement('div'); err.className = 'field-error'; input.parentElement.appendChild(err); }
  err.textContent = msg;
  err.classList.toggle('show', !ok);
  return ok;
}

function initFormValidation() {
  document.querySelectorAll('form').forEach(form => {
    form.querySelectorAll('.form-input, .form-textarea').forEach(input => {
      input.addEventListener('blur', () => validateField(input));
      input.addEventListener('input', () => { if (input.classList.contains('error')) validateField(input); });
    });
    form.addEventListener('submit', e => {
      /* If the form has a real action (PHP backend), validate then let it submit normally */
      if (form.getAttribute('action') && form.getAttribute('action') !== '#') {
        let valid = true;
        form.querySelectorAll('.form-input[required], .form-textarea[required]').forEach(input => { if (!validateField(input)) valid = false; });
        if (!valid) { e.preventDefault(); return; }
        /* valid → let the browser submit to PHP */
        return;
      }
      /* No real action → prevent default (static demo behaviour) */
      e.preventDefault();
      let valid = true;
      form.querySelectorAll('.form-input[required], .form-textarea[required]').forEach(input => { if (!validateField(input)) valid = false; });
      if (!valid) return;
      const successEl = form.querySelector('.form-success') || form.nextElementSibling;
      if (successEl?.classList.contains('form-success')) {
        successEl.classList.add('show');
        const btn = form.querySelector('[type=submit]');
        if (btn) {
          const orig = btn.innerHTML;
          btn.innerHTML = '<i class="fa-solid fa-check"></i> Sent!';
          btn.disabled = true;
          setTimeout(() => { btn.innerHTML = orig; btn.disabled = false; successEl.classList.remove('show'); }, 4000);
        }
      }
    });
  });
}

function initHeroTabs() {
  document.querySelectorAll('.tab-pill').forEach(tab => {
    tab.addEventListener('click', () => {
      const isRent = tab.textContent.trim() === 'For Rent';
      document.querySelectorAll('.adv-field select').forEach(sel => {
        if (sel.querySelector('option[value=""]') && sel.options.length <= 3)
          sel.value = isRent ? 'For Rent' : 'For Sale';
      });
    });
  });
}

function initEmptyStates() {
  document.querySelectorAll('[data-empty-check]').forEach(container => {
    const emptyEl = container.querySelector('.empty-state');
    if (emptyEl) emptyEl.style.display = container.querySelectorAll('[data-empty-item]').length === 0 ? 'block' : 'none';
  });
}

function initListingSteps() {
  const stepsEl = document.getElementById('listingSteps');
  const sections = document.querySelectorAll('.listing-section[data-step]');
  if (!stepsEl || !sections.length) return;
  sections.forEach((sec, i) => {
    new IntersectionObserver(entries => {
      if (!entries[0].isIntersecting) return;
      stepsEl.querySelectorAll('.form-step').forEach((s, si) => {
        s.classList.toggle('active', si === i);
        s.classList.toggle('done', si < i);
      });
    }, { threshold: 0.5, rootMargin: '-100px 0px' }).observe(sec);
  });
}

function initListingFilter() {
  document.querySelectorAll('.listing-tab').forEach(tab => {
    tab.addEventListener('click', () => {
      document.querySelectorAll('.listing-tab').forEach(t => t.classList.remove('active'));
      tab.classList.add('active');
      const filter = tab.dataset.filter;
      document.querySelectorAll('.prop-card-h').forEach(card => {
        if (!filter || filter === 'all') { card.style.display = ''; return; }
        const badgeText = card.querySelector('.prop-badge')?.textContent.toLowerCase() || '';
        card.style.display = badgeText.includes(filter) ? '' : 'none';
      });
    });
  });
}

function initKeyboardNav() {
  document.addEventListener('keydown', e => {
    if (e.key === 'ArrowLeft') document.querySelector('.swiper-button-prev')?.click();
    if (e.key === 'ArrowRight') document.querySelector('.swiper-button-next')?.click();
  });
}


document.addEventListener('DOMContentLoaded', () => {
  initNavbar();
  const closeDrawer = initMobileNav();
  initBackTop();
  initReveal();
  initModals(closeDrawer);
  initSearchTabs();
  initRanges();
  initFavs();
  initNewsletter();
  initCounters();
  initTyped();
  initUpload();
  initScrollHint();
  initFormValidation();
  initHeroTabs();
  initEmptyStates();
  initListingSteps();
  initListingFilter();
  initKeyboardNav();
  initForgotPassword();
  initDarkMode();
});

/* ── Forgot Password toggle ──────────────────────────────── */
function initForgotPassword() {
  const showBtn = document.getElementById('showForgotPassword');
  const backBtn = document.getElementById('backToLogin');
  const loginForm = document.querySelector('#tab-login > form');
  const forgotForm = document.getElementById('forgotPasswordForm');
  if (!showBtn || !forgotForm || !loginForm) return;

  showBtn.addEventListener('click', e => {
    e.preventDefault();
    loginForm.style.display = 'none';
    forgotForm.style.display = 'block';
  });
  backBtn?.addEventListener('click', e => {
    e.preventDefault();
    forgotForm.style.display = 'none';
    loginForm.style.display = 'block';
  });
}

/* ── Dark / Light Mode ───────────────────────────────────── */
function initDarkMode() {
  const saved = localStorage.getItem('fh-theme');
  if (saved === 'dark') document.documentElement.classList.add('dark-mode');

  const btn = document.getElementById('themeToggle');
  if (!btn) return;
  updateThemeIcon(btn);

  btn.addEventListener('click', () => {
    document.documentElement.classList.toggle('dark-mode');
    const isDark = document.documentElement.classList.contains('dark-mode');
    localStorage.setItem('fh-theme', isDark ? 'dark' : 'light');
    updateThemeIcon(btn);
  });
}

function updateThemeIcon(btn) {
  const isDark = document.documentElement.classList.contains('dark-mode');
  btn.innerHTML = isDark
    ? '<i class="fa-solid fa-sun"></i>'
    : '<i class="fa-solid fa-moon"></i>';
  btn.title = isDark ? 'Switch to Light Mode' : 'Switch to Dark Mode';
}
