/**
 * Stats Showcase Section — shared circular icon orbit + masked text lines.
 *
 * Reference: Animation.mp4 (not itself in Figma — the Figma "Card" node
 * only has the static layout/one generic icon slot). Re-derived from that
 * video frame-by-frame; see the geometry note below for how the exact
 * base angles were solved.
 *
 * Mechanics
 * ---------
 * Four FIXED 3D icons (arrow/gear/coins/chart — theme assets, see
 * stats_showcase_section.php, not per-slide editor uploads) sit on one
 * shared circular orbit, each ICON_ANGLE_SPACING_DEG apart. The orbit's
 * center sits below the section (ORBIT_CENTER_Y_RATIO × section height)
 * with a large radius (ORBIT_RADIUS_RATIO × section height), so only the
 * icons currently rotated into the top ~half of the circle are visible —
 * the rest sit below the fold, clipped by the section's own
 * `overflow: hidden`. Advancing to the next slide rotates the whole
 * orbit STATE_ROTATION_DEG (~90°) clockwise; every icon's screen
 * position is recomputed every frame from ONE shared `orbitAngle`
 * (state.orbitAngle below) via:
 *
 *   x = cx + cos(baseAngle + orbitAngle) * radius
 *   y = cy + sin(baseAngle + orbitAngle) * radius
 *
 * then written as `translate3d(...)` only — the icon element itself is
 * never rotated, so it stays upright throughout (see updateIconTransforms).
 *
 * Geometry note (how ICON_BASE_ANGLES was solved): the reference shows
 * each icon visible for exactly 2 consecutive states — one turn in the
 * "left" slot, the next turn in the "right" slot — then hidden for 2
 * states before re-entering. That 4-step cadence only falls out of the
 * base-angle assignment below (icons spaced 90° apart, orbit rotating
 * 90°/state); reusing this file for a different icon SET still needs
 * that same relationship (4 icons, 90° spacing, 90°/state rotation) to
 * reproduce the same visible-2/hidden-2 pattern.
 *
 * Center text (stat / two title lines / description) is NOT a
 * crossfade — each of the 4 roles has its own fixed-height,
 * `overflow: hidden` mask (see stats_showcase_section.scss) containing
 * every slide's value for that role, absolutely stacked. Advancing
 * translates the outgoing line up out of its mask while the incoming
 * one rises in from below, staggered ~90ms per role in a fixed order
 * (stat → title line 1 → title line 2 → description) — see advanceTo().
 */

document.addEventListener('DOMContentLoaded', () => {
  const cleanups = Array.from(document.querySelectorAll('.stats-showcase-section'), initStatsShowcase).filter(Boolean);

  // No SPA-style unmount happens on this static theme, but a future
  // dynamic context (AJAX-loaded content, a page-builder preview, ...)
  // might replace this section without a full page reload — this is
  // the hook that would call into for that, so the rAF loop/observers
  // don't keep running against a detached section.
  window.addEventListener('pagehide', () => cleanups.forEach((fn) => fn()), { once: true });
});

// Clockwise ring order + fixed base angle in degrees (0 = right/east,
// increasing CLOCKWISE since screen-space y grows downward — under that
// convention, increasing angle visually rotates clockwise, matching the
// spec directly with no sign-flipping). Keys must stay in sync with the
// data-icon values stats_showcase_section.php renders.
const ICON_BASE_ANGLES = {
  coins: 135,
  arrow: 225,
  gear: 315,
  chart: 45,
};

const ORBIT_CENTER_Y_RATIO = 1.11; // × section height, below the section
const ORBIT_RADIUS_RATIO = 0.84; // × section height
// Where the text block centers vertically — inside the dome's visible
// upper arc (roughly midway between the dome's top edge and its
// horizon), not simply 50% of the section. See
// stats_showcase_section.scss's .stats-showcase-section__inner comment.
const TEXT_CENTER_Y_RATIO = 0.67; // × section height
const STATE_ROTATION_DEG = 90;

