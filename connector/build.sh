#!/usr/bin/env bash
set -euo pipefail
ROOT="$(cd "$(dirname "$0")" && pwd)"
python "$ROOT/build_0730.py"
python "$ROOT/tests/test_connector_0730.py"
python "$ROOT/tests/test_updater_guard_0730.py"
python "$ROOT/tests/test_reusable_block_evidence_0730.py"
python "$ROOT/tests/test_openapi_description_limits.py"
php -l "$ROOT/artifacts/generated/nkt-gpt-connector-upgrader-0.7.30.php"
php -l "$ROOT/artifacts/generated/protected-lifecycle-0.7.30.php"
unzip -tq "$ROOT/artifacts/nkt-gpt-connector-0.7.30-reusable-block-object-evidence-upgrader.zip"
