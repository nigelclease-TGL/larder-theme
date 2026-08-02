# NKT GPT Connector 0.7.30 changelog

- Adds `inspectReusableBlockEvidence`, a standalone read-only action for arbitrary WordPress object IDs expected to be reusable blocks (`wp_block`).
- Uses canonical raw server-side `get_post` lookup rather than the ordinary-post REST endpoint.
- Distinguishes accessible reusable blocks, existing wrong-post-type objects, access denial, and missing/deleted IDs.
- Returns literal WordPress status, including non-public states such as draft, pending, private, future and Trash.
- Returns title, slug, author, parent, local/GMT timestamps, optional raw content, raw content hash, deterministic status-independent and status-inclusive object hashes, and direct `core/block` references.
- Supports optional public-render hash evidence only for published reusable blocks.
- Uses the existing connector API-key permission callback and does not require a logged-in WordPress user.
- Adds a filter-controlled explicit access-denial path for site-specific evidence restrictions and isolated testing.
- Adds published, draft, private, trashed, missing, wrong-type and access-denied behavioral fixtures.
- Expands the compact OpenAPI schema from 27 to 28 actions while keeping every action description within ChatGPT’s 300-character limit.
- Retains all protected article lifecycle, cleanup, native Trash and legacy reconciliation actions without semantic changes.
- Does not make unavailable reusable blocks automatically acceptable to protected baselines.
- Adds a guarded deterministic 0.7.29-to-0.7.30 updater with protected backups, SHA-256 verification, cache invalidation, automatic restoration and self-deactivation.
- No live WordPress request or mutation is part of development or CI.
