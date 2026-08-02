# NKT GPT Connector 0.7.29 validation

## Scope

Repository-only source, generated-lifecycle, OpenAPI, updater, packaging and PHP-syntax validation. No live WordPress request or mutation is part of this acceptance check.

## Required validation

The 0.7.29 suite verifies:

1. the connector and runtime guards target exactly 0.7.29;
2. stored protected baseline compatibility includes versions through 0.7.29;
3. the complete 0.7.28 cleanup inventory and native WordPress Trash actions remain present;
4. the legacy reconciliation inventory requires exact draft state, a published source article, protected structural evidence and at least three independent connector signals;
5. baseline/source, lifecycle/source and lifecycle/draft-ID mismatches fail closed;
6. titles, authors, slugs, dates and content similarity are not used to infer ownership;
7. only drafts blocked by `preserve_manual_or_unknown` and `preserve_required_workflow` can become reconciliation candidates;
8. hard-preserved, active, initialised, audited, approved, not-applied, applied, failure, clone, programme-ledger and intentional-multi-recipe records remain blocked;
9. candidate rows expose deterministic reconciliation, ownership and classification hashes plus exact retained connector-managed successor IDs;
10. `inventoryLegacyConnectorDraftReconciliation` is read-only and paginated;
11. `reconcileLegacyConnectorDraftSupersession` requires one to 100 exact pairs and both explicit confirmations;
12. every obsolete and superseding hash, status, lifecycle, ledger, clone and failure-evidence guard is mandatory;
13. duplicate obsolete IDs and cross-pair chains are refused;
14. successors must be newer, retained, connector-owned drafts for the same source article;
15. the whole batch is refused before writing when any pair fails;
16. dry-run mode performs no writes;
17. non-dry-run mode writes only explicit ownership, obsolete disposition, cleanup reason, superseding ID and audit metadata to obsolete drafts;
18. post-write verification requires the unchanged cleanup classifier to return `safe_to_trash: true` and `eligible_obsolete_connector_draft`;
19. exact prior reconciliation metadata is restored when verification fails;
20. the reconciliation action contains no article-content write, `wp_trash_post`, archive or permanent-delete path;
21. every specifically protected Pumpkin, Giant Flat Chocolate Chunk and Spiced Ginger & Chocolate Loaf workflow object remains hard-preserved by ID;
22. the compact OpenAPI schema is valid JSON with 27 operations and no `allOf`, `oneOf` or `anyOf`;
23. the generated updater targets exactly 0.7.28 to 0.7.29, creates protected backups, verifies SHA-256, restores on failure and self-deactivates;
24. generated lifecycle and updater PHP both pass `php -l`;
25. the deterministic updater archive passes `unzip -tq`.

## Acceptance boundary

Repository acceptance must not call either reconciliation action or either cleanup action against the live WordPress installation. No live connector inventory, dry run or write is part of implementation acceptance.

After deployment, the required operational sequence is:

1. take a full WordPress and database backup;
2. activate the one-time 0.7.29 updater;
3. refresh the ChatGPT Action schema from `openapi-0.7.29.json`;
4. run `checkWordPressConnection` read-only and confirm connector version 0.7.29 and 27 actions;
5. run every page of `inventoryLegacyConnectorDraftReconciliation` read-only;
6. review only rows with `eligible_for_supersession_recording: true` and the exact `eligible_superseding_draft_ids` returned;
7. perform a separately authorised `reconcileLegacyConnectorDraftSupersession` call with `dry_run: true` and every current guard;
8. do not perform a non-dry-run reconciliation until the exact dry-run report has been reviewed;
9. after reconciliation, rerun `inventoryConnectorManagedDraftCleanup` read-only;
10. do not call the Trash action unless exact current rows return `safe_to_trash: true` and a separate Trash execution is explicitly authorised.

## Preservation boundary

No repository test or build step connects to WordPress. Live posts, article content, drafts, recipes, WPRM Nutrition, media, reusable or synced blocks, programme-ledger records, intentional multi-recipe exceptions, links, Amazon destinations and affiliate identifiers remain unchanged by this acceptance work.
