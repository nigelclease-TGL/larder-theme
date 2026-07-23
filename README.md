# Nigel's Kitchen Table WordPress Theme

A custom editorial recipe theme for **Nigel's Kitchen Table**, developed at:

**Repository:** https://github.com/nigelclease-TGL/larder-theme  
**Website:** https://thegourmetlarder.com  
**Current theme version:** `2.0.1`

The theme presents Nigel's Kitchen Table as the public-facing brand while preserving the existing domain, recipe URLs, structured data and search visibility.

## Brand source of truth

The approved positioning, visual system, tone of voice, content pillars and business direction are documented in:

- [`docs/brand-master.md`](docs/brand-master.md)

Treat that document as the source of truth for future design, content and commercial decisions.

## Brand assets

Approved logo assets are stored in:

- [`docs/brand-assets/`](docs/brand-assets/)

Available marks:

- `nkt-logo-horizontal.svg` — primary logo for desktop headers, emails and wide layouts.
- `nkt-logo-compact.svg` — compact wordmark for narrow and mobile layouts.
- `nkt-monogram.svg` — square mark for favicons, profile images and small applications.

The live theme also includes the complete favicon and app-icon package required for browser tabs, Apple shortcuts and Android/installable shortcuts.

## Design direction

- Warm, calm editorial presentation.
- Food photography leads the visual identity.
- Nigel's portrait remains small and secondary.
- Spacious layouts with practical, readable navigation.
- Elegant without feeling exclusive.
- Knowledgeable without becoming formal or pretentious.

### Typography

- **Cormorant Garamond** — page titles, recipe titles and major headings.
- **Source Sans 3** — body copy, navigation, labels, buttons and forms.
- **Instrument Serif** — introductions, tips, pull quotes and editorial accents.

### Canonical colour palette

| Token | Hex | Primary use |
|---|---:|---|
| Ivory | `#F8F5EF` | Main page and editor background |
| Porcelain | `#FDFCF9` | Cards, forms and light surfaces |
| Linen | `#EFE9DF` | Borders, separators and quiet panels |
| Soft sage | `#A7B397` | Secondary backgrounds and subtle accents |
| Olive | `#66785F` | Primary brand colour and selected states |
| Forest olive | `#465443` | Buttons, footer and high-contrast areas |
| Copper | `#9A6B46` | Links, dividers, labels and restrained highlights |
| Charcoal | `#2E2E2B` | Primary text and logo lettering |
| Muted grey | `#66635D` | Secondary text and supporting details |

Olive and ivory should dominate. Copper is an accent rather than a large background colour.

The final global tokens and component alignment are applied in `assets/css/brand-system.css`, which loads after the page-specific and recipe-template styles.

## Main templates and experiences

- Editorial homepage.
- Recipes hub.
- Recipe posts with branded WP Recipe Maker styling.
- Collections and category archives.
- Kitchen Notes.
- About Nigel / My Story.
- Contact page.
- Search results.
- 404 page.
- Newsletter landing page and optional welcome gift.
- Work with Nigel, Editorial Standards and Affiliate Disclosure pages.

## WordPress requirements

- WordPress `6.6` or newer.
- Tested up to WordPress `6.8`.
- PHP `8.1` or newer.
- WP Recipe Maker.
- Yoast SEO.
- Contact Form 7.
- Mailchimp for WordPress for the newsletter form.

The theme also supports the site's existing UpdraftPlus, WP Super Cache and thumbnail-regeneration workflow.

## Installation on staging

1. Download the installable theme ZIP produced by GitHub Actions, or ZIP the theme folder without the `.git`, `.github` and `build` folders.
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

The selected portrait recipe photographs are mapped in [`docs/photo-asset-plan.md`](docs/photo-asset-plan.md). They should be uploaded through the WordPress Media Library rather than committed to this repository.

## Release and launch readiness

Version `2.0.1` includes the approved Nigel's Kitchen Table brand system, responsive horizontal and compact logos, the NKT monogram, favicon package and final header logo sizing.

Theme-specific checks under **Tools → Site Health** cover:

- page structure,
- WP Recipe Maker,
- staging and production search visibility,
- HTTPS,
- permalinks,
- privacy,
- newsletter readiness,
- commercial transparency,
- editorial content readiness.

## Launch safeguards

- Existing posts, recipes, categories and URLs are not rewritten by theme activation.
- Quick setup creates only missing pages and configures a static homepage.
- Safe menu fallbacks remain available until WordPress menus are assigned.
- Newsletter signup is displayed only when a valid MC4WP form is configured, or when an existing Newsletter page is available.
- Legal links are displayed only when the corresponding WordPress pages exist.
- Welcome-gift and homepage-promotion sections stay hidden until a destination URL or widget is configured.
- Conversion events contain no form-field values and are sent only to an analytics data layer that already exists.
- Visible publication dates are omitted from evergreen recipe pages and recipe cards; Kitchen Notes retain editorial dates.
- A read-only audit under **Tools → Recipe Content Audit** highlights missing featured images, alt text, excerpts, categories, recipe cards and supporting copy without changing posts or URLs.

See [`docs/editorial-workflow.md`](docs/editorial-workflow.md) and [`docs/launch-checklist.md`](docs/launch-checklist.md).

## Automated checks

Every push runs GitHub Actions to:

- lint all PHP files,
- validate JSON files,
- check required theme templates,
- check the editorial-audit files required by the release,
- build an installable WordPress ZIP artifact.
