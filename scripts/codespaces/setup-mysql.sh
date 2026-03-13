#!/usr/bin/env bash

set -euo pipefail

ROOT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")/../.." && pwd)"
cd "$ROOT_DIR"

DB_NAME="${DB_NAME:-latsar}"
DB_USER="${DB_USER:-latsar}"
DB_PASSWORD="${DB_PASSWORD:-latsar123}"
DB_HOST="${DB_HOST:-127.0.0.1}"
DB_PORT="${DB_PORT:-3306}"
APT_UPDATED=0
PHP_CMD=""

set_env_value() {
  local key="$1"
  local value="$2"

  if grep -q "^${key}=" .env 2>/dev/null; then
    sed -i "s#^${key}=.*#${key}=${value}#" .env
  else
    echo "${key}=${value}" >> .env
  fi
}

ensure_apt_update() {
  if [ "$APT_UPDATED" -eq 1 ]; then
    return 0
  fi

  if ! sudo apt-get update; then
    echo "[codespaces] apt update failed. Trying to disable broken Yarn repo..."
    if [ -f /etc/apt/sources.list.d/yarn.list ]; then
      sudo mv /etc/apt/sources.list.d/yarn.list /etc/apt/sources.list.d/yarn.list.disabled
    fi
    sudo apt-get update
  fi

  APT_UPDATED=1
}

resolve_php() {
  if [ -x "${ROOT_DIR}/scripts/codespaces/php-bin.sh" ]; then
    PHP_CMD="$("${ROOT_DIR}/scripts/codespaces/php-bin.sh" 0 || true)"
  fi

  if [ -z "$PHP_CMD" ] && command -v php >/dev/null 2>&1; then
    PHP_CMD="$(command -v php)"
  fi

  if [ -z "$PHP_CMD" ]; then
    echo "[codespaces] ERROR: PHP binary not found." >&2
    exit 1
  fi
}

has_pdo_mysql() {
  "$PHP_CMD" -m 2>/dev/null | grep -qi "^pdo_mysql$"
}

enable_existing_pdo_mysql_if_available() {
  local ext_dir=""
  local scan_dir_raw=""
  local scan_dir=""
  local ini_file=""
  local candidate=""

  ext_dir="$("$PHP_CMD" -i 2>/dev/null | awk -F'=> ' '/^extension_dir => / {print $2; exit}' | awk '{print $1}' || true)"
  if [ -z "$ext_dir" ]; then
    ext_dir="$("$PHP_CMD" -r 'echo ini_get("extension_dir");' 2>/dev/null || true)"
  fi

  if [ -z "$ext_dir" ] || [ ! -f "${ext_dir}/pdo_mysql.so" ]; then
    return 0
  fi

  scan_dir_raw="$("$PHP_CMD" --ini 2>/dev/null | awk -F': ' '/Scan for additional \.ini files in:/{print $2; exit}' || true)"

  IFS=':' read -r -a scan_dir_candidates <<< "$scan_dir_raw"
  for candidate in "${scan_dir_candidates[@]}"; do
    if [ -n "$candidate" ] && [ "$candidate" != "(none)" ]; then
      scan_dir="$candidate"
      break
    fi
  done

  if [ -z "$scan_dir" ]; then
    scan_dir="/usr/local/etc/php/conf.d"
  fi

  ini_file="${scan_dir}/99-pdo-mysql.ini"

  echo "[codespaces] Found pdo_mysql.so in ${ext_dir}, ensuring ini is enabled..."
  if [ ! -d "$scan_dir" ]; then
    sudo mkdir -p "$scan_dir" || mkdir -p "$scan_dir"
  fi

  if [ ! -f "$ini_file" ] || ! grep -qi "pdo_mysql" "$ini_file"; then
    (echo "extension=pdo_mysql" | sudo tee "$ini_file" >/dev/null) || echo "extension=pdo_mysql" >"$ini_file"
  fi
}

try_install_with_docker_php_ext() {
  local installer=""
  local enabler=""

  installer="$(command -v docker-php-ext-install 2>/dev/null || true)"
  enabler="$(command -v docker-php-ext-enable 2>/dev/null || true)"

  [ -n "$installer" ] || installer="/usr/local/bin/docker-php-ext-install"
  [ -n "$enabler" ] || enabler="/usr/local/bin/docker-php-ext-enable"

  if [ -x "$installer" ]; then
    echo "[codespaces] Installing pdo_mysql via docker-php-ext-install..."
    sudo env PATH="$PATH" "$installer" pdo_mysql || "$installer" pdo_mysql || true
  fi

  if [ -x "$enabler" ]; then
    echo "[codespaces] Enabling pdo_mysql via docker-php-ext-enable..."
    sudo env PATH="$PATH" "$enabler" pdo_mysql || "$enabler" pdo_mysql || true
  fi
}

