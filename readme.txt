=== Nigel's Kitchen Table ===
Contributors: nigelclease-TGL
Requires at least: 6.6
Tested up to: 6.8
Requires PHP: 8.1
Stable tag: 2.0.14
License: Proprietary project theme

A premium editorial WordPress recipe theme for Nigel's Kitchen Table at thegourmetlarder.com.

== Description ==

Nigel's Kitchen Table is a custom recipe-publishing theme designed to preserve the existing content and recipe URLs while presenting one complete, consistent editorial identity.

Key features include:

* Complete Nigel's Kitchen Table logo, favicon and app-icon system
* Canonical olive, copper, ivory, linen and charcoal brand palette
* Cormorant Garamond, Source Sans 3 and Instrument Serif typography system
* Editorial homepage and portrait food photography
* WP Recipe Maker styling
* Recipe-focused search
* Recipe collections and seasonal landing pages
* Kitchen Notes and baking guides
* About Nigel and Contact layouts
* Mailchimp for WordPress integration with a direct signup-link fallback
* Cook mode, print controls and ingredient checking
* Responsive navigation and accessible search dialog
* Yoast SEO compatibility
* Staging setup assistant under Appearance
* Dedicated newsletter, welcome gift and homepage promotion options
* Editorial Standards, Affiliate Disclosure and partnership page templates
* Privacy-conscious conversion events for existing analytics integrations
* Read-only recipe and Kitchen Note content audit with CSV export
* Public brand identity safeguards for the site title, tagline, browser titles and Yoast metadata

== Installation ==

1. Take a complete UpdraftPlus backup.
2. Install and activate the theme on staging only.
3. Open Appearance > Kitchen Table Setup.
4. Create missing pages and configure the static homepage.
5. Select the homepage hero and small About portrait in the Customizer.
6. Assign Primary and Footer menus.
7. Connect Mailchimp for WordPress and enter the numeric form ID, or use the direct Mailchimp signup-link fallback.
8. Regenerate thumbnails and clear caches.
9. Open Tools > Recipe Content Audit.
10. Confirm the NKT favicon appears in the browser tab and on a mobile shortcut.
11. Open Tools > Site Health and confirm the Public brand identity test passes.
12. Complete the launch checklist in docs/launch-checklist.md.

== Changelog ==

= 2.0.14 =
* Restored the Cakes & Muffins homepage collection card after the category rename.
* Kept parent collection cards visible when recipes are assigned only to child categories.
* Included child-category recipes in homepage collection counts.
* Strengthened image overlays and forced high-contrast collection-card text for reliable readability.

= 2.0.13 =
* Changed the Recipes page category boxes and filters to show top-level categories only.
* Kept child-category recipes included inside their parent category results.
* Included child-category recipes in the displayed parent category counts.
* Prevented child categories such as Scones from remaining as separate recipe-box entries after being moved beneath Pastry.

= 2.0.12 =
* Corrected the Public brand identity Site Health test so harmless apostrophe, entity and whitespace differences do not create a false critical result.
* Allowed the site owner to use a custom non-empty tagline provided it does not use the former brand or the WordPress default tagline.
* Preserved the approved Nigel's Kitchen Table site title, browser-title and Yoast branding safeguards.

= 2.0.11 =
* Restored separate recipe selectors for the Spring & Summer, Autumn & Winter, Cakes, Biscuits and Bread collection-card covers.
* Fixed the collection-card order so it no longer changes according to category post counts.
* Kept each card linked to its collection while allowing its background image to come from a selected recipe in that collection.
* Added conservative migration support for earlier collection-cover recipe settings.

= 2.0.10 =
* Restored manual homepage recipe selectors for the hero, Latest recipes and “This is what everyone’s cooking” sections.
* Stopped the hero card from automatically switching to the newest recipe when no featured recipe is selected.
* Restored the four equal-card reader favourites layout and approved section wording.
* Kept the Mailchimp signup form embedded inside the newsletter panel when a valid form ID is configured.
* Corrected homepage and recipe newsletter colours without making the light homepage panel unreadable.

