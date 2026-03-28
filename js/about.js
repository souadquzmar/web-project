document.addEventListener('DOMContentLoaded', () => {

  if (document.querySelector('.swiper-partners') && typeof Swiper !== 'undefined') {
    new Swiper('.swiper-partners', {
      loop: true,
      slidesPerView: 'auto',
      spaceBetween: 60,
      speed: 4000,
      autoplay: {
        delay: 0,
        disableOnInteraction: false,
      },
      allowTouchMove: false,
      freeMode: {
        enabled: true,
        momentum: false,
      },
    });
  }

});
