#!/usr/bin/env python3
"""Build a safe offline Hungarian translation workspace from an NKT manifest.

This tool never connects to WordPress and never changes source content. It turns
an exported English manifest into small, reviewable JSON batches with blank
Hungarian target fields.
"""

from __future__ import annotations

import argparse
import csv
import json
import shutil
import sys
from collections import OrderedDict
from datetime import datetime, timezone
from pathlib import Path
from typing import Any, Iterable

SUPPORTED_MANIFEST_VERSION = 1
TRANSLATION_PACK_VERSION = 1
DEFAULT_BATCH_SIZE = 20
CONTENT_TYPES = ("page", "post", "wprm_recipe")


def load_json(path: Path) -> dict[str, Any]:
    try:
        data = json.loads(path.read_text(encoding="utf-8"))
    except FileNotFoundError as exc:
        raise ValueError(f"Manifest not found: {path}") from exc
    except json.JSONDecodeError as exc:
        raise ValueError(f"Invalid JSON in {path}: {exc}") from exc
    if not isinstance(data, dict):
        raise ValueError("Manifest root must be a JSON object.")
    return data


def validate_manifest(manifest: dict[str, Any]) -> None:
    if manifest.get("manifest_version") != SUPPORTED_MANIFEST_VERSION:
        raise ValueError(
            f"Unsupported manifest_version {manifest.get('manifest_version')!r}; "
            f"expected {SUPPORTED_MANIFEST_VERSION}."
        )
    site = manifest.get("site")
    if not isinstance(site, dict) or not site.get("url"):
        raise ValueError("Manifest is missing site.url.")
    content = manifest.get("content")
    if not isinstance(content, dict):
        raise ValueError("Manifest is missing its content object.")
    for post_type in CONTENT_TYPES:
        records = content.get(post_type, [])
        if not isinstance(records, list):
            raise ValueError(f"content.{post_type} must be an array.")


def chunks(items: list[dict[str, Any]], size: int) -> Iterable[list[dict[str, Any]]]:
    for index in range(0, len(items), size):
        yield items[index : index + size]


def source_record(record: dict[str, Any]) -> OrderedDict[str, Any]:
    fields = (
        "id", "post_type", "status", "title", "slug", "excerpt", "content",
        "parent", "menu_order", "permalink", "page_template", "yoast_title",
        "yoast_metadesc", "featured_image", "taxonomies", "recipe_meta",
    )
    return OrderedDict((field, record.get(field)) for field in fields if field in record)


def target_record(record: dict[str, Any]) -> OrderedDict[str, Any]:
    target: OrderedDict[str, Any] = OrderedDict(
        (
            ("language", "hu"),
            ("review_status", "not_started"),
            ("title", ""),
            ("slug", ""),
            ("excerpt", ""),
            ("content", ""),
            ("yoast_title", ""),
            ("yoast_metadesc", ""),
            ("translator_notes", ""),
        )
    )
    if str(record.get("post_type", "")) == "wprm_recipe":
        target["recipe_meta"] = {}
    return target


def translation_item(record: dict[str, Any]) -> OrderedDict[str, Any]:
    return OrderedDict(
        (
            ("source_id", int(record.get("id", 0) or 0)),
            ("post_type", str(record.get("post_type", ""))),
            ("source", source_record(record)),
            ("target", target_record(record)),
        )
    )


