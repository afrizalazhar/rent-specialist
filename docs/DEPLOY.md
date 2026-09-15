# Deploying to cPanel

Deployment is done by **cPanel's Git Version Control** feature: each push to
GitHub triggers a webhook, cPanel pulls the latest commit on the server,
and runs `deploy.sh` to install dependencies and refresh caches.

GitHub Actions is used only for CI (tests + lint), not for deployment.

---

## Prerequisites (one-time, on the cPanel server)

1. **SSH / Terminal access** — cPanel → *Security* → *SSH Access*. Most
   shared hosts require you to explicitly enable it.
2. **PHP 8.4 CLI** — cPanel → *MultiPHP Manager*. Set the domain (or the
   directory you deploy to) to `ea-php84`. Without 8.4, the deploy script
   will fail.
3. **Composer** — available in cPanel *Terminal* on most hosts; otherwise
   install per Composer docs.
4. **Node.js + npm** — cPanel → *Setup Node.js App* (or *Node.js
   Selector*). Create an app with Node 20.x. Note the activation command
   it shows (`source .../enable.sh`) — paste it into the deploy script if
   npm is not on the default PATH (see Troubleshooting).

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
Developer settings → PATs, **classic**, scope: `repo`) and paste it as
the password when prompted.

After creation, cPanel shows a **Manage** page. Open it.

---

## Step 2 — Set the document root

cPanel → *Domains* → pick the domain → change **Document Root** to:

```
/home/<cpanel-user>/rent-specialist/public
```

(Or symlink: `public_html/public` → `../rent-specialist/public`.)

---

## Step 3 — Paste the deploy script

In cPanel → *Git™ Version Control* → *Manage* → **Deployment script**,
paste the **contents** of `deploy.sh` from this repo (cPanel versions
v100–v106 only accept an inline script, not a file path).

The script:
- Installs Composer deps (no dev)
- Runs `npm ci && npm run build` (skipped with a warning if npm missing)
- Runs `php artisan migrate --force`
- Caches config / routes / views / events / filament
- Creates `public/storage` symlink and `.env` on first run

---

## Step 4 — Add the webhook in GitHub

In cPanel's Git™ manage page, copy the **Webhook URL** it displays. Then
in GitHub:

*Settings → Webhooks → Add webhook*

| Field         | Value                                  |
|---------------|----------------------------------------|
| Payload URL   | (the URL from cPanel)                  |
| Content type  | `application/json`                     |
| Events        | Just the *push* event                  |
| SSL verify    | enabled                                |

Save. The first push to `main` after this will arrive at cPanel within a
few seconds.

---

## Step 5 — First-time server setup

Trigger an initial deploy (push a commit, or click **Pull** in cPanel's
Git manage page). Then SSH into the server:

```bash
cd ~/rent-specialist

# Edit .env with real DB / mail credentials
nano .env

# Generate APP_KEY (replace the placeholder line in .env)
php artisan key:generate --force

# Make storage writable
chmod -R 775 storage bootstrap/cache

# Confirm the symlink exists
ls -la public/storage
```

Visit the domain — the app should be live.

---

## Troubleshooting

**`npm: command not found` in deploy log**
Node.js is installed but not on the default PATH in cPanel's deploy
environment. Edit `deploy.sh` and prepend the activation line from
*Setup Node.js App*, e.g.:

```bash
source /home/<user>/nodevenv/rent-specialist/20/bin/activate
```

**`Your lock file does not contain a compatible set of packages`**
The server's PHP is older than the lock requires. Set the document-root
directory to `ea-php84` in MultiPHP Manager. Verify with `php -v` in
Terminal while inside the deploy directory.

**Deploy runs but the site shows the default cPanel page**
Document root is wrong. Re-check Step 2.

**Webhook not firing**
In cPanel → Git™ manage → *History* tab, check whether pulls are
arriving. If they are, the webhook is fine. If not, regenerate the
webhook URL in cPanel and re-add it in GitHub.

**Composer runs out of memory**
Some shared hosts cap PHP memory. In cPanel → *MultiPHP INI Editor* →
set `memory_limit = 512M` for the deploy directory.

---

## Manual deploy

If you ever need to deploy without pushing to GitHub (e.g. emergency hotfix):

```bash
ssh <user>@<server>
cd ~/rent-specialist
git pull origin main
./deploy.sh
```

---

## Why this is faster than FTP upload

cPanel Git pulls are delta-only — only changed files transfer, no
folder-creation round trips. First deploy takes ~30 s; subsequent deploys
are seconds. Composer install and the Vite build run server-side, so
GitHub Actions minutes drop to a few seconds of CI only.