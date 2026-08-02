# NKT GPT Connector 0.7.26 validation

## Scope

Isolated source, schema, generated-updater, helper-runtime, protected-policy, packaging and syntax validation only. No live WordPress site was inspected or mutated.

## Results

- Static/source/schema/updater checks: **67/67 passed**
- Isolated PHP runtime checks with WordPress mocks: **25/25 passed**
- Generated updater PHP syntax: passed
- Generated lifecycle PHP syntax: passed
- OpenAPI: valid compact JSON, **23 operations**, no `allOf`, `oneOf` or `anyOf`
- Deterministic updater archive: integrity verified with `unzip -tq`
- Connector Check: passed
- Theme Check: passed

## Production-shaped evidence

The positive fixture reproduces the confirmed article structure:

- one raw `Serving: one pumpkin chocolate chip cookie` H3;
- a Gutenberg `core/table` presentation;
- five rows whose first cells contain nested `<strong>` labels;
- second cells containing numeric values separated with `&nbsp;` entities;
- no literal colon after any nutrient label;
- one `Nutrition per serving` marker;
- one Daily Values caption;
- one 2,000-calorie basis marker;
- zero sections from the legacy Nutrition parser.

The isolated result is five distinct value-backed nutrient labels and accepted source `unique_article_serving_h3_with_unique_nutrition_presentation`.

## Structured nutrient evidence coverage

1. Exact recognised labels in first table cells are accepted only when a following cell contains a numeric value.
2. Nested markup and HTML entities are normalized.
3. Conventional `Label: numeric value` text remains supported.
4. Same-record and adjacent-record list, paragraph and column layouts are supported.
5. Distinct labels are counted once even when repeated.
6. Prose mentions without numeric values are rejected.
7. Table labels with nonnumeric value cells are rejected.
8. Fewer than four distinct value-backed labels reject the zero-section signature.
9. Duplicate `Nutrition per serving` presentations are rejected.
10. Ambiguous Serving headings and multiple parser sections remain rejected.
11. Existing section-scoped and single-parser-section extraction paths remain supported.

## Protected baseline and write coverage

1. Stored 0.7.23, 0.7.24 and 0.7.25 baselines remain compatible when only parser-derived serving evidence changes.
2. Content and Nutrition-section hashes remain protected.
3. Recipe objects and WPRM Nutrition remain protected.
4. Media, reusable blocks, metadata, Amazon destinations and affiliate identifiers remain protected.
5. Expected serving labels before and after remain explicit operation guards.
6. Exact guarded replacement counts remain enforced.
7. A post-write serving mismatch remains a protected-policy failure with draft restoration.

## Updater coverage

1. Source version is exactly 0.7.25 and target version is exactly 0.7.26.
2. The primary connector and installed 0.7.25 lifecycle file are backed up before replacement.
3. Version header, connector constant and lifecycle loader must each match and change exactly once.
4. The generated 0.7.26 lifecycle SHA-256 is verified after installation.
5. PHP opcode cache is invalidated for replaced files when available.
6. WordPress caches are flushed.
7. Previous files are restored automatically after write or verification failure.
8. The updater self-deactivates after successful activation.

## Action-count decision

The compact schema remains at **23 actions**. No new public action is required.

## Limitation

The package is not integration-tested against the live WordPress installation. Deployment must begin with a full backup followed by read-only connector and draft-status verification. Draft 41045 must not be updated until that verification passes.
