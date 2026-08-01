# NKT GPT Connector 0.7.25 source

This directory extends the tested connector 0.7.24 lifecycle source to 0.7.25 while retaining the internal 0723 function and metadata names required to resume existing protected drafts safely.

## Serving extraction

The primary extractor still accepts one `Serving: <label>` H3 inside the recognized top-level Nutrition section. The 0.7.24 article-wide fallback remains available when the legacy parser identifies exactly one Nutrition section.

Connector 0.7.25 adds a second, tightly guarded path for legacy articles where the parser reports zero Nutrition sections. That path accepts a serving label only when all of the following are true:

1. exactly one visible `Serving: <label>` H3 exists across the article;
2. exactly one matching raw Serving H3 exists;
3. the content immediately following that H3 contains exactly one `Nutrition per serving` marker;
4. the full article also contains exactly one `Nutrition per serving` marker;
5. at least four recognised nutrient labels are present in that bounded presentation;
6. exactly one Daily Values marker is present;
7. exactly one 2,000-calorie diet basis marker is present;
8. the parsed and raw serving labels agree.

A unique Serving H3 by itself is insufficient. Missing, duplicated, ambiguous or weakly corroborated evidence is rejected without guessing.

The status action reports the label, source, block path, parsed Nutrition-section count, matching Serving-H3 count, fallback gate, raw-H3 count, Nutrition marker counts, nutrient-label count, Daily Values count, calorie-basis count and signature decision.

## Protected baseline compatibility

Existing 0.7.23 and 0.7.24 baselines remain valid when the only difference is corrected parser-derived serving evidence. Baseline equality excludes only the serving label/hash/source/path and serving-extraction diagnostic fields. Content hashes, Nutrition hashes, recipe guards, WPRM Nutrition, media, reusable blocks, metadata, Amazon destinations, affiliate identifiers, exact replacement counts, Nutrition-change permission and serving before/after checks remain enforced.

## Updater

The one-time updater requires exactly one active 0.7.24 connector. It backs up the primary connector and 0.7.24 lifecycle file, installs the 0.7.25 lifecycle and schema, replaces the version and loader exactly once, invalidates opcode cache when available, flushes WordPress caches, verifies the installed lifecycle SHA-256, restores prior files on failure and self-deactivates after success.

## Build and test

```bash
python connector/tests/test_connector_0725.py
bash connector/build.sh
```

Tests use isolated WordPress mocks and fixtures only. They do not connect to or modify the live WordPress site.
