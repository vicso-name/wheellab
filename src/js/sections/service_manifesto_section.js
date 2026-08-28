/**
 * Service Manifesto Section — reveal on scroll, replays every time.
 * The heading starts shifted down; the diamond and subheading start
 * hidden (opacity/translateY). This just toggles one .is-visible class
 * once ~30% of the block is in view — the heading-first, diamond/
 * subheading-second stagger is a transition-delay set on each element
 * in service_manifesto_section.scss, not sequenced here. Resets back to
 * hidden when the block scrolls back out — unlike Case Study Quote
 * Section's one-shot reveal, this one plays again each time the block
 * re-enters the viewport (scrolling up and back down replays it too).
 *
 * Observation starts two rAFs after DOMContentLoaded rather than
 * immediately. Without that, a page that loads already scrolled to (or
 * past) this block — a reload mid-scroll, a same-page anchor jump, a
 * bfcache restore — lets IntersectionObserver's first callback fire
 * before the browser has ever painted the hidden opacity:0/translateY
 * starting state, so there's nothing for the transition to animate
 * from and the reveal just snaps straight to visible instead of easing
 * in. Two rAFs guarantee a real painted frame of the hidden state first.
 */

document.addEventListener("DOMContentLoaded", () => {
  const blocks = document.querySelectorAll(".service-manifesto-section__inner");
  if (!blocks.length) return;

  const prefersReducedMotion = window.matchMedia("(prefers-reduced-motion: reduce)").matches;

  if (prefersReducedMotion || typeof IntersectionObserver === "undefined") {
    blocks.forEach((block) => block.classList.add("is-visible"));
    return;
  }

  const observer = new IntersectionObserver(
    (entries) => {
      entries.forEach((entry) => {
        entry.target.classList.toggle("is-visible", entry.isIntersecting);
      });
    },
    { threshold: 0.3 }
  );

  requestAnimationFrame(() => {
    requestAnimationFrame(() => {
      blocks.forEach((block) => observer.observe(block));
    });
  });
});
