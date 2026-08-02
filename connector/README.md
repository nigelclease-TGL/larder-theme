# NKT GPT Connector 0.7.30 source

This directory extends connector 0.7.29 to 0.7.30 while retaining the internal 0723 lifecycle function and metadata names required to resume existing protected drafts safely.

## Reusable-block object evidence

`inspectReusableBlockEvidence` is a standalone read-only GET action for an arbitrary WordPress object ID. It uses a canonical raw `get_post` lookup rather than the ordinary-post connector endpoint and reports:

- whether the object exists;
- exact accessible, inaccessible, wrong-post-type or missing/deleted classification;
- literal post type and WordPress status, including draft, pending, private, future and Trash states;
- title, slug, author, parent and local/GMT timestamps;
- optional raw `post_content`, raw content length and SHA-256;
- deterministic status-independent and status-inclusive object hashes;
- direct `core/block` references found in raw content;
- optional public-render hash evidence for published `wp_block` objects;
- exact error codes and messages.

The route is protected by the existing connector API-key permission callback. It deliberately does not require a logged-in WordPress user, because API-key requests may not establish one. A site may deny evidence for a specific object using the `nkt_gpt_par_0730_reusable_block_evidence_access` filter.

The action never edits reusable blocks, articles, recipes, WPRM Nutrition, media, metadata or statuses. It is not wired into protected baseline acceptance. Existing `reusable_block_not_available` findings therefore remain fail-closed until the returned literal object states have been reviewed and a later change is separately authorised.

## Existing 0.7.29 reconciliation and cleanup protections

The complete 0.7.29 legacy reconciliation inventory and exact-pair metadata reconciliation remain available without semantic changes. Ownership and obsolescence are never inferred from titles, authors, slugs, dates or content similarity.

`inventoryConnectorManagedDraftCleanup` and `trashConnectorManagedArticleDrafts` also remain unchanged in principle. Trash still requires an exact current allowlist and every ownership, lifecycle, ledger, active-protection, clone/evidence and classification guard. It uses `wp_trash_post` only and never archives, permanently deletes or empties Trash.

The protected Pumpkin Chocolate Chip Cookies, Giant Flat Chocolate Chunk Cookies and Spiced Ginger & Chocolate Loaf Cake workflow objects remain hard-preserved by ID. Stored protected baselines through 0.7.30 remain readable.

## Updater

The generated one-time updater requires exactly one active 0.7.29 connector. It backs up the primary connector and 0.7.29 lifecycle file, installs the 0.7.30 lifecycle and schema, replaces the version and loader exactly once, invalidates opcode cache when available, flushes WordPress caches, verifies the lifecycle SHA-256, restores prior files on failure and self-deactivates after success.

## Build and test

```bash
bash connector/build.sh
```

The repository suite generates and syntax-checks the lifecycle and updater, validates the 28-operation compact OpenAPI schema, runs object-state fixtures for published, draft, private, trashed, missing, wrong-type and access-denied objects, verifies nested references and deterministic hashes, and checks deterministic ZIP integrity. It does not connect to or modify the live WordPress site.
