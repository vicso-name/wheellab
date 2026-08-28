# Block Creation Playbook (working reference)

Personal cheat sheet distilled from the actual codebase (not aspirational — every
pattern here is copied from a real, currently-shipping block). Read this before
starting a new block; update it whenever a new convention is established.
Complements [acf-block-patterns.md](acf-block-patterns.md), which has the fuller
recipe/checklist form — this file is the fast-lookup version plus the parts that
doc doesn't cover (naming policy, CPT relationship, current inventory, tokens).

---

## 1. Naming policy

Two kinds of block, same 4-file mechanics — only the **name** signals scope:

| Scope | Naming pattern | Example | Meaning |
|---|---|---|---|
| General-purpose | `{purpose}_section` | `hero_section`, `faq_section`, `cta_banner_section` | Used anywhere, name doesn't imply a page |
| Context-flavored | `{context}_{purpose}` | `case_study_hero`, `case_study_about_section`, `case_study_tabs_section` | Built for a specific CPT/page type, prefixed **only so it's easy to find in the inserter** — NOT a technical restriction. Nothing stops `case_study_hero` from being dropped into a regular Page if it fits. |

**Rule for Services blocks:** prefix with `service_` the same way Case Study
does, e.g. `service_hero`, `service_about_section`, `service_features_section`.
Title-cases automatically to "Service Hero", "Service About Section" via
`ucwords(str_replace('_', ' ', $slug))` — no manual title needed.

---

## 2. End-to-end workflow for a new block

1. **Study the Figma spec first.** Get the node's design context/screenshot/metadata
   before writing any code — layout, spacing, states (hover/open/mobile), motion
   reference if any (some blocks like `stats_showcase_section` cite a separate
   `.mp4` export, not just the static Figma node). Note the Figma node ID in the
   PHP file's header comment (see §4) — every existing block does this for
   traceability.
2. **Design the ACF field architecture** before touching code — decide which of
   the 4 standard patterns fits (§5), what's a repeater vs fixed fields, what's
   optional vs required, whether it needs an image/link/textarea/wysiwyg per the
   Field Type Rules (§6).
3. **Backend:**
   - Add slug to `$blocks` array in `inc/acf_blocks.php` (or use
     `node generate-sections.js <name> [--js]` to scaffold all of this at once).
   - Write `template-parts/sections/{slug}.php`.
   - Create the ACF field group — either in wp-admin then export to
     `acf-json/group_{slug}.json`, or hand-write the JSON directly (faster,
     and is what's actually been done for every block so far — see §5/§6 for
     the field-type recipes to copy from).
4. **Frontend:**
   - `src/scss/sections/{slug}.scss` — BEM, tokens only (§7), breakpoints via
     `$medium`/`$small` SCSS vars (never raw px media queries).
   - `src/js/sections/{slug}.js` only if interactive (accordion, carousel,
     animation, IntersectionObserver-triggered motion, etc). Guard on
     `document.querySelector('.{slug-with-dashes}')` returning null.
5. **Build & verify:** `npm run dev`, check the block appears under "SMLFY
   Blocks" category with the right title, fields work in Edit mode, preview
   renders, frontend CSS/JS load ONLY on pages containing the block (check
   Network tab), no console errors, test mobile breakpoints.

---

## 3. Registration snippet (inc/acf_blocks.php)

```php
$blocks = [
    // ...
    'service_hero',          // ← add new slug here, snake_case
];
```

That's the *only* manual step — `wheellab_enqueue_detected_block_assets()`
reads `$blocks` at runtime and enqueues `build/css/sections/{slug}.min.css` /
`build/js/sections/{slug}.min.js` automatically, only on pages where the block
is actually used (falls back to enqueueing all block CSS if block-detection
parsing finds nothing, e.g. classic-editor content).

Registration always carries (do not omit any of these — dropping `align/full`
silently shrinks the section off full-bleed, confirmed regression on
2026-08-25):

```php
'category' => 'smlfy',
'icon'     => 'admin-customizer',
'align'    => 'full',
'supports' => ['align' => ['full'], 'mode' => true, 'jsx' => true],
```

