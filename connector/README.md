# NKT GPT Connector 0.7.29 source

This directory extends connector 0.7.28 to 0.7.29 while retaining the internal 0723 lifecycle function and metadata names required to resume existing protected drafts safely.

## Why 0.7.29 exists

The complete 0.7.28 live cleanup inventory found 270 article drafts but no `safe_to_trash: true` rows. Most drafts were correctly preserved as manual or ownership-ambiguous, and no record contained the explicit obsolete disposition plus valid superseding connector draft required by the existing cleanup classifier.

Connector 0.7.29 adds a separate reconciliation stage. It does not weaken 0.7.28 cleanup classification and it does not infer obsolescence from titles, authors, slugs, dates or content similarity.

## Read-only legacy reconciliation inventory

`inventoryLegacyConnectorDraftReconciliation` is a paginated GET action restricted to article drafts carrying connector metadata signals. Each row reports:

- exact source-live-post, protected-baseline, protected-lifecycle and workflow signals;
- baseline, lifecycle, ownership, classification and reconciliation hashes;
- source, lifecycle and draft-ID consistency failures;
- current review, application, active-protection and programme-ledger state;
- linked recipe-clone and failure-evidence IDs;
- current preservation reasons;
- whether ownership can be reconciled without inference;
- whether the draft is otherwise blocked only by `preserve_manual_or_unknown` and `preserve_required_workflow`;
- exact newer connector-managed drafts for the same source that are retained and may be proposed as successors.

A draft is not eligible when it is hard-preserved, programme-ledger referenced, active, initialised, audited, approved, not-applied, applied, failure evidence, clone-linked, intentional-multi-recipe evidence, already safe to Trash, structurally inconsistent, or missing sufficient independent connector evidence.

## Guarded exact-pair reconciliation

`reconcileLegacyConnectorDraftSupersession` accepts one to 100 exact obsolete/superseding draft pairs. Every pair must include all current reconciliation, classification, ownership, WordPress-status, review, application, programme-ledger, clone and failure-evidence guards from the immediately preceding inventory.

The whole batch is rejected before writing when:

- either confirmation is absent;
- an ID is missing, duplicated or used as both obsolete and superseding in the same batch;
- any current hash or status differs;
- the obsolete draft has any preservation reason beyond the two reconciliation-only blockers;
- the proposed successor is not a newer, retained, connector-owned draft for the same source article;
- the source, baseline or lifecycle evidence is inconsistent;
- the supersession reason is empty.

A dry run performs no write. A successful non-dry-run writes only explicit connector ownership, obsolete disposition, cleanup reason, superseding-draft ID and an audit record to the obsolete draft. It then invokes the unchanged 0.7.28 classifier and requires `safe_to_trash: true` with classification `eligible_obsolete_connector_draft`. If verification fails, exact prior metadata snapshots are restored where possible.

This action never edits article content, live posts, recipes, WPRM Nutrition, media, reusable blocks, links, Amazon destinations or affiliate identifiers. It never invokes WordPress Trash, archive or permanent deletion.

## Existing cleanup protections

`inventoryConnectorManagedDraftCleanup` and `trashConnectorManagedArticleDrafts` remain unchanged in principle. Trash still requires an exact current allowlist and every 0.7.28 ownership, lifecycle, ledger, active-protection, clone/evidence and classification guard. It uses `wp_trash_post` only and never archives, permanently deletes or empties Trash.

The protected Pumpkin Chocolate Chip Cookies, Giant Flat Chocolate Chunk Cookies and Spiced Ginger & Chocolate Loaf Cake workflow objects remain hard-preserved by ID. Stored protected baselines through 0.7.29 remain readable.

## Updater

The generated one-time updater requires exactly one active 0.7.28 connector. It backs up the primary connector and 0.7.28 lifecycle file, installs the 0.7.29 lifecycle and schema, replaces the version and loader exactly once, invalidates opcode cache when available, flushes WordPress caches, verifies the lifecycle SHA-256, restores prior files on failure and self-deactivates after success.

## Build and test

```bash
python connector/tests/test_connector_0729.py
bash connector/build.sh
```

Repository validation is isolated. It generates and syntax-checks the lifecycle and updater, validates the 27-operation schema, verifies exact reconciliation guards and confirms that the reconciliation action contains no article-content, Trash, archive or permanent-delete path. It does not connect to or modify the live WordPress site.
