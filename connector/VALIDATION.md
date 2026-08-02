# NKT GPT Connector 0.7.23 validation

## Scope

Isolated source, schema, route-registration, helper-runtime, cleanup-safety, packaging and syntax validation only. No live WordPress site was inspected or mutated.

## Results

- Static/source/schema checks: **40/40 passed**
- Isolated PHP runtime checks with WordPress mocks: **21/21 passed**
- PHP syntax: updater and lifecycle extension passed `php -l`
- OpenAPI: valid JSON, **1 line**, **23 operations**, no `allOf`, `oneOf` or `anyOf`
- Updater archive: integrity verified with `unzip -tq`

## Required behaviour covered

1. Runtime update callback reads both update guard maps.
2. REST update route registers and forwards both maps.
3. Internal preflight receives both maps.
4. Malformed hashes reject before writing.
5. Missing map IDs reject before writing.
6. Extra map IDs reject before writing.
7. Recipe-status drift rejects before writing.
8. Status-independent-hash drift rejects before writing.
9. Post-write recipe/component/timestamp drift triggers draft and recipe restoration paths.
10. Serving label literal and hash derive from the same H3 inside the top-level NUTRITION section.
11. Cleanup defaults to dry-run and performs no writes.
12. Cleanup refuses live-referenced objects.
13. Protected IDs `41019`, `41037`, `30780` and `30800` require separate explicit authorisation.
14. Permanent deletion requires prior archive in the same batch and separate explicit authorisation.
15. A protected 0.7.22 draft baseline can be migrated in-memory and persisted only after a successful guarded 0.7.23 update.

## Action-count decision

The schema exposes **23 actions**. `cleanupRevisionObjects` is separate because the existing `archiveRevisionPairs` runtime is implemented in the primary connector source, which was not part of the 0.7.22 updater package. Replacing or wrapping that unavailable implementation would be less safe than adding one explicit, isolated cleanup route.

## Limitations

The package has not been integration-tested against the live WordPress installation. Deployment must begin with a full backup and a read-only capability check. Cleanup must remain `dry_run: true` until separately authorised.
