/**
 * Case Study Section — Swiper carousel of client case cards.
 * Same setup as Solutions Section: bounded (loop off). spaceBetween drops
 * to 12px under the 576px mobile breakpoint (matches
 * .case-study-section__swiper's own $small styling) instead of desktop's
 * 24px gap.
 */

document.addEventListener("DOMContentLoaded", () => {
  document.querySelectorAll(".case-study-section__swiper").forEach(initCaseStudySwiper);
});

function initCaseStudySwiper(el) {
  if (typeof Swiper === "undefined") return;

  const section = el.closest(".case-study-section");

  new Swiper(el, {
    slidesPerView: "auto",
    spaceBetween: 24,
    speed: 500,
    grabCursor: true,
    keyboard: { enabled: true },
    navigation: section
      ? {
          nextEl: section.querySelector(".case-study-section__nav--next"),
          prevEl: section.querySelector(".case-study-section__nav--prev"),
        }
      : undefined,
    breakpoints: {
      0: { spaceBetween: 12 },
      577: { spaceBetween: 24 },
    },
  });
}
