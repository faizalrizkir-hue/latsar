#!/usr/bin/env bash
set -euo pipefail

if [[ $# -ne 1 ]]; then
  echo "Usage: bash scripts/backup/verify-backup.sh <backup_dir>"
  exit 1
fi

backup_dir="$1"
db_dump="${backup_dir}/db.sql"
uploads_archive="${backup_dir}/uploads.tar.gz"
checksum_file="${backup_dir}/checksums.sha256"

if [[ ! -d "$backup_dir" ]]; then
  echo "ERROR: backup directory tidak ditemukan: $backup_dir"
  exit 1
fi

if [[ ! -f "$db_dump" || ! -s "$db_dump" ]]; then
  echo "ERROR: file db.sql tidak ditemukan atau kosong."
  exit 1
fi

if [[ -f "$uploads_archive" ]]; then
  if ! tar -tzf "$uploads_archive" >/dev/null 2>&1; then
    echo "ERROR: uploads.tar.gz tidak valid."
    exit 1
  fi
fi

if [[ -f "$checksum_file" ]]; then
  if command -v sha256sum >/dev/null 2>&1; then
    (cd "$backup_dir" && sha256sum -c "$(basename "$checksum_file")")
  elif command -v shasum >/dev/null 2>&1; then
    while read -r hash file; do
      [[ -z "$hash" || -z "$file" ]] && continue
      actual="$(shasum -a 256 "$backup_dir/$file" | awk '{print $1}')"
      if [[ "$actual" != "$hash" ]]; then
        echo "ERROR: checksum tidak cocok untuk $file"
        exit 1
      fi
    done <"$checksum_file"
  else
    echo "WARN: sha256sum/shasum tidak tersedia, checksum dilewati."
  fi
fi

echo "Backup valid: $backup_dir"