// --- Mobile (≤ MOBILE_BREAKPOINT_PX): single centered icon, not the
// left/right orbit pair — matches the Figma mobile frame (node 758:61539),
// which shows one icon sitting directly above the text, not two flanking
// it. No mobile motion reference exists (Animation.mp4 is desktop-only),
// so this is a plain crossfade + small vertical shift rather than
// reusing the desktop's circular arc — see initStatsShowcase's mode
// branch in runIntro()/advanceTo().
const MOBILE_BREAKPOINT_PX = 768; // keep in sync with src/scss/partials/_variables.scss's $medium
const MOBILE_ICON_Y_RATIO = 0.28; // × section height
const MOBILE_TEXT_CENTER_Y_RATIO = 0.64; // × section height — lower than desktop's, to leave room for the icon above
const MOBILE_ICON_SHIFT_PX = 20;
const MOBILE_ICON_DURATION_MS = 420;
const MOBILE_ICON_EASING_CSS = 'cubic-bezier(0.65, 0, 0.35, 1)';
// The slot angle state 0 puts arrow in on desktop — reused here as "the
// one visible slot" so mobile shows the exact same icon-per-state as
// desktop's left position (arrow → coins → chart → …), just without the
// right-side partner.
const MOBILE_SLOT_ANGLE_DEG = ICON_BASE_ANGLES.arrow;
// Loop forever by default (matches the live reference) — set to false
// to stop advancing after the last slide instead.
const LOOP = true;

const STATE_ROTATION_MS = 750;
const STATE_ROTATION_EASING = cubicBezier(0.65, 0, 0.35, 1);

const TEXT_LINE_ROLES = ['stat', 'title-1', 'title-2', 'description'];
const TEXT_LINE_STAGGER_MS = 90;
const TEXT_LINE_DURATION_MS = 390;
const TEXT_LINE_EASING = 'cubic-bezier(0.65, 0, 0.35, 1)';
// How long after a rotation starts the text starts changing (spec: ~180–200ms).
const TEXT_AFTER_ROTATION_START_DELAY_MS = 190;

const ICON_INTRO_DURATION_MS = 900;
const ICON_INTRO_EASING = cubicBezier(0.16, 1, 0.3, 1);
const ICON_INTRO_EASING_CSS = 'cubic-bezier(0.16, 1, 0.3, 1)';
const GEAR_INTRO_START_DELAY_MS = 100;
const ARROW_INTRO_START_DELAY_MS = 380;
// Gear's intro sweeps in from further round the ring (left side, arcing
// over the top) rather than fading in at rest — see initGearIntroSweep().
const GEAR_INTRO_ANGLE_OFFSET_DEG = -150;

// Stable-state hold durations (ms) before the NEXT rotation starts,
// measured from when that state's text finished settling — tuned to the
// reference timeline (state 1 stable ~0.9s, next rotation starts ~1.8s;
// state 2 stable ~2.5s, next rotation starts ~4.05s). Any state beyond
// the ones with an explicit hold below reuses HOLD_MS_DEFAULT.
const HOLD_MS_AFTER_STATE = [900, 1500];
const HOLD_MS_DEFAULT = 1500;

const DEG2RAD = Math.PI / 180;

function isMobile() {
  return window.matchMedia(`(max-width: ${MOBILE_BREAKPOINT_PX}px)`).matches;
}

// Which icon occupies the (desktop) left / (mobile) single slot for a
// given state index — derived from the same base-angle math the desktop
// orbit uses, rather than a separate hardcoded sequence, so it always
// agrees with what desktop actually shows in that slot.
function iconKeyForState(stateIndex) {
  const rotated = (((stateIndex * STATE_ROTATION_DEG) % 360) + 360) % 360;
  return Object.keys(ICON_BASE_ANGLES).find((key) => {
    const angle = ((ICON_BASE_ANGLES[key] + rotated) % 360 + 360) % 360;
    return Math.abs(angle - MOBILE_SLOT_ANGLE_DEG) < 0.01;
  });
}

