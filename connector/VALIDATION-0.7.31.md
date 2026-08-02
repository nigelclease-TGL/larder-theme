# NKT GPT Connector 0.7.31 validation

## Scope

Repository-only implementation, generation, OpenAPI validation, updater packaging, PHP syntax and isolated protected-lifecycle fixtures. Development and CI make no live WordPress request and change no WordPress object.

## Exact capability

Connector 0.7.31 adds only `recipe_name_only` to the five existing complete-recipe lifecycle actions:

1. `startCompleteRecipeRevision`
2. `updateClonedRecipeRevisions`
3. `auditClonedRecipeRevision`
4. `reviewRecipeRevision`
5. `applyCompleteRecipeRevision`

The established `nutrition_section_only` path delegates unchanged to the existing callbacks. Unknown and generic recipe-edit scopes remain rejected.

## Protected creation

A name-only start must use the current live article and current sole live recipe, preserve all prior revision evidence, and create a fresh draft and fresh clone. A current live source that is itself a connector clone requires explicit `allow_current_live_connector_clone_source: true` and `skip_live_connector_clones: false`.

The extension captures the live article row and metadata, source recipe row and metadata, taxonomies, object and status-independent hashes, protected component hashes and WPRM Nutrition hash. It verifies that creation changed neither the live article nor source recipe and that the draft differs only through the exact recipe-ID substitution. On failure, source-side state is restored and any discovered new objects are retained and marked as failure evidence.

## Protected update, audit, review and apply

For a stored `recipe_name_only` baseline, update accepts exactly one item containing only `draft_post_id`, `cloned_recipe_id` and `name`. The name must be non-empty and differ from the source. Every extra key is rejected before delegation. After delegation, every non-name recipe field, WPRM Nutrition, source recipe, live article and draft article is verified. Any error or drift restores all exact pre-write snapshots.

Audit returns explicit authorised- and unexpected-difference manifests. Approval requires a fresh passing current-state audit hash. Apply requires the same approved hash, may run once, preserves the source and draft, and verifies exact article recipe-ID substitution, recipe order/count, target name, protected article evidence, recipe fields and Nutrition. Delegated apply failure or failed post-write verification triggers exact restoration.

## Apricot Cinnamon Cake isolated fixture

- live post ID: `8657`
- article title: `Apricot Cinnamon Cake`
- ordered source recipe IDs: `[38952]`
- source recipe name: `Apricot Cinnamon Cake – Revision`
- target recipe name: `Apricot Cinnamon Cake`
- source object hash: `b17a5945eb753717dc7fd4edaf81d4b33ce1be013f1d5647e68f5f006e28dbcd`
- source status-independent hash: `973571af40e234f2163590b8b7705c838850530a7b4ddf4bdabe5d9ef5451f11`
- source WPRM Nutrition hash: `4f53cda18c2baa0c0354bb5f9a3ecbe5ed12ab4d8e11ba873c2f11161202b945`
- historical recipe ID: `8664`
- prior applied draft ID: `38951`

The fixture is synthetic and proves preservation rules without connecting to WordPress.

## Automated checks

The suite verifies:

1. exact connector, updater and schema versions;
2. both valid scopes and rejection of generic scopes;
3. unchanged delegation for `nutrition_section_only`;
4. fresh pair and explicit connector-clone-source guards;
5. pre-write rejection of empty, unchanged and multi-field name updates;
6. exact name-only substantive comparison;
7. restoration paths for delegated errors, hook-induced drift and post-apply verification failure;
8. strict audit manifests and article recipe-ID-only substitution;
9. stale-audit approval/apply rejection and single-apply enforcement;
10. retained source, draft and historical evidence requirements;
11. absence of archive, Trash, permanent deletion, repair, migration and cleanup calls from the extension;
12. retention of the 0.7.30 reusable-block evidence action and every protected lifecycle action;
13. exactly 28 unique OpenAPI operations, descriptions no longer than 300 characters and no schema combinators;
14. guarded 0.7.30-to-0.7.31 updater markers, backups, restoration, cache invalidation and self-deactivation;
15. generated PHP syntax and deterministic release ZIP integrity.

## Deployment boundary

Deployment acceptance is capability-only. It must confirm version 0.7.31, the five complete-recipe actions, both valid scopes and zero WordPress changes. The Apricot Cinnamon Cake correction remains a later separately authorised issue #68 execution.
