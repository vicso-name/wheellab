# ACF Blocks v3 / WordPress 7.1 Iframe Editor — Migration Guide (v2, final)

**Status:** Live and verified on lionwood (theme repo + `lionwood.smplfy.eu`), 2026-08-24. Applied to **wheellab** (`butterfly-theme-main`), 2026-08-25. Apply the same changes to **kasinot**, **fcs-theme**, **kcs-theme** (and any other project using the same `acf_register_block_type()` pattern).

This supersedes an earlier, more ambitious version of this migration that tried to render each block's real frontend markup inside the editor canvas. That approach worked but kept surfacing new canvas-only quirks to chase one at a time (see "What NOT to do" below) — this version replaces all of that with a much simpler, final design.

Give this file to a fresh Claude Code session (or any engineer) working on **any WordPress theme that registers custom blocks via `acf_register_block_type()`** and has a bug report like:

- "the block editor is broken / custom blocks can't be edited anymore"
- "clicking a block does nothing" / "no fields show up"
- "there's a red admin notice about updating ACF PRO"

## Root cause

**WordPress 7.1 removed the last way to opt out of the always-iframed block editor canvas.** Every post/page editor now renders the block canvas inside a real `<iframe>`, unconditionally — no theme filter, no classic-editor plugin, no setting brings back the old non-iframed canvas.

Themes that built a **custom inline field-editing UI** for ACF blocks (a collapsible form injected directly into the canvas) relied on the canvas NOT being an iframe. Once it's iframed, that JS/CSS (enqueued via `enqueue_block_editor_assets`, which only reaches the top-level admin document, never replayed into the iframe) simply stops working.

**ACF Pro 6.6+ ("Blocks v3")** is built to work inside this iframe, but only via its own UI: a toolbar "Edit" button that opens a modal, plus (unless restricted) a duplicate button + full fields form inline in the sidebar Inspector panel. **ACF Pro below 6.6** doesn't know about any of this and just silently fails to render fields in the iframe at all.

**There are only two real options:**
1. Permanently pin ACF Pro below 6.6 — a growing security/compatibility liability, not recommended.
2. Upgrade ACF Pro to ≥ 6.6 and adopt Blocks v3. **This guide documents option 2.**

## The final design, in one sentence

Block editing works entirely through ACF v3's own toolbar "Edit" modal; **the editor canvas never shows a block's real rendered content at all** — just a small icon + title placeholder card, so editors can see what block is where and click to edit it, without the theme having to make a live, unstyled-JS, un-animated frontend snapshot look presentable inside an iframe.

## Step 0 — locate the files, confirm the pattern matches

Don't assume file names/paths — locate them per project:

```bash
grep -rn "acf_register_block_type" wp-content/themes/<theme>/ --include=*.php
grep -rln "enqueue_block_editor_assets" wp-content/themes/<theme>/inc/ 2>/dev/null
```

If `acf_register_block_type` isn't found at all (block.json-based registration, or a completely different pattern), stop and re-assess — this guide assumes a PHP loop calling `acf_register_block_type()` per block, each with `render_template`, and blindly applying Step 3 to a different pattern won't do what you expect.

Also check the current ACF Pro version and WP core version before touching anything — they determine whether this bug is even present yet:

```bash
grep -n "Version:" wp-content/plugins/advanced-custom-fields-pro/acf.php | head -1
grep -n "wp_version = " wp-includes/version.php
```

## Step 1 — mu-plugin: force ACF Blocks v3

Create `wp-content/mu-plugins/acf_blocks_wp71_compat.php` (portable, drop-in — copy unchanged into any project). It lives **outside the theme's own git repo** (mu-plugins aren't theme-specific) — deploy it manually to every environment (local, staging, production), it does not travel with a `git pull`/PR merge.

The current, final version of this file (v1.3.0) is checked into `wheellab`'s repo at `wp-content/mu-plugins/acf_blocks_wp71_compat.php` — copy that file verbatim into every other project's `wp-content/mu-plugins/`. It:

- Forces every ACF block to `api_version`/`acf_block_version` 3 once ACF PRO ≥ 6.6 is active (gated — does nothing below that version, just shows an admin notice).
- On ACF PRO ≥ 6.8, also sets `mode: 'preview'`, `supports.mode: true`, `expanded_editor_buttons: ['toolbar']`, and `hide_fields_in_sidebar: true` on every block — this is what makes the "Edit" pencil open a modal instead of also duplicating the full fields form into the sidebar Inspector panel.
- Corrects `acf/settings/url` when a legacy theme bundled an old bundled ACF copy but is now running the standalone plugin (keeps PHP and browser JS/CSS on the same version).
- Replaces every ACF block's rendered preview inside the iframe canvas with a compact icon+title card (`enqueue_block_assets`, iframe-pass only) — see "The final design" above for why.
- Hides any duplicate fields form ACF still renders into the Inspector sidebar (`admin_enqueue_scripts`, top document only).
- Shows an admin-only notice if WP ≥ 7.1 but ACF PRO is below 6.6 (blocks broken, explains why) or below 6.8 (sidebar duplicate not yet suppressed).

