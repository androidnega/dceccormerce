#!/usr/bin/env bash
# Run from your Laravel root on the server (e.g. public_html) before git pull in cPanel.
#
# Fixes: "untracked working tree files would be overwritten by merge: .htaccess"
# That happens when .htaccess was created on the server before the same path was
# tracked in the remote repo. This script backs up the untracked file, removes it,
# then pulls. Compare the backup to the new tracked .htaccess if you had custom rules.
set -euo pipefail

ROOT="$(git rev-parse --show-toplevel 2>/dev/null || true)"
if [[ -z "${ROOT}" ]]; then
  echo "Error: not inside a git repository." >&2
  exit 1
fi
cd "${ROOT}"

HT=".htaccess"
if [[ -f "${HT}" ]]; then
  # Untracked file shows as: ?? .htaccess
  if git status --porcelain --untracked-files=all -- "${HT}" 2>/dev/null | grep -q '^??'; then
    BAK="${HT}.bak-before-pull-$(date +%Y%m%d-%H%M%S)"
    echo "Untracked ${HT} would block merge. Copying to ${BAK} and removing ${HT}."
    cp -a "${HT}" "${BAK}"
    rm -f "${HT}"
  fi
fi

exec git pull "$@"
