# NKT GPT Connector 0.7.30 validation

## Scope

Repository-only generation, OpenAPI, updater, packaging, PHP syntax and isolated evidence-action validation. No live WordPress request or mutation is part of development or CI.

## New capability

`inspectReusableBlockEvidence` is a standalone read-only GET action. It uses canonical server-side `get_post(..., OBJECT, 'raw')` lookup rather than the ordinary-post connector endpoint and reports literal object type, literal status, identity fields, dates, content hashes, direct `core/block` references and exact access classification.

The action returns exactly one principal classification:

- `exists_accessible`
- `exists_wrong_post_type`
- `exists_inaccessible`
- `missing_or_deleted`
- `insufficient_evidence`

## Authentication and safety boundary

The existing connector API-key permission callback protects the route. The evidence callback deliberately does not require `current_user_can('read_post', ...)`, because an authenticated API-key request may not establish a logged-in WordPress user. Site-specific object denial can be enforced through the read-only `nkt_gpt_par_0730_reusable_block_evidence_access` filter.

The callback contains no post, metadata, status, Trash, archive, deletion, recipe, Nutrition, media or reusable-block mutation call. It reports `changes_made: false`, `writes_attempted: false` and `writes_performed: 0`. It is not wired into protected article baseline acceptance. Existing unavailable reusable blocks remain unavailable to protected create/update/apply operations until their literal states have been established and separately reviewed.

## Updater guard hotfix

The first 0.7.30 package retained escaped 0.7.28 regular-expression markers in three updater checks even though its literal source version was 0.7.29. Activation therefore stopped safely before backups or writes. Package revision 0.7.30.2 transforms both literal and escaped versions, requires the exact 0.7.29 header, constant, lifecycle marker and loader, and rejects any generated updater containing a stale 0.7.28 source marker.

## Automated checks

The 0.7.30 suite verifies:

1. exact connector and updater versions;
2. existing protected lifecycle compatibility through 0.7.30;
3. published, draft, private and trashed `wp_block` fixtures through literal status reporting;
4. missing/deleted, wrong-post-type and explicit access-denied fixtures;
5. direct nested `core/block` reference extraction and stable numeric ordering;
6. deterministic raw, status-independent and status-inclusive hashes;
7. status-independent hash stability across a status-only change;
8. optional raw-content and published public-render evidence;
9. absence of WordPress-user capability dependency from the API-key callback;
10. absence of WordPress mutation functions from the evidence helper;
11. connection-status capability reporting without protected-lifecycle semantic changes;
12. retention of all existing protected lifecycle, cleanup and reconciliation functions;
13. exactly 28 unique OpenAPI actions, no combinators and every action description below 300 characters;
14. guarded 0.7.29-to-0.7.30 updater generation, including exact literal and escaped source markers, predecessor-updater identity, backups, SHA verification, cache invalidation, restoration and self-deactivation;
15. rejection of every stale literal or escaped 0.7.28 source marker in the generated updater;
16. PHP syntax for generated lifecycle and updater;
17. deterministic updater ZIP integrity.

## Post-deployment acceptance

The first live use must call only `inspectReusableBlockEvidence` for IDs `21167`, `4263` and `7416`, with `include_raw_content`, `include_reference_scan` and `include_public_render_evidence` enabled. No protected lifecycle or write action may run in the same execution.
