#!/usr/bin/env bash
# teardown.sh — kill any lingering artisan-serve processes for the
# fresh-install demo, then remove the target dir.
#
# Usage:
#   bash tests/e2e/scripts/teardown.sh
#   bash tests/e2e/scripts/teardown.sh --target /path/to/demo

set -euo pipefail

TARGET="${TABLAR_E2E_TARGET:-${HOME}/laravel/revamp-demo}"

while [[ $# -gt 0 ]]; do
    case "$1" in
        --target) TARGET="$2"; shift 2 ;;
        -h|--help)
            sed -n '2,8p' "$0"
            exit 0
            ;;
        *) echo "Unknown flag: $1" >&2; exit 2 ;;
    esac
done

# Kill any artisan-serve bound to a 8xxx port for this dir.
pkill -f "artisan serve.*${TARGET}" 2>/dev/null || true

if [[ -d "${TARGET}" ]]; then
    rm -rf "${TARGET}"
    echo "Removed ${TARGET}"
else
    echo "Nothing to remove at ${TARGET}"
fi
