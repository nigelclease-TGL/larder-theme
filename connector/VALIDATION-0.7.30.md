# NKT GPT Connector 0.7.30 validation

## Scope

Repository-only generation, OpenAPI, updater, packaging, PHP syntax and isolated evidence-action validation. No live WordPress request or mutation is part of development or CI.

## New capability

`inspectReusableBlockEvidence` is a standalone read-only GET action. It uses a canonical server-side `get_post(..., OBJECT, 'raw')` lookup rather than the ordinary-post connector endpoint and reports literal object type, literal status, identity fields, dates, content hashes, direct `core/block` references and exact access classification.

The action returns exactly one principal classification:

- `exists_accessible`
- `exists_wrong_post_type`
- `exists_inaccessible`
- `missing_or_deleted`
- `insufficient_evidence` is reserved for a future lookup path that cannot determine object existence safely.

## Safety boundary

The callback contains no post, metadata, status, Trash, archive, deletion, recipe, Nutrition, media or reusable-block mutation call. It is not wired into protected article baseline acceptance. Existing unavailable reusable blocks remain unavailable to protected create/update/apply operations until their literal states have been established and separately reviewed.

## Automated checks

The 0.7.30 suite verifies:

1. exact connector and updater versions;
2. existing protected lifecycle compatibility through 0.7.30;
3. published, draft, private and trashed object-state support through literal status reporting;
4. missing/deleted, wrong-post-type and access-denied classifications;
5. direct nested `core/block` reference extraction;
6. deterministic raw, status-independent and status-inclusive hashes;
7. optional raw-content and public-render evidence;
8. absence of WordPress mutation functions from the callback;
9. retention of all existing protected lifecycle, cleanup and reconciliation functions;
10. exactly 28 unique OpenAPI actions, no combinators and action descriptions below 300 characters;
11. guarded 0.7.29-to-0.7.30 updater generation, backup, SHA verification, cache invalidation, restoration and self-deactivation;
12. PHP syntax for generated lifecycle and updater;
13. deterministic updater ZIP integrity.

## Post-deployment acceptance

The first live use must call only `inspectReusableBlockEvidence` for IDs `21167`, `4263` and `7416`, with no protected lifecycle or write action in the same execution.