**Do NOT also deploy `block_editor_reveal_animation_fix.php`** if you find it already sitting in a project's `mu-plugins/` (wheellab had one; it's been deleted there as of 2026-08-25). It existed to fix scroll-reveal animations / Swiper / clickable-links inside a *live-rendered* canvas preview — with Step 3 below removing the live preview entirely, there's nothing left for it to fix. Delete it if present in any other project too.

## Step 2 — server: update ACF Pro

**ACF Pro must be ≥ 6.6** on every environment (≥ 6.8 to get the toolbar-only modal, no sidebar duplicate). Nothing in Step 1 or Step 3 has any effect below 6.6 — every filter checks `smplfy_acf_blocks_v3_supported()` first — blocks stay broken exactly as before, just with an extra admin notice explaining why.

## Step 3 — theme code

Find this project's block-registration file (commonly `inc/acf_blocks.php`) and its asset-enqueue file (commonly `inc/enqueue.php`).

### 3a. Block registration — back to plain

The mu-plugin now sets `mode`, `supports.mode`, `expanded_editor_buttons`, and `hide_fields_in_sidebar` itself, globally, via the `acf/register_block_type_args` filter — **the theme does not need to set any of these per block.** Each block's registration should be the plain baseline:

```php
acf_register_block_type([
    'name'            => $block_name,
    'title'           => ucwords(str_replace('_', ' ', $block_name)),
    'render_template' => "template-parts/sections/{$block_name}.php",
    'category'        => 'smlfy', // project's own block category slug
    'icon'            => 'admin-customizer',
    'keywords'        => ['section', $block_name],
    'supports'        => [
        'align' => false,
        'mode'  => true,
        'jsx'   => true,
    ],
]);
```

Do **not** add `'align' => 'full'` or change `supports.align` to allow full/wide width, and don't add `add_theme_support('align-wide')` for this — an earlier iteration of this migration did, to make the (now-removed) live preview render at a readable size. With no live preview left to size, there's nothing to gain from it.

### 3b. Remove the old inline-toggle entirely

Delete, don't gate:
- The theme's collapsible-toggle JS (commonly `src/js/admin-scripts.js`) and SCSS (commonly `src/scss/acf-block-toggle.scss`).
- Their `enqueue_block_editor_assets` / `enqueue_block_assets` enqueue calls in the theme's enqueue file.
- Any Gulp/webpack build tasks compiling them, and their entries in `styles`/`scripts`/`watch` task groups.
- Any live-preview scaffolding (a `stylesEditorPreview`-style task piping the theme's frontend CSS into the iframe via `@scope`, an `editor-canvas-background.scss` file, etc.) — see "What NOT to do" below.

The mu-plugin's own `enqueue_block_assets`/`admin_enqueue_scripts` hooks (Step 1) fully replace what the theme's `enqueue_block_assets` hook used to do (re-enqueuing `acf-global`/`acf-input`/`acf-field-group`/`buttons`/`dashicons` into the iframe) — **the theme needs zero editor-canvas enqueue code of its own** once the mu-plugin is installed.

## What NOT to do (already tried on lionwood, abandoned)

Skip straight to Step 3 above. Don't re-derive any of this:

- **Don't** pipe the project's compiled frontend CSS into the iframe (`@scope (body) to (:where(.acf-fields))`, a `stylesEditorPreview` gulp task, etc.) to make a live block preview look right. It works, but only opens the door to the next three bullet points, each block by block, project by project.
- **Don't** write canvas-only fixes for decorative elements, carousels (`.swiper-wrapper` never initializing without its frontend JS), or scroll-reveal animations stuck at `opacity: 0`. All are consequences of trying to live-preview real frontend markup in an iframe with no frontend JS running — moot once there's no live preview.
- **Don't** add canvas-only CSS container-query hacks to individual sections (e.g. swapping a `vw`-based `clamp()` for `cqw` so a heading "looks right" at the iframe's narrower width) — same reasoning, moot once there's no live preview to size for.
- **Don't** deploy `block_editor_reveal_animation_fix.php` — same reasoning.
- **Don't** add `'align' => 'full'` / `supports.align` full-width / `add_theme_support('align-wide')` for these blocks — see 3a's note.

## A caveat that isn't a bug

