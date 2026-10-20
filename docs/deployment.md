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

Production is **not** touched by the workflow; it is updated by hand over SSH.
It lives on the same SiteGround account as stage and is a git checkout of the
`deploy` branch (`origin` = this repo), so releasing is a fast-forward pull:

```bash
ssh -p <port> <user>@<host>        # credentials: SiteGround > Devs > SSH Keys Manager
cd ~/www/wheellab.io/public_html/wp-content/themes/wheellab-1.1.13
git status                         # should be clean; manual edits on the server would block the pull
git pull --ff-only
git log --oneline -1               # "build: theme from <sha>" of the master commit you released
cd ../../..
wp eval 'do_action("breeze_clear_all_cache");'
```

Then purge Cloudflare (Purge Everything). Without both purges, cached page HTML
keeps serving the previous version even though the files on disk are new.

Wait until the "Publish deploy branch" run for your commit is green before pulling:
the pull only sees what has already been published to `deploy`.

Do not put the SSH host, user or key in this repo (it is public); they live in the
repository secrets (stage) and in SiteGround.

## After every production release

- Purge the Breeze cache (`wp eval` above) and Cloudflare, otherwise browsers keep the previous build (the `?ver=` stamp is shared and cached for a year).
- If a change added or edited an ACF field group in `acf-json/`: Custom Fields → **Sync available** → sync.
- Spot-check the changed pages on production, not only on stage.

## Gotchas

- `build/` is tracked in git. Finish a change with `npm run build` (not only `gulp` dev tasks), otherwise dozens of unrelated `build/` files show as modified.
- Open Graph / Twitter tags are printed by Rank Math. The theme prints its own only when Rank Math is not active (`wheellab_opengraph_meta_tags()` in `inc/theme_function.php`). The default share image is set in Rank Math → Titles & Meta → Global Meta → Open Graph Thumbnail.
- GitHub warns that `actions/checkout@v4` / `actions/setup-node@v4` run on Node 24 and that `ubuntu-latest` moves to Ubuntu 26 (Oct 2026). Both are non-blocking; bump the action versions when convenient.
