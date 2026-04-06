#!/usr/bin/env bash
set -euo pipefail

PHP_BIN="${PHP_BIN:-php}"
CLEAR_FIRST=0

print_usage() {
  cat <<'USAGE'
Usage:
  bash scripts/deploy/optimize-runtime.sh [--clear-first]

Options:
  --clear-first  Jalankan optimize:clear sebelum optimize.
  -h, --help     Tampilkan bantuan.

Env:
  PHP_BIN        Path binary PHP (default: php).
USAGE
}

for arg in "$@"; do
  case "$arg" in
    --clear-first)
      CLEAR_FIRST=1
      ;;
    -h|--help)
      print_usage
      exit 0
      ;;
    *)
      echo "ERROR: opsi tidak dikenal: $arg"
      print_usage
      exit 1
      ;;
  esac
done

echo "== LATSAR runtime optimize =="
echo "PHP_BIN: ${PHP_BIN}"

if [[ $CLEAR_FIRST -eq 1 ]]; then
  echo "[1/3] php artisan optimize:clear"
  "$PHP_BIN" artisan optimize:clear
else
  echo "[1/2] skip optimize:clear"
fi

if [[ $CLEAR_FIRST -eq 1 ]]; then
  echo "[2/3] php artisan optimize"
else
  echo "[2/2] php artisan optimize"
fi
"$PHP_BIN" artisan optimize

echo "php artisan ops:schema-cache:bump (best effort)"
if ! "$PHP_BIN" artisan ops:schema-cache:bump; then
  echo "WARN: ops:schema-cache:bump gagal."
fi

echo "php artisan queue:restart (best effort)"
if ! "$PHP_BIN" artisan queue:restart; then
  echo "WARN: queue:restart gagal (queue worker mungkin belum aktif)."
fi

echo "Selesai."
