# Safe offline import workflow

This process creates **plans only**. It does not connect to WordPress and cannot
publish or alter English content.

## 1. Build the translation workspace

```bash
python offline-tools/build_translation_workspace.py \
  nkt-english-source.json \
  workspace
```

Translate only fields inside each `target` object. Keep items as
`not_started`, `translated`, `reviewed` or `approved`.

## 2. Validate the translated workspace

```bash
python offline-tools/validate_translation_workspace.py \
  workspace \
  --report workspace-validation.json
```

The report must contain zero errors before an import plan is generated.

## 3. Build the no-write draft plan

```bash
python offline-tools/build_draft_import_plan.py \
  workspace \
  hungarian-draft-import-plan.json
```

By default, only `approved` items are included. Recipe objects are ordered before
articles so WP Recipe Maker references can later be mapped safely.

## 4. Validate the plan

```bash
python offline-tools/validate_draft_import_plan.py \
  hungarian-draft-import-plan.json \
  --report import-plan-validation.json
```

The plan records source fingerprints so a future WordPress importer can refuse
to write when the English source has changed since export.

## Safeguards recorded in every plan

- English source records are read-only.
- Hungarian targets must be created as drafts.
- Automatic publishing is forbidden.
- Automatic taxonomy creation is forbidden because WordPress terms do not have
  a draft state.
- Polylang linking occurs only after the Hungarian draft exists.
- WP Recipe Maker metadata must be translated; untranslated recipe metadata must
  not be copied into a Hungarian recipe.
- Unresolved recipe references are listed explicitly.

No WordPress importer should be enabled until the first real manifest has been
translated, reviewed and used to test these plans against a disposable staging
copy.
