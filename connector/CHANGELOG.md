# Changelog

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
