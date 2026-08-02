#!/usr/bin/env bash
set -euo pipefail
ROOT="$(cd "$(dirname "$0")" && pwd)"
python "$ROOT/tests/test_connector_0730.py"
python "$ROOT/build_0730.py"
php -l "$ROOT/artifacts/generated/nkt-gpt-connector-upgrader-0.7.30.php"
php -l "$ROOT/artifacts/generated/protected-lifecycle-0.7.30.php"
unzip -tq "$ROOT/artifacts/nkt-gpt-connector-0.7.30-reusable-block-object-evidence-upgrader.zip"
