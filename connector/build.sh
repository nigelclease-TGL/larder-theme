#!/usr/bin/env bash
set -euo pipefail
ROOT="$(cd "$(dirname "$0")" && pwd)"
python "$ROOT/tests/test_connector_0725.py"
php -l "$ROOT/updater/nkt-gpt-connector-upgrader.php"
php -l "$ROOT/artifacts/generated/protected-lifecycle-0.7.25.php"
python "$ROOT/build_release.py"
unzip -tq "$ROOT/artifacts/nkt-gpt-connector-0.7.25-zero-section-serving-evidence-upgrader.zip"
