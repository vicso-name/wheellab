/**
 * Case Study Tabs Section — click a tab, crossfade the image on the
 * right. Every tab's image already sits in the DOM (position: absolute,
 * stacked — case_study_tabs_section.scss); this only toggles which one
 * is .is-active, so the swap is a plain CSS opacity transition with no
 * src-swap reload flash.
 */

document.addEventListener("DOMContentLoaded", () => {
  document.querySelectorAll(".case-study-tabs-section").forEach((section) => {
    const tabs = section.querySelectorAll(".case-study-tabs-section__tab");
    const images = section.querySelectorAll(".case-study-tabs-section__image");
    if (!tabs.length || !images.length) return;

    tabs.forEach((tab) => {
      tab.addEventListener("click", () => {
        if (tab.classList.contains("is-active")) return;

        const index = tab.dataset.index;

        tabs.forEach((t) => {
          t.classList.remove("is-active");
          t.setAttribute("aria-selected", "false");
        });
        tab.classList.add("is-active");
        tab.setAttribute("aria-selected", "true");

        images.forEach((img) => {
          img.classList.toggle("is-active", img.dataset.index === index);
        });
      });
    });
  });
});
