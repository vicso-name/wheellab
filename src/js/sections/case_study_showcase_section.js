/**
 * Case Study Showcase Section — scroll-into-view stagger reveal.
 * Each image card starts hidden (opacity/translateY set in
 * case_study_showcase_section.scss) and gets .is-visible added once
 * ~15% of it has scrolled into view. Reveals once per card, then stops
 * observing it — same IntersectionObserver pattern as Domains Section's
 * own card reveal (domains_section.js).
 *
 * Delay is index % 2 rather than domains_section's plain index — these
 * cards are full page-width and stacked one per row (except the one
 * Half Width pair), so they scroll into view independently one at a
 * time rather than all at once like a compact grid; the only place a
 * stagger actually reads as intentional is the side-by-side pair, which
 * this still offsets by 100ms.
 */

document.addEventListener("DOMContentLoaded", () => {
  const items = document.querySelectorAll(".case-study-showcase-section__item");
  if (!items.length) return;

  const prefersReducedMotion = window.matchMedia("(prefers-reduced-motion: reduce)").matches;

  if (prefersReducedMotion || typeof IntersectionObserver === "undefined") {
    items.forEach((item) => item.classList.add("is-visible"));
    return;
  }

  const observer = new IntersectionObserver(
    (entries) => {
      entries.forEach((entry) => {
        if (!entry.isIntersecting) return;
        entry.target.classList.add("is-visible");
        observer.unobserve(entry.target);
      });
    },
    { threshold: 0.15 }
  );

  items.forEach((item, index) => {
    item.style.transitionDelay = `${(index % 2) * 100}ms`;
    observer.observe(item);
  });
});
