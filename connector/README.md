# NKT GPT Connector 0.7.28 source

This directory extends connector 0.7.27 to 0.7.28 while retaining the internal 0723 lifecycle function and metadata names required to resume existing protected drafts safely.

## Connector-managed draft ownership

Newly initialised protected article drafts receive stable connector ownership metadata after the protected baseline and lifecycle are complete. The ownership bundle records the connector identity and creation version, workflow type, protected revision ID, source live post, timestamp, lifecycle/review/application states, supersession links, linked clone and failure-evidence IDs, programme-ledger state and active-protection state.

Legacy ownership is accepted only when the draft has a mutually consistent source-live-post association, protected workflow type, protected baseline and protected lifecycle. Titles, authors, dates, duplicate-looking content and draft status are never sufficient. Ambiguous drafts remain `preserve_manual_or_unknown`.

## Read-only cleanup inventory

`inventoryConnectorManagedDraftCleanup` is a paginated GET action. It returns deterministic ownership and cleanup evidence for every inspected article draft, including:

- WordPress status and source live post;
- ownership proof and evidence source;
- lifecycle, review and application status;
- active protected-draft state;
- complete programme-ledger reference state;
- linked recipe-clone and failure-evidence IDs;
- supersession and conclusive-obsolescence state;
- preservation reasons;
- `safe_to_trash` and classification/ownership hashes.

The inventory fails closed. An unavailable or incomplete programme-ledger scan produces `unknown`, which blocks cleanup.

## Guarded native WordPress Trash

`trashConnectorManagedArticleDrafts` accepts only an exact draft-ID allowlist. Every ID must repeat the latest inventory evidence through required status, ownership, lifecycle, ledger, active-protection, clone/evidence and classification-hash maps. The whole batch is rejected before writing if any guard differs or any draft is not `safe_to_trash: true`.

A non-dry-run operation uses `wp_trash_post` only. It does not archive, permanently delete, empty Trash, process titles or accept an unrestricted all-drafts selector. If a native Trash write fails after earlier IDs were processed, the action attempts to restore those IDs with `wp_untrash_post`.

## Hard preservation

The protected Pumpkin Chocolate Chip Cookies, Giant Flat Chocolate Chunk Cookies and Spiced Ginger & Chocolate Loaf Cake workflow objects are hard-preserved by ID. The general classifier also preserves active, initialised, audited, approved, not-applied, applied, rejected/failure, clone-linked, ledger-linked, intentional multi-recipe and manually created or ownership-ambiguous records.

## Existing protections

The 0.7.27 colon-tolerant structured nutrient evidence fix and all existing content, Nutrition, recipe, WPRM Nutrition, media, reusable-block, metadata, Amazon, affiliate, exact-replacement, audit and rollback protections remain included. Stored protected baselines through 0.7.28 remain readable.

## Updater

The generated one-time updater requires exactly one active 0.7.27 connector. It backs up the primary connector and 0.7.27 lifecycle file, installs the 0.7.28 lifecycle and schema, replaces the version and loader exactly once, invalidates opcode cache when available, flushes WordPress caches, verifies the lifecycle SHA-256, restores prior files on failure and self-deactivates after success.

## Build and test

```bash
python connector/tests/test_connector_0728.py
bash connector/build.sh
```

Repository validation is isolated. It generates and syntax-checks the lifecycle and updater, validates the 25-operation schema and verifies that the new Trash action contains no permanent-delete, archive-mode or empty-Trash path. It does not connect to or modify the live WordPress site.
