#!/usr/bin/env bash
set -euo pipefail

ENV_FILE=".env"
OUTPUT_DIR="backups/runtime"
MYSQLDUMP_BIN="${MYSQLDUMP_BIN:-mysqldump}"

print_usage() {
  cat <<'USAGE'
Usage:
  bash scripts/backup/create-backup.sh [ENV_FILE] [OUTPUT_DIR]

Env:
  MYSQLDUMP_BIN   Path binary mysqldump (default: mysqldump)
USAGE
}

if [[ "${1:-}" == "-h" || "${1:-}" == "--help" ]]; then
  print_usage
  exit 0
fi

if [[ $# -ge 1 ]]; then
  ENV_FILE="$1"
fi
if [[ $# -ge 2 ]]; then
  OUTPUT_DIR="$2"
fi
if [[ $# -gt 2 ]]; then
  echo "ERROR: argumen terlalu banyak."
  print_usage
  exit 1
fi

if [[ ! -f "$ENV_FILE" ]]; then
  echo "ERROR: file env tidak ditemukan: $ENV_FILE"
  exit 1
fi

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

db_host="$(get_env_value "DB_HOST")"
db_port="$(get_env_value "DB_PORT")"
db_name="$(get_env_value "DB_DATABASE")"
db_user="$(get_env_value "DB_USERNAME")"
db_pass="$(get_env_value "DB_PASSWORD")"

if [[ -z "$db_host" || -z "$db_port" || -z "$db_name" || -z "$db_user" ]]; then
  echo "ERROR: konfigurasi DB belum lengkap (DB_HOST, DB_PORT, DB_DATABASE, DB_USERNAME)."
  exit 1
fi

timestamp="$(date +%Y%m%d_%H%M%S)"
backup_dir="${OUTPUT_DIR}/${timestamp}"
mkdir -p "$backup_dir"

db_dump="${backup_dir}/db.sql"
db_err="${backup_dir}/db.stderr.log"
uploads_archive="${backup_dir}/uploads.tar.gz"
checksum_file="${backup_dir}/checksums.sha256"
manifest_file="${backup_dir}/manifest.txt"

echo "== LATSAR Backup =="
echo "Backup dir: $backup_dir"

dump_args=(
  "--host=${db_host}"
  "--port=${db_port}"
  "--user=${db_user}"
  "--single-transaction"
  "--quick"
  "--skip-lock-tables"
  "--default-character-set=utf8mb4"
  "${db_name}"
)

if [[ -n "$db_pass" ]]; then
  MYSQL_PWD="$db_pass" "$MYSQLDUMP_BIN" "${dump_args[@]}" >"$db_dump" 2>"$db_err"
else
  "$MYSQLDUMP_BIN" "${dump_args[@]}" >"$db_dump" 2>"$db_err"
fi

if [[ ! -s "$db_dump" ]]; then
  echo "ERROR: dump database kosong: $db_dump"
  exit 1
fi

has_uploads=0
if [[ -d "public/uploads" ]]; then
  if find "public/uploads" -mindepth 1 -print -quit | grep -q .; then
    tar -czf "$uploads_archive" -C public uploads
    has_uploads=1
  fi
fi

if command -v sha256sum >/dev/null 2>&1; then
  if [[ $has_uploads -eq 1 ]]; then
    sha256sum "$db_dump" "$uploads_archive" >"$checksum_file"
  else
    sha256sum "$db_dump" >"$checksum_file"
  fi
elif command -v shasum >/dev/null 2>&1; then
  if [[ $has_uploads -eq 1 ]]; then
    shasum -a 256 "$db_dump" "$uploads_archive" >"$checksum_file"
  else
    shasum -a 256 "$db_dump" >"$checksum_file"
  fi
else
  echo "WARN: sha256sum/shasum tidak tersedia, checksum tidak dibuat."
fi

{
  echo "created_at=$(date -Is)"
  echo "env_file=$ENV_FILE"
  echo "db_host=$db_host"
  echo "db_port=$db_port"
  echo "db_name=$db_name"
  echo "db_user=$db_user"
  echo "db_dump=$(basename "$db_dump")"
  if [[ $has_uploads -eq 1 ]]; then
    echo "uploads_archive=$(basename "$uploads_archive")"
  fi
  if [[ -f "$checksum_file" ]]; then
    echo "checksums=$(basename "$checksum_file")"
  fi
} >"$manifest_file"

echo "Backup database: $db_dump"
if [[ $has_uploads -eq 1 ]]; then
  echo "Backup uploads : $uploads_archive"
else
  echo "WARN: public/uploads kosong atau tidak ada, arsip upload tidak dibuat."
fi
if [[ -f "$checksum_file" ]]; then
  echo "Checksums      : $checksum_file"
fi
echo "Manifest       : $manifest_file"
echo "Selesai."
