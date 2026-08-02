#!/usr/bin/env bash
set -euo pipefail
ROOT="$(cd "$(dirname "$0")" && pwd)"
python "$ROOT/tests/test_connector_0728.py"
python "$ROOT/build_release.py"
php -l "$ROOT/artifacts/generated/nkt-gpt-connector-upgrader-0.7.28.php"
php -l "$ROOT/artifacts/generated/protected-lifecycle-0.7.28.php"
unzip -tq "$ROOT/artifacts/nkt-gpt-connector-0.7.28-managed-draft-ownership-guarded-trash-upgrader.zip"
