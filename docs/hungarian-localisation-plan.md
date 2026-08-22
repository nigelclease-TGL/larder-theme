# Hungarian localisation (offline work)

This branch prepares Hungarian as an optional second language while preserving English as the unchanged default language.

## Completed offline

- Polylang-compatible EN / HU language selector in the header
- 203 Hungarian translations for the reusable theme interface
- Hungarian support for WP Recipe Maker recipe objects
- Hungarian support for the `recipe_collection` taxonomy
- Separate translations for editable homepage, newsletter, welcome-gift and promotion text
- Read-only translation coverage audit under Tools
- Safety rule that hides HU on pages without a published Hungarian equivalent
- No changes to existing English URLs, copy or published content

## URL and publishing rules

- English remains the default language at the current URLs
- Hungarian uses the `/hu/` directory
- Hungarian pages, posts, categories and recipes are separate Polylang translations
- Each Hungarian recipe post uses a separate Hungarian WP Recipe Maker recipe
- Hungarian content remains draft until article, recipe, links and SEO are reviewed

## Next content stage

1. Export WordPress posts, pages, categories and media references.
2. Export or inventory WP Recipe Maker recipe records and their parent posts.
3. Build the English-to-Hungarian translation manifest.
4. Translate in controlled batches while preserving blocks, shortcodes and recipe references.
5. Import as unpublished Hungarian translations.
6. Review navigation, internal links, search, structured data and recipe cards before any Hungarian launch.
