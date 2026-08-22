#!/usr/bin/env python3
"""Validate an NKT Hungarian translation workspace without changing WordPress."""

from __future__ import annotations

import argparse
import json
import re
import sys
from collections import Counter
from pathlib import Path
from typing import Any

EXPECTED_PACK_VERSION = 1
SUPPORTED_POST_TYPES = {"page", "post", "wprm_recipe"}
BLOCK_RE = re.compile(r"<!--\s*\/?wp:([a-zA-Z0-9_\-/]+)")
SHORTCODE_RE = re.compile(r"\[(?!/)([a-zA-Z][a-zA-Z0-9_-]*)(?:\s|\])")
INTERNAL_LINK_RE = re.compile(r"https?://(?:www\.)?thegourmetlarder\.com/[^\s\"'<>)]*", re.I)


def load_json(path: Path) -> Any:
    try:
        return json.loads(path.read_text(encoding="utf-8"))
    except (OSError, json.JSONDecodeError) as exc:
        raise ValueError(f"Cannot read {path}: {exc}") from exc


def add_issue(issues: list[dict[str, Any]], severity: str, path: str, message: str) -> None:
    issues.append({"severity": severity, "path": path, "message": message})


def structure_counts(value: str) -> tuple[Counter[str], Counter[str]]:
    return Counter(BLOCK_RE.findall(value or "")), Counter(SHORTCODE_RE.findall(value or ""))


def validate_item(item: dict[str, Any], location: str, issues: list[dict[str, Any]]) -> None:
    source_id = item.get("source_id")
    post_type = item.get("post_type")
    source = item.get("source")
    target = item.get("target")

    if not isinstance(source_id, int) or source_id <= 0:
        add_issue(issues, "error", location, "source_id must be a positive integer.")
    if post_type not in SUPPORTED_POST_TYPES:
        add_issue(issues, "error", location, f"Unsupported post_type: {post_type!r}.")
    if not isinstance(source, dict):
        add_issue(issues, "error", location, "source must be an object.")
        return
    if not isinstance(target, dict):
        add_issue(issues, "error", location, "target must be an object.")
        return

    if target.get("language") != "hu":
        add_issue(issues, "error", f"{location}.target.language", "Language must remain hu.")

    status = target.get("review_status")
    if status not in {"not_started", "translated", "reviewed", "approved"}:
        add_issue(
            issues,
            "error",
            f"{location}.target.review_status",
            "Use not_started, translated, reviewed or approved.",
        )

    source_title = str(source.get("title", ""))
    target_title = str(target.get("title", ""))
    if status != "not_started" and not target_title.strip():
        add_issue(issues, "error", f"{location}.target.title", "Translated items need a Hungarian title.")
    if target_title.strip() and target_title.strip() == source_title.strip():
        add_issue(issues, "warning", f"{location}.target.title", "Title is unchanged from English.")

    slug = str(target.get("slug", ""))
    if status in {"reviewed", "approved"} and not slug.strip():
        add_issue(issues, "warning", f"{location}.target.slug", "Reviewed items should have a Hungarian slug.")
    if slug and any(character in slug for character in "/?#"):
        add_issue(issues, "error", f"{location}.target.slug", "Slug must not contain /, ? or #.")

    source_content = str(source.get("content", ""))
    target_content = str(target.get("content", ""))
    if status != "not_started" and source_content.strip() and not target_content.strip():
        add_issue(issues, "error", f"{location}.target.content", "Translated item is missing content.")
    if target_content.strip() and target_content.strip() == source_content.strip():
        add_issue(issues, "warning", f"{location}.target.content", "Content is unchanged from English.")

    if target_content:
        source_blocks, source_shortcodes = structure_counts(source_content)
        target_blocks, target_shortcodes = structure_counts(target_content)
        if source_blocks != target_blocks:
            add_issue(
                issues,
                "error",
                f"{location}.target.content",
                f"Gutenberg block structure changed: source={dict(source_blocks)}, target={dict(target_blocks)}.",
            )
        if source_shortcodes != target_shortcodes:
            add_issue(
                issues,
                "error",
                f"{location}.target.content",
                f"Shortcodes changed: source={dict(source_shortcodes)}, target={dict(target_shortcodes)}.",
            )

        english_links = [link for link in INTERNAL_LINK_RE.findall(target_content) if "/hu/" not in link]
        if english_links and status in {"reviewed", "approved"}:
            add_issue(
                issues,
                "warning",
                f"{location}.target.content",
                f"Contains {len(english_links)} internal English URL(s); map them to reviewed Hungarian pages where available.",
            )

    if post_type == "wprm_recipe":
        source_meta = source.get("recipe_meta")
        target_meta = target.get("recipe_meta")
        if status != "not_started" and not isinstance(target_meta, dict):
            add_issue(issues, "error", f"{location}.target.recipe_meta", "Recipe translation requires recipe_meta.")
        if isinstance(source_meta, dict) and isinstance(target_meta, dict) and target_meta:
            missing_keys = sorted(set(source_meta) - set(target_meta))
            if missing_keys:
                add_issue(
                    issues,
                    "warning",
                    f"{location}.target.recipe_meta",
                    f"Recipe metadata keys missing from target: {', '.join(missing_keys[:12])}",
                )


