# Nigel's Kitchen Table Theme 2.1.0-alpha.2

## Shop My Kitchen archive-title correction

This alpha update corrects the public browser and SEO title for the retailer-independent product platform introduced in Theme 2.1.0-alpha.1.

### Corrected

- The main product archive now presents `Shop My Kitchen` in the browser title instead of the generated `Kitchen Products Archive` wording.
- Product Category and Product Brand archives now include their term name followed by `Shop My Kitchen`.
- Yoast SEO title output is aligned with the corrected public wording.

### Preserved

- The WordPress administration menu remains `Kitchen Products`.
- Product post type, taxonomies, images, galleries and retailer-link data are unchanged.
- Product archive and single-page layouts are unchanged.
- Existing recipe layouts, CSS, Pinterest functionality, Ad Inserter integration, WP Recipe Maker compatibility and NKT GPT connector compatibility are unchanged.

### Release boundary

This is a narrowly scoped acceptance hotfix following the live installation of 2.1.0-alpha.1. It must pass the existing Theme Check and package workflows and produce a new uploadable WordPress theme ZIP before installation.