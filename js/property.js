document.addEventListener('DOMContentLoaded', () => {

  window.switchThumb = function(el, src) {
    document.getElementById('main-gallery-img').src = src;
    document.querySelectorAll('.gallery-thumb').forEach(t => t.classList.remove('active'));
    el.classList.add('active');
  };
  const firstThumb = document.querySelector('.gallery-thumb');
  if (firstThumb) firstThumb.classList.add('active');

  const stars = document.querySelectorAll('#starRating i');
  let currentRating = 0;

  stars.forEach((star, idx) => {
    star.addEventListener('mouseenter', () => {
      stars.forEach((s, i) => {
        s.className = i <= idx ? 'fa-solid fa-star' : 'fa-regular fa-star';
        s.style.color = i <= idx ? '#f59e0b' : '#ddd';
      });
    });

    star.addEventListener('mouseleave', () => {
      stars.forEach((s, i) => {
        const filled = i < currentRating;
        s.className = filled ? 'fa-solid fa-star active' : 'fa-regular fa-star';
        s.style.color = filled ? '#f59e0b' : '#ddd';
      });
    });

    star.addEventListener('click', () => {
      currentRating = idx + 1;
      stars.forEach((s, i) => {
        const filled = i < currentRating;
        s.className = filled ? 'fa-solid fa-star active' : 'fa-regular fa-star';
        s.style.color = filled ? '#f59e0b' : '#ddd';
      });
    });
  });

  document.querySelectorAll('.counter-wrap').forEach(wrap => {
    const input = wrap.querySelector('input[type="number"]');
    wrap.querySelector('.counter-dec')?.addEventListener('click', () => {
      input.value = Math.max(0, (parseInt(input.value) || 0) - 1);
    });
    wrap.querySelector('.counter-inc')?.addEventListener('click', () => {
      input.value = (parseInt(input.value) || 0) + 1;
    });
  });

  document.querySelectorAll('.prop-fav').forEach(btn => {
    btn.addEventListener('click', () => {
      btn.classList.toggle('active');
      const icon = btn.querySelector('i');
      if (icon) {
        icon.className = btn.classList.contains('active') ? 'fa-solid fa-heart' : 'fa-regular fa-heart';
      }
    });
  });

});
