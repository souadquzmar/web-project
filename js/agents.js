document.addEventListener('DOMContentLoaded', () => {
  if (typeof Swiper !== 'undefined') {
    const thumbs = new Swiper('.swiper-thumbs', {
      spaceBetween: 8, slidesPerView: 4, freeMode: true, watchSlidesProgress: true,
    });
    new Swiper('.swiper-gallery', {
      spaceBetween: 10,
      navigation: { nextEl: '.swiper-gallery-next', prevEl: '.swiper-gallery-prev' },
      thumbs: { swiper: thumbs },
    });
  }
  document.querySelectorAll('.counter-wrap').forEach(wrap => {
    const input = wrap.querySelector('input[type="number"]');
    wrap.querySelector('.counter-dec')?.addEventListener('click', () => {
      input.value = Math.max(0, (parseInt(input.value) || 0) - 1);
    });
    wrap.querySelector('.counter-inc')?.addEventListener('click', () => {
      input.value = (parseInt(input.value) || 0) + 1;
    });
  });
});
