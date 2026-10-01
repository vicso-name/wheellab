/**
 * Service Process Deck — scroll-driven card stack. The section is a tall
 * track with a pinned (sticky) stage; every step of scroll through the track
 * brings in the next card from the right, and the cards already shown recede
 * to the left (rotate/blur — see service_process_deck.scss).
 *
 * This file only works out which step the scroll position is on and writes
 * it onto the cards: --deck-offset (0 = front, 1..MAX_OFFSET = how far back)
 * for cards already shown, and .is-queued for cards still waiting off-screen
 * to the right. The CSS owns all the motion, so one scroll step = one card
 * transition, whatever the scroll speed.
 */

document.addEventListener("DOMContentLoaded", () => {
  document.querySelectorAll(".service-process-deck").forEach(initDeck);
});

const MAX_OFFSET = 4;
// Share of the viewport height the first card appears ahead of the stage pinning.
const LEAD = 0.3;

function initDeck(section) {
  const track = section.querySelector(".service-process-deck__track");
  const cards = Array.from(section.querySelectorAll(".service-process-deck__card"));
  if (!track || cards.length < 2) return;

  const mobile = window.matchMedia("(max-width: 576px)");
  let current = -1;
  let ticking = false;

  function getStep() {
    const vh = window.innerHeight;
    const scrolled = -track.getBoundingClientRect().top;
    const stepPx = (track.offsetHeight - vh) / cards.length;
    const raw = (scrolled + vh * LEAD) / stepPx;
    return raw < 0 ? 0 : Math.min(cards.length, Math.floor(raw) + 1);
  }

  function render(step) {
    if (step === current) return;
    current = step;
    cards.forEach((card, i) => {
      const active = i === step - 1;
      card.classList.toggle("is-queued", i >= step);
      card.classList.toggle("is-active", active);
      card.style.setProperty("--deck-offset", String(i < step ? Math.min(step - 1 - i, MAX_OFFSET) : 0));
      if (active) card.setAttribute("aria-current", "true");
      else card.removeAttribute("aria-current");
    });
  }

  function update() {
    ticking = false;
    if (mobile.matches) return;
    render(getStep());
  }

  function onScroll() {
    if (ticking) return;
    ticking = true;
    window.requestAnimationFrame(update);
  }

  window.addEventListener("scroll", onScroll, { passive: true });
  window.addEventListener("resize", onScroll);
  update();
}
