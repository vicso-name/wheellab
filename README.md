# wheellab — ACF Gutenberg Theme Boilerplate

A WordPress theme boilerplate where every page section is an independently registered ACF Gutenberg block with its own PHP template, SCSS, and JS.

---

## Requirements

- WordPress 5.x+ with **ACF Pro** installed and active
- PHP 7.4+
- Node.js 18+
- A local development server (MAMP, LocalWP, Laragon, etc.)

---

## First-time setup

```bash
git clone <repo-url> your-theme-name
cd your-theme-name
npm install
npm run setup
```

`npm run setup` will ask for your project name, slug, author details, and local dev URL, then replace every boilerplate identifier across the codebase and commit the result. The script deletes itself when done.

> If `scripts/setup.mjs` does not exist, setup has already been run on this project — skip straight to Development.

---

## Development

```bash
npm run dev      # compile + BrowserSync + file watchers
npm run build    # lint → compile (minified, production)
```

BrowserSync proxies your local WordPress URL. If it differs from the default, update it in `gulpfile.js` on the `proxy` line.

---

## Adding a section

```bash
npm run new-section -- section_name          # SCSS + PHP + block registration
npm run new-section:js -- section_name       # same, also creates the JS file
```

Use `snake_case` for section names. After running the command, create the matching ACF field group in the WordPress admin (ACF → Field Groups → Location: Block → equals → the block title).

---

## Project structure

```
src/
├── scss/
│   ├── partials/          ← shared styles: tokens, variables, general, fonts
│   └── sections/          ← one .scss per ACF block (compile independently)
└── js/
    ├── general.js         ← global JS loaded on every page
    └── sections/          ← one .js per ACF block

template-parts/sections/   ← PHP templates for ACF blocks
inc/
├── acf_blocks.php         ← block registration + per-page asset detection
├── enqueue.php            ← global CSS/JS enqueue
└── theme_function.php     ← theme helpers
fonts/                     ← source font files (.woff/.woff2)
docs/                      ← developer reference
build/                     ← Gulp output (committed; CI rebuilds it for deploy)
```

---

## Code quality

```bash
npm run lint             # run SCSS + JS linters
npm run lint:scss:fix    # auto-fix SCSS violations
```

Linters run automatically as the first step of `npm run build`.

---

## Deployment

`master` holds the sources. The server never pulls it — it pulls **`deploy`**, a
branch that contains only the runtime theme: PHP templates, `build/`, `assets/`,
`fonts/`, `acf-json/`, `style.css` and `screenshot.png`. No `src/`, no Gulp, no
`node_modules`, no docs.

That branch is generated, never edited by hand. On every push to `master`,
`.github/workflows/deploy.yml` lints, builds, strips everything listed in
`.deployignore` and commits the result to `deploy`. A lint or build failure stops
the run, so a broken commit cannot reach the server.

Point the server's auto-deploy at the `deploy` branch and let it pull into the
theme folder:

```bash
cd wp-content/themes/wheellab
git pull origin deploy
```

The workflow commits onto the branch rather than force-pushing, so the server can
always fast-forward.

**Adding a development file?** Add it to `.deployignore`, or it ships. The list is
a denylist on purpose: new templates, includes and assets go live automatically,
which is the failure mode worth avoiding.

---

## Docs

- [ACF block patterns, field group recipes, and enqueue behaviour](docs/acf-block-patterns.md)