def validate_workspace(workspace_dir: Path) -> dict[str, Any]:
    workspace = load_json(workspace_dir / "workspace.json")
    if not isinstance(workspace, dict):
        raise ValueError("workspace.json must contain an object.")

    issues: list[dict[str, Any]] = []
    batches = workspace.get("batch_files")
    if not isinstance(batches, list):
        raise ValueError("workspace.json is missing batch_files.")

    item_count = 0
    status_counts: Counter[str] = Counter()
    seen_keys: set[tuple[str, int]] = set()

    for relative_path in batches:
        payload = load_json(workspace_dir / str(relative_path))
        if not isinstance(payload, dict):
            add_issue(issues, "error", str(relative_path), "Batch root must be an object.")
            continue
        if payload.get("translation_pack_version") != EXPECTED_PACK_VERSION:
            add_issue(issues, "error", str(relative_path), "Unexpected translation_pack_version.")
        if payload.get("source_site") != workspace.get("source_site"):
            add_issue(issues, "error", str(relative_path), "source_site differs from workspace.json.")
        if payload.get("target_language") != "hu-HU":
            add_issue(issues, "error", str(relative_path), "target_language must remain hu-HU.")

        items = payload.get("items")
        if not isinstance(items, list):
            add_issue(issues, "error", str(relative_path), "items must be an array.")
            continue

        for index, item in enumerate(items):
            location = f"{relative_path}:items[{index}]"
            if not isinstance(item, dict):
                add_issue(issues, "error", location, "Item must be an object.")
                continue
            item_count += 1
            key = (str(item.get("post_type")), int(item.get("source_id", 0) or 0))
            if key in seen_keys:
                add_issue(issues, "error", location, f"Duplicate source record {key}.")
            seen_keys.add(key)
            target = item.get("target")
            if isinstance(target, dict):
                status_counts[str(target.get("review_status", "missing"))] += 1
            validate_item(item, location, issues)

    severity_counts = Counter(issue["severity"] for issue in issues)
    return {
        "workspace": str(workspace_dir),
        "source_site": workspace.get("source_site"),
        "items_checked": item_count,
        "translation_status": dict(status_counts),
        "errors": severity_counts.get("error", 0),
        "warnings": severity_counts.get("warning", 0),
        "issues": issues,
    }


def main() -> int:
    parser = argparse.ArgumentParser(description=__doc__)
    parser.add_argument("workspace", type=Path, help="Translation workspace directory")
    parser.add_argument("--report", type=Path, help="Write the JSON report here")
    args = parser.parse_args()
    try:
        report = validate_workspace(args.workspace)
    except ValueError as exc:
        print(f"Error: {exc}", file=sys.stderr)
        return 2

    if args.report:
        args.report.write_text(json.dumps(report, ensure_ascii=False, indent=2) + "\n", encoding="utf-8")

    print(f"Items checked: {report['items_checked']}")
    print(f"Errors: {report['errors']}")
    print(f"Warnings: {report['warnings']}")
    for issue in report["issues"][:50]:
        print(f"{issue['severity'].upper()}: {issue['path']}: {issue['message']}")
    if len(report["issues"]) > 50:
        print(f"... {len(report['issues']) - 50} more issue(s); use --report for the full list.")
    return 1 if report["errors"] else 0


if __name__ == "__main__":
    raise SystemExit(main())
