# Theme 2.1 Phase 2 implementation

## Relationship model

Recipe posts store Kitchen Product relationships in `_nkt_recipe_product_ids` as an ordered array of product post IDs. The field is sanitised, editable only by users who can edit posts and exposed through the WordPress REST API for future NKT integrations.

## Editor workflow

A Recommended Products panel appears on recipe posts. Editors can link published or draft Kitchen Products without changing the article body. Draft products remain unavailable to readers.

The dynamic `nkt/recommended-products` Gutenberg block supports two modes:

1. Use the products linked to the current recipe.
2. Use an explicit product selection unique to that block.

The block also controls the section heading, optional introduction and whether the primary retailer button is displayed.

For legacy or reusable content areas, the same renderer is available through `[nkt_recommended_products]`.

## Public output

Recommended-product cards display editorial product information and link first to Nigel's product page. An optional primary-retailer button uses `nofollow sponsored noopener noreferrer` and the existing affiliate-click event convention.

No live price is stored or shown.

## Reverse discovery

Published product pages query the relationship field and show published recipes that use or recommend the product. Products without linked recipes are unchanged.

## Safety boundary

The implementation does not automatically insert products into any recipe, publish any draft, edit any WPRM recipe card or alter existing article content.
