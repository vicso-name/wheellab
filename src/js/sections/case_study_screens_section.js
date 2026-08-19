/**
 * Case Study Screens Section — scroll-into-view stagger reveal.
 * Each screen's card starts hidden (opacity/translateY set in
 * case_study_screens_section.scss) and gets .is-visible added once
 * ~15% of it has scrolled into view; cards are staggered by DOM order
 * via a 100ms-per-index transition-delay. Reveals once per card, then
 * stops observing it — same pattern as Domains Section's own card
 * reveal (domains_section.js) and Case Study Showcase Section's own
 * image reveal (case_study_showcase_section.js).
 */

document.addEventListener("DOMContentLoaded", () => {
  const frames = document.querySelectorAll(".case-study-screens-section__frame");
  if (!frames.length) return;

  const prefersReducedMotion = window.matchMedia("(prefers-reduced-motion: reduce)").matches;

  if (prefersReducedMotion || typeof IntersectionObserver === "undefined") {
    frames.forEach((frame) => frame.classList.add("is-visible"));
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

  frames.forEach((frame, index) => {
    frame.style.transitionDelay = `${(index % 2) * 100}ms`;
    observer.observe(frame);
  });
});
