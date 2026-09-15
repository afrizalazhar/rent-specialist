# Deploying to cPanel

Deployment uses a hybrid setup:

- **cPanel's Git Version Control** pulls source code on every push
  (no `vendor/`, no `public/build/` — those are built in CI).
- **GitHub Actions** builds the artifacts, packages them into a single
  tarball, and uploads via FTPS.
- **Server-side `deploy.sh`** waits for the tarball, extracts it over
  the git-pulled source, and runs the artisan commands that don't
  need `proc_open`.

This works on shared cPanel hosts that have `proc_open` disabled
(which blocks Composer and any subprocess-spawning artisan command).

---

## Prerequisites (one-time, on the cPanel server)

1. **SSH / Terminal access** — cPanel → *Security* → *SSH Access*.
   Most shared hosts require you to enable it explicitly.
2. **PHP 8.4 CLI** — cPanel → *MultiPHP Manager*. Set the domain (or
   the deploy directory) to `ea-php84`. Without 8.4, artisan and the
   lock file won't agree.
3. **An FTP account** for the Actions workflow to upload to. Use the
   main cPanel FTP account or create a dedicated one in *Files →
   FTP Accounts*.
4. **No Composer, no Node.js required on the server** — everything
   that needs them runs in CI.

---

## Step 1 — Create the repo in cPanel

cPanel → *Files* → *Git™ Version Control* → **Create**.

| Field          | Value                                       |
|----------------|---------------------------------------------|
| Clone URL      | `https://github.com/<user>/<repo>.git`      |
| Repository name| `rent-specialist`                           |
| Destination    | `/home/<cpanel-user>/rent-specialist`       |
| Branch         | `main`                                      |

For HTTPS auth: create a GitHub Personal Access Token (Settings →
Developer settings → PATs, **classic**, scope: `repo`) and paste it
as the password when prompted.

---

## Step 2 — Set the document root

cPanel → *Domains* → pick the domain → change **Document Root** to:

```
/home/<cpanel-user>/rent-specialist/public
```

(Or symlink `public_html/public` → `../rent-specialist/public`.)

---

## Step 3 — Deploy task (`.cpanel.yml`)

`.cpanel.yml` at the repo root tells cPanel to run `deploy.sh` after
each pull. Already committed in this repo:

```yaml
deployment:
  tasks:
    - /bin/bash deploy.sh
```

`deploy.sh` will (after the build tarball is in place):
- Extract `vendor/` and `public/build/` from the tarball
- Run `php artisan migrate --force`
- Cache config / routes / views / events / filament
- Create `public/storage` symlink and `.env` on first run

---

## Step 4 — Add the webhook in GitHub

In cPanel's Git™ manage page, copy the **Webhook URL** it shows. Then
in GitHub: *Settings → Webhooks → Add webhook*

| Field         | Value                                  |
|---------------|----------------------------------------|
| Payload URL   | (the URL from cPanel)                  |
| Content type  | `application/json`                     |
| Events        | Just the *push* event                  |
| SSL verify    | enabled                                |

Save. Each push to `main` reaches it within seconds.

---

## Step 5 — GitHub Actions secrets

Repo → *Settings → Secrets and variables → Actions → New repository
secret*. Add:

| Name            | Value                                          |
|-----------------|------------------------------------------------|
| `FTP_SERVER`    | `ftp.example.com` (or your cPanel hostname)    |
| `FTP_USERNAME`  | cPanel FTP user                                |
| `FTP_PASSWORD`  | cPanel FTP password                            |
| `FTP_SERVER_DIR`| `/home/<cpanel-user>/rent-specialist`          |

---

## Step 6 — First-time server setup

Trigger an initial deploy (push any commit, or click **Pull** in
cPanel's Git manage page — but wait for the Actions run to finish
and upload the tarball first). Then SSH in:

