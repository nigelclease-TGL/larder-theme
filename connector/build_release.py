#!/usr/bin/env python3
from pathlib import Path
import hashlib
import json
import zipfile
from build_source import write as build_source
from build_openapi import write as build_openapi
from build_updater import write as build_updater

ROOT = Path(__file__).resolve().parent


def enforce_chatgpt_action_description_limits(schema_path: Path) -> None:
    """Keep every public action description within ChatGPT's 300-character limit."""
    schema = json.loads(schema_path.read_text(encoding='utf-8'))
    replacements = {
        'inventoryLegacyConnectorDraftReconciliation': (
            'Read-only paginated inventory of drafts with connector metadata signals. '
            'It does not infer ownership from titles, authors, slugs, dates or content similarity. '
            'Returns classifications, evidence hashes, consistency failures and exact eligible successor IDs.'
        ),
        'reconcileLegacyConnectorDraftSupersession': (
            'Requires exact obsolete and successor draft pairs plus current evidence and lifecycle guards. '
            'Refuses the full batch before writing on any mismatch. A non-dry-run writes metadata only, '
            'verifies safe-to-trash classification and restores metadata if verification fails.'
        ),
    }
    found = set()
    violations = []
    for methods in schema.get('paths', {}).values():
        for operation in methods.values():
            if not isinstance(operation, dict) or not operation.get('operationId'):
                continue
            operation_id = operation['operationId']
            if operation_id in replacements:
                operation['description'] = replacements[operation_id]
                found.add(operation_id)
            description_length = len(operation.get('description', ''))
            if description_length > 300:
                violations.append((operation_id, description_length))
    missing = sorted(set(replacements) - found)
    if missing:
        raise RuntimeError(f'OpenAPI operations missing for description correction: {missing}')
    if violations:
        raise RuntimeError(f'OpenAPI action descriptions exceed 300 characters: {violations}')
    schema_path.write_text(
        json.dumps(schema, ensure_ascii=False, separators=(',', ':')),
        encoding='utf-8',
    )


generated_source = build_source()
generated_openapi = build_openapi()
enforce_chatgpt_action_description_limits(generated_openapi)
generated_updater = build_updater()
OUT = ROOT / 'artifacts'
OUT.mkdir(parents=True, exist_ok=True)
zip_path = OUT / 'nkt-gpt-connector-0.7.29-guarded-legacy-draft-reconciliation-upgrader.zip'
sha_path = OUT / 'nkt-gpt-connector-0.7.29-guarded-legacy-draft-reconciliation-upgrader.sha256.txt'
prefix = 'nkt-gpt-connector-upgrader-0.7.29/'
files = [
    ('artifacts/generated/nkt-gpt-connector-upgrader-0.7.29.php', 'nkt-gpt-connector-upgrader.php'),
    ('artifacts/generated/protected-lifecycle-0.7.29.php', 'protected-lifecycle-0.7.29.php'),
    ('artifacts/generated/openapi-0.7.29.json', 'openapi-0.7.29.json'),
    ('CHANGELOG.md', 'CHANGELOG.txt'),
    ('README.md', 'README.txt'),
    ('VALIDATION.md', 'VALIDATION.txt'),
]
with zipfile.ZipFile(zip_path, 'w', compression=zipfile.ZIP_DEFLATED, compresslevel=9) as archive:
    for source, target in files:
        data = (ROOT / source).read_bytes()
        info = zipfile.ZipInfo(prefix + target, date_time=(2026, 8, 2, 0, 0, 0))
        info.compress_type = zipfile.ZIP_DEFLATED
        info.external_attr = (0o100644 & 0xFFFF) << 16
        archive.writestr(info, data)
digest = hashlib.sha256(zip_path.read_bytes()).hexdigest()
sha_path.write_text(f'{digest}  {zip_path.name}\n', encoding='utf-8')
print(zip_path)
print(digest)
