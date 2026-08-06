# Offline Hungarian tooling

These scripts do not connect to WordPress.

1. `build_translation_workspace.py` splits the English source manifest into reviewable Hungarian batches.
2. `validate_translation_workspace.py` checks translation structure and reports unsafe changes.
3. `build_draft_import_plan.py` includes only reviewed or approved records and creates a dry-run draft plan.
4. `validate_draft_import_plan.py` confirms that the plan cannot overwrite English content or publish automatically.

Run all commands from this directory or call the scripts using their full paths. See `../OFFLINE-IMPORT-WORKFLOW.md` for the complete process.
