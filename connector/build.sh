#!/usr/bin/env bash
set -euo pipefail
ROOT="$(cd "$(dirname "$0")" && pwd)"
python "$ROOT/tests/test_connector_0726.py"
python "$ROOT/build_release.py"
php -l "$ROOT/artifacts/generated/nkt-gpt-connector-upgrader-0.7.26.php"
php -l "$ROOT/artifacts/generated/protected-lifecycle-0.7.26.php"
unzip -tq "$ROOT/artifacts/nkt-gpt-connector-0.7.26-structured-nutrient-evidence-upgrader.zip"
