#!/usr/bin/env bash
# =============================================================================
# cPanel post-pull deploy script
# =============================================================================
# Runs on the cPanel server after `git pull` (invoked by cPanel's Git
# Version Control webhook / pull hook).
#
# What it does:
#   1. Copy .env.example -> .env on first run only
#   2. Download composer.phar on first run (only if no system composer)
#   3. composer install (production deps, optimized autoloader)
#   4. npm ci + npm run build if Node.js is available on the server
#   5. php artisan storage:link on first run only
#   6. php artisan migrate --force
#   7. php artisan optimize (config/route/view/event cache)
#
# Requirements on the cPanel server:
#   - PHP 8.4 CLI (matching composer.json / workflow PHP version)
#   - Composer (auto-bootstrapped into ./composer.phar on first run if
#     the host doesn't ship with one)
#   - Node.js + npm (cPanel: "Setup Node.js App" / Node.js Selector)
#
# Logging:
#   Every run is appended to storage/logs/deploy.log (timestamped).
#   You can read this file from cPanel File Manager without SSH.
# =============================================================================

set -euo pipefail

cd "$(dirname "$0")"

mkdir -p storage/logs
LOG_FILE="storage/logs/deploy.log"

log() {
  printf '%s [%s] %s\n' "$(date -u +%Y-%m-%dT%H:%M:%SZ)" "$$" "$*"
}

# --- Start logging (tee stdout+stderr to log file) --------------------------
exec > >(tee -a "$LOG_FILE") 2>&1

log "Deploy started"
log "PHP: $(php -v 2>/dev/null | head -1 || echo 'php not found')"
log "Node: $(node -v 2>/dev/null || echo 'node not found')"
log "Working directory: $(pwd)"

# --- 1. .env bootstrap (first run only) -------------------------------------
if [ ! -f .env ]; then
  log "Creating .env from .env.example (first deploy)"
  cp .env.example .env
  log "WARN: .env was just created. Edit it on the server before the next pull,"
  log "      or set DB creds / APP_KEY etc. via cPanel File Manager."
fi

# --- 2. Composer bootstrap (downloads composer.phar on first run) ----------
if [ ! -f composer.phar ]; then
  log "composer.phar missing — downloading (one-time)"
  EXPECTED_CHECKSUM="$(php -r 'copy("https://composer.github.io/installer.sig", "php://stdout");')"
  php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
  ACTUAL_CHECKSUM="$(php -r "echo hash_file('sha384', 'composer-setup.php');")"
  if [ "$EXPECTED_CHECKSUM" != "$ACTUAL_CHECKSUM" ]; then
    log "ERROR: Composer installer checksum mismatch"
    log "  expected: $EXPECTED_CHECKSUM"
    log "  actual:   $ACTUAL_CHECKSUM"
    rm -f composer-setup.php
    exit 1
  fi
  php composer-setup.php --quiet --filename=composer.phar
  rm -f composer-setup.php
  log "composer.phar installed"
fi

log "Composer: $(php composer.phar --version 2>/dev/null | head -1)"

# --- 3. Composer dependencies ----------------------------------------------
log "Running: composer install --no-dev"
php composer.phar install \
  --no-dev \
  --optimize-autoloader \
  --no-interaction \
  --prefer-dist

# --- 4. Frontend assets -----------------------------------------------------
if command -v npm >/dev/null 2>&1; then
  log "Running: npm ci"
  npm ci --no-audit --no-fund
  log "Running: npm run build"
  npm run build
else
  log "WARN: npm not found on PATH. Skipping JS build."
  log "      Enable Node.js in cPanel via 'Setup Node.js App' or Node.js Selector."
fi

# --- 5. Storage symlink (first run only) -----------------------------------
if [ ! -L public/storage ]; then
  log "Running: php artisan storage:link"
  php artisan storage:link
fi

# --- 6. Database migrations -------------------------------------------------
log "Running: php artisan migrate --force"
php artisan migrate --force --no-interaction

# --- 7. Optimize (config / route / view / event caches) --------------------
log "Running: php artisan optimize"
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache
php artisan filament:cache

log "Deploy finished"