/**
 * Case Study Quote Section — blur-in-on-scroll reveal.
 * The quote starts hidden (opacity/blur/translateY set in
 * case_study_quote_section.scss) and gets .is-visible added once ~30%
 * of it has scrolled into view. Reveals once, then stops observing —
 * not a repeat-on-scroll effect. Same pattern as Domains Section's own
 * card reveal (domains_section.js).
 */

document.addEventListener("DOMContentLoaded", () => {
  const quotes = document.querySelectorAll(".case-study-quote-section__inner");
  if (!quotes.length) return;

  const prefersReducedMotion = window.matchMedia("(prefers-reduced-motion: reduce)").matches;

  if (prefersReducedMotion || typeof IntersectionObserver === "undefined") {
    quotes.forEach((quote) => quote.classList.add("is-visible"));
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
    { threshold: 0.3 }
  );

  quotes.forEach((quote) => observer.observe(quote));
});
