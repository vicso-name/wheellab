/**
 * Service Feature Cards — Swiper carousel of capability cards.
 *
 * Explicit instruction: below 576px there is no slider at all — cards
 * stack in a plain column instead (service_feature_cards.scss's own
 * $small rules give .swiper-wrapper/.slide that stacked look). Swiper is
 * only ever constructed above that width, and destroyed (restoring plain
 * DOM/classes via Swiper's own destroy(true, true)) if the viewport
 * shrinks back below it — same instance-per-section pattern as
 * case_study_section.js, gated by matchMedia instead of always-on.
 */

document.addEventListener("DOMContentLoaded", () => {
  document.querySelectorAll(".service-feature-cards__swiper").forEach(initServiceFeatureCardsSwiper);
});

const MOBILE_QUERY = "(max-width: 576px)";

function initServiceFeatureCardsSwiper(el) {
  if (typeof Swiper === "undefined") return;

  const section = el.closest(".service-feature-cards");
  const mql = window.matchMedia(MOBILE_QUERY);
  let instance = null;

  function create() {
    if (instance) return;
    instance = new Swiper(el, {
      slidesPerView: "auto",
      spaceBetween: 24,
      speed: 500,
      grabCursor: true,
      keyboard: { enabled: true },
      navigation: section
        ? {
            nextEl: section.querySelector(".service-feature-cards__nav--next"),
            prevEl: section.querySelector(".service-feature-cards__nav--prev"),
          }
        : undefined,
    });
  }

  function destroy() {
    if (!instance) return;
    instance.destroy(true, true);
    instance = null;
  }

  function sync(e) {
    if (e.matches) {
      destroy();
    } else {
      create();
    }
  }

  sync(mql);
  mql.addEventListener("change", sync);
}
