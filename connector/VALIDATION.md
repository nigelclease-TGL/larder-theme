# NKT GPT Connector 0.7.25 validation

## Scope

Isolated source, schema, updater, helper-runtime, protected-policy, packaging and syntax validation only. No live WordPress site was inspected or mutated.

## Results

- Static/source/schema/updater checks: **68/68 passed**
- Isolated PHP runtime checks with WordPress mocks: **32/32 passed**
- PHP syntax: updater and generated lifecycle extension passed `php -l`
- OpenAPI: valid compact JSON, **23 operations**, no `allOf`, `oneOf` or `anyOf`
- Deterministic updater archive: integrity verified with `unzip -tq`

## Serving extraction coverage

1. The existing section-scoped parsed Gutenberg path remains supported.
2. The 0.7.24 unique-H3 fallback remains supported when the legacy parser reports exactly one Nutrition section.
3. The confirmed production H3 is extracted when the legacy parser reports zero sections only when a unique raw Nutrition presentation immediately follows it.
4. Zero-section acceptance requires one raw Serving H3, one segment-local and one article-wide `Nutrition per serving` marker, at least four recognised nutrient labels, one Daily Values marker and one 2,000-calorie basis marker.
5. Nested inline markup such as `<strong>Serving:</strong>` is normalized to visible text.
6. Parsed Gutenberg blocks are traversed recursively.
7. A unique Serving H3 without the corroborating Nutrition signature is rejected.
8. Two visible Serving H3 headings are rejected as ambiguous.
9. No visible Serving H3 returns an empty result without guessing.
10. Multiple parsed Nutrition sections reject the article-wide fallback.
11. An unavailable parser count rejects the fallback.
12. Status evidence includes the final source, block path, parser-section count, matching-H3 count, fallback gate, raw-H3 count, marker counts, nutrient count, signature decision and rejection reason.

## Protected baseline and write coverage

1. Stored 0.7.23 and 0.7.24 baselines with empty parser-derived serving evidence remain equal to an otherwise identical 0.7.25 state with corrected serving evidence and diagnostics.
2. Content-hash and Nutrition-section-hash differences remain visible and protected.
3. Expected serving labels before and after remain explicit operation guards.
4. Exact guarded replacement counts remain enforced.
5. A real post-write serving mismatch remains a protected-policy failure and the update restoration path remains present.
6. Recipe objects, WPRM Nutrition, media, reusable blocks, metadata, Amazon destinations and affiliate identifiers remain protected by the existing lifecycle.

## Updater coverage

1. Source version is exactly 0.7.24 and target version is exactly 0.7.25.
2. The primary connector and installed 0.7.24 lifecycle file are backed up before replacement.
3. Version header, connector constant and lifecycle loader must each match and change exactly once.
4. The generated 0.7.25 lifecycle SHA-256 is verified after installation.
5. PHP opcode cache is invalidated for replaced files when available.
6. WordPress caches are flushed.
7. Previous files are restored automatically after write or verification failure.
8. The updater self-deactivates after successful activation.

## Action-count decision

The compact schema remains at **23 actions**. No additional action is required because serving diagnostics are returned by the existing read-only `getProtectedArticleRevisionStatus` action.

## Limitations

The package has not been integration-tested against the live WordPress installation. Deployment must begin with a full backup followed by read-only connector and draft-status checks. Existing protected draft 41045 must not be updated until those checks confirm the corrected serving evidence and preserved baseline validity.
