/**
 * Domains Section — scroll-into-view stagger reveal.
 * Each card starts hidden (opacity/translateY set in domains_section.scss)
 * and gets .is-visible added once ~20% of it has scrolled into view; cards
 * are staggered by DOM order (featured card first, then the grid) via a
 * 100ms-per-index transition-delay. Reveals once per card, then stops
 * observing it — this isn't a repeat-on-scroll effect.
 */

document.addEventListener("DOMContentLoaded", () => {
  const cards = document.querySelectorAll(".domains-section__card");
  if (!cards.length) return;

  const prefersReducedMotion = window.matchMedia("(prefers-reduced-motion: reduce)").matches;

  if (prefersReducedMotion || typeof IntersectionObserver === "undefined") {
    cards.forEach((card) => card.classList.add("is-visible"));
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
    { threshold: 0.2 }
  );

  cards.forEach((card, index) => {
    card.style.transitionDelay = `${index * 100}ms`;
    observer.observe(card);
  });
});
