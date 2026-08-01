# NKT GPT Connector 0.7.23 source

This directory imports the actual 0.7.22 lifecycle/updater source used for the deployed connector and advances it to 0.7.23.

## Changes

- Aligns `updateProtectedArticleRevision` route/runtime handling for `expected_recipe_statuses`, `expected_recipe_object_hashes`, and `expected_status_independent_recipe_hashes`.
- Rejects malformed, missing, and extra recipe-map IDs before any draft write.
- Verifies recipe type, literal status, object hash, status-independent hash, component hashes, WPRM Nutrition, modification timestamps, recipe order/count, and intentional exception before and after the write.
- Restores the exact draft and recipe snapshots if post-write drift is detected.
- Extracts the serving label and hash from the same H3 inside the top-level NUTRITION section.
- Adds `cleanupRevisionObjects` as a 23rd action. This separate action is necessary because the existing `archiveRevisionPairs` implementation lives in the unavailable primary connector source and cannot be safely replaced from the lifecycle extension. Cleanup defaults to dry-run, targets exact IDs only, refuses live references, archives first, and requires separate permanent-delete authorisation.

## Build and test

```bash
python connector/tests/test_connector_0723.py
bash connector/build.sh
```

Tests use mocks and static/runtime fixtures only. They do not connect to WordPress or inspect live data.
