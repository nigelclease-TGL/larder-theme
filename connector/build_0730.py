#!/usr/bin/env python3
from pathlib import Path
import hashlib
import json
import zipfile

from build_source import write as build_0729_source
from build_openapi import write as build_0729_openapi
from build_updater import write as build_0729_updater

ROOT = Path(__file__).resolve().parent
GENERATED = ROOT / 'artifacts' / 'generated'
VERSION = '0.7.30'
SLUG = 'reusable-block-object-evidence'


def promote_versions(text: str) -> str:
    return text.replace('0.7.29', '0.7.30')


def build_source() -> Path:
    inherited = build_0729_source().read_text(encoding='utf-8')
    evidence = (ROOT / 'src' / 'parts' / '08f-reusable-block-evidence.phpfrag').read_text(encoding='utf-8')
    marker = "add_action( 'rest_api_init', 'nkt_gpt_par_0723_register_routes', 99 );"
    if inherited.count(marker) != 1:
        raise RuntimeError('Unable to locate inherited route registration marker')
    inherited = inherited.replace(marker, evidence + '\n' + marker, 1)
    inherited = promote_versions(inherited)
    inherited = inherited.replace(
        "array( '0.7.23', '0.7.24', '0.7.25', '0.7.26', '0.7.27', '0.7.28', '0.7.30' )",
        "array( '0.7.23', '0.7.24', '0.7.25', '0.7.26', '0.7.27', '0.7.28', '0.7.29', '0.7.30' )",
    )
    required = [
        "const NKT_GPT_PAR_0723_VERSION         = '0.7.30';",
        'function nkt_gpt_par_0730_inspect_reusable_block',
        "'classification'                 => $is_block ? 'exists_accessible' : 'exists_wrong_post_type'",
        "'classification'          => 'missing_or_deleted'",
        "'classification'          => 'exists_inaccessible'",
        "'/reusable-block-evidence'",
        "'writes_attempted'        => false",
    ]
    missing = [item for item in required if item not in inherited]
    if missing:
        raise RuntimeError('0.7.30 lifecycle missing: ' + ', '.join(missing))
    output = GENERATED / 'protected-lifecycle-0.7.30.php'
    output.parent.mkdir(parents=True, exist_ok=True)
    output.write_text(inherited, encoding='utf-8')
    return output


def build_openapi() -> Path:
    inherited_path = build_0729_openapi()
    schema = json.loads(inherited_path.read_text(encoding='utf-8').replace('0.7.29', '0.7.30'))
    schema['info']['title'] = "Nigel's Kitchen Table GPT Connector - Compact Complete 0.7.30"
    schema['info']['version'] = '0.7.30-schema.1'
    schema['info']['description'] = (
        'Complete compact schema for connector 0.7.30. Retains all 27 existing actions and adds one standalone '
        'read-only reusable-block object evidence action. The new action does not alter protected lifecycle '
        'acceptance and never writes WordPress data.'
    )
    schema['paths']['/reusable-block-evidence'] = {
        'get': {
            'operationId': 'inspectReusableBlockEvidence',
            'summary': 'Inspect one reusable-block object and its literal WordPress state',
            'description': (
                'Read-only canonical wp_block lookup by numeric ID. Returns literal type, status, dates, content hashes, '
                'direct block references and exact missing, wrong-type or access classification without changing WordPress.'
            ),
            'parameters': [
                {'name': 'connector_version', 'in': 'query', 'required': True, 'schema': {'type': 'string', 'enum': ['0.7.30']}},
                {'name': 'reusable_block_id', 'in': 'query', 'required': True, 'schema': {'type': 'integer', 'minimum': 1}},
                {'name': 'include_raw_content', 'in': 'query', 'required': False, 'schema': {'type': 'boolean', 'default': False}},
                {'name': 'include_reference_scan', 'in': 'query', 'required': False, 'schema': {'type': 'boolean', 'default': True}},
                {'name': 'include_public_render_evidence', 'in': 'query', 'required': False, 'schema': {'type': 'boolean', 'default': False}},
            ],
            'responses': {
                '200': {'description': 'Reusable-block evidence returned.', 'content': {'application/json': {'schema': {'$ref': '#/components/schemas/ConnectorResponse'}}}},
                '400': {'description': 'Invalid request.', 'content': {'application/json': {'schema': {'$ref': '#/components/schemas/ErrorResponse'}}}},
                '409': {'description': 'Connector version conflict.', 'content': {'application/json': {'schema': {'$ref': '#/components/schemas/ErrorResponse'}}}},
            },
        }
    }
    descriptions = []
    operations = []
    for methods in schema['paths'].values():
        for operation in methods.values():
            if isinstance(operation, dict) and operation.get('operationId'):
                operations.append(operation['operationId'])
                descriptions.append((operation['operationId'], len(operation.get('description', ''))))
    if len(operations) != 28 or operations.count('inspectReusableBlockEvidence') != 1:
        raise RuntimeError(f'Expected exactly 28 unique actions, found {len(operations)}')
    violations = [item for item in descriptions if item[1] > 300]
    if violations:
        raise RuntimeError(f'Action descriptions exceed 300 characters: {violations}')
    text = json.dumps(schema, ensure_ascii=False, separators=(',', ':'))
    if any(token in text for token in ['"allOf"', '"oneOf"', '"anyOf"']):
        raise RuntimeError('OpenAPI combinators are not allowed')
    output = GENERATED / 'openapi-0.7.30.json'
    output.write_text(text, encoding='utf-8')
    return output


