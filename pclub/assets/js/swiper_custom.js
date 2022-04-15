var swiper = new Swiper(".mySwiper", {
    slidesPerView: 4.3,
    spaceBetween: 10,
    loop: true,
    freeMode: true,
    pagination: {
      el: ".swiper-pagination",
      clickable: true,
    },
    breakpoints: {
      320: {
        slidesPerView: 1.2,
        loop: false,
        spaceBetween: 5,
      },
      640: {
        slidesPerView: 3.2
      },
      768: {
        slidesPerView: 3.6
      },
      1024: {
        slidesPerView: 4.3
      },
    },
});
