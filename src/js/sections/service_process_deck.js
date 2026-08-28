/**
 * Service Process Deck — fanned card stack, click a peeking card to
 * bring it to front. Auto-advances through the steps in order every
 * 3.5s, pausing while the pointer is over the deck or a card holds
 * keyboard focus, and restarting its hold on every manual bring-to-front
 * so a click doesn't get immediately undone by the next auto tick.
 *
 * Maintains one front-to-back order array per deck (DOM elements, not
 * indices) and re-derives every card's --deck-offset (0 = active) from
 * its position in that array on every change — a plain "move item to
 * the front" reorder, same idea as a most-recently-used list. The CSS
 * custom property alone drives the whole recession look (rotate/blur/
 * z-index — see service_process_deck.scss), so this file never touches
 * transform/filter directly.
 */

document.addEventListener("DOMContentLoaded", () => {
  document.querySelectorAll(".service-process-deck__deck").forEach(initDeck);
});

const MAX_OFFSET = 4;
const AUTOPLAY_MS = 3500;

function initDeck(deck) {
  const cards = Array.from(deck.querySelectorAll(".service-process-deck__card"));
  if (cards.length < 2) return;

  let order = cards;
  let timer = null;
  const reduceMotion = window.matchMedia("(prefers-reduced-motion: reduce)").matches;

  function render() {
    order.forEach((card, position) => {
      const offset = Math.min(position, MAX_OFFSET);
      card.style.setProperty("--deck-offset", String(offset));
      card.classList.toggle("is-active", position === 0);
      card.setAttribute("aria-current", position === 0 ? "true" : "false");
    });
  }

  function bringToFront(card) {
    if (order[0] === card) return;
    order = [card, ...order.filter((c) => c !== card)];
    render();
  }

  function advance() {
    const currentIndex = Number(order[0].dataset.index);
    const nextIndex = (currentIndex + 1) % cards.length;
    const next = cards.find((card) => Number(card.dataset.index) === nextIndex);
    if (next) bringToFront(next);
  }

  function stop() {
    if (timer) {
      window.clearInterval(timer);
      timer = null;
    }
  }

  function start() {
    if (reduceMotion || timer) return;
    timer = window.setInterval(advance, AUTOPLAY_MS);
  }

  function restart() {
    stop();
    start();
  }

  cards.forEach((card) => {
    card.addEventListener("click", () => {
      bringToFront(card);
      restart();
    });
  });

  deck.addEventListener("pointerenter", stop);
  deck.addEventListener("pointerleave", start);
  deck.addEventListener("focusin", stop);
  deck.addEventListener("focusout", start);

  render();
  start();
}
