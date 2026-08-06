# Nigel's Kitchen Table Theme 2.1 — Phase 2

## Recipe and product integration

This cumulative Theme 2.1 milestone connects the retailer-independent Kitchen Products platform to recipe articles without altering existing recipe content or automatically inserting commercial material.

### Added

- Structured `_nkt_recipe_product_ids` relationship meta for recipe posts.
- A Recommended Products editor panel on recipe posts.
- Selection of published or draft Kitchen Products while editing a recipe.
- A dynamic `nkt/recommended-products` Gutenberg block.
- Block controls for linked products, manually selected products, section wording and retailer-button visibility.
- A `[nkt_recommended_products]` shortcode for legacy and reusable content areas.
- Branded product cards designed for placement inside recipe articles.
- Primary retailer buttons with sponsored-link attributes and existing click-event conventions.
- Automatic hiding of draft products from public recipe pages.
- Reverse “Recipes using this product” discovery on individual product pages.
- Linked-recipe counts in the Kitchen Products administration list.
- REST API exposure for the recipe-to-product relationship.

### Editorial boundary

- Products are not automatically inserted into existing recipes.
- A recipe displays recommendations only after an editor links products and inserts the Recommended Products block or shortcode.
- Draft products remain private.
- No price is stored or displayed.
- Retailer names remain flexible and are not tied to Amazon.

### Preserved

- Existing recipe text, layouts and URLs.
- All existing CSS outside the new scoped Recommended Products stylesheet.
- Pinterest functionality.
- Ad Inserter integration.
- WP Recipe Maker compatibility.
- NKT GPT connector compatibility.
- Existing Shop My Kitchen archive and product templates.

### Packaging plan

This milestone is developed and reviewed independently, but Nigel will receive one cumulative WordPress theme ZIP after the approved Theme 2.1 work is complete rather than installing each intermediate development package.
