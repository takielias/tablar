#!/usr/bin/env bash
# rebuild.sh — idempotent provisioning gate.
#
# Walks teardown -> run-fresh-install -> teardown -> run-fresh-install
# and asserts both runs produce a green welcome smoke. Catches
# install-flow non-determinism (caches, lockfiles, lingering volumes).
#
# Usage:
#   bash tests/e2e/scripts/rebuild.sh
#   bash tests/e2e/scripts/rebuild.sh --target /tmp/demo

set -euo pipefail

SCRIPT_DIR="$(cd "$(dirname "$0")" && pwd)"

ARGS=("$@")

run_cycle() {
    local label="$1"
    echo "── cycle: ${label} ─────────────────────────────"

    bash "${SCRIPT_DIR}/teardown.sh" "${ARGS[@]}"
    bash "${SCRIPT_DIR}/run-fresh-install.sh" "${ARGS[@]}"
    bash "${SCRIPT_DIR}/teardown.sh" "${ARGS[@]}"
}

run_cycle "first"
run_cycle "second"

echo
echo "Idempotent rebuild green — both cycles passed."
