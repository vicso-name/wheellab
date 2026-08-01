/**
 * Featured Posts Section — Swiper carousel of blog cards.
 * No centeredSlides (unlike reviews_section.js): cards start flush with
 * .container's left edge via the swiper's own padding, not centered.
 * No slideToClickedSlide either — each card is itself a link to the post,
 * and that click should just navigate, not be intercepted to "slide to it".
 */

document.addEventListener("DOMContentLoaded", () => {
  document.querySelectorAll(".featured-posts-section__swiper").forEach(initFeaturedPostsSwiper);
});

function initFeaturedPostsSwiper(el) {
  if (typeof Swiper === "undefined") return;

  const section = el.closest(".featured-posts-section");
  const slideCount = el.querySelectorAll(".swiper-slide").length;

  new Swiper(el, {
    slidesPerView: "auto",
    spaceBetween: 24,
    // Looping with too few slides relative to the visible count makes
    // Swiper duplicate/jump oddly — only loop once there's a comfortable
    // surplus of slides to draw from.
    loop: slideCount > 4,
    speed: 500,
    grabCursor: true,
    keyboard: { enabled: true },
    navigation: section
      ? {
          nextEl: section.querySelector(".featured-posts-section__nav--next"),
          prevEl: section.querySelector(".featured-posts-section__nav--prev"),
        }
      : undefined,
  });
}
