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
3. **Composer** — auto-bootstrapped. If your cPanel doesn't ship with
   `composer`, `deploy.sh` downloads `composer.phar` into the project on
   the first run. Nothing to install.
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

## Step 3 — Deploy task configuration (.cpanel.yml)

cPanel v110+ reads `.cpanel.yml` from the repo root to know what to run
after each pull. This repo already includes one:

```yaml
deployment:
  tasks:
    - /bin/bash deploy.sh
```

`deploy.sh` is the actual deploy script (also committed in this repo).
It will:
- Install Composer deps (no dev)
- Run `npm ci && npm run build` (skipped with a warning if npm missing)
- Run `php artisan migrate --force`
- Cache config / routes / views / events / filament
- Create `public/storage` symlink and `.env` on first run

No further configuration is required on the cPanel side — once the Git
repo is cloned (Step 1) and the webhook is in place (Step 4), cPanel
will pick up `.cpanel.yml` automatically.

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

---

## Monitoring and troubleshooting

### 1. Did the webhook fire? (GitHub)

GitHub repo → *Settings* → *Webhooks* → click the cPanel webhook →
**Recent deliveries**. Each push should appear within a few seconds with
a green ✓. If the delivery is red, expand it — the response body is
cPanel's error message.

### 2. Did cPanel pull? (cPanel)

cPanel → *Files* → *Git™ Version Control* → *Manage* → **History** tab.
Lists every pull with timestamp and commit hash. If the list is empty,
the webhook never reached cPanel.

### 3. Read the deploy log (no SSH needed)

`deploy.sh` appends every run to:

```
storage/logs/deploy.log
```

Open it in **cPanel → File Manager → navigate to your deploy directory
→ storage/logs/deploy.log → View / Edit**.

Each run starts with `Deploy started`, lists PHP / Composer / Node
versions, then logs each step. The last line of a successful run is
`Deploy finished`. A failed run ends mid-step with the error.

### 4. Run deploy.sh by hand (live output)

If you have Terminal / SSH access, open **two terminals**:

```bash
# Terminal 1: watch the log live
cd ~/rent-specialist
tail -f storage/logs/deploy.log

# Terminal 2: trigger the deploy
cd ~/rent-specialist
bash deploy.sh
```

cPanel's deploy environment doesn't support the bash process
substitution needed to mirror stdout to both terminal and log, so the
script writes to the log file only. `tail -f` gives you the same live
view.

### 5. Laravel application logs

If the deploy succeeds but the site errors at runtime, Laravel's own log
captures it:

```
storage/logs/laravel.log
```

Same File Manager location as `deploy.log`.

### 6. Web server errors

cPanel → *Metrics* → *Errors* shows Apache's error log. Useful when
the site returns 500 / blank page. Look for `public/index.php` or
Laravel-specific stack traces.