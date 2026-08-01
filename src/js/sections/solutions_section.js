/**
 * Solutions Section — Swiper carousel of large solution cards.
 * loop is off on purpose: the Figma design shows the prev arrow starting
 * in its muted/disabled color, i.e. a bounded (not looping) carousel —
 * Swiper's own .swiper-button-disabled state matches that directly.
 * No slideToClickedSlide either — each card is itself a link.
 */

document.addEventListener("DOMContentLoaded", () => {
  document.querySelectorAll(".solutions-section__swiper").forEach(initSolutionsSwiper);
});

function initSolutionsSwiper(el) {
  if (typeof Swiper === "undefined") return;

  const section = el.closest(".solutions-section");

  new Swiper(el, {
    slidesPerView: "auto",
    spaceBetween: 24,
    speed: 500,
    grabCursor: true,
    keyboard: { enabled: true },
    navigation: section
      ? {
          nextEl: section.querySelector(".solutions-section__nav--next"),
          prevEl: section.querySelector(".solutions-section__nav--prev"),
        }
      : undefined,
  });
}
