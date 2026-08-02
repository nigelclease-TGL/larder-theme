#!/usr/bin/env bash
set -euo pipefail
ROOT="$(cd "$(dirname "$0")" && pwd)"
python "$ROOT/build_0731.py"
python "$ROOT/tests/test_connector_0731.py"
python "$ROOT/tests/test_recipe_name_only_0731.py"
python "$ROOT/tests/test_reusable_block_evidence_0730.py"
python "$ROOT/tests/test_openapi_description_limits.py"
php -l "$ROOT/artifacts/generated/nkt-gpt-connector-upgrader-0.7.31.php"
php -l "$ROOT/artifacts/generated/protected-lifecycle-0.7.31.php"
unzip -tq "$ROOT/artifacts/nkt-gpt-connector-0.7.31-protected-recipe-name-only-revision-upgrader.zip"
