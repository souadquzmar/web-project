document.addEventListener('DOMContentLoaded', () => {
  const form = document.querySelector('.contact-form-card form');
  if (!form) return;
  form.addEventListener('submit', e => {
    e.preventDefault();
    const btn = form.querySelector('[type="submit"]');
    if (!btn) return;
    const orig = btn.textContent;
    btn.textContent = '✓ Message Sent!';
    btn.style.background = '#18a65c';
    setTimeout(() => { btn.textContent = orig; btn.style.background = ''; }, 3000);
  });
});