```bash
cd ~/rent-specialist

# Verify the tarball is present
ls -lh build-artifacts.tar.gz

# Pull the source if not already done
# (cPanel's Pull button or: git pull origin main)

# Run deploy.sh manually to see live output
bash deploy.sh

# Edit .env with real DB / mail credentials
nano .env

# Generate APP_KEY (replaces the placeholder in .env)
php artisan key:generate --force

# Make storage writable
chmod -R 775 storage bootstrap/cache

# Confirm the symlink exists
ls -la public/storage
```

Visit the domain — the app should be live.

---

## Troubleshooting

**Deploy log shows `Waiting for build-artifacts.tar.gz ... ERROR`**
GitHub Actions did not finish (or failed) before the 5-minute wait
expired. Check the Actions run for errors. Re-run the workflow once
it passes, then in the server run `./deploy.sh` manually — the
tarball will already be in place.

**`proc_open` / `Process class relies on proc_open` errors**
Should not happen any more — all subprocess-spawning work is in CI.
If you still see it, an artisan command is unexpectedly spawning a
process; check `php artisan` docs for the specific command.

**Deploy succeeds but the site is blank / 500**
- `vendor/` or `public/build/` missing → check the tarball arrived
- `.env` missing or wrong credentials → fix in cPanel File Manager
- Document root wrong → re-check Step 2

**FTPS upload fails in the Actions log**
Wrong FTP credentials, wrong path, or TLS mismatch. Verify the
values in *Settings → Secrets and variables → Actions*. cPanel's
default FTPS uses port 21 with explicit TLS — the action handles
this automatically when `protocol: ftps`.

**cPanel's Git webhook not firing**
In cPanel → Git™ manage → *History* tab — if empty, the webhook
never arrived. Regenerate the webhook URL in cPanel and re-add it
in GitHub.

**Manual deploy without waiting for CI**
Useful for hotfixes. SSH in and run:

```bash
cd ~/rent-specialist
git pull origin main
bash deploy.sh
```

This works only if the CI build for the current commit has already
uploaded its tarball (otherwise deploy.sh errors out after the wait).
If you're deploying without CI, you must upload a pre-built
tarball yourself (FTP it to `build-artifacts.tar.gz` in the deploy
directory) before running deploy.sh.

---

## How a deploy runs end to end

1. You push a commit to `main`.
2. **GitHub** sends a webhook to **cPanel** → cPanel `git pull`s the
   source code into `~/rent-specialist`.
3. **GitHub Actions** starts: `composer install --no-dev`, `npm ci`,
   `npm run build`, packages `vendor/` + `public/build/` into
   `build-artifacts.tar.gz`, uploads via FTPS to
   `~/rent-specialist/build-artifacts.tar.gz`.
4. **cPanel** runs `.cpanel.yml` → `deploy.sh`.
5. `deploy.sh` waits up to 5 min for the tarball to appear, then
   extracts it, runs `migrate --force`, refreshes caches.
6. Site is live at the new commit.

Typical total time: 3–5 minutes from push to live (mostly waiting
on `composer install` and `npm ci` in CI).

---

## Monitoring and debugging

### Did the webhook fire?

GitHub → repo → *Settings* → *Webhooks* → click the cPanel webhook
→ **Recent deliveries**. Green ✓ = ok; red ✗ = response body shows
the error.

### Did cPanel pull?

cPanel → *Files* → *Git™ Version Control* → *Manage* → **History**.
Lists every pull with timestamp and commit hash.

### Did Actions build and upload succeed?

GitHub → repo → **Actions** tab → the latest run. Each step shows
✓ or ✗. The upload step's log shows the tarball size and FTPS
response.

### Read the deploy log (no SSH needed)

`deploy.sh` appends every run to:

```
storage/logs/deploy.log
```

Open it in **cPanel → File Manager → ~/rent-specialist/storage/logs/
deploy.log → View / Edit**.

### Watch deploy.sh live (SSH)

```bash
# Terminal 1
cd ~/rent-specialist && tail -f storage/logs/deploy.log

# Terminal 2
cd ~/rent-specialist && bash deploy.sh
```

### Laravel / Apache logs

If the deploy succeeded but the site errors at runtime:

```
storage/logs/laravel.log   # application errors
```

cPanel → *Metrics* → *Errors* shows Apache's error log.