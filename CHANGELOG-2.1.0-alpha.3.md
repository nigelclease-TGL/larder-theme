# Nigel's Kitchen Table Theme 2.1.0-alpha.3

## Phase 2: recipe and product integration

This cumulative alpha release connects recipe articles to the retailer-independent Shop My Kitchen product platform while keeping every recommendation under explicit editorial control.

### Added

- Structured recipe-to-product relationships stored as `_nkt_recipe_product_ids`.
- A Recommended Products panel in the recipe editor.
- Selection of published or draft Kitchen Products while editing recipes.
- A dynamic `nkt/recommended-products` Gutenberg block.
- Block controls for linked products, manually selected products, heading text, introduction and retailer-button visibility.
- A `[nkt_recommended_products]` shortcode for reusable and legacy content areas.
- Responsive recommended-product cards for recipe articles.
- Primary retailer buttons with sponsored-link attributes and existing affiliate-click analytics conventions.
- Reverse “Recipes using this product” discovery on individual product pages.
- Linked-recipe counts in the Kitchen Products administration list.
- REST API exposure for recipe-product relationship data.

### Editorial safeguards

- No product is automatically inserted into an existing recipe.
- No draft product is displayed publicly.
- No product or recipe is automatically published.
- No live price is stored or displayed.
- Retailer storage remains generic and supports Amazon UK, future retailers and Nigel's own products.

### Preserved

- Existing recipe content, layouts and URLs.
- WP Recipe Maker compatibility.
- Pinterest functionality.
- Ad Inserter integration.
- NKT GPT connector compatibility.
- Existing Shop My Kitchen archive and product templates.
- Existing CSS outside the new scoped Recommended Products stylesheet.
