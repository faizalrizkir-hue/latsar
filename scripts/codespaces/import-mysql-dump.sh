#!/usr/bin/env bash

set -euo pipefail

ROOT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")/../.." && pwd)"
cd "$ROOT_DIR"

DB_NAME="${DB_NAME:-latsar}"
DB_USER="${DB_USER:-latsar}"
DB_PASSWORD="${DB_PASSWORD:-latsar123}"
PHP_CMD=""

resolve_php() {
  if [ -x "${ROOT_DIR}/scripts/codespaces/php-bin.sh" ]; then
    PHP_CMD="$("${ROOT_DIR}/scripts/codespaces/php-bin.sh" 1 || true)"
  fi

  if [ -z "$PHP_CMD" ] && command -v php >/dev/null 2>&1; then
    PHP_CMD="$(command -v php)"
  fi

  if [ -z "$PHP_CMD" ]; then
    echo "[codespaces] ERROR: PHP binary not found." >&2
    exit 1
  fi
}

resolve_dump_path() {
  local input_path="${1:-}"

  if [ -n "$input_path" ]; then
    if [ -f "$input_path" ]; then
      printf '%s\n' "$input_path"
      return 0
    fi

    if [ -f "${ROOT_DIR}/${input_path}" ]; then
      printf '%s\n' "${ROOT_DIR}/${input_path}"
      return 0
    fi
  fi

  local candidates=(
    "${ROOT_DIR}/latsar.sql"
    "${ROOT_DIR}/latsar.sql.gz"
    "${ROOT_DIR}/latsar"
    "${ROOT_DIR}/latsar.gz"
  )

  for candidate in "${candidates[@]}"; do
    if [ -f "$candidate" ]; then
      printf '%s\n' "$candidate"
      return 0
    fi
  done

  local first_sql
  first_sql="$(find "$ROOT_DIR" -maxdepth 1 -type f \( -name '*.sql' -o -name '*.sql.gz' \) | head -n 1 || true)"
  if [ -n "$first_sql" ]; then
    printf '%s\n' "$first_sql"
    return 0
  fi

  return 1
}

DUMP_PATH="$(resolve_dump_path "${1:-}" || true)"

if [ -z "$DUMP_PATH" ]; then
  echo "[codespaces] Dump not found."
  echo "Usage:"
  echo "  bash scripts/codespaces/import-mysql-dump.sh /workspaces/<repo>/latsar.sql"
  echo ""
  echo "Supported auto-detect names in repo root:"
  echo "  latsar.sql, latsar.sql.gz, latsar, latsar.gz, or first *.sql/*.sql.gz file"
  exit 1
fi

echo "[codespaces] Using dump: $DUMP_PATH"

bash scripts/codespaces/setup-mysql.sh
resolve_php

import_sql_stream() {
  # Force import into target DB by stripping DB-switch statements from dump.
  sed -E '/^[[:space:]]*CREATE[[:space:]]+DATABASE[[:space:]]+/Id; /^[[:space:]]*USE[[:space:]]+/Id'
}

if [[ "$DUMP_PATH" == *.gz ]]; then
  echo "[codespaces] Importing compressed dump..."
  gzip -dc "$DUMP_PATH" | import_sql_stream | mysql -u "$DB_USER" "-p${DB_PASSWORD}" "$DB_NAME"
else
  echo "[codespaces] Importing dump..."
  cat "$DUMP_PATH" | import_sql_stream | mysql -u "$DB_USER" "-p${DB_PASSWORD}" "$DB_NAME"
fi

"$PHP_CMD" artisan migrate --graceful --force
"$PHP_CMD" artisan db:seed --class=AccountsSeeder --force
"$PHP_CMD" artisan optimize:clear

TABLE_COUNT="$(mysql -u "$DB_USER" "-p${DB_PASSWORD}" "$DB_NAME" -Nse "SELECT COUNT(*) FROM information_schema.tables WHERE table_schema='${DB_NAME}'" 2>/dev/null || echo 0)"
echo "[codespaces] Tables in ${DB_NAME}: ${TABLE_COUNT}"

echo "[codespaces] Import completed."
echo "[codespaces] You can run:"
echo "  ${PHP_CMD} -d display_errors=0 -d xdebug.mode=off artisan serve --host=0.0.0.0 --port=8000"
