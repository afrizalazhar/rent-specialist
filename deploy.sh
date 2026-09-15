#!/usr/bin/env bash
# =============================================================================
# cPanel post-pull deploy script
# =============================================================================
# Runs on the cPanel server after `git pull` (invoked by cPanel's Git
# Version Control webhook).
#
# Architecture:
#   1. cPanel Git pulls source from GitHub (this repo, no vendor/, no build/)
#   2. GitHub Actions builds vendor/ and public/build/ in CI, packages them
#      into build-artifacts.tar.gz, and uploads via FTPS to this directory
#   3. This script waits for build-artifacts.tar.gz, extracts it over the
#      source tree, and runs the artisan commands that don't need proc_open
#
# Why this design:
#   - The cPanel server's PHP has proc_open disabled, so composer and any
#     artisan command that spawns a subprocess fails. Everything that needs
#     a subprocess is built in CI instead.
#   - Repo stays small (no vendor/, no public/build/ in source control).
#   - Tarball upload is a single FTPS PUT — no folder-creation overhead.
#
# Requirements on the cPanel server:
#   - PHP 8.4 CLI (for artisan migrate / optimize / etc.)
#   - bash, tar (standard)
#
# Logging:
#   Every run is appended to storage/logs/deploy.log (timestamped).
#   Read it from cPanel File Manager: storage/logs/deploy.log
# =============================================================================

set -euo pipefail

cd "$(dirname "$0")"

mkdir -p storage/logs
LOG_FILE="storage/logs/deploy.log"

log() {
  printf '%s [%s] %s\n' "$(date -u +%Y-%m-%dT%H:%M:%SZ)" "$$" "$*"
}

# --- Start logging (append stdout+stderr to log file) -----------------------
# cPanel's deploy env doesn't support process substitution (no /dev/fd),
# so we just append to the log file. For live SSH monitoring, run
# `tail -f storage/logs/deploy.log` in a second terminal.
exec >>"$LOG_FILE" 2>&1

log "Deploy started"
log "PHP: $(php -v 2>/dev/null | head -1 || echo 'php not found')"
log "Working directory: $(pwd)"

# --- 1. .env bootstrap (first run only) -------------------------------------
if [ ! -f .env ]; then
  log "Creating .env from .env.example (first deploy)"
  cp .env.example .env
  log "WARN: .env was just created. Edit it on the server via cPanel File Manager,"
  log "      set DB creds / APP_KEY / etc., then re-run this script (or pull again)."
  exit 0
fi

# --- 2. Wait for build artifacts tarball from CI -----------------------------
TARBALL="build-artifacts.tar.gz"
MAX_WAIT="${DEPLOY_TARBALL_TIMEOUT:-300}"  # 5 min default; cPanel task timeout

if [ -f "$TARBALL" ]; then
  log "Tarball found (size: $(du -h "$TARBALL" | cut -f1))"
else
  log "Waiting for $TARBALL from GitHub Actions (max ${MAX_WAIT}s)..."
fi

WAITED=0
while [ ! -f "$TARBALL" ] && [ $WAITED -lt $MAX_WAIT ]; do
  sleep 10
  WAITED=$((WAITED + 10))
done

if [ ! -f "$TARBALL" ]; then
  log "ERROR: $TARBALL not found after ${MAX_WAIT}s"
  log "  Check the GitHub Actions workflow run for build status."
  log "  Without the tarball, vendor/ and public/build/ are missing and the"
  log "  site will not boot. Re-run the workflow, then pull on the server:"
  log "    cd ~/rent-specialist && ./deploy.sh"
  exit 1
fi

# --- 3. Extract build artifacts ---------------------------------------------
log "Extracting $TARBALL"
tar -xzf "$TARBALL"
rm -f "$TARBALL"
log "Build artifacts extracted"

# --- 4. Storage symlink (first run only) ------------------------------------
if [ ! -L public/storage ]; then
  log "Running: php artisan storage:link"
  php artisan storage:link
fi

# --- 5. Database migrations -------------------------------------------------
log "Running: php artisan migrate --force"
php artisan migrate --force --no-interaction

# --- 6. Optimize (config / route / view / event caches) --------------------
log "Running: php artisan optimize"
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache
php artisan filament:cache

log "Deploy finished"