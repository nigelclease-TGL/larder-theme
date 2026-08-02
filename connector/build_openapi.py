#!/usr/bin/env python3
from pathlib import Path
import json

ROOT = Path(__file__).resolve().parent
PARTS = sorted((ROOT / 'openapi' / 'parts').glob('schema-*'))
OUTPUT = ROOT / 'artifacts' / 'generated' / 'openapi-0.7.29.json'


def response_ref(name: str) -> dict:
    return {
        "description": "Operation completed." if name == "ConnectorResponse" else "Protected validation or state conflict.",
        "content": {"application/json": {"schema": {"$ref": f"#/components/schemas/{name}"}}},
    }


def integer_list() -> dict:
    return {"type": "array", "items": {"type": "integer", "minimum": 1}}


def assemble() -> str:
    schema = json.loads(''.join(path.read_text(encoding='utf-8') for path in PARTS).replace('0.7.23', '0.7.29'))
    schema["info"]["title"] = "Nigel's Kitchen Table GPT Connector - Compact Complete 0.7.29"
    schema["info"]["version"] = "0.7.29-schema.1"
    schema["info"]["description"] = (
        "Complete compact schema for connector 0.7.29. Includes 27 actions: protected article lifecycle, "
        "read-only connector-managed cleanup inventory, exact-ID native WordPress Trash, read-only legacy "
        "reconciliation evidence, and guarded exact-pair ownership/supersession recording. Reconciliation "
        "never changes article content or performs Trash, archive, or permanent deletion."
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
                {"name": "connector_version", "in": "query", "required": True, "schema": {"type": "string", "enum": ["0.7.29"]}},
                {"name": "page", "in": "query", "required": False, "schema": {"type": "integer", "minimum": 1, "default": 1}},
                {"name": "per_page", "in": "query", "required": False, "schema": {"type": "integer", "minimum": 1, "maximum": 100, "default": 20}},
                {"name": "source_live_post_id", "in": "query", "required": False, "schema": {"type": "integer", "minimum": 1}},
            ],
            "responses": {"200": response_ref("ConnectorResponse"), "409": response_ref("ErrorResponse")},
        }
    }

    id_map = {"type": "object", "additionalProperties": True}
    trash_request = {
        "type": "object",
        "additionalProperties": False,
        "required": [
            "connector_version", "dry_run", "confirm_native_wordpress_trash", "cleanup_reason", "draft_post_ids",
            "expected_classification_hashes", "expected_connector_ownership_hashes", "expected_wordpress_statuses",
            "expected_source_live_post_ids", "expected_review_statuses", "expected_application_statuses",
            "expected_programme_ledger_states", "expected_protected_active_states", "expected_linked_recipe_clone_ids",
            "expected_linked_failure_evidence_ids",
        ],
        "properties": {
            "connector_version": {"type": "string", "enum": ["0.7.29"]},
            "dry_run": {"type": "boolean"},
            "confirm_native_wordpress_trash": {"type": "boolean"},
            "cleanup_reason": {"type": "string", "minLength": 1},
            "draft_post_ids": {"type": "array", "minItems": 1, "maxItems": 100, "uniqueItems": True, "items": {"type": "integer", "minimum": 1}},
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
                "Recomputes every inventory classification and rejects the entire batch if ownership, status, lifecycle, "
                "programme-ledger, active-protection, clone/evidence linkage or snapshot hashes differ. Uses wp_trash_post "
                "only; it never archives, permanently deletes, or empties Trash."
            ),
            "requestBody": {"required": True, "content": {"application/json": {"schema": trash_request}}},
            "responses": {"200": response_ref("ConnectorResponse"), "409": response_ref("ErrorResponse"), "500": response_ref("ErrorResponse")},
        }
    }

    schema["paths"]["/protected-article-revisions/legacy-reconciliation"] = {
        "get": {
            "operationId": "inventoryLegacyConnectorDraftReconciliation",
            "summary": "Inventory exact legacy connector evidence and eligible retained successors",
            "description": (
                "Read-only paginated evidence inventory restricted to drafts with connector metadata signals. Ownership "
                "is never inferred from title, author, slug, date, or content similarity. Rows expose current classifications, "
                "deterministic reconciliation hashes, consistency failures and exact connector-managed successor IDs."
            ),
            "parameters": [
                {"name": "connector_version", "in": "query", "required": True, "schema": {"type": "string", "enum": ["0.7.29"]}},
                {"name": "page", "in": "query", "required": False, "schema": {"type": "integer", "minimum": 1, "default": 1}},
                {"name": "per_page", "in": "query", "required": False, "schema": {"type": "integer", "minimum": 1, "maximum": 100, "default": 20}},
                {"name": "source_live_post_id", "in": "query", "required": False, "schema": {"type": "integer", "minimum": 1}},
            ],
            "responses": {"200": response_ref("ConnectorResponse"), "409": response_ref("ErrorResponse")},
        }
    }

    reconciliation_required = [
        "obsolete_draft_id", "superseding_draft_id", "supersession_reason", "expected_reconciliation_evidence_hash",
        "expected_source_live_post_id", "expected_obsolete_classification_hash", "expected_obsolete_ownership_hash",
        "expected_obsolete_wordpress_status", "expected_obsolete_review_status", "expected_obsolete_application_status",
        "expected_obsolete_programme_ledger_state", "expected_obsolete_linked_recipe_clone_ids",
        "expected_obsolete_linked_failure_evidence_ids", "expected_superseding_classification_hash",
        "expected_superseding_ownership_hash", "expected_superseding_wordpress_status", "expected_superseding_review_status",
        "expected_superseding_application_status", "expected_superseding_programme_ledger_state",
        "expected_superseding_linked_recipe_clone_ids", "expected_superseding_linked_failure_evidence_ids",
    ]
    reconciliation_item = {
        "type": "object",
        "additionalProperties": False,
        "required": reconciliation_required,
        "properties": {
            "obsolete_draft_id": {"type": "integer", "minimum": 1},
            "superseding_draft_id": {"type": "integer", "minimum": 1},
            "supersession_reason": {"type": "string", "minLength": 1},
            "expected_reconciliation_evidence_hash": {"type": "string", "minLength": 64, "maxLength": 64},
            "expected_source_live_post_id": {"type": "integer", "minimum": 1},
            "expected_obsolete_classification_hash": {"type": "string", "minLength": 64, "maxLength": 64},
            "expected_obsolete_ownership_hash": {"type": "string", "minLength": 64, "maxLength": 64},
            "expected_obsolete_wordpress_status": {"type": "string"},
            "expected_obsolete_review_status": {"type": "string"},
            "expected_obsolete_application_status": {"type": "string"},
            "expected_obsolete_programme_ledger_state": {"type": "string", "enum": ["not_referenced"]},
            "expected_obsolete_linked_recipe_clone_ids": integer_list(),
            "expected_obsolete_linked_failure_evidence_ids": integer_list(),
            "expected_superseding_classification_hash": {"type": "string", "minLength": 64, "maxLength": 64},
            "expected_superseding_ownership_hash": {"type": "string", "minLength": 64, "maxLength": 64},
            "expected_superseding_wordpress_status": {"type": "string"},
            "expected_superseding_review_status": {"type": "string"},
            "expected_superseding_application_status": {"type": "string"},
            "expected_superseding_programme_ledger_state": {"type": "string"},
            "expected_superseding_linked_recipe_clone_ids": integer_list(),
            "expected_superseding_linked_failure_evidence_ids": integer_list(),
        },
    }
    reconcile_request = {
        "type": "object",
        "additionalProperties": False,
        "required": [
            "connector_version", "dry_run", "confirm_connector_ownership_reconciliation",
            "confirm_supersession_and_obsolescence", "reconciliations",
        ],
        "properties": {
            "connector_version": {"type": "string", "enum": ["0.7.29"]},
            "dry_run": {"type": "boolean"},
            "confirm_connector_ownership_reconciliation": {"type": "boolean"},
            "confirm_supersession_and_obsolescence": {"type": "boolean"},
            "reconciliations": {"type": "array", "minItems": 1, "maxItems": 100, "items": reconciliation_item},
        },
    }
    schema["paths"]["/protected-article-revisions/legacy-reconciliation/record"] = {
        "post": {
            "operationId": "reconcileLegacyConnectorDraftSupersession",
            "summary": "Record exact guarded legacy ownership, supersession and obsolescence metadata",
            "description": (
                "Requires exact obsolete/superseding draft pairs and every current evidence hash and lifecycle guard. The entire "
                "batch is refused before writing if any value differs. A non-dry-run writes metadata only, verifies that the "
                "existing cleanup classifier now returns safe_to_trash, and restores exact metadata snapshots if verification "
                "fails. It never changes article content or invokes Trash, archive, or deletion."
            ),
            "requestBody": {"required": True, "content": {"application/json": {"schema": reconcile_request}}},
            "responses": {"200": response_ref("ConnectorResponse"), "409": response_ref("ErrorResponse"), "500": response_ref("ErrorResponse")},
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
