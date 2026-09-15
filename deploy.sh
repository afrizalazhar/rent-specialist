#!/usr/bin/env bash
# =============================================================================
# cPanel post-pull deploy script
# =============================================================================
# Runs on the cPanel server after `git pull` (invoked by cPanel's Git
# Version Control webhook / pull hook).
#
# What it does:
#   1. composer install (production deps, optimized autoloader)
#   2. npm ci + npm run build if Node.js is available on the server
#   3. php artisan migrate --force
#   4. php artisan optimize (config/route/view/event cache)
#   5. php artisan storage:link on first run only
#   6. Copy .env.example -> .env on first run only
#
# Requirements on the cPanel server:
#   - PHP 8.4 CLI (matching composer.json / workflow PHP version)
#   - Composer (cPanel ships with it via "Terminal")
#   - Node.js + npm (cPanel: "Setup Node.js App" / Node.js Selector)
#
# To enable in cPanel:
#   Git Version Control -> Manage repo -> "Deployment script" -> paste this
#   file's contents, or set the path to this file (depends on cPanel
#   version: some only accept an inline script, some accept a file path).
# =============================================================================

set -euo pipefail

cd "$(dirname "$0")"

echo "==> Deploy started at $(date -u +%Y-%m-%dT%H:%M:%SZ)"

# --- 1. .env bootstrap (first run only) -------------------------------------
if [ ! -f .env ]; then
  echo "==> Creating .env from .env.example (first deploy)"
  cp .env.example .env
  echo "WARN: .env was just created. Edit it on the server before the next pull,"
  echo "      or set FTP_SERVER / DB creds / APP_KEY etc. via cPanel File Manager."
fi

# --- 2. Composer dependencies ----------------------------------------------
echo "==> composer install --no-dev"
composer install \
  --no-dev \
  --optimize-autoloader \
  --no-interaction \
  --prefer-dist

# --- 3. Frontend assets -----------------------------------------------------
if command -v npm >/dev/null 2>&1; then
  echo "==> npm ci"
  npm ci --no-audit --no-fund
  echo "==> npm run build"
  npm run build
else
  echo "WARN: npm not found on PATH. Skipping JS build."
  echo "      Enable Node.js in cPanel via 'Setup Node.js App' or Node.js Selector."
fi

# --- 4. Storage symlink (first run only) -----------------------------------
if [ ! -L public/storage ]; then
  echo "==> php artisan storage:link"
  php artisan storage:link
fi

# --- 5. Database migrations -------------------------------------------------
echo "==> php artisan migrate --force"
php artisan migrate --force --no-interaction

# --- 6. Optimize (config / route / view / event caches) --------------------
echo "==> php artisan optimize"
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache
php artisan filament:cache

echo "==> Deploy finished at $(date -u +%Y-%m-%dT%H:%M:%SZ)"