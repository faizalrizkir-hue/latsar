#!/usr/bin/env bash
set -euo pipefail

ENV_FILE=".env"
REQUIRE_REALTIME=1

print_usage() {
  cat <<'USAGE'
Usage:
  bash scripts/deploy/preflight.sh [ENV_FILE] [--skip-realtime]

Options:
  --skip-realtime  Lewati validasi kredensial Reverb (untuk hosting tanpa websocket).
  -h, --help       Tampilkan bantuan.
USAGE
}

env_file_set=0
for arg in "$@"; do
  case "$arg" in
    --skip-realtime)
      REQUIRE_REALTIME=0
      ;;
    -h|--help)
      print_usage
      exit 0
      ;;
    --*)
      echo "ERROR: opsi tidak dikenal: $arg"
      print_usage
      exit 1
      ;;
    *)
      if [[ $env_file_set -eq 1 ]]; then
        echo "ERROR: hanya boleh satu ENV_FILE."
        print_usage
        exit 1
      fi
      ENV_FILE="$arg"
      env_file_set=1
      ;;
  esac
done

if [[ ! -f "$ENV_FILE" ]]; then
  echo "ERROR: file '$ENV_FILE' tidak ditemukan."
  exit 1
fi

errors=0
warnings=0

get_env_value() {
  local key="$1"
  local line
  line="$(grep -E "^[[:space:]]*${key}=" "$ENV_FILE" | tail -n 1 || true)"
  if [[ -z "$line" ]]; then
    echo ""
    return
  fi

  local value="${line#*=}"
  value="${value%$'\r'}"
  value="${value%\"}"
  value="${value#\"}"
  echo "$value"
}

require_not_empty() {
  local key="$1"
  local value
  value="$(get_env_value "$key")"
  if [[ -z "$value" ]]; then
    echo "ERROR: ${key} kosong atau belum diisi."
    errors=$((errors + 1))
  fi
}

require_equals() {
  local key="$1"
  local expected="$2"
  local value
  value="$(get_env_value "$key")"
  if [[ "$value" != "$expected" ]]; then
    echo "ERROR: ${key} harus '${expected}', nilai saat ini: '${value}'."
    errors=$((errors + 1))
  fi
}

warn_if_not_equals() {
  local key="$1"
  local expected="$2"
  local value
  value="$(get_env_value "$key")"
  if [[ "$value" != "$expected" ]]; then
    echo "WARN: ${key} disarankan '${expected}', nilai saat ini: '${value}'."
    warnings=$((warnings + 1))
  fi
}

echo "== LATSAR production preflight =="
echo "ENV file: ${ENV_FILE}"
if [[ $REQUIRE_REALTIME -eq 1 ]]; then
  echo "Mode: realtime required"
else
  echo "Mode: realtime optional (--skip-realtime)"
fi

require_equals "APP_ENV" "production"
require_equals "APP_DEBUG" "false"
require_not_empty "APP_KEY"
require_not_empty "APP_URL"
require_not_empty "DB_CONNECTION"
require_not_empty "DB_HOST"
require_not_empty "DB_PORT"
require_not_empty "DB_DATABASE"
require_not_empty "DB_USERNAME"

if [[ $REQUIRE_REALTIME -eq 1 ]]; then
  require_not_empty "REVERB_APP_ID"
  require_not_empty "REVERB_APP_KEY"
  require_not_empty "REVERB_APP_SECRET"
  require_not_empty "REVERB_HOST"
  require_not_empty "REVERB_PORT"
  require_not_empty "REVERB_SCHEME"
fi

app_url="$(get_env_value "APP_URL")"
if [[ -n "$app_url" && ! "$app_url" =~ ^https:// ]]; then
  echo "ERROR: APP_URL harus menggunakan https://"
  errors=$((errors + 1))
fi

warn_if_not_equals "SESSION_SECURE_COOKIE" "true"

if [[ $REQUIRE_REALTIME -eq 1 ]]; then
  warn_if_not_equals "BROADCAST_CONNECTION" "reverb"
else
  current_broadcast="$(get_env_value "BROADCAST_CONNECTION")"
  if [[ "$current_broadcast" == "reverb" ]]; then
    echo "WARN: mode --skip-realtime aktif tetapi BROADCAST_CONNECTION masih 'reverb'."
    warnings=$((warnings + 1))
  fi
fi

echo
if [[ $errors -gt 0 ]]; then
  echo "Preflight GAGAL: ${errors} error, ${warnings} warning."
  exit 1
fi

echo "Preflight OK: 0 error, ${warnings} warning."
exit 0
