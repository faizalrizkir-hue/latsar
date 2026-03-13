#!/usr/bin/env bash

set -euo pipefail

REQUIRE_PDO_MYSQL="${1:-0}"

has_pdo_mysql() {
  local php_bin="$1"
  "$php_bin" -m 2>/dev/null | grep -qi "^pdo_mysql$"
}

print_candidate_if_valid() {
  local php_bin="$1"

  [ -n "$php_bin" ] || return 1
  [ -x "$php_bin" ] || return 1
  "$php_bin" -v >/dev/null 2>&1 || return 1

  # Skip problematic Oryx runtime when possible.
  if [ "$php_bin" = "/home/codespace/.php/current/bin/php" ]; then
    return 1
  fi

  if [ "$REQUIRE_PDO_MYSQL" = "1" ]; then
    has_pdo_mysql "$php_bin" || return 1
  fi

  printf '%s\n' "$php_bin"
  return 0
}

CANDIDATES=()

if [ -n "${PHP_BIN:-}" ]; then
  CANDIDATES+=("${PHP_BIN}")
fi

if command -v php >/dev/null 2>&1; then
  CANDIDATES+=("$(command -v php)")
fi

CANDIDATES+=(
  "/usr/bin/php"
  "/usr/local/bin/php"
  "/home/codespace/.php/current/bin/php"
)

SEEN="|"
for candidate in "${CANDIDATES[@]}"; do
  [ -n "$candidate" ] || continue
  case "$SEEN" in
    *"|$candidate|"*) continue ;;
  esac
  SEEN="${SEEN}${candidate}|"

  if print_candidate_if_valid "$candidate"; then
    exit 0
  fi
done

exit 1
