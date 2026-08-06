# Nigel's Kitchen Table Theme 2.1.0-alpha.1

## Phase 1: Shop My Kitchen product platform

This alpha milestone introduces the first retailer-independent product system for Nigel's Kitchen Table. Amazon UK is the first suggested retailer, but retailer data is stored as repeatable named offers so other retailers and Nigel's own future products can be added without redesigning the theme.

### Added

- `Kitchen Products` custom post type with REST API support, revisions, excerpts, custom fields and featured images.
- Hierarchical `Product Categories` taxonomy.
- Flexible non-hierarchical `Product Brands` taxonomy.
- Primary product image through WordPress featured images.
- Optional product gallery managed through the WordPress media library.
- Repeatable retailer rows containing retailer name, destination URL, optional button wording and one primary retailer.
- Default first retailer suggestion for Amazon UK without hard-coding Amazon into the public data model.
- Product list administration columns for images, categories, brands and retailer names.
- Public `/shop-my-kitchen/` product archive.
- Public product category and product brand archives.
- Individual product templates with image galleries, editorial content, retailer buttons and affiliate disclosure.
- Reusable product-card template for archive and future theme integrations.
- Responsive Shop My Kitchen and product administration styles.
- Shop My Kitchen links in safe fallback header and footer navigation.
- Product-platform validation in GitHub Actions and automatic installable theme ZIP packaging.

### Preserved

- Existing CSS and public recipe presentation.
- Recipe layouts and recipe URLs.
- Pinterest functionality.
- Ad Inserter integration points.
- WP Recipe Maker compatibility.
- NKT GPT connector code and packaging exclusions.
- Existing shortcodes, affiliate-link treatment and analytics event attributes.

### Data model

- Post type: `nkt_product`
- Category taxonomy: `nkt_product_category`
- Brand taxonomy: `nkt_product_brand`
- Product gallery meta: `_nkt_product_gallery_ids`
- Retailer offers meta: `_nkt_product_retailers`

Retailer rows use a generic structure:

- `retailer`
- `url`
- `button_text`
- `is_primary`

No price is stored in Phase 1, avoiding stale public pricing and keeping the first release focused on reliable product recommendations and destination links.

### Release boundary

This is an alpha implementation for review and upload testing. Phase 1 stops after the branch, pull request, CI validation and installable ZIP are delivered. No merge or production deployment is authorised by this milestone.