= 2.0.9 =
* Restored the direct Mailchimp signup link when an embedded Mailchimp for WordPress form is not configured.
* Corrected newsletter heading, benefit, privacy and button contrast on recipe pages and other non-homepage locations.
* Prevented administrators from seeing a public setup warning in place of the signup action.

= 2.0.8 =
* Changed category archive results headings from the generic Recipes and notes label to the current collection or category name.

= 2.0.7 =
* Migrated legacy WordPress site-title and tagline settings to Nigel's Kitchen Table when appropriate.
* Ensured browser titles and Yoast Open Graph/WebSite schema use the approved public brand name.
* Added a WordPress Site Health test that detects an incorrect public brand identity.

= 2.0.6 =
* Corrected recipe heading styling so H3-H6 subsection headings never inherit the copper H2 accent line, including headings inside Groups and Columns.
* Preserved the copper accent and larger typography for main H2 recipe sections only.

= 2.0.5 =
* Corrected nested recipe heading hierarchy styling for subsection headings inside Group and Columns blocks.

= 2.0.4 =
* Improved recipe hero image loading by using the selected original featured-image attachment directly.

= 2.0.3 =
* Corrected recipe heading hierarchy so subsection headings use smaller styling without the copper accent line.

= 2.0.2 =
* Fixed recipe-card link markup so the image, recipe title and View recipe or Read the note action are all clickable.
* Removed invalid nested links around category metadata for better browser consistency and accessibility.

= 2.0.1 =
* Switched the website header to the approved bundled Nigel's Kitchen Table wordmark.
* Prevented an older WordPress custom-logo upload with excess transparent space from making the header branding appear too small.
* Kept the compact approved logo for mobile screens.

= 2.0.0 =
* Completed brand alignment across browser icons, app icons, the WordPress editor palette and visible social branding.
* Added a branded SVG favicon, multi-size ICO, Apple touch icon, Android shortcut icon and web app manifest.
* Reconciled the published brand colours with the premium logo system.
* Removed the conflicting pill-button default from the editor and restored the approved compact button radius.
* Retained existing social URLs while presenting Nigel's Kitchen Table as the visible brand.

= 1.9.0 =
* Added a read-only Recipe Content Audit under Tools.
* Added recipe and Kitchen Note readiness columns to the Posts screen.
* Added a Kitchen Table readiness checklist to the post editor.
* Added CSV export for editorial planning and content-improvement sprints.
* Added Site Health and setup checks for missing featured images, excerpts, categories and WP Recipe Maker cards.
* Preserved the date-free evergreen recipe presentation introduced in v1.8.0.

= 1.8.0 =
* Added the complete business-growth and trust-page system.
* Added a dedicated newsletter landing page, configurable newsletter copy and optional welcome gift.
* Added an optional homepage promotion that remains hidden until configured.
* Added Work with Nigel, Editorial Standards and Affiliate Disclosure page templates.
* Added privacy-conscious conversion events for existing analytics integrations without loading a tracker.
* Removed visible publication dates from recipe pages and recipe cards while retaining dates on Kitchen Notes and in hidden SEO metadata.
* Expanded setup and Site Health checks for newsletter, commercial transparency and measurement readiness.

= 1.7.0 =
* Added launch-readiness checks to WordPress Site Health and the theme setup screen.
* Improved fallback SEO metadata, social image details, canonical URLs and structured data.
* Added accessible focus management for the search dialog and mobile navigation.
* Fixed footer links so unpublished Newsletter or legacy Collection URLs cannot create avoidable 404s.
* Added conservative performance, reduced-motion, print and final accessibility refinements.

= 1.0.0-rc2 =
* Added final Nigel's Kitchen Table brand system.
* Added homepage, recipes hub, collections, Kitchen Notes, About and Contact templates.
* Added setup assistant and staging checklist.
* Added WP Recipe Maker integration, cook mode, print tools and related recipes.
* Added recipe-focused search and accessible navigation.
* Added automated PHP, JSON and package checks through GitHub Actions.

== Upgrade Notice ==

= 2.0.14 =
Replace the current theme, clear all caches, then confirm that all five homepage collection cards appear and their text remains readable over every selected image.
