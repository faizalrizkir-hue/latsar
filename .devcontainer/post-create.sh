#!/usr/bin/env bash

set -euo pipefail

cd "${WORKSPACE_FOLDER:-$(pwd)}"

# Always prefer container PHP over legacy Oryx PHP.
export PATH="/usr/bin:/usr/local/bin:/bin:${PATH}"
hash -r

if [ ! -f .env ]; then
  cp .env.example .env
fi

# Set APP_URL automatically for Codespaces forwarded domain.
if [ -n "${CODESPACE_NAME:-}" ] && [ -n "${GITHUB_CODESPACES_PORT_FORWARDING_DOMAIN:-}" ]; then
  APP_URL_VALUE="https://${CODESPACE_NAME}-8000.${GITHUB_CODESPACES_PORT_FORWARDING_DOMAIN}"
  if grep -q "^APP_URL=" .env; then
    sed -i "s#^APP_URL=.*#APP_URL=${APP_URL_VALUE}#" .env
  else
    echo "APP_URL=${APP_URL_VALUE}" >> .env
  fi

  if grep -q "^ASSET_URL=" .env; then
    sed -i "s#^ASSET_URL=.*#ASSET_URL=${APP_URL_VALUE}#" .env
  else
    echo "ASSET_URL=${APP_URL_VALUE}" >> .env
  fi
fi

mkdir -p database
if [ ! -f database/database.sqlite ]; then
  touch database/database.sqlite
fi

composer install --no-interaction --prefer-dist

if [ -f package-lock.json ]; then
  npm ci
else
  npm install
fi

if ! grep -q "^APP_KEY=base64:" .env; then
  php artisan key:generate --force
fi

php artisan migrate --graceful --force
php artisan db:seed --force
php artisan storage:link || true

chmod -R ug+rw storage bootstrap/cache || true

echo ""
echo "Codespaces setup complete."
echo "Run app server: php -d display_errors=0 artisan serve --host=0.0.0.0 --port=8000"
echo "Run Vite dev:   npm run dev -- --host 0.0.0.0 --port 5173"
echo "Default login:  admin / admin123"
echo "MySQL sync:     bash scripts/codespaces/import-mysql-dump.sh ./latsar.sql"
