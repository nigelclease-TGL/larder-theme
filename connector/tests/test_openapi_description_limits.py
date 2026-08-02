#!/usr/bin/env python3
from pathlib import Path
import json

ROOT = Path(__file__).resolve().parents[1]
SCHEMA = ROOT / 'artifacts' / 'generated' / 'openapi-0.7.31.json'
schema = json.loads(SCHEMA.read_text(encoding='utf-8'))
operations = []
violations = []
for path, methods in schema.get('paths', {}).items():
    for method, operation in methods.items():
        if not isinstance(operation, dict) or not operation.get('operationId'):
            continue
        operation_id = operation['operationId']
        length = len(operation.get('description', ''))
        operations.append((operation_id, path, method, length))
        if length > 300:
            violations.append((operation_id, length))

assert len(operations) == 28, f'Expected 28 operations, found {len(operations)}'
assert len({operation_id for operation_id, _, _, _ in operations}) == 28, 'Operation IDs must be unique'
assert not violations, f'Action descriptions exceed 300 characters: {violations}'

lengths = {operation_id: length for operation_id, _, _, length in operations}
for operation_id in [
    'inventoryLegacyConnectorDraftReconciliation',
    'reconcileLegacyConnectorDraftSupersession',
    'inspectReusableBlockEvidence',
    'startCompleteRecipeRevision',
    'updateClonedRecipeRevisions',
    'auditClonedRecipeRevision',
    'reviewRecipeRevision',
    'applyCompleteRecipeRevision',
]:
    assert operation_id in lengths, f'Missing operation: {operation_id}'
    assert lengths[operation_id] <= 300, f'{operation_id} description exceeds 300 characters'

print(f'{len(operations)} operations validated; all action descriptions are at most 300 characters')