def extract_terms(manifest: dict[str, Any]) -> list[OrderedDict[str, Any]]:
    terms: dict[tuple[str, int], OrderedDict[str, Any]] = {}
    for post_type in CONTENT_TYPES:
        for record in manifest.get("content", {}).get(post_type, []):
            taxonomies = record.get("taxonomies", {})
            if not isinstance(taxonomies, dict):
                continue
            for taxonomy, taxonomy_terms in taxonomies.items():
                if not isinstance(taxonomy_terms, list):
                    continue
                for term in taxonomy_terms:
                    if not isinstance(term, dict):
                        continue
                    term_id = int(term.get("id", 0) or 0)
                    if term_id <= 0:
                        continue
                    key = (str(taxonomy), term_id)
                    if key in terms:
                        continue
                    terms[key] = OrderedDict(
                        (
                            ("taxonomy", str(taxonomy)),
                            ("source_term_id", term_id),
                            ("source", OrderedDict(
                                (
                                    ("name", str(term.get("name", ""))),
                                    ("slug", str(term.get("slug", ""))),
                                    ("description", str(term.get("description", ""))),
                                    ("parent_source_term_id", int(term.get("parent", 0) or 0)),
                                )
                            )),
                            ("target", OrderedDict(
                                (
                                    ("language", "hu"),
                                    ("review_status", "not_started"),
                                    ("name", ""),
                                    ("slug", ""),
                                    ("description", ""),
                                    ("translator_notes", ""),
                                )
                            )),
                        )
                    )
    return sorted(terms.values(), key=lambda row: (row["taxonomy"], row["source_term_id"]))


def write_json(path: Path, payload: Any) -> None:
    path.write_text(json.dumps(payload, ensure_ascii=False, indent=2) + "\n", encoding="utf-8")


