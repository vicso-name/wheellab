/**
 * ===========================================================
 * General Frontend Interactions
 * ===========================================================
 * Organized into small, readable functions
 * - Header (mobile menu, desktop mega menu, search toggle)
 * - Smooth scroll
 * - Replace <img.svg> with inline <svg>
 * - AOS-style fade-up animations
 */

document.addEventListener("DOMContentLoaded", () => {
  initHeader();
  initSmoothScroll();
  replaceImagesWithInlineSVGs();
});

/* ===========================================================
 * 1. Header — mobile menu, desktop mega menu, search toggle
 * =========================================================== */
function initHeader() {
  const header = document.querySelector(".header");
  if (!header) return;

  initMobileMenuToggle(header);
  initSearchToggle(header);
  initMegaMenus(header);
  initMobileAccordion(header);
  initHeaderDismiss(header);
}

function initMobileMenuToggle(header) {
  const toggle = header.querySelector(".header__menu-toggle");
  const panel = header.querySelector(".header__mobile-panel");
  if (!toggle || !panel) return;

  toggle.addEventListener("click", () => {
    const isOpen = header.classList.contains("header--menu-open");
    setMobileMenuOpen(header, toggle, panel, !isOpen);
  });
}

function setMobileMenuOpen(header, toggle, panel, open) {
  header.classList.toggle("header--menu-open", open);
  toggle.setAttribute("aria-expanded", String(open));
  panel.hidden = !open;
  document.body.classList.toggle("overflow-hidden", open);

  // The search panel lives in the same collapsed bar on small screens —
  // don't let both be open at once.
  if (open) {
    const search = header.querySelector(".header__search");
    const searchToggle = header.querySelector(".header__search-toggle");
    if (search && !search.hidden) {
      setSearchOpen(searchToggle, search, false);
    }
  } else {
    // Collapse any open accordion trigger (e.g. "Services") and whatever
    // category was expanded inside it, so nothing reopens pre-expanded next
    // time, and drop the mega-open background tint it set on
    // .header__mobile-panel-inner.
    header.querySelectorAll(".header__accordion-trigger").forEach((trigger) => {
      const accordionPanel = document.getElementById(trigger.getAttribute("aria-controls"));
      trigger.setAttribute("aria-expanded", "false");
      if (accordionPanel) accordionPanel.hidden = true;
    });
    collapseAccordionSubitems(header);
    header.classList.remove("header--mega-open");
  }
}

function initSearchToggle(header) {
  const toggle = header.querySelector(".header__search-toggle");
  const panel = header.querySelector(".header__search");
  if (!toggle || !panel) return;

  toggle.addEventListener("click", () => {
    setSearchOpen(toggle, panel, panel.hidden);
  });
}

function setSearchOpen(toggle, panel, open) {
  panel.hidden = !open;
  toggle.setAttribute("aria-expanded", String(open));

  if (open) {
    const input = panel.querySelector('input[type="search"]');
    if (input) input.focus();
  }
}

function initMegaMenus(header) {
  const items = header.querySelectorAll(".header__nav-item--mega");

  items.forEach((item) => {
    const trigger = item.querySelector(".header__nav-link");
    // .header__mega is a sibling of .header__bar-inner (not nested inside
    // this nav item) so the header can grow as one continuous panel — look
    // it up by the id the trigger's aria-controls points to.
    const mega = trigger && document.getElementById(trigger.getAttribute("aria-controls"));
    if (!trigger || !mega) return;

    trigger.addEventListener("click", () => {
      const isOpen = item.classList.contains("is-open");
      closeAllMegaMenus(header);
      if (!isOpen) {
        setMegaMenuOpen(item, trigger, mega, true);
        header.classList.add("header--mega-open");
      }
    });

    initMegaMenuTabs(mega);
  });
}

function setMegaMenuOpen(item, trigger, mega, open) {
  item.classList.toggle("is-open", open);
  trigger.setAttribute("aria-expanded", String(open));
  mega.hidden = !open;
}

function closeAllMegaMenus(header) {
  header.querySelectorAll(".header__nav-item--mega.is-open").forEach((item) => {
    const trigger = item.querySelector(".header__nav-link");
    const mega = trigger && document.getElementById(trigger.getAttribute("aria-controls"));
    if (trigger && mega) setMegaMenuOpen(item, trigger, mega, false);
  });
  header.classList.remove("header--mega-open");
}

function initMegaMenuTabs(mega) {
  const tabs = mega.querySelectorAll('[role="tab"]');
  if (!tabs.length) return;

  tabs.forEach((tab) => {
    tab.addEventListener("click", () => {
      const panel = mega.querySelector("#" + tab.getAttribute("aria-controls"));
      if (!panel) return;

      tabs.forEach((t) => {
        t.classList.remove("is-active");
        t.setAttribute("aria-selected", "false");
        t.tabIndex = -1;
      });
      mega.querySelectorAll('[role="tabpanel"]').forEach((p) => {
        p.classList.remove("is-active");
        p.hidden = true;
      });

      tab.classList.add("is-active");
      tab.setAttribute("aria-selected", "true");
      tab.tabIndex = 0;
      panel.classList.add("is-active");
      panel.hidden = false;
    });
  });
}

