/**
 * Service Comparison Section — draggable before/after image reveal.
 *
 * Plain Pointer Events (unifies mouse/touch/pen — no separate touch
 * listeners needed) + a single CSS custom property
 * (--comparison-reveal, read by .service-comparison-section__image--after's
 * clip-path and .service-comparison-section__handle's own left) set on
 * .service-comparison-section__card. Dragging anywhere on the card (not
 * just the handle itself) moves it — see the pointerdown listener below.
 */

document.addEventListener("DOMContentLoaded", () => {
  document.querySelectorAll(".service-comparison-section__card").forEach(initComparison);
});

function initComparison(card) {
  const handle = card.querySelector(".service-comparison-section__handle");
  const frame = card.querySelector(".service-comparison-section__frame");
  if (!handle || !frame) return;

  let dragging = false;

  function setReveal(percent) {
    const clamped = Math.min(100, Math.max(0, percent));
    card.style.setProperty("--comparison-reveal", `${clamped}%`);
    handle.setAttribute("aria-valuenow", String(Math.round(clamped)));
  }

  function percentFromClientX(clientX) {
    const rect = frame.getBoundingClientRect();
    if (!rect.width) return 50;
    return ((clientX - rect.left) / rect.width) * 100;
  }

  function onPointerDown(e) {
    dragging = true;
    card.classList.add("is-dragging");
    card.setPointerCapture(e.pointerId);
    setReveal(percentFromClientX(e.clientX));
  }

  function onPointerMove(e) {
    if (!dragging) return;
    setReveal(percentFromClientX(e.clientX));
  }

  function onPointerUp(e) {
    if (!dragging) return;
    dragging = false;
    card.classList.remove("is-dragging");
    if (card.hasPointerCapture(e.pointerId)) {
      card.releasePointerCapture(e.pointerId);
    }
  }

  card.addEventListener("pointerdown", onPointerDown);
  card.addEventListener("pointermove", onPointerMove);
  card.addEventListener("pointerup", onPointerUp);
  card.addEventListener("pointercancel", onPointerUp);

  // Keyboard access — the handle itself is the role="slider" element.
  handle.addEventListener("keydown", (e) => {
    const current = parseFloat(handle.getAttribute("aria-valuenow")) || 50;
    const step = e.shiftKey ? 10 : 2;

    if (e.key === "ArrowLeft") {
      setReveal(current - step);
    } else if (e.key === "ArrowRight") {
      setReveal(current + step);
    } else if (e.key === "Home") {
      setReveal(0);
    } else if (e.key === "End") {
      setReveal(100);
    } else {
      return;
    }
    e.preventDefault();
  });

  setReveal(50);
}
