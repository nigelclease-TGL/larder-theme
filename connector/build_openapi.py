#!/usr/bin/env python3
from pathlib import Path
import json

ROOT = Path(__file__).resolve().parent
PARTS = sorted((ROOT / 'openapi' / 'parts').glob('schema-*'))
OUTPUT = ROOT / 'artifacts' / 'generated' / 'openapi-0.7.28.json'


def response_ref(name: str) -> dict:
    return {
        "description": "Operation completed." if name == "ConnectorResponse" else "Protected validation or state conflict.",
        "content": {"application/json": {"schema": {"$ref": f"#/components/schemas/{name}"}}},
    }


def assemble() -> str:
    schema = json.loads(''.join(path.read_text(encoding='utf-8') for path in PARTS).replace('0.7.23', '0.7.28'))
    schema["info"]["title"] = "Nigel's Kitchen Table GPT Connector - Compact Complete 0.7.28"
    schema["info"]["version"] = "0.7.28-schema.1"
    schema["info"]["description"] = (
        "Complete compact schema for connector 0.7.28. Includes 25 actions: the existing protected "
        "article lifecycle plus read-only connector-managed draft cleanup inventory and an exact-ID, "
        "fail-closed native WordPress Trash action. No action empties Trash."
    )

    schema["paths"]["/protected-article-revisions/cleanup-inventory"] = {
        "get": {
            "operationId": "inventoryConnectorManagedDraftCleanup",
            "summary": "Inventory connector-managed article drafts with fail-closed Trash eligibility",
            "description": (
                "Read-only paginated inventory. Explicit or conclusive legacy connector ownership, lifecycle, "
                "programme-ledger, clone, failure-evidence, active-protection, supersession and preservation evidence "
                "are returned with deterministic classification hashes. Ambiguous drafts are preserved."
            ),
            "parameters": [
                {"name": "connector_version", "in": "query", "required": True, "schema": {"type": "string", "enum": ["0.7.28"]}},
                {"name": "page", "in": "query", "required": False, "schema": {"type": "integer", "minimum": 1, "default": 1}},
                {"name": "per_page", "in": "query", "required": False, "schema": {"type": "integer", "minimum": 1, "maximum": 100, "default": 20}},
                {"name": "source_live_post_id", "in": "query", "required": False, "schema": {"type": "integer", "minimum": 1}},
            ],
            "responses": {
                "200": response_ref("ConnectorResponse"),
                "409": response_ref("ErrorResponse"),
            },
        }
    }

    id_map = {"type": "object", "additionalProperties": True}
    trash_request = {
        "type": "object",
        "additionalProperties": False,
        "required": [
            "connector_version",
            "dry_run",
            "confirm_native_wordpress_trash",
            "cleanup_reason",
            "draft_post_ids",
            "expected_classification_hashes",
            "expected_connector_ownership_hashes",
            "expected_wordpress_statuses",
            "expected_source_live_post_ids",
            "expected_review_statuses",
            "expected_application_statuses",
            "expected_programme_ledger_states",
            "expected_protected_active_states",
            "expected_linked_recipe_clone_ids",
            "expected_linked_failure_evidence_ids",
        ],
        "properties": {
            "connector_version": {"type": "string", "enum": ["0.7.28"]},
            "dry_run": {"type": "boolean"},
            "confirm_native_wordpress_trash": {"type": "boolean"},
            "cleanup_reason": {"type": "string", "minLength": 1},
            "draft_post_ids": {
                "type": "array",
                "minItems": 1,
                "maxItems": 100,
                "uniqueItems": True,
                "items": {"type": "integer", "minimum": 1},
            },
            "expected_classification_hashes": id_map,
            "expected_connector_ownership_hashes": id_map,
            "expected_wordpress_statuses": id_map,
            "expected_source_live_post_ids": id_map,
            "expected_review_statuses": id_map,
            "expected_application_statuses": id_map,
            "expected_programme_ledger_states": id_map,
            "expected_protected_active_states": id_map,
            "expected_linked_recipe_clone_ids": id_map,
            "expected_linked_failure_evidence_ids": id_map,
        },
    }
    schema["paths"]["/protected-article-revisions/trash"] = {
        "post": {
            "operationId": "trashConnectorManagedArticleDrafts",
            "summary": "Move an exact allowlist of proven obsolete connector article drafts to native WordPress Trash",
            "description": (
                "Recomputes every inventory classification and rejects the entire batch if ownership, status, "
                "lifecycle, programme-ledger, active-protection, clone/evidence linkage or snapshot hashes differ. "
                "Uses wp_trash_post only; it never archives, permanently deletes, or empties Trash."
            ),
            "requestBody": {
                "required": True,
                "content": {"application/json": {"schema": trash_request}},
            },
            "responses": {
                "200": response_ref("ConnectorResponse"),
                "409": response_ref("ErrorResponse"),
                "500": response_ref("ErrorResponse"),
            },
        }
    }

    return json.dumps(schema, ensure_ascii=False, separators=(',', ':'))


def write() -> Path:
    text = assemble()
    json.loads(text)
    OUTPUT.parent.mkdir(parents=True, exist_ok=True)
    OUTPUT.write_text(text, encoding='utf-8')
    return OUTPUT


if __name__ == '__main__':
    print(write())
