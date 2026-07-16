# Nigel's Kitchen Table WordPress Theme

A custom editorial recipe theme for **Nigel's Kitchen Table**, running at `thegourmetlarder.com` while preserving the existing recipe URLs and search visibility.

## Design direction

- Warm, modern editorial presentation
- Large recipe photography
- Small, secondary portrait treatment
- Cormorant Garamond headings
- Source Sans 3 body copy
- Instrument Serif accents
- Deep olive, warm cream, linen, antique brass and charcoal palette

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
6. Open the Customizer and select:
   - the mango and passion fruit cheesecake as the homepage hero,
   - Nigel's portrait as the small About image,
   - the numeric Mailchimp for WordPress form ID.
7. Assign the Primary and Footer menus.
8. Regenerate thumbnails.
9. Clear WP Super Cache.

## Photo assets

The ten selected portrait recipe photographs are mapped in [`docs/photo-asset-plan.md`](docs/photo-asset-plan.md). They should be uploaded through the WordPress Media Library, not committed to the theme repository.

## Launch safeguards

- Existing posts, recipes, categories and URLs are not rewritten by theme activation.
- The quick setup creates only missing pages and configures a static homepage.
- Safe menu fallbacks remain available until WordPress menus are assigned.
- Newsletter signup is displayed only when a valid MC4WP form is configured, or when an existing Newsletter page is available.
- Legal links are displayed only when the corresponding WordPress pages exist.

See [`docs/launch-checklist.md`](docs/launch-checklist.md) for the final staging and go-live procedure.

## Automated checks

Every push to `main` runs GitHub Actions to:

- lint all PHP files,
- validate JSON files,
- check required theme templates,
- build an installable WordPress ZIP artifact.
