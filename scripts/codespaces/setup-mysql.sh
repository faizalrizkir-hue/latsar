#!/usr/bin/env bash

set -euo pipefail

ROOT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")/../.." && pwd)"
cd "$ROOT_DIR"

DB_NAME="${DB_NAME:-latsar}"
DB_USER="${DB_USER:-latsar}"
DB_PASSWORD="${DB_PASSWORD:-latsar123}"
DB_HOST="${DB_HOST:-127.0.0.1}"
DB_PORT="${DB_PORT:-3306}"

set_env_value() {
  local key="$1"
  local value="$2"

  if grep -q "^${key}=" .env 2>/dev/null; then
    sed -i "s#^${key}=.*#${key}=${value}#" .env
  else
    echo "${key}=${value}" >> .env
  fi
}

echo "[codespaces] Preparing MySQL (MariaDB) service..."

if ! command -v mysql >/dev/null 2>&1; then
  echo "[codespaces] Installing mariadb-server and mariadb-client..."
  sudo apt-get update
  sudo DEBIAN_FRONTEND=noninteractive apt-get install -y mariadb-server mariadb-client
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

php artisan config:clear >/dev/null 2>&1 || true
php artisan cache:clear >/dev/null 2>&1 || true

echo "[codespaces] MySQL ready."
echo "[codespaces] Database: ${DB_NAME}, User: ${DB_USER}, Host: ${DB_HOST}:${DB_PORT}"
