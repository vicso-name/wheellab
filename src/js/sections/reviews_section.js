/**
 * Reviews Section — Swiper-powered testimonial carousel.
 * centeredSlides + slidesPerView:'auto' reproduce the Figma design's peeking
 * side slides for free (Swiper clips them at the track edges); the
 * dim/blur/scale-down look for inactive slides is pure CSS driven by the
 * .swiper-slide-active class Swiper toggles on the current slide.
 */

document.addEventListener("DOMContentLoaded", () => {
  document.querySelectorAll(".reviews-section__swiper").forEach(initReviewsSwiper);
});

function initReviewsSwiper(el) {
  if (typeof Swiper === "undefined") return;

  const section = el.closest(".reviews-section");
  const slideCount = el.querySelectorAll(".swiper-slide").length;
  const loop = slideCount > 1;

  new Swiper(el, {
    slidesPerView: "auto",
    centeredSlides: true,
    spaceBetween: 60,
    loop,
    speed: 500,
    grabCursor: true,
    slideToClickedSlide: true,
    keyboard: { enabled: true },
    navigation: section
      ? {
          nextEl: section.querySelector(".reviews-section__nav--next"),
          prevEl: section.querySelector(".reviews-section__nav--prev"),
        }
      : undefined,
  });
}
