document.addEventListener('DOMContentLoaded', () => {

  if (document.querySelector('.swiper-testimonials') && typeof Swiper !== 'undefined') {
    new Swiper('.swiper-testimonials', {
      loop: true,
      speed: 600,
      slidesPerView: 1,
      spaceBetween: 24,
      breakpoints: {
        640:  { slidesPerView: 2, spaceBetween: 20 },
        1024: { slidesPerView: 3, spaceBetween: 24 },
      },
      navigation: {
        nextEl: '.testimonials-next',
        prevEl: '.testimonials-prev',
      },
      autoplay: {
        delay: 4000,
        disableOnInteraction: false,
        pauseOnMouseEnter: true,
      },
    });
  }

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
