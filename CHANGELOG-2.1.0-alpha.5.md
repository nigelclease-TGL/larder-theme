# Nigel's Kitchen Table 2.1.0-alpha.5

## Compact recommendation cards and clearer shopping CTAs

- Reduces the visual footprint of Recommended Products sections on recipe pages.
- Limits desktop product cards to a compact editorial width and reduces image height, padding and heading scale.
- Keeps one-column mobile behaviour and responsive multi-product layouts.
- Changes the internal product-card CTA to `View in Shop My Kitchen`.
- Changes standard retailer CTAs to `Shop at {Retailer}`, including `Shop at Amazon UK`.
- Migrates only legacy generated `View at {Retailer}` button text; genuinely custom retailer wording is preserved.
- Preserves retailer URLs, sponsored/nofollow attributes, recipe-product relationships, WPRM data, Pinterest, Ad Inserter and NKT GPT connector behaviour.

The Theme 2.1 custom Recommended Products Gutenberg block remains outside this release's acceptance scope following the earlier live editor crash. The existing shortcode rendering route is unchanged and remains the safe editorial route for current recipe work.
