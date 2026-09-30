/**
 * Single post sidebar — Table of Contents.
 *
 * Desktop: the card is pinned by CSS and this script only runs the scroll-spy
 * that highlights whichever <h2> is currently in view.
 *
 * Mobile: the card sits above the article, so once the reader scrolls past it
 * the navigation would be gone for the rest of the page. Past that point the
 * nav collapses into a bar pinned under the site header showing the current
 * section, which expands on tap. Clicking a TOC link still works as a plain
 * anchor even if this script never runs.
 */
document.addEventListener("DOMContentLoaded", () => {
  initSinglePostToc();
});

// Matches the $medium breakpoint the sidebar layout flips at (_single_post_body).
const TOC_MOBILE_QUERY = "(max-width: 768px)";

function initSinglePostToc() {
  const shell = document.querySelector("[data-toc-shell]");
  const toc = document.querySelector(".single-post-toc");
  if (!toc) return;

  const links = Array.from(toc.querySelectorAll(".single-post-toc__link"));
  if (!links.length) return;

  const trigger = toc.querySelector(".single-post-toc__trigger");
  const currentLabel = toc.querySelector('[data-role="toc-current"]');
  const header = document.getElementById("header");

  const linkByHeading = new Map();
  const headings = [];

  links.forEach((link) => {
    const anchor = link.dataset.tocAnchor;
    const heading = anchor ? document.getElementById(anchor) : null;
    if (!heading) return;
    linkByHeading.set(heading, link);
    headings.push(heading);
  });

  if (!headings.length) return;

  const setActive = (activeHeading) => {
    links.forEach((link) => link.classList.remove("is-active"));
    const activeLink = linkByHeading.get(activeHeading);
    if (!activeLink) return;
    activeLink.classList.add("is-active");
    // Keep the collapsed bar naming the section the reader is actually in.
    if (currentLabel) currentLabel.textContent = activeLink.textContent.trim();
  };

  if (typeof IntersectionObserver !== "undefined") {
    // A heading counts as "current" once it crosses a line near the top
    // of the viewport, rather than only while fully visible — matches
    // how reading position naturally tracks past each section.
    const observer = new IntersectionObserver(
      (entries) => {
        entries.forEach((entry) => {
          if (entry.isIntersecting) setActive(entry.target);
        });
      },
      { rootMargin: "-15% 0px -70% 0px", threshold: 0 }
    );
    headings.forEach((heading) => observer.observe(heading));
  }
  setActive(headings[0]);

  if (shell && trigger) {
    initTocCollapse({ shell, toc, trigger, links, header });
  }
}

function initTocCollapse({ shell, toc, trigger, links, header }) {
  const mobile = window.matchMedia(TOC_MOBILE_QUERY);
  let stuck = false;
  let ticking = false;

  const setOpen = (open) => {
    toc.classList.toggle("is-open", open);
    trigger.setAttribute("aria-expanded", String(open));
  };

  // Anchor scrolls must clear the collapsed bar as well as the header, so the
  // bar's height feeds the same --scroll-offset the root scroller uses.
  const setScrollOffset = (px) => {
    document.documentElement.style.setProperty("--toc-offset", `${px}px`);
  };

  const setStuck = (next) => {
    if (next === stuck) return;
    stuck = next;
    toc.classList.toggle("is-stuck", next);
    if (!next) setOpen(false);
    setScrollOffset(next ? toc.offsetHeight : 0);
  };

  // While the nav is pinned it leaves the flow, so the shell has to hold the
  // card's original height or everything below it would jump up.
  const lockShellHeight = () => {
    if (!mobile.matches) {
      shell.style.height = "";
      return;
    }
    const wasStuck = toc.classList.contains("is-stuck");
    if (wasStuck) toc.classList.remove("is-stuck");
    shell.style.height = "";
    shell.style.height = `${toc.offsetHeight}px`;
    if (wasStuck) toc.classList.add("is-stuck");
  };

  const update = () => {
    ticking = false;
    if (!mobile.matches) return;

    // Collapse once the card itself has scrolled off the top of the viewport.
    setStuck(shell.getBoundingClientRect().bottom <= 0);

    // Ride with the site header: sit below it while it is on screen, slide up
    // to the top gap once it hides, so the bar is never covered and never
    // leaves a gap where the header used to be.
    if (header) {
      toc.classList.toggle(
        "is-below-header",
        !header.classList.contains("header--hidden")
      );
    }
  };

  const onScroll = () => {
    if (ticking) return;
    ticking = true;
    window.requestAnimationFrame(update);
  };

  const teardown = () => {
    setStuck(false);
    setOpen(false);
    shell.style.height = "";
    toc.classList.remove("is-below-header");
    setScrollOffset(0);
  };

  const onBreakpointChange = () => {
    if (mobile.matches) {
      lockShellHeight();
      update();
    } else {
      teardown();
    }
  };

  trigger.addEventListener("click", () => {
    setOpen(!toc.classList.contains("is-open"));
  });

  // Picking a section closes the panel; the anchor itself does the scrolling.
  links.forEach((link) => {
    link.addEventListener("click", () => setOpen(false));
  });

  document.addEventListener("keydown", (e) => {
    if (e.key !== "Escape" || !toc.classList.contains("is-open")) return;
    setOpen(false);
    trigger.focus();
  });

  document.addEventListener("click", (e) => {
    if (!toc.classList.contains("is-open")) return;
    if (!toc.contains(e.target)) setOpen(false);
  });

  window.addEventListener("scroll", onScroll, { passive: true });
  window.addEventListener("resize", () => {
    lockShellHeight();
    update();
  }, { passive: true });
  mobile.addEventListener("change", onBreakpointChange);

  onBreakpointChange();
}
