# NKT GPT Connector 0.7.27 validation

## Scope

Isolated source, schema, generated-updater, helper-runtime, protected-policy, packaging and syntax validation only. No live WordPress request or mutation occurred.

## Results

- Static/source/schema/updater checks: **77/77 passed**
- Isolated PHP runtime checks with WordPress mocks: **40/40 passed**
- Generated updater PHP syntax: passed
- Generated lifecycle PHP syntax: passed
- OpenAPI: valid compact JSON, **23 operations**, no `allOf`, `oneOf` or `anyOf`
- Deterministic updater archive: integrity verified with `unzip -tq`
- Connector Check: passed
- Theme Check: passed

## Exact production transition coverage

The before-state fixture reproduces the current raw article structure for post 34505:

- one raw `Serving: one pumpkin chocolate chip cookie` H3;
- zero sections from the legacy Nutrition parser;
- a Gutenberg table whose first cells contain nested `<strong>` labels without colons;
- numeric values in following cells using `&nbsp;` entities and parenthesised percentages;
- one `Nutrition per serving` marker;
- one Daily Values caption;
- one 2,000-calorie basis marker.

The exact proposed after-state fixture reproduces the guarded replacement:

- first-cell labels `Calories:`, `Total Fat:`, `Carbohydrates:`, `Sugars:` and `Protein:`;
- exactly one terminal ASCII colon per label;
- numeric values in following cells with em-dash percentages;
- `Percent Daily Values are based on a 2,000-calorie diet.` as the caption;
- the Serving H3 unchanged.

Both states return five distinct value-backed labels and accepted source `unique_article_serving_h3_with_unique_nutrition_presentation`. The after-state also passes the explicit `expected_serving_label_after` policy check.

## Canonical label coverage

1. Exact labels without punctuation are accepted.
2. The same labels followed by one terminal ASCII colon are accepted and mapped to punctuation-free canonical labels.
3. Whitespace before or after the single terminal colon is normalized.
4. Double colons are rejected.
5. Descriptive labels such as `Calories per serving:` are rejected.
6. Other punctuation such as semicolons is rejected.
7. Colon-terminated labels still require a numeric value in the same or following structural record.
8. Prose mentions without paired numeric values remain rejected.

## Serving and signature coverage

1. Existing parsed section-scoped extraction remains supported.
2. Existing raw section-scoped extraction remains supported.
3. The single-parser-section article-wide fallback remains supported.
4. The zero-section strongly corroborated fallback succeeds for both exact production states.
5. Fewer than four distinct value-backed labels reject the zero-section signature.
6. Duplicate `Nutrition per serving` presentations are rejected.
7. Ambiguous Serving headings are rejected.
8. Multiple parser sections are rejected.
9. Daily Values and calorie-basis markers remain required and unique.

## Protected baseline and write coverage

1. Stored 0.7.23 through 0.7.26 baselines remain compatible when only parser-derived serving evidence changes.
2. Content and Nutrition-section hashes remain protected.
3. Recipe objects and WPRM Nutrition remain protected.
4. Media, reusable blocks, metadata, Amazon destinations and affiliate identifiers remain protected.
5. Expected serving labels before and after remain explicit operation guards.
6. Exact guarded replacement counts remain enforced.
7. The exact proposed after-state passes its serving-label guard.
8. An actual post-write serving mismatch remains a protected-policy failure requiring draft restoration.

## Updater coverage

1. Source version is exactly 0.7.26 and target version is exactly 0.7.27.
2. The primary connector and installed 0.7.26 lifecycle file are backed up before replacement.
3. Version header, connector constant and lifecycle loader are verified semantically and replaced exactly once.
4. The generated 0.7.27 lifecycle SHA-256 is verified after installation.
5. PHP opcode cache is invalidated for replaced files when available.
6. WordPress caches are flushed.
7. Previous files are restored automatically after write or verification failure.
8. The updater self-deactivates after successful activation.
9. No stale escaped 0.7.24 installer patterns remain.
10. The preceding 0.7.26 updater is identified by its exact plugin name for deactivation when present.

## Action-count decision

The compact schema remains at **23 actions**. No new public action is required.

## Deployment boundary

The package has not been used to modify the live WordPress installation. Deployment must begin with a full backup, one-time updater activation, schema refresh and read-only connector/draft verification. Draft 41045 must not be updated until that verification passes.
