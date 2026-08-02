## Summary

- replaces the 0.7.25 colon-only nutrient counter with structured, value-backed evidence
- recognises Gutenberg table rows whose first cell is an exact nutrient label and whose following cell contains a numeric value
- preserves colon-form text and adds boundary-aware paragraph, list, column and adjacent-record support
- counts distinct labels only and rejects prose mentions or labels without numeric values
- adds a production-shaped fixture matching post 34505: nested `<strong>` labels, separate table cells, `&nbsp;` values and no label colons
- retains all earlier serving-extraction paths and protected baseline compatibility
- adds a guarded 0.7.25-to-0.7.26 updater and keeps 23 OpenAPI actions

## Safety

Repository tests use isolated mocks and fixtures only. No live WordPress request or mutation is performed. Drafts 40706, 40707 and 41045, live post 34505 and recipe 34548 are not touched.

## Status

Draft PR. Do not merge until CI, deterministic package checksum and read-only deployment verification pass.