function initMobileAccordion(header) {
  const triggers = header.querySelectorAll(".header__accordion-trigger");

  triggers.forEach((trigger) => {
    const panel = document.getElementById(trigger.getAttribute("aria-controls"));
    if (!panel) return;

    trigger.addEventListener("click", () => {
      const isOpen = trigger.getAttribute("aria-expanded") === "true";
      trigger.setAttribute("aria-expanded", String(!isOpen));
      panel.hidden = isOpen;

      // Closing "Services" collapses whatever category was expanded inside
      // it too, so reopening it later always starts from a clean state.
      if (isOpen) {
        collapseAccordionSubitems(panel);
      }

      // Same "a dropdown is open" flag .header__bar uses on desktop — here
      // it tints .header__mobile-panel-inner instead (see _header.scss).
      const anyOpen = Array.from(triggers).some((t) => t.getAttribute("aria-expanded") === "true");
      header.classList.toggle("header--mega-open", anyOpen);
    });
  });

  header.querySelectorAll(".header__accordion-subtrigger").forEach((subtrigger) => {
    subtrigger.addEventListener("click", () => {
      const group = subtrigger.closest(".header__accordion-sub");
      const cards = document.getElementById(subtrigger.getAttribute("aria-controls"));
      if (!group || !cards) return;

      const wasActive = subtrigger.classList.contains("is-active");
      collapseAccordionSubitems(group);

      // Clicking an already-open category closes it (collapseAccordionSubitems
      // above already handled that); clicking a different/closed one opens it.
      if (!wasActive) {
        subtrigger.classList.add("is-active");
        subtrigger.setAttribute("aria-expanded", "true");
        cards.hidden = false;
      }
    });
  });
}

function collapseAccordionSubitems(scope) {
  scope.querySelectorAll(".header__accordion-subtrigger").forEach((t) => {
    t.classList.remove("is-active");
    t.setAttribute("aria-expanded", "false");
  });
  scope.querySelectorAll(".header__accordion-cards").forEach((c) => {
    c.hidden = true;
  });
}

function initHeaderDismiss(header) {
  document.addEventListener("click", (e) => {
    if (header.contains(e.target)) return;

    closeAllMegaMenus(header);

    const search = header.querySelector(".header__search");
    const searchToggle = header.querySelector(".header__search-toggle");
    if (search && !search.hidden) setSearchOpen(searchToggle, search, false);
  });

  document.addEventListener("keydown", (e) => {
    if (e.key !== "Escape") return;

    closeAllMegaMenus(header);

    const search = header.querySelector(".header__search");
    const searchToggle = header.querySelector(".header__search-toggle");
    if (search && !search.hidden) setSearchOpen(searchToggle, search, false);

    const menuToggle = header.querySelector(".header__menu-toggle");
    const mobilePanel = header.querySelector(".header__mobile-panel");
    if (mobilePanel && !mobilePanel.hidden) {
      setMobileMenuOpen(header, menuToggle, mobilePanel, false);
    }
  });
}

/* ===========================================================
 * 2. Smooth Scroll to Section
 * =========================================================== */
function initSmoothScroll() {
  const links = document.querySelectorAll('a[href^="#"]');
  if (!links.length) return;

  links.forEach((link) => {
    link.addEventListener("click", (e) => {
      const targetId = link.getAttribute("href").slice(1);
      const targetEl = document.getElementById(targetId);

      if (!targetEl) return;
      e.preventDefault();
      targetEl.scrollIntoView({ behavior: "smooth" });
    });
  });
}

/* ===========================================================
 * 3. Convert <img class="svg"> → inline <svg>
 * =========================================================== */
function replaceImagesWithInlineSVGs() {
  const svgImages = document.querySelectorAll("img.svg");
  if (!svgImages.length) return;

  svgImages.forEach((img) => {
    const imgURL = img.src;

    fetch(imgURL)
      .then((res) => res.text())
      .then((data) => {
        const parser = new DOMParser();
        const xmlDoc = parser.parseFromString(data, "image/svg+xml");
        const svg = xmlDoc.querySelector("svg");

        if (!svg) {
          console.error("SVG not found in:", imgURL);
          return;
        }

        // Copy ID and class
        if (img.id) svg.id = img.id;
        if (img.className) svg.classList.add(...img.classList, "replaced-svg");

        // Remove unnecessary attributes
        svg.removeAttribute("xmlns:a");

        // Add viewBox if missing
        if (
          !svg.hasAttribute("viewBox") &&
          svg.hasAttribute("height") &&
          svg.hasAttribute("width")
        ) {
          svg.setAttribute(
            "viewBox",
            `0 0 ${svg.getAttribute("width")} ${svg.getAttribute("height")}`
          );
        }

        img.replaceWith(svg);
      })
      .catch((err) => console.error("Error fetching SVG:", err));
  });
}
