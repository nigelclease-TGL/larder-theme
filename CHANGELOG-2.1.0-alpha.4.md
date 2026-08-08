# Nigel's Kitchen Table 2.1.0-alpha.4

## Recommended Products editor compatibility hotfix

- Marks the JavaScript block registration explicitly as Block API version 2.
- Applies WordPress `useBlockProps()` to the Recommended Products editor wrapper, as required for API v2 blocks.
- Keeps `InspectorControls` outside the block wrapper and leaves the server-rendered preview inside the correctly registered native wrapper element.
- Prevents current Gutenberg/Floating UI selection controls from attempting to anchor to an invalid block reference.
- Preserves all Phase 2 public markup, retailer-link behaviour, recipe-to-product relationships, shortcode rendering and Kitchen Product functionality.

No recipe content, WPRM recipe data, Pinterest integration, Ad Inserter behaviour or NKT GPT connector code is changed by this hotfix.
