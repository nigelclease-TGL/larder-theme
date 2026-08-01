# NKT GPT Connector 0.7.24 source

This directory extends the tested connector 0.7.23 lifecycle source to 0.7.24 while retaining the internal 0723 function and metadata names required to resume existing protected drafts safely.

## Serving extraction

The primary extractor still accepts one `Serving: <label>` H3 inside the recognized top-level Nutrition section. When that path finds no label, the 0.7.24 fallback:

1. requires exactly one Nutrition section from the legacy article parser;
2. recursively scans all parsed Gutenberg blocks for H3 headings;
3. falls back to raw H3 markup when parsed blocks provide no candidate;
4. removes nested markup, decodes entities and collapses whitespace;
5. accepts only one visible heading matching `^Serving\s*:\s*(.+)$`;
6. rejects zero, ambiguous or multi-Nutrition-section states without guessing.

The status action reports the label, source, block path, parsed Nutrition-section count, matching Serving-H3 count, fallback acceptance and rejection reason.

## Protected baseline compatibility

Existing 0.7.23 baselines remain valid when the only difference is corrected parser-derived serving evidence. Baseline equality excludes only the serving label/hash/source/path and its diagnostic count/decision fields. Content hashes, Nutrition hashes, recipe guards, WPRM Nutrition, media, reusable blocks, metadata, Amazon destinations, affiliate identifiers, exact replacement counts, Nutrition-change permission and serving before/after checks remain enforced.

## Updater

The one-time updater requires exactly one active 0.7.23 connector. It backs up the primary connector and 0.7.23 lifecycle file, installs the 0.7.24 lifecycle and schema, replaces the version and loader exactly once, invalidates opcode cache when available, flushes WordPress caches, verifies the installed lifecycle SHA-256, restores prior files on failure and self-deactivates after success.

## Build and test

```bash
python connector/tests/test_connector_0724.py
bash connector/build.sh
```

Tests use isolated WordPress mocks and fixtures only. They do not connect to or modify the live WordPress site.
