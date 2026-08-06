#!/usr/bin/env python3
"""Validate a no-write Hungarian draft import plan."""

from __future__ import annotations

import argparse
import json
import sys
from collections import Counter
from pathlib import Path
from typing import Any


def read_json(path: Path) -> Any:
    try:
        return json.loads(path.read_text(encoding="utf-8"))
    except (OSError, json.JSONDecodeError) as exc:
        raise ValueError(f"Cannot read {path}: {exc}") from exc


def validate(plan: dict[str, Any]) -> dict[str, Any]:
    issues: list[dict[str, str]] = []

    def issue(severity: str, path: str, message: str) -> None:
        issues.append({"severity": severity, "path": path, "message": message})

    if plan.get("import_plan_version") != 1:
        issue("error", "import_plan_version", "Expected version 1.")
    if plan.get("mode") != "dry_run_only":
        issue("error", "mode", "Plan must remain dry_run_only.")
    if plan.get("target_language") != "hu-HU":
        issue("error", "target_language", "Target language must remain hu-HU.")

    behaviour = plan.get("required_wordpress_behaviour")
    if not isinstance(behaviour, dict):
        issue("error", "required_wordpress_behaviour", "Missing behaviour safeguards.")
    else:
        required_true = (
            "create_target_as_draft",
            "never_modify_source",
            "verify_source_fingerprint_before_write",
            "link_with_polylang_after_draft_creation",
        )
        for key in required_true:
            if behaviour.get(key) is not True:
                issue("error", f"required_wordpress_behaviour.{key}", "Safeguard must be true.")
        if behaviour.get("publish_automatically") is not False:
            issue("error", "required_wordpress_behaviour.publish_automatically", "Must be false.")
        if behaviour.get("create_taxonomy_terms_automatically") is not False:
            issue("error", "required_wordpress_behaviour.create_taxonomy_terms_automatically", "Must be false.")

    operations = plan.get("operations")
    if not isinstance(operations, list):
        issue("error", "operations", "Operations must be an array.")
        operations = []

    seen: set[str] = set()
    recipe_sources: set[int] = set()
    for index, operation in enumerate(operations):
        path = f"operations[{index}]"
        if not isinstance(operation, dict):
            issue("error", path, "Operation must be an object.")
            continue
        if operation.get("operation") != "create_or_update_hungarian_draft":
            issue("error", f"{path}.operation", "Unsupported operation.")
        if operation.get("required_post_status") != "draft":
            issue("error", f"{path}.required_post_status", "Only draft creation is permitted.")
        if operation.get("target_language") != "hu":
            issue("error", f"{path}.target_language", "Target must remain hu.")
        key = str(operation.get("source_key", ""))
        if not key:
            issue("error", f"{path}.source_key", "Missing source key.")
        elif key in seen:
            issue("error", f"{path}.source_key", f"Duplicate source key {key}.")
        seen.add(key)
        if not str(operation.get("source_fingerprint", "")):
            issue("error", f"{path}.source_fingerprint", "Missing source fingerprint.")
        if not str(operation.get("target_fingerprint", "")):
            issue("error", f"{path}.target_fingerprint", "Missing target fingerprint.")

        fields = operation.get("fields")
        if not isinstance(fields, dict):
            issue("error", f"{path}.fields", "Missing draft fields.")
        else:
            if not str(fields.get("post_title", "")).strip():
                issue("error", f"{path}.fields.post_title", "Hungarian title is required.")
            slug = str(fields.get("post_name", ""))
            if not slug.strip():
                issue("warning", f"{path}.fields.post_name", "Hungarian slug is blank.")
            if any(character in slug for character in "/?#"):
                issue("error", f"{path}.fields.post_name", "Slug contains a reserved character.")

        post_type = operation.get("post_type")
        if post_type not in {"page", "post", "wprm_recipe"}:
            issue("error", f"{path}.post_type", "Unsupported post type.")
        if post_type == "wprm_recipe":
            source_id = int(operation.get("source_id", 0) or 0)
            recipe_sources.add(source_id)
            recipe = operation.get("recipe")
            if not isinstance(recipe, dict):
                issue("error", f"{path}.recipe", "Recipe operation is missing recipe safeguards.")
            else:
                if recipe.get("must_not_copy_untranslated_recipe_meta") is not True:
                    issue("error", f"{path}.recipe.must_not_copy_untranslated_recipe_meta", "Must remain true.")
                target_meta = recipe.get("target_recipe_meta")
                if not isinstance(target_meta, dict) or not target_meta:
                    issue("error", f"{path}.recipe.target_recipe_meta", "Approved recipes need translated recipe metadata.")

    unresolved = plan.get("unresolved_dependencies", [])
    if not isinstance(unresolved, list):
        issue("error", "unresolved_dependencies", "Must be an array.")
    elif unresolved:
        issue("warning", "unresolved_dependencies", f"Plan has {len(unresolved)} unresolved dependency item(s).")

    counts = Counter(item["severity"] for item in issues)
    return {
        "errors": counts.get("error", 0),
        "warnings": counts.get("warning", 0),
        "operations": len(operations),
        "recipe_operations": len(recipe_sources),
        "issues": issues,
    }


def main() -> int:
    parser = argparse.ArgumentParser(description=__doc__)
    parser.add_argument("plan", type=Path)
    parser.add_argument("--report", type=Path)
    args = parser.parse_args()
    try:
        payload = read_json(args.plan)
        if not isinstance(payload, dict):
            raise ValueError("Plan root must be an object.")
        report = validate(payload)
    except ValueError as exc:
        print(f"Error: {exc}", file=sys.stderr)
        return 2

    if args.report:
        args.report.write_text(json.dumps(report, ensure_ascii=False, indent=2) + "\n", encoding="utf-8")
    print(f"Operations: {report['operations']}")
    print(f"Recipe operations: {report['recipe_operations']}")
    print(f"Errors: {report['errors']}")
    print(f"Warnings: {report['warnings']}")
    for item in report["issues"][:50]:
        print(f"{item['severity'].upper()}: {item['path']}: {item['message']}")
    return 1 if report["errors"] else 0


if __name__ == "__main__":
    raise SystemExit(main())