def build_workspace(manifest_path: Path, output_dir: Path, batch_size: int) -> None:
    manifest = load_json(manifest_path)
    validate_manifest(manifest)
    if batch_size < 1 or batch_size > 100:
        raise ValueError("Batch size must be between 1 and 100.")

    output_dir.mkdir(parents=True, exist_ok=True)
    batches_dir = output_dir / "batches"
    batches_dir.mkdir(exist_ok=True)
    shutil.copy2(manifest_path, output_dir / "english-source-manifest.json")

    site = manifest["site"]
    generated_at = datetime.now(timezone.utc).replace(microsecond=0).isoformat()
    summary_rows: list[dict[str, Any]] = []
    batch_files: list[str] = []

    for post_type in CONTENT_TYPES:
        records = [r for r in manifest.get("content", {}).get(post_type, []) if isinstance(r, dict)]
        for batch_number, batch in enumerate(chunks(records, batch_size), start=1):
            filename = f"{post_type}-batch-{batch_number:03d}.json"
            payload = OrderedDict(
                (
                    ("translation_pack_version", TRANSLATION_PACK_VERSION),
                    ("source_manifest_version", manifest["manifest_version"]),
                    ("source_site", site.get("url")),
                    ("source_generated_at_gmt", manifest.get("generated_at_gmt")),
                    ("target_language", "hu-HU"),
                    ("batch", OrderedDict(
                        (("post_type", post_type), ("number", batch_number), ("item_count", len(batch)))
                    )),
                    ("items", [translation_item(record) for record in batch]),
                )
            )
            write_json(batches_dir / filename, payload)
            batch_files.append(f"batches/{filename}")

        for record in records:
            summary_rows.append(
                {
                    "post_type": post_type,
                    "source_id": int(record.get("id", 0) or 0),
                    "source_title": str(record.get("title", "")),
                    "source_slug": str(record.get("slug", "")),
                    "source_permalink": str(record.get("permalink", "")),
                    "translation_status": "not_started",
                    "reviewer": "",
                    "notes": "",
                }
            )

    terms = extract_terms(manifest)
    write_json(
        output_dir / "taxonomy-terms.json",
        OrderedDict(
            (
                ("translation_pack_version", TRANSLATION_PACK_VERSION),
                ("source_manifest_version", manifest["manifest_version"]),
                ("source_site", site.get("url")),
                ("target_language", "hu-HU"),
                ("terms", terms),
            )
        ),
    )

    theme_strings = manifest.get("theme_mod_values", {})
    write_json(
        output_dir / "theme-customizer-strings.json",
        OrderedDict(
            (
                ("translation_pack_version", TRANSLATION_PACK_VERSION),
                ("source_site", site.get("url")),
                ("target_language", "hu-HU"),
                ("strings", [
                    {
                        "setting_id": key,
                        "source": value,
                        "target": "",
                        "review_status": "not_started",
                    }
                    for key, value in sorted(theme_strings.items())
                    if isinstance(value, str) and value.strip()
                ]),
            )
        ),
    )

    with (output_dir / "translation-register.csv").open("w", encoding="utf-8-sig", newline="") as handle:
        fieldnames = [
            "post_type", "source_id", "source_title", "source_slug",
            "source_permalink", "translation_status", "reviewer", "notes",
        ]
        writer = csv.DictWriter(handle, fieldnames=fieldnames)
        writer.writeheader()
        writer.writerows(summary_rows)

    with (output_dir / "culinary-glossary.csv").open("w", encoding="utf-8-sig", newline="") as handle:
        writer = csv.writer(handle)
        writer.writerow(["English term", "Approved Hungarian", "Notes"])
        writer.writerows(
            [
                ["baking powder", "sütőpor", ""],
                ["bicarbonate of soda", "szódabikarbóna", ""],
                ["caster sugar", "apró szemű kristálycukor", "Do not change the quantity."],
                ["double cream", "habtejszín", "Use the Hungarian ingredient commonly available."],
                ["icing sugar", "porcukor", ""],
                ["plain flour", "finomliszt", ""],
                ["self-raising flour", "sütőporos liszt", "Retain substitution notes."],
                ["cornflour", "kukoricakeményítő", "British usage: starch, not cornmeal."],
                ["aubergine", "padlizsán", ""],
                ["courgette", "cukkini", ""],
                ["tablespoon", "evőkanál", "Abbreviation: ek."],
                ["teaspoon", "teáskanál", "Abbreviation: tk."],
            ]
        )

    workspace = OrderedDict(
        (
            ("workspace_version", 1),
            ("created_at_gmt", generated_at),
            ("source_manifest", "english-source-manifest.json"),
            ("source_site", site.get("url")),
            ("source_language", site.get("language", "en-GB")),
            ("target_language", "hu-HU"),
            ("batch_size", batch_size),
            ("batch_files", batch_files),
            ("taxonomy_file", "taxonomy-terms.json"),
            ("theme_strings_file", "theme-customizer-strings.json"),
            ("translation_register", "translation-register.csv"),
            ("glossary", "culinary-glossary.csv"),
            ("publishing_rule", "Hungarian items remain drafts until linguistic, recipe, link and SEO review is complete."),
        )
    )
    write_json(output_dir / "workspace.json", workspace)

    (output_dir / "README.txt").write_text(
        """Nigel's Kitchen Table – Hungarian translation workspace

English remains the unchanged default language. Hungarian content is prepared
for /hu/ and must remain unpublished until review.

Translate only fields inside each target object. Do not edit source_id,
post_type, source or source_site. Preserve Gutenberg block comments, shortcodes,
HTML structure, quantities, temperatures, timings and ingredient ordering. Run
validate_translation_workspace.py before any WordPress import is built.
""",
        encoding="utf-8",
    )

    print(f"Workspace created: {output_dir}")
    print(f"Content records: {len(summary_rows)}")
    print(f"Taxonomy terms: {len(terms)}")
    print(f"Batch files: {len(batch_files)}")


def main() -> int:
    parser = argparse.ArgumentParser(description=__doc__)
    parser.add_argument("manifest", type=Path, help="English source manifest JSON")
    parser.add_argument("output", type=Path, help="Output workspace directory")
    parser.add_argument("--batch-size", type=int, default=DEFAULT_BATCH_SIZE)
    args = parser.parse_args()
    try:
        build_workspace(args.manifest, args.output, args.batch_size)
    except ValueError as exc:
        print(f"Error: {exc}", file=sys.stderr)
        return 2
    return 0


if __name__ == "__main__":
    raise SystemExit(main())