try_install_with_install_php_extensions() {
  local installer=""
  installer="$(command -v install-php-extensions 2>/dev/null || true)"
  [ -n "$installer" ] || installer="/usr/local/bin/install-php-extensions"

  if [ -x "$installer" ]; then
    echo "[codespaces] Installing pdo_mysql via install-php-extensions..."
    sudo env PATH="$PATH" "$installer" pdo_mysql mysqli || "$installer" pdo_mysql mysqli || true
  fi
}

echo "[codespaces] Preparing MySQL (MariaDB) service..."
resolve_php

if ! command -v mysql >/dev/null 2>&1; then
  echo "[codespaces] Installing mariadb-server and mariadb-client..."
  ensure_apt_update
  sudo DEBIAN_FRONTEND=noninteractive apt-get install -y mariadb-server mariadb-client
fi

if ! has_pdo_mysql; then
  enable_existing_pdo_mysql_if_available
fi

if ! has_pdo_mysql; then
  try_install_with_docker_php_ext
fi

if ! has_pdo_mysql; then
  enable_existing_pdo_mysql_if_available
fi

if ! has_pdo_mysql; then
  try_install_with_install_php_extensions
fi

if ! has_pdo_mysql; then
  enable_existing_pdo_mysql_if_available
fi

if ! has_pdo_mysql; then
  echo "[codespaces] Trying apt package fallback for pdo_mysql..."
  ensure_apt_update
  if ! sudo DEBIAN_FRONTEND=noninteractive apt-get install -y php8.2-mysql; then
    if ! sudo DEBIAN_FRONTEND=noninteractive apt-get install -y php-mysql; then
      echo "[codespaces] WARNING: apt fallback packages not available on this image."
    fi
  fi
  if command -v phpenmod >/dev/null 2>&1; then
    sudo phpenmod pdo_mysql mysqli mysqlnd >/dev/null 2>&1 || true
  fi
fi

if ! has_pdo_mysql && [ -x /usr/bin/php ] && /usr/bin/php -m 2>/dev/null | grep -qi "^pdo_mysql$"; then
  PHP_CMD="/usr/bin/php"
fi

if command -v service >/dev/null 2>&1; then
  sudo service mariadb start >/dev/null 2>&1 || sudo service mysql start >/dev/null 2>&1 || true
fi

sudo mysql <<SQL
CREATE DATABASE IF NOT EXISTS \`${DB_NAME}\` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER IF NOT EXISTS '${DB_USER}'@'localhost' IDENTIFIED BY '${DB_PASSWORD}';
GRANT ALL PRIVILEGES ON \`${DB_NAME}\`.* TO '${DB_USER}'@'localhost';
FLUSH PRIVILEGES;
SQL

if [ ! -f .env ]; then
  cp .env.example .env
fi

set_env_value "DB_CONNECTION" "mysql"
set_env_value "DB_HOST" "${DB_HOST}"
set_env_value "DB_PORT" "${DB_PORT}"
set_env_value "DB_DATABASE" "${DB_NAME}"
set_env_value "DB_USERNAME" "${DB_USER}"
set_env_value "DB_PASSWORD" "${DB_PASSWORD}"

"$PHP_CMD" artisan config:clear >/dev/null 2>&1 || true
"$PHP_CMD" artisan cache:clear >/dev/null 2>&1 || true

if ! has_pdo_mysql; then
  echo "[codespaces] ERROR: pdo_mysql extension is still missing." >&2
  echo "[codespaces] PHP in use: ${PHP_CMD}" >&2
  "$PHP_CMD" --ini >&2 || true
  "$PHP_CMD" -m | grep -Ei '^pdo$|pdo_mysql|mysqli|mysqlnd' >&2 || true
  exit 1
fi

if [ -f "$HOME/.bashrc" ] && ! grep -q "LATSAR_PHP_BIN" "$HOME/.bashrc"; then
  cat >>"$HOME/.bashrc" <<EOF

# LATSAR_PHP_BIN
export PATH="$(dirname "$PHP_CMD"):/usr/bin:/usr/local/bin:/bin:\${PATH}"
EOF
fi

echo "[codespaces] MySQL ready."
echo "[codespaces] PHP in use: ${PHP_CMD}"
echo "[codespaces] Database: ${DB_NAME}, User: ${DB_USER}, Host: ${DB_HOST}:${DB_PORT}"
