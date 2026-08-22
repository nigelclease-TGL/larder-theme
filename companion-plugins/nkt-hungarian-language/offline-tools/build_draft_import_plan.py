#!/usr/bin/env python3
"""Build a no-write WordPress draft import plan from approved Hungarian batches.

The generated plan is deliberately not executable. It records the exact draft
operations, dependencies and source fingerprints that a later WordPress importer
must verify before creating anything.
"""

from __future__ import annotations

import argparse
import hashlib
import json
import re
import sys
from collections import OrderedDict
from datetime import datetime, timezone
from pathlib import Path
from typing import Any

from validate_translation_workspace import validate_workspace

PLAN_VERSION = 1
WPRM_SHORTCODE_RE = re.compile(r"\[wprm-recipe\b[^\]]*\bid=[\"']?(\d+)", re.I)


def read_json(path: Path) -> Any:
    try:
        return json.loads(path.read_text(encoding="utf-8"))
    except (OSError, json.JSONDecodeError) as exc:
        raise ValueError(f"Cannot read {path}: {exc}") from exc


def fingerprint(value: Any) -> str:
    raw = json.dumps(value, ensure_ascii=False, sort_keys=True, separators=(",", ":"))
    return hashlib.sha256(raw.encode("utf-8")).hexdigest()


def approved_items(workspace_dir: Path, minimum_status: str) -> list[dict[str, Any]]:
    order = {"not_started": 0, "translated": 1, "reviewed": 2, "approved": 3}
    threshold = order[minimum_status]
    workspace = read_json(workspace_dir / "workspace.json")
    items: list[dict[str, Any]] = []
    for relative in workspace.get("batch_files", []):
        payload = read_json(workspace_dir / str(relative))
        for item in payload.get("items", []):
            target = item.get("target", {})
            if order.get(str(target.get("review_status")), -1) >= threshold:
                items.append(item)
    return items


def recipe_dependencies(content: str) -> list[int]:
    return sorted({int(match) for match in WPRM_SHORTCODE_RE.findall(content or "")})


def build_operation(item: dict[str, Any]) -> OrderedDict[str, Any]:
    source = item["source"]
    target = item["target"]
    post_type = str(item["post_type"])
    source_id = int(item["source_id"])

    operation: OrderedDict[str, Any] = OrderedDict(
        (
            ("operation", "create_or_update_hungarian_draft"),
            ("source_key", f"{post_type}:{source_id}"),
            ("source_id", source_id),
            ("post_type", post_type),
            ("target_language", "hu"),
            ("required_post_status", "draft"),
            ("source_fingerprint", fingerprint(source)),
            ("target_fingerprint", fingerprint(target)),
            ("fields", OrderedDict(
                (
                    ("post_title", target.get("title", "")),
                    ("post_name", target.get("slug", "")),
                    ("post_excerpt", target.get("excerpt", "")),
                    ("post_content", target.get("content", "")),
                    ("menu_order", source.get("menu_order", 0)),
                    ("page_template", source.get("page_template", "")),
                    ("yoast_title", target.get("yoast_title", "")),
                    ("yoast_metadesc", target.get("yoast_metadesc", "")),
                )
            )),
            ("media", OrderedDict(
                (
                    ("reuse_featured_attachment_id", (source.get("featured_image") or {}).get("id")),
                    ("source_featured_image", source.get("featured_image")),
                )
            )),
            ("relationships", OrderedDict(
                (
                    ("polylang_source_language", "en"),
                    ("polylang_target_language", "hu"),
                    ("parent_source_id", source.get("parent", 0)),
                    ("source_taxonomies", source.get("taxonomies", {})),
                )
            )),
            ("dependencies", OrderedDict(
                (
                    ("wprm_source_recipe_ids", recipe_dependencies(str(target.get("content", "")))),
                    ("translated_parent_required", bool(source.get("parent"))),
                    ("translated_terms_required", bool(source.get("taxonomies"))),
                )
            )),
            ("review", OrderedDict(
                (
                    ("status", target.get("review_status")),
                    ("translator_notes", target.get("translator_notes", "")),
                )
            )),
        )
    )

    if post_type == "wprm_recipe":
        operation["recipe"] = OrderedDict(
            (
                ("source_recipe_meta_fingerprint", fingerprint(source.get("recipe_meta", {}))),
                ("target_recipe_meta", target.get("recipe_meta", {})),
                ("must_create_before_parent_article", True),
                ("must_not_copy_untranslated_recipe_meta", True),
            )
        )
    return operation


def build_plan(workspace_dir: Path, output_path: Path, minimum_status: str) -> None:
    validation = validate_workspace(workspace_dir)
    if validation["errors"]:
        raise ValueError(
            f"Workspace validation failed with {validation['errors']} error(s). "
            "Run validate_translation_workspace.py for details."
        )

    workspace = read_json(workspace_dir / "workspace.json")
    items = approved_items(workspace_dir, minimum_status)
    operations = [build_operation(item) for item in items]
    operations.sort(key=lambda op: (0 if op["post_type"] == "wprm_recipe" else 1, op["post_type"], op["source_id"]))

    included_recipe_sources = {
        operation["source_id"]
        for operation in operations
        if operation["post_type"] == "wprm_recipe"
    }
    unresolved: list[dict[str, Any]] = []
    for operation in operations:
        for recipe_id in operation["dependencies"]["wprm_source_recipe_ids"]:
            if recipe_id not in included_recipe_sources:
                unresolved.append(
                    {
                        "source_key": operation["source_key"],
                        "dependency": f"wprm_recipe:{recipe_id}",
                        "message": "Referenced recipe is not included at the selected approval threshold.",
                    }
                )

    payload = OrderedDict(
        (
            ("import_plan_version", PLAN_VERSION),
            ("generated_at_gmt", datetime.now(timezone.utc).replace(microsecond=0).isoformat()),
            ("mode", "dry_run_only"),
            ("source_site", workspace.get("source_site")),
            ("source_language", workspace.get("source_language", "en-GB")),
            ("target_language", "hu-HU"),
            ("minimum_review_status", minimum_status),
            ("required_wordpress_behaviour", OrderedDict(
                (
                    ("create_target_as_draft", True),
                    ("never_modify_source", True),
                    ("verify_source_fingerprint_before_write", True),
                    ("link_with_polylang_after_draft_creation", True),
                    ("publish_automatically", False),
                    ("create_taxonomy_terms_automatically", False),
                )
            )),
            ("summary", OrderedDict(
                (
                    ("operation_count", len(operations)),
                    ("unresolved_dependency_count", len(unresolved)),
                    ("workspace_validation_warnings", validation["warnings"]),
                )
            )),
            ("operations", operations),
            ("unresolved_dependencies", unresolved),
        )
    )
    output_path.write_text(json.dumps(payload, ensure_ascii=False, indent=2) + "\n", encoding="utf-8")
    print(f"Import plan written: {output_path}")
    print(f"Draft operations: {len(operations)}")
    print(f"Unresolved dependencies: {len(unresolved)}")


def main() -> int:
    parser = argparse.ArgumentParser(description=__doc__)
    parser.add_argument("workspace", type=Path)
    parser.add_argument("output", type=Path)
    parser.add_argument(
        "--minimum-status",
        choices=("reviewed", "approved"),
        default="approved",
        help="Only include items at or above this review state.",
    )
    args = parser.parse_args()
    try:
        build_plan(args.workspace, args.output, args.minimum_status)
    except ValueError as exc:
        print(f"Error: {exc}", file=sys.stderr)
        return 2
    return 0


if __name__ == "__main__":
    raise SystemExit(main())