If someone reports the block toolbar (drag handle, "Edit" pencil, ⋮ menu) sitting pinned at the very top of the canvas instead of floating right above the selected block — that's WordPress core's own **"Top Toolbar"** editor preference (Options ⋮ → Preferences → Appearance), stored per-browser/per-user, unrelated to any of this. Don't chase it as a regression from this migration.

## Verification checklist

- [ ] `php -l` every changed PHP file
- [ ] ACF Pro is ≥ 6.6 on the environment being tested (≥ 6.8 for toolbar-only, no sidebar duplicate)
- [ ] `wp-content/mu-plugins/acf_blocks_wp71_compat.php` is present on that environment
- [ ] Open the block editor for a page/post using several custom blocks — each shows as a small icon + title card, not a rendered preview
- [ ] Click a block: toolbar "Edit" pencil opens a modal; the sidebar Inspector shows **no** duplicate fields form when the block is selected
- [ ] Inside the modal, confirm repeaters/image pickers/link fields are fully styled (not bare unstyled inputs)
- [ ] Save and confirm the real, logged-out frontend is completely unchanged — none of this touches frontend-facing CSS/HTML at all
- [ ] Confirm no build task still references a deleted source file (`npm run build`/`gulp build` should not error on a missing `src/js/admin-scripts.js` or `src/scss/acf-block-toggle.scss`)

### Verify without a browser first (WP-CLI)

Faster than reasoning about it, and catches most mistakes before ever opening wp-admin. Needs local DB access — usually already true for a local dev copy of the site. Run from the site root (`wp-load.php`'s directory), not the theme directory:

```bash
# Confirm a specific block resolves to v3 + expanded_editor_buttons, e.g.:
wp eval '
$bt = WP_Block_Type_Registry::get_instance()->get_registered("acf/your-block-name");
echo "api_version=" . var_export($bt->api_version ?? null, true) . "\n";
echo "expanded_editor_buttons=" . var_export($bt->expanded_editor_buttons ?? null, true) . "\n";
'

# Confirm the placeholder CSS + re-enqueued ACF/buttons/dashicons styles
# actually land in what gets sent to the iframe (this is the exact
# mechanism WordPress core uses to build the canvas's own <head>):
wp eval '
if (!defined("WP_ADMIN")) define("WP_ADMIN", true);
$result = _wp_get_iframed_editor_assets();
$s = $result["styles"];
foreach (["acf-block-preview[data-type", "acf-input", "buttons.min.css", "dashicons.min.css"] as $needle) {
    echo "$needle: " . (strpos($s, $needle) !== false ? "YES" : "NO") . "\n";
}
'
```

## Per-project notes

| Project | Path | Status |
|---|---|---|
| lionwood | `lionwood.smplfy.eu` theme repo | Done — reference implementation, 2026-08-24 |
| wheellab | `C:\laragon\www\wheellab\wp-content\themes\butterfly-theme-main` | **Done** — 2026-08-25. Old toggle (`admin-scripts.js`, `acf-block-toggle.scss`) and the live-preview scaffolding (`stylesEditorPreview`/`stylesEditorCanvasBg` gulp tasks, `editor-canvas-background.scss`, `hero_section.scss`'s cqw hack, `align-wide` theme support) fully removed. `mu-plugins/acf_blocks_wp71_compat.php` updated to v1.3.0; `block_editor_reveal_animation_fix.php` deleted. |
| kasinot | `C:\laragon\www\kasinot\wp-content\themes\kasinot` | Not yet started |
| kcs-theme | `C:\laragon\www\klartcasino\wp-content\themes\kcs-theme` | Not yet started |
| fcs-theme | `C:\laragon\www\fcs-stage.local\wp-content\themes\fcs-theme` | Not yet started — this project is currently pinned to ACF Pro 6.2.7 (below 6.6) with the old `mode: 'edit'` inline-toggle behavior instead, see `ACF-BLOCK-EDITOR-FIX-PLAN.md` in that repo. Revisit whether to move it onto this v3 migration instead of staying pinned. |

Before starting on any of these: confirm the block registration function name and pattern actually matches (`acf_register_block_type()` + `render_template`), current ACF Pro version, and current WP core version — the fix checklist above assumes all three match wheellab's/lionwood's starting point.

## Deployment — don't assume any one project's mechanism

Each project has its own deploy setup (git-based, its own FTP script, a hosting panel, etc.). Find out how *this* project actually ships code before writing or running anything — check for a `scripts/` dir, `package.json` deploy-related npm scripts, a `README`/`CLAUDE.md`/`AGENTS.md` deploy section, or just ask. Same goes for how the mu-plugin file specifically gets onto each environment (it's never part of a theme's own git repo or its normal deploy path, by design).
