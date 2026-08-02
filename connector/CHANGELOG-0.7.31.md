# NKT GPT Connector 0.7.31 changelog

- Adds the exact protected complete-recipe correction scope `recipe_name_only` to the existing clone, audit, review and apply lifecycle.
- Retains `nutrition_section_only` without behavioural changes and does not expose a generic recipe-edit scope.
- Requires one fresh article revision draft and one fresh isolated recipe clone; earlier drafts and clones are retained as evidence and are never reused or overwritten.
- Allows the sole currently referenced connector-created live recipe to act as source only when explicitly authorised and when the existing skip guard is deliberately disabled.
- Restricts a `recipe_name_only` clone update to one non-empty changed `name` value. Summary, notes, comments, article, instructions, ingredients, times, servings, Nutrition, media, taxonomies, equipment and other metadata are rejected before writing.
- Captures exact source, clone, article, metadata, taxonomy, component, object, status-independent and WPRM Nutrition evidence.
- Verifies after update that only the name changed; any delegated-write error or unexpected protected difference restores exact live, draft, source and clone snapshots.
- Adds a strict audit with authorised- and unexpected-difference manifests. The draft article may differ only through the exact source-recipe-ID to clone-recipe-ID substitution.
- Requires a fresh current passing audit before approval or apply and prevents a second apply.
- Verifies after apply that the public article references the clone exactly once, the approved name is present, the source recipe and retained draft remain, and every other protected article and recipe field is unchanged.
- Restores exact pre-apply snapshots after delegated failure or failed post-write verification.
- Adds the isolated Apricot Cinnamon Cake production fixture identifiers and hashes without calling the live WordPress connector.
- Updates the compact OpenAPI schema while retaining 28 unique actions, descriptions within 300 characters and no `allOf`, `oneOf` or `anyOf` combinators.
- Adds a guarded deterministic 0.7.30-to-0.7.31 updater with backups, syntax and SHA-256 verification, cache invalidation, full restoration and self-deactivation.
- Does not archive, Trash, delete, repair, migrate or clean up any WordPress object.
