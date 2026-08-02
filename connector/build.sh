#!/usr/bin/env bash
set -euo pipefail
ROOT="$(cd "$(dirname "$0")" && pwd)"
python "$ROOT/tests/test_connector_0724.py"
php -l "$ROOT/updater/nkt-gpt-connector-upgrader.php"
php -l "$ROOT/artifacts/generated/protected-lifecycle-0.7.24.php"
python "$ROOT/build_release.py"
unzip -tq "$ROOT/artifacts/nkt-gpt-connector-0.7.24-serving-heading-extraction-upgrader.zip"
