=== NKT Translation Manifest Exporter ===
Requires at least: 6.6
Requires PHP: 8.1
Version: 1.0.0

A temporary, admin-only exporter for Nigel's Kitchen Table.

It creates a JSON copy of the English source material needed for offline Hungarian translation. It does not change posts, recipes, menus, settings, URLs or public output.

Export includes:

* Published pages and posts
* WP Recipe Maker recipe objects and recipe metadata
* Contact Form 7 forms when available
* Categories, tags and recipe collections
* Featured-image references and alt text
* Existing menu structure
* Relevant homepage and newsletter Customizer text
* Yoast title and meta-description fields
* Stable source fingerprints used to detect later English changes

Usage:

1. Upload and activate the plugin.
2. Open Tools > Translation Manifest Export.
3. Click Download English translation manifest.
4. Save the JSON file.
5. Deactivate and delete the exporter.

The exporter performs no import, translation, publication or database update.
