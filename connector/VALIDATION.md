# NKT GPT Connector 0.7.28 validation

## Scope

Repository-only source, generated-lifecycle, OpenAPI, updater, packaging and PHP-syntax validation. No live WordPress request or mutation is part of this acceptance check.

## Required validation

The 0.7.28 test suite verifies:

1. the connector and runtime guards target exactly 0.7.28;
2. existing protected baseline compatibility includes versions through 0.7.28;
3. explicit connector ownership metadata is persisted only after conclusive lifecycle initialisation;
4. legacy ownership requires source-live-post, workflow, baseline and lifecycle agreement;
5. ambiguous ownership remains `preserve_manual_or_unknown`;
6. `inventoryConnectorManagedDraftCleanup` is exposed as a read-only paginated action;
7. inventory rows include ownership and classification hashes;
8. incomplete programme-ledger evidence fails closed;
9. protected, initialised, audited, approved, not-applied, applied, failure, clone, ledger and intentional-exception records remain preserved;
10. every specifically protected Pumpkin, Giant Flat Chocolate Chunk and Spiced Ginger & Chocolate Loaf workflow object is hard-preserved by ID;
11. `trashConnectorManagedArticleDrafts` requires an exact ID allowlist and every inventory guard map;
12. the whole batch is refused before writing when any guard fails;
13. the write path uses native `wp_trash_post` and attempts `wp_untrash_post` restoration after a partial native failure;
14. the new Trash action contains no `wp_delete_post`, permanent-delete, archive-mode or empty-Trash path;
15. the OpenAPI schema is valid compact JSON with 25 operations and no `allOf`, `oneOf` or `anyOf`;
16. the generated updater targets exactly 0.7.27 to 0.7.28, creates protected backups, verifies SHA-256, restores on failure and self-deactivates;
17. generated lifecycle and updater PHP both pass `php -l`;
18. the deterministic updater archive passes `unzip -tq`.

## Read-only acceptance boundary

The repository acceptance process must not call either cleanup action against the live WordPress installation. In particular, it must not call the Trash action even with `dry_run: true` during implementation acceptance.

The following remain outside repository validation and require a later deployed read-only connector check:

- live connection status;
- deployed connector version;
- live merged-action exposure;
- actual draft inventory classifications;
- actual programme-ledger results.

## Deployment sequence

1. Take a full WordPress and database backup.
2. Activate the one-time 0.7.28 updater.
3. Refresh the ChatGPT Action schema from `openapi-0.7.28.json`.
4. Run `checkWordPressConnection` read-only and confirm both cleanup actions and all no-delete/no-empty-Trash capability flags.
5. Run `inventoryConnectorManagedDraftCleanup` read-only across every page.
6. Do not submit any draft to Trash unless its exact current inventory row returns `safe_to_trash: true`.
7. For any later Trash execution, pass only exact IDs and every expected guard value from the immediately preceding inventory.

## Preservation boundary

No repository test or build step connects to WordPress. Therefore live posts, drafts, recipes, WPRM Nutrition, media, reusable or synced blocks, programme-ledger classifications, intentional multi-recipe exceptions, links, Amazon destinations and affiliate identifiers remain unchanged by this acceptance work.
