#!/usr/bin/env bash
# run-fresh-install.sh — provision a clean Laravel install with all four
# Tablar-suite packages wired in via local path repos, then boot the
# app and smoke-test the welcome page.
#
# Usage:
#   bash tests/e2e/scripts/run-fresh-install.sh
#   bash tests/e2e/scripts/run-fresh-install.sh --dry-run     # print plan, exit
#   bash tests/e2e/scripts/run-fresh-install.sh --target /path/to/demo
#
# Requires: composer, php 8.3+, node 22+, npm. DDEV optional — if a
# .ddev/config.yaml is present in the target dir the script uses
# `ddev composer` / `ddev npm` instead of host binaries.

set -euo pipefail

DRY_RUN=0
TARGET="${TABLAR_E2E_TARGET:-${HOME}/laravel/revamp-demo}"
LARAVEL_VERSION="${LARAVEL_VERSION:-^13.0}"
PORT="${TABLAR_E2E_PORT:-8000}"

while [[ $# -gt 0 ]]; do
    case "$1" in
        --dry-run) DRY_RUN=1; shift ;;
        --target)  TARGET="$2"; shift 2 ;;
        --port)    PORT="$2"; shift 2 ;;
        -h|--help)
            sed -n '2,12p' "$0"
            exit 0
            ;;
        *) echo "Unknown flag: $1" >&2; exit 2 ;;
    esac
done

# Resolve the package source root from the script location.
SCRIPT_DIR="$(cd "$(dirname "$0")" && pwd)"
PACKAGE_ROOT="$(cd "${SCRIPT_DIR}/../../.." && pwd)"
SUITE_ROOT="$(cd "${PACKAGE_ROOT}/.." && pwd)"

# Path repos the demo will resolve against.
TABLAR_PATH="${PACKAGE_ROOT}"
TABLAR_KIT_PATH="${SUITE_ROOT}/tablar-kit"
CRUD_GEN_PATH="${SUITE_ROOT}/tablar-crud-generator"
LAB_PATH="${SUITE_ROOT}/laravel-ajax-builder"

cat <<EOF
Plan:
  Target dir       : ${TARGET}
  Laravel version  : ${LARAVEL_VERSION}
  Port             : ${PORT}
  Path repos:
    takielias/tablar                  -> ${TABLAR_PATH}
    takielias/tablar-kit              -> ${TABLAR_KIT_PATH}
    takielias/tablar-crud-generator   -> ${CRUD_GEN_PATH}
    takielias/lab                     -> ${LAB_PATH}
EOF

if [[ "${DRY_RUN}" == "1" ]]; then
    echo "[dry-run] no changes made."
    exit 0
fi

# 1. Provision target dir
mkdir -p "$(dirname "${TARGET}")"
if [[ -d "${TARGET}" ]]; then
    echo "Target ${TARGET} already exists — refusing to clobber. Run teardown.sh first." >&2
    exit 1
fi

# 2. composer create-project
composer create-project "laravel/laravel:${LARAVEL_VERSION}" "${TARGET}" --no-interaction --prefer-dist

cd "${TARGET}"

# 3. Wire path repos
composer config repositories.tablar          path "${TABLAR_PATH}"
composer config repositories.tablar-kit      path "${TABLAR_KIT_PATH}"
composer config repositories.tablar-crud-gen path "${CRUD_GEN_PATH}"
composer config repositories.lab             path "${LAB_PATH}"

composer require \
    "takielias/tablar:*@dev" \
    "takielias/tablar-kit:*@dev" \
    "takielias/tablar-crud-generator:*@dev" \
    "takielias/lab:*@dev" \
    --no-interaction

# 4. Install Tablar
php artisan tablar:install --force --no-credits

# 5. Build assets
npm install
npm run build

# 6. Migrate (sqlite default in fresh L13)
touch database/database.sqlite
php artisan migrate --force

# 7. Boot the app and smoke-test
php artisan serve --host=127.0.0.1 --port="${PORT}" > server.log 2>&1 &
SERVER_PID=$!

# Give it a moment to bind.
sleep 4

cleanup() {
    if kill -0 "${SERVER_PID}" 2>/dev/null; then
        kill "${SERVER_PID}" 2>/dev/null || true
    fi
}
trap cleanup EXIT

status=$(curl -s -o response.html -w "%{http_code}" "http://127.0.0.1:${PORT}/")
echo "GET / → HTTP ${status}"

if [[ "${status}" != "200" ]]; then
    echo "Welcome page did not return 200." >&2
    head -50 server.log >&2
    exit 1
fi

if ! grep -q "Welcome to Tablar" response.html; then
    echo "Response did not contain 'Welcome to Tablar'." >&2
    exit 1
fi

# 8. Doctor exit code
php artisan tablar:doctor

echo
echo "Fresh install green. App live at http://127.0.0.1:${PORT}/"
echo "Run \`export TABLAR_E2E_BASE_URL=http://127.0.0.1:${PORT}\` then \`npm test\` from tests/e2e/"
