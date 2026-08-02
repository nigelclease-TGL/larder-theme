# NKT GPT Connector 0.7.27 source

This directory extends connector 0.7.26 to 0.7.27 while retaining the internal 0723 function and metadata names required to resume existing protected drafts safely.

## Serving extraction

The connector retains all established paths:

1. section-scoped parsed Gutenberg Serving H3 extraction;
2. raw section-scoped extraction;
3. unique article-wide Serving H3 extraction when the legacy parser reports exactly one Nutrition section;
4. a corroborated zero-section fallback when the legacy parser reports zero sections.

The zero-section path still requires one Serving H3, one segment-local and article-wide `Nutrition per serving` marker, at least four distinct recognised nutrient labels, one Daily Values marker, one 2,000-calorie basis marker and agreement between raw and parsed serving evidence.

## Colon-tolerant structured nutrient evidence

Connector 0.7.27 keeps the 0.7.26 structure-aware evidence extractor and fixes the standardisation transition. A nutrient label is accepted only when paired with a numeric value through one of these general structures:

- `Label: numeric value` text;
- Gutenberg or equivalent HTML table rows where the recognised label is in the first cell and a following cell contains a numeric value;
- list, paragraph, column or adjacent block records where the recognised label is immediately followed by a numeric value in the same record or the next boundary record.

For table-cell and adjacent-record labels, the canonicaliser accepts:

- the exact label, such as `Calories`;
- the same label followed by exactly one terminal ASCII colon, such as `Calories:`.

It does not accept double colons, descriptive text or unrelated punctuation. Internally, canonical labels remain punctuation-free.

This covers both production states for post 34505:

- legacy table labels without colons and parenthesised percentages;
- the exact proposed standard table with `Calories:`, `Total Fat:`, `Carbohydrates:`, `Sugars:` and `Protein:`, em-dash percentages and the standard Daily Values disclaimer.

## Protected baseline compatibility

Existing 0.7.23 through 0.7.26 baselines remain valid when the only difference is parser-derived serving evidence. Content hashes, Nutrition hashes, recipe guards, WPRM Nutrition, media, reusable blocks, metadata, Amazon destinations, affiliate identifiers, exact replacement counts, Nutrition-change permission and serving before/after checks remain enforced.

## Updater

The generated one-time updater requires exactly one active 0.7.26 connector. It backs up the primary connector and 0.7.26 lifecycle file, installs the 0.7.27 lifecycle and schema, replaces the version and loader exactly once using semantic guards, invalidates opcode cache when available, flushes WordPress caches, verifies the lifecycle SHA-256, restores prior files on failure and self-deactivates after success.

## Build and test

```bash
python connector/tests/test_connector_0727.py
bash connector/build.sh
```

Tests use isolated WordPress mocks and production-shaped fixtures only. They do not connect to or modify the live WordPress site.
