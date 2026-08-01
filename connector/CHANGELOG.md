# Changelog

## NKT GPT Connector 0.7.24

- Preserves the existing section-scoped Nutrition serving-heading extractor as the primary path.
- Adds a safe article-wide fallback that requires exactly one legacy Nutrition section and exactly one visible `Serving: <label>` H3.
- Recursively scans parsed Gutenberg blocks and falls back to raw H3 markup with nested-markup removal, HTML-entity decoding and whitespace normalization.
- Reports serving-label source, block path, parsed Nutrition-section count, matching Serving-H3 count, fallback acceptance and rejection reason.
- Rejects zero, ambiguous and multi-Nutrition-section fallback states without guessing.
- Keeps existing 0.7.23 protected drafts valid by excluding only parser-derived serving evidence from baseline equality while retaining content, Nutrition, recipe, media, reusable-block, metadata, Amazon and affiliate guards.
- Adds a guarded 0.7.23-to-0.7.24 updater with primary/lifecycle backups, exact loader replacement, opcode-cache invalidation, SHA-256 verification, automatic restoration and self-deactivation.
- Keeps the complete compact OpenAPI schema at 23 actions.

## NKT GPT Connector 0.7.23

- Runtime-aligned update recipe-status and status-independent hash guards.
- Exact recipe-ID map validation before draft writes.
- Full preflight and post-write recipe evidence with automatic restoration.
- Section-scoped Nutrition serving-label extraction.
- Dry-run-first exact cleanup action with live-reference refusal, protected-ID refusal, archive-first operation, and separately authorised permanent deletion.
- Complete one-line OpenAPI schema with 23 actions and no allOf/oneOf/anyOf.
