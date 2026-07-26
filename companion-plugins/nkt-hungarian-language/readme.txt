=== Nigel's Kitchen Table – Hungarian Language ===
Requires at least: 6.6
Requires PHP: 8.1
Version: 0.3.0

Offline preparation for Hungarian as an optional second language.

The plugin does not alter English content, URLs or settings. It requires Polylang and only displays Hungarian links where a published Hungarian translation exists.

Features:

* Compact EN / HU selector in the Nigel's Kitchen Table header
* 203 Hungarian theme-interface translations
* Separate Polylang management for WP Recipe Maker recipe objects
* Separate Polylang management for the recipe_collection taxonomy
* Translatable homepage, newsletter, welcome-gift and promotion text
* Read-only Tools > Hungarian Translation Audit coverage report
* One-click JSON export of English pages, posts, taxonomies, media references and WP Recipe Maker recipe data for offline translation
* No automatic publishing and no modification of English content

Setup after offline translation review:

1. Install Polylang.
2. Set English (en_GB, slug en) as the default language.
3. Add Hungarian (hu_HU, slug hu) as the second language.
4. Hide the language code for the default language so all English URLs remain unchanged.
5. Use directories for languages so Hungarian content uses /hu/.
6. Assign all existing content to English.
7. Create separate Hungarian pages, posts, categories and WP Recipe Maker recipes.
8. Create English and Hungarian menus.
9. Translate registered homepage and newsletter strings under Languages > Translations.
10. Keep Hungarian drafts unpublished until content, recipe, SEO and link QA is complete.
11. Use Tools > Hungarian Translation Audit to download the English source manifest and track coverage.
