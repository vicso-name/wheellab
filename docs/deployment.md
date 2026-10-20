# Deployment

## Branches

| Branch   | Contents                                                        | Who writes it            |
|----------|-----------------------------------------------------------------|--------------------------|
| `master` | Sources (`src/`, gulp, linters) plus the committed `build/`     | developers               |
| `deploy` | The runtime theme only: what belongs in `wp-content/themes/<theme>/` | GitHub Actions (never by hand) |

The server never sees `master`. It only ever pulls `deploy`.

## What happens on every push to `master`

Workflow: [.github/workflows/deploy.yml](../.github/workflows/deploy.yml) ("Publish deploy branch").
It can also be started by hand: GitHub → Actions → Publish deploy branch → Run workflow.

1. `npm ci`, then `npm run build`. The build lints first, so a lint error stops everything before anything is published.
2. The theme is assembled with `rsync`, minus everything listed in [.deployignore](../.deployignore) (a denylist: a new file ships unless it is named there; a new dev-only file must be added to it).
3. The result is committed to `deploy` as `build: theme from <sha>`. If nothing changed, the run ends with "nothing to publish".
4. **Stage only:** the workflow SSHes into stage, runs `git pull --ff-only` in the theme folder, then purges the Breeze cache (`breeze_clear_all_cache`) so Cloudflare re-fetches the new CSS/JS.

Stage is `https://stage.wheellab.io/`. Its SSH host, user, port, key and path are repository secrets (`SSH_HOST`, `SSH_USER`, `SSH_PORT`, `SSH_KEY`, `DEPLOY_PATH`) because the repo is public.

Running the workflow again on the same commit only re-pulls and re-purges stage; it does not touch production.

## Production

> **TODO — fill in.** The workflow does not deploy production, and nothing in this repo describes how it is done (FTPS was mentioned). Write down here, once confirmed:
>
> - what triggers it (tool / repo / hosting panel / manual FTP client) and where it is started;
> - what it uploads: it must be the `deploy` branch (a built theme), never `master`;
> - the post-deploy steps below.

## After every production release

- Purge the Breeze cache and Cloudflare, otherwise browsers keep the previous build (the `?ver=` stamp is shared and cached for a year).
- If a change added or edited an ACF field group in `acf-json/`: Custom Fields → **Sync available** → sync.
- Spot-check the changed pages on production, not only on stage.

## Gotchas

- `build/` is tracked in git. Finish a change with `npm run build` (not only `gulp` dev tasks), otherwise dozens of unrelated `build/` files show as modified.
- Open Graph / Twitter tags are printed by Rank Math. The theme prints its own only when Rank Math is not active (`wheellab_opengraph_meta_tags()` in `inc/theme_function.php`). The default share image is set in Rank Math → Titles & Meta → Global Meta → Open Graph Thumbnail.
- GitHub warns that `actions/checkout@v4` / `actions/setup-node@v4` run on Node 24 and that `ubuntu-latest` moves to Ubuntu 26 (Oct 2026). Both are non-blocking; bump the action versions when convenient.
