#!/usr/bin/env bash

set -euo pipefail

if [ ! -x /usr/local/bin/php ]; then
  exit 0
fi

BASHRC="$HOME/.bashrc"
touch "$BASHRC"

# Remove stale Oryx PHP PATH entries that can break php runtime.
sed -i '/\/home\/codespace\/\.php\/current\/bin/d' "$BASHRC" || true

if ! grep -q "LATSAR_FORCE_CONTAINER_PHP" "$BASHRC"; then
  cat >>"$BASHRC" <<'EOF'

# LATSAR_FORCE_CONTAINER_PHP
export PATH="/usr/local/bin:/usr/bin:/bin:${PATH}"
hash -r
EOF
fi
