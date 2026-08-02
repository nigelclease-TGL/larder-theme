#!/usr/bin/env python3
from pathlib import Path
import json

ROOT = Path(__file__).resolve().parents[1]
SCHEMA = ROOT / 'artifacts' / 'generated' / 'openapi-0.7.29.json'
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

assert len(operations) == 27, f'Expected 27 operations, found {len(operations)}'
assert not violations, f'Action descriptions exceed 300 characters: {violations}'

expected = {
    'inventoryLegacyConnectorDraftReconciliation': 255,
    'reconcileLegacyConnectorDraftSupersession': 267,
}
lengths = {operation_id: length for operation_id, _, _, length in operations}
for operation_id, expected_length in expected.items():
    assert lengths.get(operation_id) == expected_length, (
        f'{operation_id} description length changed: '
        f'{lengths.get(operation_id)} != {expected_length}'
    )

print(f'{len(operations)} operations validated; all action descriptions are at most 300 characters')
