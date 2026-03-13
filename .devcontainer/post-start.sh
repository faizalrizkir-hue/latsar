#!/usr/bin/env bash

set -euo pipefail

if ! command -v php >/dev/null 2>&1 && [ ! -x /usr/bin/php ] && [ ! -x /usr/local/bin/php ]; then
  exit 0
fi

cd "${WORKSPACE_FOLDER:-$(pwd)}"

BASHRC="$HOME/.bashrc"
touch "$BASHRC"
PHP_USER_CONF_DIR="$HOME/.config/php/conf.d"
PHP_USER_CONF_FILE="$PHP_USER_CONF_DIR/99-latsar-codespaces.ini"

mkdir -p "$PHP_USER_CONF_DIR"
cat >"$PHP_USER_CONF_FILE" <<'EOF'
; Keep CLI-server transport notices (e.g. broken pipe) out of rendered HTML.
display_errors=Off
log_errors=On
error_reporting=E_ALL
EOF

# Remove stale Oryx PHP PATH entries that can break php runtime.
sed -i '/\/home\/codespace\/\.php\/current\/bin/d' "$BASHRC" || true

if ! grep -q "LATSAR_FORCE_CONTAINER_PHP" "$BASHRC"; then
  cat >>"$BASHRC" <<'EOF'

# LATSAR_FORCE_CONTAINER_PHP
export PATH="/usr/bin:/usr/local/bin:/bin:${PATH}"
hash -r
EOF
fi

if ! grep -q "LATSAR_PHP_INI_SCAN_DIR" "$BASHRC"; then
  cat >>"$BASHRC" <<'EOF'

# LATSAR_PHP_INI_SCAN_DIR
export PHP_INI_SCAN_DIR="/usr/local/etc/php/conf.d:/home/vscode/.config/php/conf.d"
EOF
fi

# Keep APP_URL / ASSET_URL aligned with Codespaces forwarded host so css/js/images
# are loaded from the public app.github.dev domain instead of localhost.
if [ -f .env.example ] && [ ! -f .env ]; then
  cp .env.example .env
fi

if [ -f .env ] && [ -n "${CODESPACE_NAME:-}" ] && [ -n "${GITHUB_CODESPACES_PORT_FORWARDING_DOMAIN:-}" ]; then
  PUBLIC_URL="https://${CODESPACE_NAME}-8000.${GITHUB_CODESPACES_PORT_FORWARDING_DOMAIN}"

  if grep -q "^APP_URL=" .env; then
    sed -i "s#^APP_URL=.*#APP_URL=${PUBLIC_URL}#" .env
  else
    echo "APP_URL=${PUBLIC_URL}" >> .env
  fi

  if grep -q "^ASSET_URL=" .env; then
    sed -i "s#^ASSET_URL=.*#ASSET_URL=${PUBLIC_URL}#" .env
  else
    echo "ASSET_URL=${PUBLIC_URL}" >> .env
  fi

  php artisan optimize:clear >/dev/null 2>&1 || true
fi