---

## 4. PHP template conventions (template-parts/sections/{slug}.php)

Header comment block — copy this shape every time:

```php
<?php
/**
 * Block: {Human Title}
 * Registered as: acf/{slug-with-dashes}
 * Source: WheelLab Website (Figma) — node {id} ("{node name}")
 * Assets: build/css/sections/{slug}.min.css
 *         build/js/sections/{slug}.min.js   (omit line if no JS)
 */
```

Body rules, all confirmed across every current block:

- `get_field()` not `the_field()` — always capture to a variable, `?: ''` /
  `?: []` / `?: null` default, then guard.
- **Early bail-out is normal**: if the block's minimum required data is
  missing, `return;` before any markup (`stats_showcase_section.php`,
  `cta_banner_section.php`, `faq_section.php` all do this — a block with no
  slides/title/items renders nothing rather than an empty shell).
- Always forward block chrome:
  ```php
  $class  = '{slug-with-dashes}';
  $class .= !empty($block['className']) ? ' ' . $block['className']  : '';
  $class .= !empty($block['align'])     ? ' align' . $block['align'] : '';
  $id     = !empty($block['anchor'])    ? ' id="' . esc_attr($block['anchor']) . '"' : '';
  ```
- **Mobile that's a genuinely different layout, not a reflow**: usually mobile
  is the same elements reshaped via media queries (every block above does
  this). When the mobile Figma frame drops/adds whole pieces (e.g.
  `service_comparison_section`'s desktop drag-reveal-over-one-image vs.
  mobile's two independent plain cards, no chrome bar, no overlap) — don't
  fight it into one DOM tree. Render both as siblings
  (`.{slug}__desktop` / `.{slug}__mobile`), toggle with a single
  `display: none` media query pair, and give the JS-driven one's elements
  classes the JS only looks for — the other tree then needs zero JS
  awareness (no matchMedia gating) since the init selector simply never
  matches it.
- Escaping: `esc_html()` plain text, `esc_url()` URLs, `esc_attr()` attributes,
  `wp_kses_post()` only for WYSIWYG fields, `nl2br(esc_html(...))` for
  `textarea` fields (never `wpautop()` on those — wraps in unwanted `<p>`).
- Root element class = slug in kebab-case; children use BEM
  (`{slug}__part`, `{slug}__part--modifier`).
- `.container` wraps content directly UNLESS the section is deliberately
  full-bleed background with an inner `.container` (see
  `stats_showcase_section.php`'s bg/circle/logos siblings outside `.container`,
  content inside it).
- Decorative images get `alt=""` + `aria-hidden="true"`; content images get
  real `alt` from ACF (`$image['alt'] ?: $image['title']`).
- Fallback pattern for CPT context: a block placed inside a CPT's content can
  read the *post* as a fallback when its own field is empty — e.g.
  `case_study_hero.php` falls back to `has_post_thumbnail()` when its own
  "Background Image" field is unset. Reuse this for Service blocks placed
  inside `service` posts.

---

## 5. ACF field pattern quick-pick

Pick one (or combine) per block — full JSON skeletons in
[acf-block-patterns.md §3](acf-block-patterns.md#3-standard-field-patterns):

| Pattern | Use when | Real example |
|---|---|---|
| A — Text Block | heading + optional wysiwyg body + CTA | — |
| B — Repeater | list of cards/stats/items, `layout: block` or `table` | `case_study_cpt` stats, `stats_showcase_section` slides, `faq_section` items |
| C — Media + Text | image/video next to a text column, optional side toggle | — |
| D — Settings/Toggle | select/true_false flags controlling layout variant | `faq_section`'s `width` (post/full) select |

Every field group's `location` rule:
```json
"location": [[{ "param": "block", "operator": "==", "value": "acf/{slug-with-dashes}" }]]
```
Field keys are namespaced per block with a short prefix, e.g. `field_stss_*`
for `stats_showcase_section`, `field_csc_*` for the case-study CPT group —
pick a 3-5 letter acronym of the slug and stay consistent within that group.

`wrapper.width` (percent, e.g. `"33"`, `"50"`, `"100"`) is used throughout to
lay out the admin field UI in rows — set it on every field, even top-level
ones.

---

## 6. Field type rules (from acf-block-patterns.md §6 — keep these two in sync)

| Data | ACF type | Rendering rule |
|---|---|---|
| Phone | `text` | Strip to `+`+digits for `href="tel:..."`, display raw value |
| Email | `email` | `antispambot()` before output, `mailto:` |
| Link (internal/external) | `link`, `return_format: array` | `$link['url']/['title']/['target']`, never split url+label text fields |
| Icon / SVG | `image`, `return_format: array` | `<img>` tag; only inline raw SVG (`file_get_contents`) if it must recolor via `currentColor` |
| Multi-line text | `textarea` | `nl2br(esc_html(...))`, never `wpautop()` |
| Rich text | `wysiwyg` | `wp_kses_post()` |

---

## 7. SCSS conventions

- `@use "../partials/variables";` at the top if breakpoints are needed.
- Breakpoints: `variables.$mobile` (480), `variables.$small` (576),
  `variables.$medium` (768), `variables.$large` (992) — literal SCSS values,
  never CSS custom properties (can't be used inside `@media`).
- Every color/spacing/typography value comes from a `var(--token)` in
  `_tokens.scss` — never a raw hex/px. Key token families (Figma-named,
  don't invent new ones without checking `_tokens.scss` first):
  - `--bg-main-background`, `--bg-main-surface-1/2`, `--bg-solid-accent(-2)`,
    `--bg-ghost-*` — backgrounds
  - `--content-main-primary/secondary/tertiary`, `--content-solid-accent(-2)`,
    `--content-inv-*` — text/icon color
  - `--text-{style}-size`, `--text-{style}-line-height`,
    `--text-{style}-letter-spacing` (display-1/2/3, h1-4, body-m/s, etc. — see
    CLAUDE.md's Type Scale table)
  - `--font-family-display` (Funnel Display, headings), `--font-family-body`
    (Inter, everything else), `--font-weight-regular/bold`
- Fixed-height text swaps (marquee/carousel-style) use fluid `clamp()` sized
  off viewport width instead of a breakpoint step function when the
  section's own layout also scales continuously with viewport (see
  `stats_showcase_section.scss`'s long comment on why — avoids a width
  range where text overflows its allotted box).
- Motion driven by JS (rAF/WAAPI) must never also get a competing CSS
  `transition` on the same property.
- `@media (prefers-reduced-motion: reduce)` — stop/skip decorative
  animation; both CSS (`animation: none`) and JS (skip to resting state)
  sides should check it independently.

---

## 8. CPTs and how blocks attach to them

| CPT | File | Slug | Archive | Templates |
|---|---|---|---|---|
| Case Study | `inc/cpt_cases.php` | `case_study` | `/cases/` | `single-case_study.php`, `archive-case_study.php` |
| Service | `inc/cpt_services.php` | `service` | `/services/` | `single-service.php` (minimal — no fixed section yet, just `the_content()`) |

Pattern demonstrated by Case Study (replicate for Service):
- The CPT itself gets one ACF field group for data that isn't block-shaped
  (e.g. `group_case_study_cpt.json`: short description + stats repeater,
  location rule `post_type == case_study`) — rendered directly by
  `single-case_study.php`/`archive-case_study.php`, NOT through a block.
- Page-building content on the single CPT view goes through real ACF blocks
  placed in the post's `the_content()` (e.g. `case_study_hero`,
  `case_study_about_section`, ...) exactly like a normal Page — the CPT
  template just calls `the_content()` and lets the editor arrange blocks.
- The archive template hand-rolls its own card grid (reusing
  `case_study_section`'s own `.case-study-section__card*` classes for visual
  consistency) — there is no ACF block for "archive card", it's plain PHP
  querying `have_posts()`.

---

## 9. Current block inventory (inc/acf_blocks.php, as of 2026-08-27)

General-purpose (usable on any page):
`hero_section`, `reviews_section`, `contact_section`, `featured_posts_section`,
`solutions_section`, `domains_section`, `tile_section`, `about_section`,
`case_study_section` (a *listing* block — different from the CPT-context
blocks below), `faq_section`, `ai_highlight_section`, `table_section`,
`cta_banner_section`, `stats_showcase_section`

`faq_section` extended 2026-08-27 for the Service page's node
806:11724 — itself a reused Figma instance of this same component, not
a new design. "Show Header" now renders a plain centered outside-card
H2 in "full" width mode instead of the original inside-card icon+title
row; a new optional "Load More Button" field adds a centered button
below the card using the same recipe as the existing per-item CTA
button.

Case Study CPT-context (`case_study_` prefix, meant for use inside a
`case_study` post but not restricted to it):
`case_study_hero`, `case_study_about_section`, `case_study_quote_section`,
`case_study_showcase_section`, `case_study_tabs_section`,
`case_study_screens_section`, `case_study_what_we_did_section`

Service CPT-context (`service_` prefix): `service_hero`, `service_manifesto_section`
(reveal-on-scroll statement block — heading starts shifted down,
diamond+subheading start at opacity:0, all three settle/fade in via
IntersectionObserver + .is-visible once ~30% scrolled into view, same
pattern as `case_study_quote_section.js`; Figma's own two-variant
Smart Animate was reproduced as translateY/opacity/font-size deltas on
a normal flex column rather than literal absolute-position keyframes;
no bundled fallback background image — see the PHP template's own
header comment for why), `service_feature_cards`,
`service_comparison_section`, `service_process_deck`, `service_industry_tiles`,
`service_capability_cards` (plain wrapping-row repeater — reuses
`.solutions-section__card*`'s bezel/glow/image/title-row/arrow recipe
byte-for-byte, incl. the same corner arrow SVG, but as static flex-wrap
instead of Swiper; own 640px card height vs. Solutions Section's 720px;
per-card image has no fallback — bespoke art only; link is optional,
card renders as `<div>` and drops the corner arrow when absent)

`case_study_section` (existing homepage block, general-purpose — see
above) was also placed on the Service demo page 2026-08-27 for node
806:11730, itself a reused Figma instance of the same component. No
code changes — it already pulls real `case_study` CPT posts
automatically (title/thumbnail/description/stats), so this was just
adding the block with its default title/description copy (which
already matches this node's own text verbatim) and `cases_source:
latest`.

`contact_section` (existing homepage block, general-purpose) was also
placed on the Service demo page 2026-08-27, no code changes and no
per-block field overrides — every field falls back to Theme Options >
Contact when empty (title, description, badges, CF7 form shortcode all
already set there), so adding the block with empty data was enough.

---

## 10. Picking a JS approach (library vs. vanilla)

This codebase leans hard toward vanilla JS for one-off interactions and
reserves real dependencies for the cases they're actually good at:

| Need | Reach for | Why |
|---|---|---|
| Multi-item horizontal carousel, peek/overflow, swipe | **Swiper** (`assets/swiper/`, already loaded sitewide) | Genuinely non-trivial (touch physics, snap, a11y) — every carousel block (`solutions_section`, `case_study_section`, `service_feature_cards`) already uses it, same config shape each time |
| Modal/fullscreen media viewer (image or video, with its own gallery nav) | **GLightbox** (`glightbox` is already in `package.json` `dependencies` but **not yet vendored/wired up** — no `assets/glightbox/`, no enqueue) | If a block genuinely needs a popup viewer, wire it up the same way Swiper was: copy `node_modules/glightbox/dist/` into `assets/glightbox/`, enqueue conditionally like `wheellab_should_load_swiper()` does |
| Accordion, tabs, drag-to-reveal, thumbnail swap, mobile menu, anything single-purpose and already bespoke-styled | **Vanilla JS** | `faq_section.js` (accordion), `case_study_tabs_section.js` (tab crossfade), `service_comparison_section.js` (Pointer Events + `clip-path` drag reveal) — a library saves little when the visible chrome (buttons, handles) is 100% custom-styled anyway, and adds a dependency for something a few dozen lines of vanilla JS already does correctly. Default to this unless the interaction has real physics/a11y complexity a library has already solved well (that's the Swiper case).

When genuinely unsure which bucket a new interaction falls into, ask —
it's a real architectural fork, not a formatting nit.

---

## 10a. The header-pull-up hero pattern's short-viewport gotcha

`hero_section`, `case_study_hero`, and `service_hero` all share the same
"pull up behind the sticky header" shape: `margin-top: -96px;
min-height: calc(100vh + 96px);` (`-72px`/`72px` at `$small`), so the
section's top lines up with the true page top behind the header, and
content is bottom-anchored inside it via `justify-content: flex-end`.

That `+96px` supplement is necessary for the pull-up math, but it also
means the section's own bottom edge — and therefore the bottom-anchored
CTA content pinned against it — sits ~96px **past** one real viewport
height on *every* viewport, not just ones with unusually long copy. On
a normal tall-ish viewport this is invisible (the background's own
gradient already fades to solid `var(--bg-main-background)` well before
that point, so it's just empty background color). On a wide-but-short
window — a maximized browser on a 1440p monitor, not an edge case —
there isn't enough headroom to absorb it, and the real buttons end up
sitting on or past the fold.

Fixed for `service_hero` 2026-08-27 with a scoped safety valve:
```scss
@media screen and (min-width: variables.$small + 1px) and (height <= 1440px) {
    min-height: 100vh; // drop the +96 supplement
    padding-bottom: 40px;
}
```
Scoped to `min-width: $small + 1px` deliberately — the `$small` block
already has its own correct, different offset math (`-72px`) and its
own zero-outer-padding/`.container`-padding split; mobile portrait
viewports don't have this problem anyway (tall relative to width).

`hero_section` and `case_study_hero` share the identical root-cause
math and would likely benefit from the same fix, but weren't touched —
out of scope unless asked, same as the sitewide mobile-overflow issue
noted elsewhere in this file.

---

## 10b. WP-CLI demo-content gotcha: `<`/`&` leaking as literal "u003c"/"u0026"

Found 2026-08-27 on `service_capability_cards`, `service_process_deck`,
and `service_industry_tiles`' demo content on post 208: text containing
`&` or WYSIWYG HTML (`<ul><li>`) rendered on the live page as literal
text — `u0026`, `u003c`, `u003e` — instead of `&`/`<`/`>`. Both
`wp_json_encode()` (forces `JSON_HEX_*` flags, escaping these to
`<` etc.) and plain PHP `json_encode()` (no forced escaping —
should leave real characters as real characters) produced this same
symptom once run through a `wp_insert_post()` call building a **new**
post from an existing block's JSON. Root cause not fully pinned down
(something in the block-attribute save pipeline appears to mangle
these specific escape sequences on `wp_insert_post()` — `wp_update_post()`
against the SAME already-existing post did not reproduce it), so treat
this as an open gotcha, not a solved one.

What actually worked: fix the data directly on the real target post via
`wp_update_post()` + `preg_replace()` on its existing `post_content`
(see the pattern already used for `faq_section`'s answer-content fix),
then **verify by curling the real post's own live URL** — grep the
actual rendered HTML for `u003c`/`u0026` — rather than trusting a
`wp_insert_post()`-built isolated preview copy, which can show the
corruption even when the real post is already fine. This bit both the
initial demo-content authoring AND the first verification attempt
during the fix itself.

---

## 11. Pre-flight checklist (condensed from acf-block-patterns.md §5)

```
[ ] Figma node studied (layout, states, motion) — node ID noted in PHP header
[ ] snake_case slug chosen, {context}_ prefixed if CPT-specific
[ ] Slug added to $blocks in inc/acf_blocks.php
[ ] template-parts/sections/{slug}.php — get_field + guard + escape + block chrome
[ ] acf-json/group_{slug}.json — location: block == acf/{slug-dashed}
[ ] src/scss/sections/{slug}.scss — BEM, tokens, $medium/$small breakpoints
[ ] src/js/sections/{slug}.js — only if interactive
[ ] npm run dev — block appears, title correct, fields work, preview renders
[ ] Frontend: CSS/JS load only on pages using the block, no console errors
[ ] Mobile breakpoints checked
```