function initStatsShowcase(section) {
  const orbitIcons = Array.from(section.querySelectorAll('.stats-showcase-section__orbit-icon'));
  const circleImg = section.querySelector('.stats-showcase-section__circle img');
  const lineRoleGroups = TEXT_LINE_ROLES.map((role) =>
    Array.from(section.querySelectorAll(`.stats-showcase-section__line[data-role="${role}"]`))
  );
  const slideCount = lineRoleGroups[0] ? lineRoleGroups[0].length : 0;

  if (!orbitIcons.length || slideCount < 1) return;

  const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

  const metrics = { cx: 0, cy: 0, radius: 0 };
  const iconState = { orbitAngle: 0, extra: {}, mobileCurrentKey: null };
  let currentIndex = 0;
  let started = false;
  let destroyed = false;
  let intersectionObserver = null;
  const pendingTimeouts = new Set();

  function setTimeoutTracked(fn, ms) {
    const id = window.setTimeout(() => {
      pendingTimeouts.delete(id);
      if (!destroyed) fn();
    }, ms);
    pendingTimeouts.add(id);
    return id;
  }

  function measure() {
    const rect = section.getBoundingClientRect();
    const mobile = isMobile();
    metrics.cx = rect.width / 2;
    metrics.cy = rect.height * ORBIT_CENTER_Y_RATIO;
    metrics.radius = rect.height * ORBIT_RADIUS_RATIO;
    metrics.mobileIconY = rect.height * MOBILE_ICON_Y_RATIO;

    section.style.setProperty('--stats-orbit-cy', `${metrics.cy}px`);
    section.style.setProperty('--stats-orbit-diameter', `${metrics.radius * 2}px`);
    section.style.setProperty(
      '--stats-text-cy',
      `${rect.height * (mobile ? MOBILE_TEXT_CENTER_Y_RATIO : TEXT_CENTER_Y_RATIO)}px`
    );

    updateIconTransforms();
  }

  function positionIcon(icon, x, y) {
    const halfW = (icon._w || icon.offsetWidth || 0) / 2;
    const halfH = (icon._h || icon.offsetHeight || 0) / 2;
    icon.style.transform = `translate3d(${(x - halfW).toFixed(2)}px, ${(y - halfH).toFixed(2)}px, 0)`;
  }

  // Single shared layout pass, mode-aware — called on init and on every
  // resize (including one that crosses the mobile breakpoint). Desktop
  // computes all 4 icons' orbit positions from the shared orbitAngle;
  // mobile just re-centers every icon at the one fixed slot (harmless
  // for the 3 that are currently opacity: 0 — see runIntro()/advanceTo()'s
  // mobile branch for why only one icon is ever visible there).
  function updateIconTransforms() {
    if (isMobile()) {
      orbitIcons.forEach((icon) => positionIcon(icon, metrics.cx, metrics.mobileIconY));
      return;
    }
    orbitIcons.forEach((icon) => {
      const key = icon.dataset.icon;
      const baseAngle = ICON_BASE_ANGLES[key] || 0;
      const extra = iconState.extra[key] || 0;
      const angleRad = (baseAngle + iconState.orbitAngle + extra) * DEG2RAD;
      const x = metrics.cx + Math.cos(angleRad) * metrics.radius;
      const y = metrics.cy + Math.sin(angleRad) * metrics.radius;
      positionIcon(icon, x, y);
    });
  }

  function cacheIconSizes() {
    orbitIcons.forEach((icon) => {
      icon._w = icon.offsetWidth;
      icon._h = icon.offsetHeight;
    });
  }

  const resizeObserver = new ResizeObserver(() => {
    cacheIconSizes();
    measure();
  });
  resizeObserver.observe(section);

  // Sticky header hides upward while this section is in view (frees up
  // headroom for the dome) and slides back down once it isn't —
  // independent of the intro/orbit animation above, so this runs the
  // same way whether or not prefers-reduced-motion is set. A fairly low
  // threshold (0.15) hides it as soon as a meaningful chunk of the
  // section is on screen rather than waiting for it to fill the
  // viewport.
  const headerEl = document.querySelector('.header');
  let headerObserver = null;
  if (headerEl) {
    headerObserver = new IntersectionObserver(
      (entries) => {
        entries.forEach((entry) => {
          headerEl.classList.toggle('is-hidden-for-stats-showcase', entry.isIntersecting);
        });
      },
      { threshold: 0.15 }
    );
    headerObserver.observe(section);
  }

  // --- Reduced motion: first state's resting values, no animation at all. ---
  if (prefersReducedMotion) {
    cacheIconSizes();
    measure();
    if (isMobile()) {
      // Unlike desktop's pair (where the 2 icons NOT in play sit
      // naturally off-screen via the orbit math, even at opacity: 1),
      // every mobile icon shares the exact same centered spot — turning
      // all 4 on here would stack them visibly on top of each other.
      const key = iconKeyForState(0);
      orbitIcons.forEach((icon) => {
        icon.style.opacity = icon.dataset.icon === key ? '1' : '0';
      });
    } else {
      orbitIcons.forEach((icon) => {
        icon.style.opacity = '1';
      });
    }
    lineRoleGroups.forEach((lines) => {
      lines.forEach((line, index) => {
        line.style.transform = index === 0 ? 'translateY(0)' : 'translateY(120%)';
        if (index === 0) line.classList.add('is-active');
      });
    });
    return destroy;
  }

  // --- Normal path: wait for images, then trigger once on scroll into view. ---
  Promise.all(
    [circleImg, ...orbitIcons]
      .filter(Boolean)
      .map((img) => (img.decode ? img.decode().catch(() => {}) : Promise.resolve()))
  ).then(() => {
    if (destroyed) return;
    cacheIconSizes();
    measure();

    intersectionObserver = new IntersectionObserver(
      (entries) => {
        entries.forEach((entry) => {
          if (entry.isIntersecting && !started) {
            started = true;
            intersectionObserver.disconnect();
            runIntro();
          }
        });
      },
      { threshold: 0.35 }
    );
    intersectionObserver.observe(section);
  });

  return destroy;

  function destroy() {
    destroyed = true;
    resizeObserver.disconnect();
    if (intersectionObserver) intersectionObserver.disconnect();
    if (headerObserver) headerObserver.disconnect();
    if (headerEl) headerEl.classList.remove('is-hidden-for-stats-showcase');
    pendingTimeouts.forEach((id) => window.clearTimeout(id));
    pendingTimeouts.clear();
  }

  function tweenValue(from, to, duration, easingFn, onUpdate) {
    return new Promise((resolve) => {
      const start = performance.now();
      function frame(now) {
        if (destroyed) return;
        const elapsed = now - start;
        const t = Math.min(1, elapsed / duration);
        onUpdate(from + (to - from) * easingFn(t));
        if (t < 1) {
          requestAnimationFrame(frame);
        } else {
          resolve();
        }
      }
      requestAnimationFrame(frame);
    });
  }

  function enterLine(line, delayMs) {
    line.setAttribute('aria-hidden', 'false');
    return line.animate(
      [{ transform: 'translateY(120%)' }, { transform: 'translateY(0)' }],
      { duration: TEXT_LINE_DURATION_MS, delay: delayMs, easing: TEXT_LINE_EASING, fill: 'forwards' }
    ).finished.then(() => {
      line.style.transform = 'translateY(0)';
      line.classList.add('is-active');
    });
  }

  // outgoing/incoming get aria-hidden flipped immediately (not after the
  // animation settles) — every slide's text for every role lives in the
  // DOM at once, purely CSS-hidden via the mask/transform; without this,
  // a screen reader reads all N slides' copy concatenated instead of
  // just the one currently on screen.
  function transitionLine(outgoing, incoming, delayMs) {
    const anims = [];
    if (outgoing) {
      outgoing.classList.remove('is-active');
      outgoing.setAttribute('aria-hidden', 'true');
      anims.push(
        outgoing.animate(
          [{ transform: 'translateY(0)' }, { transform: 'translateY(-120%)' }],
          { duration: TEXT_LINE_DURATION_MS, delay: delayMs, easing: TEXT_LINE_EASING, fill: 'forwards' }
        ).finished.then(() => {
          outgoing.style.transform = 'translateY(120%)';
        })
      );
    }
    if (incoming) {
      incoming.setAttribute('aria-hidden', 'false');
      anims.push(
        incoming.animate(
          [{ transform: 'translateY(120%)' }, { transform: 'translateY(0)' }],
          { duration: TEXT_LINE_DURATION_MS, delay: delayMs, easing: TEXT_LINE_EASING, fill: 'forwards' }
        ).finished.then(() => {
          incoming.style.transform = 'translateY(0)';
          incoming.classList.add('is-active');
        })
      );
    }
    return Promise.all(anims);
  }

  function runIntro() {
    if (isMobile()) {
      runIntroMobile();
      return;
    }
    runIntroDesktop();
  }

  // A WAAPI animation with fill: 'forwards' keeps pinning `transform` at
  // higher priority than a later direct style write until it's
  // cancelled — see the identical note on the desktop arrow's intro
  // below. Every mobile icon animation goes through this same
  // fetch-and-release step so a resize (or the icon's next turn) can
  // freely reposition/refade it afterward.
  function releaseAnimation(anim) {
    return anim.finished.then(() => anim.cancel());
  }

  function runIntroMobile() {
    const key = iconKeyForState(0);
    iconState.mobileCurrentKey = key;
    updateIconTransforms();

    const icon = orbitIcons.find((i) => i.dataset.icon === key);
    const introPromises = [];

    if (icon) {
      icon.style.opacity = '0';
      const restTransform = icon.style.transform;
      const anim = icon.animate(
        [
          { transform: `${restTransform} translateY(${MOBILE_ICON_SHIFT_PX}px)`, opacity: 0 },
          { transform: restTransform, opacity: 1 },
        ],
        { duration: ICON_INTRO_DURATION_MS, delay: ARROW_INTRO_START_DELAY_MS, easing: ICON_INTRO_EASING_CSS, fill: 'forwards' }
      );
      introPromises.push(releaseAnimation(anim).then(() => {
        icon.style.opacity = '1';
      }));
    }

    lineRoleGroups.forEach((lines, roleIndex) => {
      const first = lines[0];
      if (first) introPromises.push(enterLine(first, 200 + roleIndex * TEXT_LINE_STAGGER_MS));
    });

    Promise.all(introPromises).then(() => scheduleNext(0));
  }

  function runIntroDesktop() {
    // Icons start hidden; coins/chart already sit off-screen (state 0
    // math) so they need no motion — only opacity, applied once the
    // intro is fully done, as a safety net (see this file's header
    // comment on why coins/chart must never flash near the top).
    updateIconTransforms();

    const arrowIcon = orbitIcons.find((icon) => icon.dataset.icon === 'arrow');
    const gearIcon = orbitIcons.find((icon) => icon.dataset.icon === 'gear');
    const coinsIcon = orbitIcons.find((icon) => icon.dataset.icon === 'coins');
    const chartIcon = orbitIcons.find((icon) => icon.dataset.icon === 'chart');

    const introPromises = [];

    if (gearIcon) {
      iconState.extra.gear = GEAR_INTRO_ANGLE_OFFSET_DEG;
      gearIcon.style.opacity = '0';
      introPromises.push(
        new Promise((resolve) => {
          setTimeoutTracked(() => {
            gearIcon.animate([{ opacity: 0 }, { opacity: 1 }], {
              duration: Math.min(300, ICON_INTRO_DURATION_MS),
              easing: 'linear',
              fill: 'forwards',
            });
            tweenValue(GEAR_INTRO_ANGLE_OFFSET_DEG, 0, ICON_INTRO_DURATION_MS, ICON_INTRO_EASING, (v) => {
              iconState.extra.gear = v;
              updateIconTransforms();
            }).then(resolve);
          }, GEAR_INTRO_START_DELAY_MS);
        })
      );
    }

    if (arrowIcon) {
      arrowIcon.style.opacity = '0';
      introPromises.push(
        new Promise((resolve) => {
          setTimeoutTracked(() => {
            arrowIcon.style.opacity = '1';
            const anim = arrowIcon.animate(
              [
                { transform: `${arrowIcon.style.transform} translateY(60px)`, opacity: 0 },
                { transform: arrowIcon.style.transform, opacity: 1 },
              ],
              { duration: ICON_INTRO_DURATION_MS, easing: ICON_INTRO_EASING_CSS, fill: 'forwards' }
            );
            anim.finished.then(() => {
              // A fill: 'forwards' Web Animation keeps its last-keyframe
              // value pinned on `transform` at HIGHER priority than any
              // later `element.style.transform = ...` write — left
              // running, this permanently freezes the arrow at its
              // intro's resting spot, silently no-op'ing every
              // updateIconTransforms() call for the rest of the page's
              // life (confirmed live: this exact bug was why the arrow
              // never rejoined the orbit rotation). Cancelling hands
              // `transform` back to the element's own inline style,
              // which updateIconTransforms() has been keeping in sync
              // the whole time anyway (it runs every frame, including
              // during this intro), so there's nothing to visually snap.
              anim.cancel();
              resolve();
            });
          }, ARROW_INTRO_START_DELAY_MS);
        })
      );
    }

    // Text: stat -> title line 1 -> title line 2 -> description, each
    // ~90ms after the previous, starting alongside the icon entrances.
    lineRoleGroups.forEach((lines, roleIndex) => {
      const first = lines[0];
      if (first) introPromises.push(enterLine(first, 200 + roleIndex * TEXT_LINE_STAGGER_MS));
    });

    Promise.all(introPromises).then(() => {
      if (coinsIcon) coinsIcon.style.opacity = '1';
      if (chartIcon) chartIcon.style.opacity = '1';
      scheduleNext(0);
    });
  }

  function scheduleNext(fromIndex) {
    if (slideCount <= 1) return; // nothing to advance to — avoid a no-op rotation/transition every hold cycle
    if (!LOOP && fromIndex >= slideCount - 1) return; // stop on the last state
    const holdMs = HOLD_MS_AFTER_STATE[fromIndex] ?? HOLD_MS_DEFAULT;
    setTimeoutTracked(() => advanceTo((fromIndex + 1) % slideCount), holdMs);
  }

  function advanceTo(nextIndex) {
    if (isMobile()) {
      advanceToMobile(nextIndex);
      return;
    }
    advanceToDesktop(nextIndex);
  }

  function advanceToMobile(nextIndex) {
    const prevIndex = currentIndex;
    currentIndex = nextIndex;

    const prevKey = iconState.mobileCurrentKey;
    const nextKey = iconKeyForState(nextIndex);
    iconState.mobileCurrentKey = nextKey;

    const prevIcon = orbitIcons.find((i) => i.dataset.icon === prevKey);
    const nextIcon = orbitIcons.find((i) => i.dataset.icon === nextKey);

    if (prevIcon && prevIcon !== nextIcon) {
      const restTransform = prevIcon.style.transform;
      const anim = prevIcon.animate(
        [
          { transform: restTransform, opacity: 1 },
          { transform: `${restTransform} translateY(-${MOBILE_ICON_SHIFT_PX}px)`, opacity: 0 },
        ],
        { duration: MOBILE_ICON_DURATION_MS, easing: MOBILE_ICON_EASING_CSS, fill: 'forwards' }
      );
      releaseAnimation(anim).then(() => {
        prevIcon.style.opacity = '0';
      });
    }

    if (nextIcon && nextIcon !== prevIcon) {
      positionIcon(nextIcon, metrics.cx, metrics.mobileIconY);
      const restTransform = nextIcon.style.transform;
      const anim = nextIcon.animate(
        [
          { transform: `${restTransform} translateY(${MOBILE_ICON_SHIFT_PX}px)`, opacity: 0 },
          { transform: restTransform, opacity: 1 },
        ],
        { duration: MOBILE_ICON_DURATION_MS, easing: MOBILE_ICON_EASING_CSS, fill: 'forwards' }
      );
      releaseAnimation(anim).then(() => {
        nextIcon.style.opacity = '1';
      });
    }

    lineRoleGroups.forEach((lines, roleIndex) => {
      transitionLine(lines[prevIndex], lines[nextIndex], roleIndex * TEXT_LINE_STAGGER_MS);
    });

    setTimeoutTracked(() => scheduleNext(nextIndex), MOBILE_ICON_DURATION_MS);
  }

  function advanceToDesktop(nextIndex) {
    const prevIndex = currentIndex;
    currentIndex = nextIndex;

    const targetAngle = iconState.orbitAngle + STATE_ROTATION_DEG;
    tweenValue(iconState.orbitAngle, targetAngle, STATE_ROTATION_MS, STATE_ROTATION_EASING, (v) => {
      iconState.orbitAngle = v;
      updateIconTransforms();
    }).then(() => {
      // Keep the angle bounded — purely for numeric hygiene over a very
      // long-lived loop (cos/sin are periodic regardless), never for
      // correctness.
      iconState.orbitAngle %= 360;
    });

    lineRoleGroups.forEach((lines, roleIndex) => {
      transitionLine(
        lines[prevIndex],
        lines[nextIndex],
        TEXT_AFTER_ROTATION_START_DELAY_MS + roleIndex * TEXT_LINE_STAGGER_MS
      );
    });

    setTimeoutTracked(() => scheduleNext(nextIndex), STATE_ROTATION_MS);
  }
}

