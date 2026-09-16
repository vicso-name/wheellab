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
  wheellabInitVisitorTracking();
});

/* ===========================================================
 * 1. Header — mobile menu, desktop mega menu, search toggle
 * =========================================================== */
function initHeader() {
  const header = document.querySelector(".header");
  if (!header) return;

  initMobileMenuToggle(header);
  initSearchToggle(header);
  initLiveSearch(header);
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

function initLiveSearch(header) {
  const form = header.querySelector("[data-live-search]");
  const config = window.wheellabSearch;
  if (!form || !config) return;

  const input = form.querySelector('input[type="search"]');
  const results = form.querySelector("[data-search-results]");
  const items = form.querySelector("[data-search-items]");
  const status = form.querySelector("[data-search-status]");
  const allLink = form.querySelector("[data-search-all]");
  if (!input || !results || !items || !status || !allLink) return;

  const minLength = Number(config.minLength) || 2;
  const resultLimit = Number(config.resultLimit) || 8;
  let timer = null;
  let controller = null;
  let requestSequence = 0;

  const resetResults = () => {
    if (controller) controller.abort();
    controller = null;
    items.innerHTML = "";
    results.hidden = true;
    allLink.hidden = true;
    allLink.removeAttribute("href");
    status.hidden = true;
    status.textContent = "";
    form.classList.remove("is-loading");
  };

  const setStatus = (message) => {
    status.textContent = message;
    status.hidden = !message;
  };

  const runSearch = async () => {
    const query = input.value.trim();
    if (query.length < minLength) {
      resetResults();
      if (query.length > 0) setStatus(config.strings.minLength);
      return;
    }

    if (controller) controller.abort();
    controller = new window.AbortController();
    const sequence = ++requestSequence;

    form.classList.add("is-loading");
    setStatus(config.strings.searching);
    items.innerHTML = "";
    results.hidden = true;
    allLink.hidden = true;

    const body = new URLSearchParams({
      action: "wheellab_site_search",
      nonce: config.nonce,
      search: query,
    });

    try {
      const response = await fetch(config.ajaxUrl, {
        method: "POST",
        credentials: "same-origin",
        headers: {
          "Content-Type": "application/x-www-form-urlencoded; charset=UTF-8",
        },
        body: body.toString(),
        signal: controller.signal,
      });

      if (!response.ok) throw new Error(`Search request failed: ${response.status}`);

      const payload = await response.json();
      if (sequence !== requestSequence) return;
      if (!payload.success || !payload.data) throw new Error("Invalid search response");

      const html = payload.data.html || "";
      const foundPosts = Number(payload.data.foundPosts) || 0;

      form.classList.remove("is-loading");

      if (!html || foundPosts === 0) {
        items.innerHTML = "";
        results.hidden = true;
        setStatus(config.strings.noResults);
        return;
      }

      items.innerHTML = html;
      results.hidden = false;
      setStatus("");

      if (foundPosts > resultLimit && payload.data.searchUrl) {
        allLink.href = payload.data.searchUrl;
        allLink.textContent = config.strings.viewAll.replace("%d", String(foundPosts));
        allLink.hidden = false;
      }
    } catch (error) {
      if (error.name === "AbortError") return;
      if (sequence !== requestSequence) return;

      form.classList.remove("is-loading");
      items.innerHTML = "";
      results.hidden = true;
      setStatus(config.strings.error);
    }
  };

  input.addEventListener("input", () => {
    window.clearTimeout(timer);
    timer = window.setTimeout(runSearch, 280);
  });

  input.addEventListener("focus", () => {
    if (input.value.trim().length >= minLength && !items.children.length) {
      runSearch();
    }
  });
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
 * 3. Visitor tracking — page journey + first UTM touch, read back by
 *    contact_section.js and forwarded to the Make webhook on form submit.
 *    Storage keys are also relied on (as string literals) in
 *    contact_section.js — keep both in sync if renamed.
 * =========================================================== */
const WHEELLAB_USER_JOURNEY_KEY = "wheellab_user_journey";
const WHEELLAB_VISIT_DATA_KEY = "wheellab_visit_data";
const WHEELLAB_USER_JOURNEY_LIMIT = 200;

function wheellabInitVisitorTracking() {
  try {
    wheellabRecordPageVisit();
    wheellabCaptureVisitData();
  } catch {
    // localStorage can throw (private browsing, blocked storage) — tracking
    // is best-effort and must never break the rest of the page.
  }
}

function wheellabRecordPageVisit() {
  const journey = wheellabReadJSON(WHEELLAB_USER_JOURNEY_KEY, []);
  const now = new Date();

  journey.push({
    path: window.location.origin + window.location.pathname,
    time: now.toISOString(),
    date: now.toISOString().slice(0, 10),
    timestamp: now.getTime(),
  });

  while (journey.length > WHEELLAB_USER_JOURNEY_LIMIT) journey.shift();

  window.localStorage.setItem(WHEELLAB_USER_JOURNEY_KEY, JSON.stringify(journey));
}

function wheellabCaptureVisitData() {
  const params = new URLSearchParams(window.location.search);
  const utmKeys = ["source", "medium", "campaign", "term", "content"];
  const hasUtm = utmKeys.some((key) => params.has(`utm_${key}`));

  // First-touch attribution: once we have UTM data stored, later visits
  // without UTM params (e.g. a direct revisit) don't overwrite it.
  if (!hasUtm && window.localStorage.getItem(WHEELLAB_VISIT_DATA_KEY)) return;

  const visitData = {};
  utmKeys.forEach((key) => {
    visitData[key] = params.get(`utm_${key}`) || null;
  });

  window.localStorage.setItem(WHEELLAB_VISIT_DATA_KEY, JSON.stringify(visitData));
}

function wheellabReadJSON(key, fallback) {
  try {
    const raw = window.localStorage.getItem(key);
    return raw ? JSON.parse(raw) : fallback;
  } catch {
    return fallback;
  }
}

/* ===========================================================
 * 4. Convert <img class="svg"> → inline <svg>
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
