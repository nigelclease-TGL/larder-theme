#!/usr/bin/env bash
set -euo pipefail
ROOT="$(cd "$(dirname "$0")" && pwd)"
python "$ROOT/tests/test_connector_0723.py"
php -l "$ROOT/updater/nkt-gpt-connector-upgrader.php"
php -l "$ROOT/src/protected-lifecycle-0.7.23.php"
python "$ROOT/build_release.py"
unzip -tq "$ROOT/artifacts/nkt-gpt-connector-0.7.23-update-runtime-guards-upgrader.zip"
