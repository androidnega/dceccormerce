#!/usr/bin/env bash
# Start Laravel dev server reachable on your LAN (other devices on the same Wi‑Fi).
# Usage: ./start.sh   or   bash start.sh

set -euo pipefail

cd "$(dirname "$0")"

HOST="${SERVE_HOST:-0.0.0.0}"
PORT="${SERVE_PORT:-8000}"

echo "Starting Laravel at http://${HOST}:${PORT} (all interfaces)"
echo ""

LAN_IP=""
if command -v ipconfig >/dev/null 2>&1; then
  LAN_IP=$(ipconfig getifaddr en0 2>/dev/null || ipconfig getifaddr en1 2>/dev/null || true)
fi
if [[ -n "${LAN_IP}" ]]; then
  echo "On another device, open: http://${LAN_IP}:${PORT}"
else
  echo "On another device, open: http://<this-machine-LAN-IP>:${PORT}"
fi
echo ""

exec php artisan serve --host="${HOST}" --port="${PORT}"