/**
 * Standard cubic-bezier easing (Newton–Raphson), matching CSS's own
 * cubic-bezier(x1, y1, x2, y2) timing function — used wherever this file
 * needs the eased VALUE at a given t inside a requestAnimationFrame loop
 * (orbit rotation, gear's intro sweep), since the Web Animations API used
 * everywhere else here only exposes easing as an opaque string, not a
 * per-frame callback.
 */
function cubicBezier(x1, y1, x2, y2) {
  function a(c1, c2) {
    return 1 - 3 * c2 + 3 * c1;
  }
  function b(c1, c2) {
    return 3 * c2 - 6 * c1;
  }
  function c(c1) {
    return 3 * c1;
  }
  function calc(t, c1, c2) {
    return ((a(c1, c2) * t + b(c1, c2)) * t + c(c1)) * t;
  }
  function slope(t, c1, c2) {
    return 3 * a(c1, c2) * t * t + 2 * b(c1, c2) * t + c(c1);
  }
  function tForX(x) {
    let t = x;
    for (let i = 0; i < 8; i++) {
      const s = slope(t, x1, x2);
      if (s === 0) return t;
      t -= (calc(t, x1, x2) - x) / s;
    }
    return t;
  }
  return function (x) {
    if (x <= 0) return 0;
    if (x >= 1) return 1;
    return calc(tForX(x), y1, y2);
  };
}
