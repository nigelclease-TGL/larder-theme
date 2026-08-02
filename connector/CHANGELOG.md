# Changelog

## NKT GPT Connector 0.7.29

- Adds the read-only paginated `inventoryLegacyConnectorDraftReconciliation` action for drafts carrying connector metadata signals.
- Requires a published source article, exact draft status, protected baseline or lifecycle structure, at least three independent connector signals and consistent source/draft IDs before legacy ownership can be considered reconcilable.
- Never infers ownership or obsolescence from titles, authors, slugs, dates or content similarity.
- Limits reconciliation eligibility to drafts currently blocked only by `preserve_manual_or_unknown` and `preserve_required_workflow`.
- Continues to block hard-preserved, active, initialised, audited, approved, not-applied, applied, failure, clone, ledger and intentional-multi-recipe records.
- Returns deterministic reconciliation, classification and ownership hashes plus exact newer retained connector-managed successor IDs for the same source article.
- Adds `reconcileLegacyConnectorDraftSupersession`, requiring exact obsolete/superseding pairs, two explicit confirmations and complete current evidence, status, lifecycle, ledger, clone and failure-evidence guards.
- Rejects the entire batch before writing when any pair fails, when IDs are duplicated, or when a draft appears as both obsolete and superseding in the same batch.
- Supports a write-free dry run.
- A non-dry-run writes only connector ownership, obsolete disposition, cleanup reason, superseding-draft ID and an audit record to the obsolete draft.
- Re-runs the unchanged cleanup classifier and requires `safe_to_trash: true`; exact metadata snapshots are restored if verification fails.
- Contains no article-content write, Trash invocation, archive action, permanent deletion or empty-Trash capability in the reconciliation action.
- Retains all 0.7.28 cleanup inventory, native Trash, hard-preservation and protected article/recipe/WPRM/media/link safeguards.
- Adds a guarded 0.7.28-to-0.7.29 updater and expands the compact OpenAPI schema from 25 to 27 actions.

## NKT GPT Connector 0.7.28

- Adds stable explicit connector ownership metadata for newly initialised protected article drafts.
- Accepts legacy connector ownership only from mutually consistent source-live-post, workflow, protected-baseline and protected-lifecycle evidence; title or duplicate-looking content is never sufficient.
- Adds the read-only paginated `inventoryConnectorManagedDraftCleanup` action with deterministic ownership and `safe_to_trash` classification hashes.
- Reads the existing Nutrition programme ledger through its read-only route and fails closed when ledger coverage cannot be proven complete.
- Preserves active, initialised, audited, approved, not-applied, applied, rejected/failure, clone-linked, ledger-linked, intentional multi-recipe and manual-or-unknown drafts.
- Hard-preserves all specified Pumpkin Chocolate Chip Cookies, Giant Flat Chocolate Chunk Cookies and Spiced Ginger & Chocolate Loaf Cake workflow objects.
- Adds `trashConnectorManagedArticleDrafts`, requiring an exact ID allowlist and complete expected ownership, WordPress-status, lifecycle, ledger, active-protection, clone/evidence and classification-hash guards.
- Rejects the whole batch before writing when any supplied draft fails a guard or is not currently `safe_to_trash: true`.
- Uses native `wp_trash_post` only and contains no archive substitution, permanent deletion or empty-Trash capability.
- Adds best-effort `wp_untrash_post` restoration if a later native Trash write fails after earlier IDs were processed.
- Retains the 0.7.27 structured nutrient-label fix and all existing protected article, recipe, WPRM Nutrition, media, reusable-block, metadata, Amazon, affiliate, audit and rollback guards.
- Adds a guarded 0.7.27-to-0.7.28 updater and expands the compact OpenAPI schema from 23 to 25 actions.

## NKT GPT Connector 0.7.27

- Accepts either an exact recognised nutrient label or the same label followed by exactly one terminal ASCII colon in structured table and adjacent-record evidence.
- Fixes the post-change serving-label rollback triggered when the standardised Nutrition table changed `Calories`, `Total Fat`, `Carbs`, `Sugars` and `Protein` to colon-terminated labels.
- Keeps canonical labels punctuation-free internally, so `Calories:` maps to `Calories` while `Calories::`, descriptive labels and unrelated punctuation remain rejected.
- Adds a production-shaped before-state fixture and the exact proposed after-state fixture with colon-terminated first-cell labels, em-dash percentages and the standard Daily Values disclaimer.
- Proves that the complete zero-section Serving fallback succeeds before and after the guarded article Nutrition replacement.
- Proves that the explicit `expected_serving_label_after` guard passes for the exact proposed after-state and still rejects an actual serving-label mismatch.
- Retains all content, Nutrition, recipe, WPRM Nutrition, media, reusable-block, metadata, Amazon, affiliate, replacement-count, audit and rollback protections.
- Keeps protected baselines from 0.7.23 through 0.7.26 compatible and adds a guarded 0.7.26-to-0.7.27 updater.
- Retains the compact 23-action OpenAPI schema.

