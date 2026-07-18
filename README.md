# Nigel's Kitchen Table WordPress Theme

A custom editorial recipe theme for **Nigel's Kitchen Table**, running at `thegourmetlarder.com` while preserving the existing recipe URLs and search visibility.

The full positioning, visual system, voice, content pillars and business concept are documented in [`docs/brand-master.md`](docs/brand-master.md). Treat that file as the source of truth for future design and content decisions.

## Design direction

- Warm, modern editorial presentation
- Large recipe photography
- Small, secondary portrait treatment
- Cormorant Garamond headings
- Source Sans 3 body copy
- Instrument Serif accents
- Warm cream `#FAF7F2`
- Porcelain `#FDFCF9`
- Linen `#E7E1D8`
- Soft sage `#A7B397`
- Deep olive `#66785F`
- Forest olive `#465443`
- Copper `#B87546`
- Charcoal `#2E2E2E`

The final global tokens and component alignment are applied in `assets/css/brand-system.css`, which loads after the page-specific and recipe-template styles.

## Main templates

- Editorial homepage
- Recipes hub
- Recipe posts with WP Recipe Maker styling
- Collections and category archives
- Kitchen Notes
- About Nigel / My Story
- Contact
- Search results
- 404 page
- Newsletter landing page and optional welcome gift
- Work with Nigel, Editorial Standards and Affiliate Disclosure pages

## WordPress requirements

- WordPress 6.6 or newer
- PHP 8.1 or newer
- WP Recipe Maker
- Yoast SEO
- Contact Form 7
- Mailchimp for WordPress for the newsletter form

The theme also supports the site's existing UpdraftPlus, WP Super Cache and thumbnail-regeneration workflow.

## Installation on staging

1. Download the `nigels-kitchen-table.zip` artifact produced by GitHub Actions, or ZIP the theme folder without the `.git`, `.github` and `build` folders.
2. In staging WordPress, open **Appearance → Themes → Add New → Upload Theme**.
3. Upload and activate the ZIP.
4. Open **Appearance → Kitchen Table Setup**.
5. Run **Create missing pages and set homepage**.
6. Open the Customizer and select the hero image, Nigel portrait and Mailchimp form ID.
7. Assign the Primary and Footer menus.
8. Regenerate thumbnails.
9. Open **Tools → Recipe Content Audit**.
10. Clear WP Super Cache.

The homepage must use a static page with **Homepage: Home** selected and the **Posts page left unselected**.

## Photo assets

The ten selected portrait recipe photographs are mapped in [`docs/photo-asset-plan.md`](docs/photo-asset-plan.md). They should be uploaded through the WordPress Media Library, not committed to the theme repository.

## Launch readiness

Version 1.9.0 adds theme-specific checks under **Tools → Site Health** for page structure, WP Recipe Maker, staging/production search visibility, HTTPS, permalinks, privacy, newsletter readiness, commercial transparency and editorial content readiness.

## Launch safeguards

- Existing posts, recipes, categories and URLs are not rewritten by theme activation.
- The quick setup creates only missing pages and configures a static homepage.
- Safe menu fallbacks remain available until WordPress menus are assigned.
- Newsletter signup is displayed only when a valid MC4WP form is configured, or when an existing Newsletter page is available.
- Legal links are displayed only when the corresponding WordPress pages exist.
- Welcome-gift and homepage-promotion sections stay hidden until a destination URL or widget is configured.
- Conversion events contain no form-field values and are sent only to an analytics data layer that already exists.
- Visible publication dates are omitted from evergreen recipe pages and recipe cards; Kitchen Notes keep their editorial dates.
- A read-only audit under **Tools → Recipe Content Audit** highlights missing featured images, alt text, excerpts, categories, recipe cards and supporting copy without changing posts or URLs.

See [`docs/editorial-workflow.md`](docs/editorial-workflow.md) and [`docs/launch-checklist.md`](docs/launch-checklist.md).

## Automated checks

Every push runs GitHub Actions to:

- lint all PHP files,
- validate JSON files,
- check required theme templates,
- check the editorial-audit files required by the release,
- build an installable WordPress ZIP artifact.
