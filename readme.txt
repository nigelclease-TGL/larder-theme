=== Nigel's Kitchen Table ===
Contributors: nigelclease-TGL
Requires at least: 6.6
Tested up to: 6.8
Requires PHP: 8.1
Stable tag: 2.1.0-alpha.3
License: Proprietary project theme

A premium editorial WordPress recipe theme for Nigel's Kitchen Table at thegourmetlarder.com.

== Description ==

Nigel's Kitchen Table is a custom recipe-publishing theme that preserves existing recipe content, URLs and search visibility while presenting the approved editorial brand.

Key features include:

* Nigel's Kitchen Table logo, favicon and mobile home-screen icon system
* Olive, copper, ivory, linen and charcoal brand palette
* Cormorant Garamond, Source Sans 3 and Instrument Serif typography
* Editorial homepage, recipe collections and Kitchen Notes
* Retailer-independent Shop My Kitchen product platform
* Recipe-to-product relationships and reusable Recommended Products blocks
* WP Recipe Maker and Yoast SEO compatibility
* Mailchimp, Contact and commercial-transparency integrations
* Public identity, staging-indexing and content-readiness safeguards

== Installation ==

1. Take a complete UpdraftPlus backup.
2. Open Appearance > Themes > Add New Theme > Upload Theme.
3. Upload the installable theme ZIP without unzipping it.
4. Choose Replace current with uploaded.
5. Clear WP Super Cache and the browser cache.
6. Confirm the active theme shows version 2.1.0-alpha.3.

== Changelog ==

= 2.1.0-alpha.3 =
* Adds structured recipe-to-product relationships through the recipe editor.
* Adds a dynamic Recommended Products Gutenberg block with linked-product and manual-product modes.
* Adds a reusable shortcode for legacy and reusable content areas.
* Adds responsive recommendation cards with editorial product links and optional primary-retailer buttons.
* Adds reverse Recipes using this product discovery on published product pages.
* Adds linked-recipe counts to Kitchen Products administration.
* Keeps draft products private and leaves all existing recipes unchanged until an editor inserts the block or shortcode.

= 2.1.0-alpha.2 =
* Corrects the Shop My Kitchen browser and SEO title so the product archive no longer appears as Kitchen Products Archive.
* Applies matching titles to Product Category and Product Brand archives.
* Preserves the Kitchen Products wording inside the WordPress administration area.

= 2.1.0-alpha.1 =
* Adds the Kitchen Products custom post type with a public Shop My Kitchen archive and individual product pages.
* Adds hierarchical Product Categories and flexible Product Brands taxonomies.
* Adds primary product images, optional product galleries and a media-library product editor.
* Adds retailer-independent repeatable retailer links, with Amazon UK as the first suggested retailer and support for future retailers or Nigel's own products.
* Adds branded product cards, product archive filters, retailer buttons and commercial-transparency notices.
* Adds product data to the WordPress REST API for future integrations while preserving the existing recipe, Pinterest, Ad Inserter, WP Recipe Maker and NKT GPT connector functionality.

= 2.0.45 =
* Displays recipe H2 section headings in title case so labels such as Recipe Tips use an initial capital on each word.
* Applies the same title-case presentation to the automatically generated Contents links.
* Keeps the Contents heading itself unchanged.

= 2.0.44 =
* Converts legacy all-capital recipe H2 headings to sentence case on public recipe pages.
* Updates the generated Contents list from the corrected heading wording so article headings and navigation remain consistent.
* Preserves headings that already use intentional mixed case and protects common acronyms such as UK, FAQ, SEO and WPRM.
* Standardises the generated single-recipe label to Recipe card.

= 2.0.43 =
* Includes every WPRM recipe card in the automatic Contents list when a post contains two or more recipes.
* Uses each recipe name to distinguish multiple recipe links, with numbered Recipe Card labels as a fallback.
* Keeps each recipe link in its correct position in the article and gives every card a unique working anchor.

= 2.0.42 =
* Replaces each stored recipe Contents panel on the public page with a canonical list generated from the current H2 sections.
* Adds the branded Contents panel automatically where it is missing.
* Creates unique working in-page anchors, keeps the fixed-header scroll offset and adds one Recipe Card link when a WPRM card exists.
* Excludes headings inside recipe cards, sharing blocks and Pinterest panels so the list stays limited to article sections.

= 2.0.26 =
* Redesigned only the main Recipes page with the approved Explore the Recipe Box layout.
* Shows all active recipe categories immediately without a secondary reveal or exact recipe count.
* Places a soft-sage recipe search panel beside the categories on desktop.
* Uses a compact single-line search above the two-column category grid on mobile.
* Leaves the homepage, individual recipes, archives, collections and every other page unchanged.

= 2.0.25 =
* Replaced the mobile home-screen artwork with the approved NKT icon that keeps the complete white border visible.
* Reduced and centred the NKT lettering so Android launcher masks do not crop the text.
* Updated the verified 180 x 180 Apple touch icon and 192 x 192 Android maskable icon.
* Updated icon cache-busting references to version 2.0.25.

= 2.0.24 =
* Added a visible Recipe by Nigel Clease and Kitchen-tested byline beneath every recipe title.
* Added an honest Updated month and year that appears only after the verified revision workflow records a meaningful update.
* Strengthened the comments invitation to request ratings, results, questions and useful reader tips.
* Expanded the Recipe Content Audit to check Yoast fields, internal links, all article image alt text, heading structure, standard editorial sections, exactly one WPRM card and essential recipe-card data.

= 2.0.23 =
* Removed the unnecessary 512 x 512 icon after GitHub binary transfer repeatedly damaged that file.
* Kept the verified 192 x 192 Android maskable icon and 180 x 180 Apple touch icon.
* Retained the final borderless NKT design, olive full-square background and copper underline.
* Updated icon cache-busting references to version 2.0.23.

= 2.0.22 =
* Replaced the 512 x 512 shortcut image with a compact PNG and updated icon references.

= 2.0.21 =
* Replaced the shortcut artwork with the final approved NKT icon without a white border.
* Filled the complete square background in olive so Android and iPhone can apply their own icon mask without distortion.

= 2.0.20 =
* Added verified Apple and Android shortcut image sizes and removed the invalid ICO fallback.

Earlier release history remains available in the Git repository.

== Upgrade Notice ==

= 2.1.0-alpha.3 =
Install this cumulative alpha release to add recipe-to-product relationships and reusable Recommended Products blocks while preserving the existing Shop My Kitchen platform and recipe functionality.