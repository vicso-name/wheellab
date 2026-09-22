/**
 * Service Tiles — caps each top row's card stack to the height of the large
 * photo beside it, so a stack with more cards than fit scrolls internally
 * instead of dragging the whole row taller than its image.
 *
 * The cap is a max-height rather than a CSS rule because the photo's height
 * is only known once it has laid out (it comes from the image's own
 * aspect-ratio against a fluid column). is-scrollable is toggled from the
 * measured overflow so the bottom fade in service_tiles_section.scss never
 * shows on a stack that already fits.
 */

document.addEventListener("DOMContentLoaded", () => {
  const rows = Array.from(
    document.querySelectorAll(".service-tiles-section__row--top")
  ).filter(
    (row) =>
      row.querySelector(".service-tiles-section__tile--large") &&
      row.querySelector(".service-tiles-section__stack")
  );

  if (!rows.length) return;

  const desktop = window.matchMedia("(min-width: 769px)");

  const syncRow = (row) => {
    const photo = row.querySelector(".service-tiles-section__tile--large");
    const stack = row.querySelector(".service-tiles-section__stack");
    if (!photo || !stack) return;

    stack.style.maxHeight = desktop.matches ? `${photo.offsetHeight}px` : "";
    stack.classList.toggle(
      "is-scrollable",
      stack.scrollHeight > stack.clientHeight + 1
    );
  };

  const syncAll = () => rows.forEach(syncRow);

  syncAll();

  // The photo drives the cap, so watch it rather than the window — it also
  // catches the height jump when the image finishes decoding.
  if (typeof ResizeObserver !== "undefined") {
    const observer = new ResizeObserver(syncAll);
    rows.forEach((row) => {
      const photo = row.querySelector(".service-tiles-section__tile--large");
      if (photo) observer.observe(photo);
    });
  } else {
    window.addEventListener("resize", syncAll);
  }

  if (typeof desktop.addEventListener === "function") {
    desktop.addEventListener("change", syncAll);
  }
});
