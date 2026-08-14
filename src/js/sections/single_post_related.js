/**
 * Single Post — Read More section. Swiper carousel of blog cards, same
 * setup as featured_posts_section.js (own copy, not shared — see that
 * file's header comment for why).
 */

document.addEventListener("DOMContentLoaded", () => {
  document.querySelectorAll(".single-post-related__swiper").forEach(initSinglePostRelatedSwiper);
});

function initSinglePostRelatedSwiper(el) {
  if (typeof Swiper === "undefined") return;

  const section = el.closest(".single-post-related");
  const slideCount = el.querySelectorAll(".swiper-slide").length;

  new Swiper(el, {
    slidesPerView: "auto",
    spaceBetween: 24,
    loop: slideCount > 4,
    speed: 500,
    grabCursor: true,
    keyboard: { enabled: true },
    navigation: section
      ? {
          nextEl: section.querySelector(".single-post-related__nav--next"),
          prevEl: section.querySelector(".single-post-related__nav--prev"),
        }
      : undefined,
  });
}