def build_updater() -> Path:
    inherited = build_0729_updater().read_text(encoding='utf-8')
    inherited = inherited.replace('0.7.29', '__NKT_TARGET__')
    inherited = inherited.replace('0.7.28', '0.7.29')
    inherited = inherited.replace('__NKT_TARGET__', '0.7.30')
    inherited = inherited.replace('0729', '0730')
    inherited = inherited.replace(
        'Guarded Legacy Draft Reconciliation Upgrader',
        'Reusable Block Object Evidence Upgrader',
    )
    inherited = inherited.replace(
        'with read-only legacy connector evidence and exact-pair guarded ownership, supersession, and obsolescence metadata reconciliation.',
        'with standalone read-only reusable-block object evidence and no protected lifecycle write changes.',
    )
    required = [
        "NKT_GPT_UPGRADER_0730_SOURCE_VERSION = '0.7.29'",
        "NKT_GPT_UPGRADER_0730_TARGET_VERSION = '0.7.30'",
        'protected-lifecycle-0.7.29.php',
        'protected-lifecycle-0.7.30.php',
        'openapi-0.7.30.json',
        'Version: 0.7.30',
        'restore_all',
        'opcache_invalidate',
        'wp_cache_flush',
    ]
    missing = [item for item in required if item not in inherited]
    if missing:
        raise RuntimeError('0.7.30 updater missing: ' + ', '.join(missing))
    output = GENERATED / 'nkt-gpt-connector-upgrader-0.7.30.php'
    output.write_text(inherited, encoding='utf-8')
    return output


def build_release() -> tuple[Path, Path]:
    source = build_source()
    openapi = build_openapi()
    updater = build_updater()
    out = ROOT / 'artifacts'
    out.mkdir(parents=True, exist_ok=True)
    zip_path = out / f'nkt-gpt-connector-{VERSION}-{SLUG}-upgrader.zip'
    sha_path = out / f'nkt-gpt-connector-{VERSION}-{SLUG}-upgrader.sha256.txt'
    prefix = 'nkt-gpt-connector-upgrader-0.7.30/'
    files = [
        (updater, 'nkt-gpt-connector-upgrader.php'),
        (source, 'protected-lifecycle-0.7.30.php'),
        (openapi, 'openapi-0.7.30.json'),
        (ROOT / 'CHANGELOG.md', 'CHANGELOG.txt'),
        (ROOT / 'README.md', 'README.txt'),
        (ROOT / 'VALIDATION-0.7.30.md', 'VALIDATION.txt'),
    ]
    with zipfile.ZipFile(zip_path, 'w', compression=zipfile.ZIP_DEFLATED, compresslevel=9) as archive:
        for source_path, target in files:
            data = source_path.read_bytes()
            info = zipfile.ZipInfo(prefix + target, date_time=(2026, 8, 2, 0, 0, 0))
            info.compress_type = zipfile.ZIP_DEFLATED
            info.external_attr = (0o100644 & 0xFFFF) << 16
            archive.writestr(info, data)
    digest = hashlib.sha256(zip_path.read_bytes()).hexdigest()
    sha_path.write_text(f'{digest}  {zip_path.name}\n', encoding='utf-8')
    return zip_path, sha_path


if __name__ == '__main__':
    release, checksum = build_release()
    print(release)
    print(checksum)