## NKT GPT Connector 0.7.26

- Replaces the zero-section fallback’s colon-only nutrient counter with structured, value-backed nutrient evidence.
- Recognises exact nutrient labels in Gutenberg table cells when a following cell contains a numeric value, including production markup with no literal colon between label and value.
- Preserves conventional `Label: value` text and adds boundary-aware list, paragraph, column and adjacent-record support.
- Counts distinct recognised labels only and rejects label mentions that are not paired with numeric values.
- Adds a production-shaped fixture matching the raw two-cell Nutrition table used by post 34505, including nested `<strong>` labels and `&nbsp;` values.
- Adds negative fixtures for prose-only labels, nonnumeric table values, duplicate presentations, ambiguous Serving headings, weak nutrient evidence and multiple parser sections.
- Keeps all earlier section-scoped, single-parser-section and corroborated zero-section Serving extraction paths.
- Keeps existing 0.7.23, 0.7.24 and 0.7.25 protected drafts compatible while preserving content, Nutrition, recipe, WPRM Nutrition, media, reusable-block, metadata, Amazon, affiliate, exact-replacement and serving-label guards.
- Adds a guarded 0.7.25-to-0.7.26 updater and retains the compact 23-action OpenAPI schema.

## NKT GPT Connector 0.7.25

- Retains the 0.7.24 section-scoped and single-parser-section Serving-H3 extraction paths.
- Adds a guarded zero-parser-section fallback for legacy articles whose Nutrition parser reports zero sections even though the raw article contains the visible Nutrition presentation.
- Requires exactly one raw `Serving: <label>` H3, exactly one article-wide and segment-local `Nutrition per serving` marker, at least four recognised nutrient labels, exactly one Daily Values marker and exactly one 2,000-calorie basis marker.
- Rejects a unique Serving H3 by itself, duplicate Nutrition presentations, insufficient nutrient evidence, ambiguous Serving headings and multiple parser sections.
- Exposes the fallback gate, raw-H3 count, Nutrition marker counts, nutrient-label count, Daily Values count, calorie-basis count and signature decision through read-only status evidence.
- Keeps existing 0.7.23 and 0.7.24 protected drafts valid by excluding only parser-derived serving diagnostics from baseline equality while retaining content, Nutrition, recipe, media, reusable-block, metadata, Amazon, affiliate, exact-replacement and explicit serving-label guards.
- Adds a guarded 0.7.24-to-0.7.25 updater with primary/lifecycle backups, exact loader replacement, opcode-cache invalidation, SHA-256 verification, automatic restoration and self-deactivation.
- Keeps the complete compact OpenAPI schema at 23 actions.

## NKT GPT Connector 0.7.24

- Preserves the existing section-scoped Nutrition serving-heading extractor as the primary path.
- Adds a safe article-wide fallback that requires exactly one legacy Nutrition section and exactly one visible `Serving: <label>` H3.
- Recursively scans parsed Gutenberg blocks and falls back to raw H3 markup with nested-markup removal, HTML-entity decoding and whitespace normalization.
- Reports serving-label source, block path, parsed Nutrition-section count, matching Serving-H3 count, fallback acceptance and rejection reason.
- Rejects zero, ambiguous and multi-Nutrition-section fallback states without guessing.
- Keeps existing 0.7.23 protected drafts valid by excluding only parser-derived serving evidence from baseline equality while retaining content, Nutrition, recipe, media, reusable blocks, metadata, Amazon and affiliate guards.
- Adds a guarded 0.7.23-to-0.7.24 updater with primary/lifecycle backups, exact loader replacement, opcode-cache invalidation, SHA-256 verification, automatic restoration and self-deactivation.
- Keeps the complete compact OpenAPI schema at 23 actions.

## NKT GPT Connector 0.7.23

- Runtime-aligned update recipe-status and status-independent hash guards.
- Exact recipe-ID map validation before draft writes.
- Full preflight and post-write recipe evidence with automatic restoration.
- Section-scoped Nutrition serving-label extraction.
- Dry-run-first exact cleanup action with live-reference refusal, protected-ID refusal, archive-first operation, and separately authorised permanent deletion.
- Complete one-line OpenAPI schema with 23 actions and no allOf/oneOf/anyOf.
