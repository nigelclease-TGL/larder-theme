#!/usr/bin/env python3
from pathlib import Path
import hashlib
import json
import zipfile

from build_0730 import build_source as build_0730_source
from build_0730 import build_openapi as build_0730_openapi
from build_0730 import build_updater as build_0730_updater

ROOT = Path(__file__).resolve().parent
GENERATED = ROOT / 'artifacts' / 'generated'
VERSION = '0.7.31'
SLUG = 'protected-recipe-name-only-revision'


def build_source() -> Path:
    inherited = build_0730_source().read_text(encoding='utf-8')
    extension = (ROOT / 'src' / 'parts' / '08g-recipe-name-only-revision.phpfrag').read_text(encoding='utf-8')
    marker = "add_action( 'rest_api_init', 'nkt_gpt_par_0723_register_routes', 99 );"
    if inherited.count(marker) != 1:
        raise RuntimeError('Unable to locate inherited route registration marker')
    inherited = inherited.replace(marker, extension + '\n' + marker, 1)
    inherited = inherited.replace('0.7.30', '0.7.31')
    inherited = inherited.replace(
        "array( '0.7.23', '0.7.24', '0.7.25', '0.7.26', '0.7.27', '0.7.28', '0.7.29', '0.7.31' )",
        "array( '0.7.23', '0.7.24', '0.7.25', '0.7.26', '0.7.27', '0.7.28', '0.7.29', '0.7.30', '0.7.31' )",
    )
    required = [
        "const NKT_GPT_PAR_0723_VERSION         = '0.7.31';",
        'function nkt_gpt_crr_0731_start',
        'function nkt_gpt_crr_0731_update',
        'function nkt_gpt_crr_0731_audit',
        'function nkt_gpt_crr_0731_review',
        'function nkt_gpt_crr_0731_apply',
        "const NKT_GPT_CRR_0731_SCOPE           = 'recipe_name_only';",
        "const NKT_GPT_CRR_0731_COMPAT_SCOPE    = 'nutrition_section_only';",
        "'0.7.30', '0.7.31'",
        "'inspectReusableBlockEvidence' => true",
        "'generic_recipe_edit'    => array( 'available' => false )",
        "nkt_gpt_crr_0731_restore_protected_states",
        "nkt_gpt_crr_0731_mark_failed_pair",
    ]
    missing = [item for item in required if item not in inherited]
    if missing:
        raise RuntimeError('0.7.31 lifecycle missing: ' + ', '.join(missing))
    output = GENERATED / 'protected-lifecycle-0.7.31.php'
    output.parent.mkdir(parents=True, exist_ok=True)
    output.write_text(inherited, encoding='utf-8')
    return output


def _operation(schema: dict, path: str, method: str) -> dict:
    try:
        return schema['paths'][path][method]
    except KeyError as exc:
        raise RuntimeError(f'Missing OpenAPI operation {method.upper()} {path}') from exc


def _require_body_connector_version(operation: dict) -> None:
    body = operation['requestBody']['content']['application/json']['schema']
    body.setdefault('properties', {})['connector_version'] = {
        'type': 'string',
        'enum': [VERSION],
        'description': 'Exact active connector version required by the protected runtime guard.',
    }
    required = body.setdefault('required', [])
    if 'connector_version' not in required:
        required.insert(0, 'connector_version')


def _require_query_connector_version(operation: dict) -> None:
    parameters = operation.setdefault('parameters', [])
    existing = [item for item in parameters if item.get('name') == 'connector_version']
    if existing:
        existing[0].update({
            'in': 'query',
            'required': True,
            'schema': {'type': 'string', 'enum': [VERSION]},
        })
        return
    parameters.insert(0, {
        'name': 'connector_version',
        'in': 'query',
        'required': True,
        'schema': {'type': 'string', 'enum': [VERSION]},
        'description': 'Exact active connector version required by the protected runtime guard.',
    })


def build_openapi() -> Path:
    schema = json.loads(build_0730_openapi().read_text(encoding='utf-8').replace('0.7.30', '0.7.31'))
    schema['info'].update({
        'title': "Nigel's Kitchen Table GPT Connector - Compact Complete 0.7.31",
        'version': '0.7.31-schema.2',
        'description': (
            'Complete compact schema for connector 0.7.31. Retains all existing actions and adds the exact '
            'recipe_name_only scope to the existing protected complete-recipe clone, audit, review and apply lifecycle.'
        ),
    })

    start = _operation(schema, '/workflow/revisions/start', 'post')
    _require_body_connector_version(start)
    start_schema = start['requestBody']['content']['application/json']['schema']
    start_props = start_schema['properties']
    start_props['correction_scope']['enum'] = ['nutrition_section_only', 'recipe_name_only']
    start_props['allow_current_live_connector_clone_source'] = {
        'type': 'boolean',
        'default': False,
        'description': 'Required only when recipe_name_only explicitly uses the sole currently referenced connector-created live recipe as its source.',
    }
    start['summary'] = 'Start a protected nutrition or recipe-name revision pair'
    start['description'] = (
        'Starts the existing complete-recipe lifecycle. recipe_name_only requires one fresh draft and clone, one current live recipe, preserved prior evidence, and explicit authorisation when that live source is itself a connector clone.'
    )

    update = _operation(schema, '/workflow/revisions/recipes/update', 'post')
    _require_body_connector_version(update)
    update['summary'] = 'Update isolated cloned recipe revisions within their protected scope'
    update['description'] = (
        'Uses the stored protected scope. recipe_name_only accepts exactly one item and only draft_post_id, cloned_recipe_id and a non-empty changed name; every other field is rejected before writing and unexpected drift is restored.'
    )

    audit = _operation(schema, '/workflow/revisions/recipes/audit', 'get')
    _require_query_connector_version(audit)
    audit['description'] = (
        'Audits the current clone and draft. For recipe_name_only, only the approved name and exact article recipe-ID substitution may differ; source recipe, Nutrition and every other protected component must remain unchanged.'
    )

    review = _operation(schema, '/workflow/revisions/review', 'post')
    _require_body_connector_version(review)
    review['description'] = (
        'Records the existing review decision. Approval for recipe_name_only requires a fresh passing audit whose current source, clone and draft state hash still matches exactly.'
    )

    apply = _operation(schema, '/workflow/revisions/apply', 'post')
    _require_body_connector_version(apply)
    apply['description'] = (
        'Applies one approved protected pair once. recipe_name_only substitutes only the recipe ID, retains the source and draft, verifies the target name and all protected fields, and restores exact snapshots on failure.'
    )

    body_guard_operations = {
        'startCompleteRecipeRevision': start,
        'updateClonedRecipeRevisions': update,
        'reviewRecipeRevision': review,
        'applyCompleteRecipeRevision': apply,
    }
    for operation_id, operation in body_guard_operations.items():
        body = operation['requestBody']['content']['application/json']['schema']
        version_property = body.get('properties', {}).get('connector_version', {})
        if 'connector_version' not in body.get('required', []) or version_property.get('enum') != [VERSION]:
            raise RuntimeError(f'{operation_id} must require connector_version {VERSION}')
    audit_version_parameters = [
        item for item in audit.get('parameters', [])
        if item.get('name') == 'connector_version' and item.get('in') == 'query'
    ]
    if len(audit_version_parameters) != 1 or not audit_version_parameters[0].get('required') or audit_version_parameters[0].get('schema', {}).get('enum') != [VERSION]:
        raise RuntimeError(f'auditClonedRecipeRevision must require connector_version {VERSION}')

    operations = []
    violations = []
    for methods in schema['paths'].values():
        for operation in methods.values():
            if isinstance(operation, dict) and operation.get('operationId'):
                operations.append(operation['operationId'])
                if len(operation.get('description', '')) > 300:
                    violations.append((operation['operationId'], len(operation['description'])))
    if len(operations) != 28 or len(set(operations)) != 28:
        raise RuntimeError(f'Expected 28 unique actions, found {len(operations)}')
    required_actions = [
        'startCompleteRecipeRevision', 'updateClonedRecipeRevisions', 'auditClonedRecipeRevision',
        'reviewRecipeRevision', 'applyCompleteRecipeRevision', 'inspectReusableBlockEvidence',
    ]
    missing_actions = [item for item in required_actions if item not in operations]
    if missing_actions:
        raise RuntimeError('Missing required actions: ' + ', '.join(missing_actions))
    if violations:
        raise RuntimeError(f'Action descriptions exceed 300 characters: {violations}')
    text = json.dumps(schema, ensure_ascii=False, separators=(',', ':'))
    if any(token in text for token in ['"allOf"', '"oneOf"', '"anyOf"']):
        raise RuntimeError('OpenAPI combinators are not allowed')
    if 'generic_recipe_edit' in text:
        raise RuntimeError('Generic recipe-edit scope must not be exposed')
    output = GENERATED / 'openapi-0.7.31.json'
    output.write_text(text, encoding='utf-8')
    return output


def build_updater() -> Path:
    inherited = build_0730_updater().read_text(encoding='utf-8')
    # Transform escaped versions used in regular expressions independently.
    inherited = inherited.replace(r'0\.7\.30', '__NKT_TARGET_ESCAPED__')
    inherited = inherited.replace(r'0\.7\.29', r'0\.7\.30')
    inherited = inherited.replace('__NKT_TARGET_ESCAPED__', r'0\.7\.31')
    # Transform literal source and target versions.
    inherited = inherited.replace('0.7.30', '__NKT_TARGET__').replace('0.7.29', '0.7.30').replace('__NKT_TARGET__', '0.7.31')
    inherited = inherited.replace('0730', '0731')
    inherited = inherited.replace('Reusable Block Object Evidence Upgrader', 'Protected Recipe Name Only Revision Upgrader')
    inherited = inherited.replace(
        'with standalone read-only reusable-block object evidence and no protected lifecycle write changes.',
        'with the narrowly guarded protected recipe_name_only complete-recipe revision scope.',
    )
    inherited = inherited.replace(
        "'NKT GPT Connector 0.7.30 Guarded Legacy Draft Reconciliation Upgrader'",
        "'NKT GPT Connector 0.7.30 Reusable Block Object Evidence Upgrader'",
    )
    inherited = inherited.replace('Version: 0.7.31.2', 'Version: 0.7.31.1')
    required = [
        "NKT_GPT_UPGRADER_0731_SOURCE_VERSION = '0.7.30'",
        "NKT_GPT_UPGRADER_0731_TARGET_VERSION = '0.7.31'",
        'protected-lifecycle-0.7.30.php',
        'protected-lifecycle-0.7.31.php',
        'openapi-0.7.31.json',
        'Version: 0.7.31.1',
        r'Version:\s*0\.7\.30\s*$',
        r'Version:\s*)0\.7\.30(\s*$)',
        r'NKT protected article lifecycle 0\.7\.30',
        r"protected-lifecycle-0\.7\.30\.php",
        "'NKT GPT Connector 0.7.30 Reusable Block Object Evidence Upgrader'",
        'restore_all',
        'opcache_invalidate',
        'wp_cache_flush',
        'deactivate_plugins( plugin_basename( __FILE__ ), true )',
    ]
    missing = [item for item in required if item not in inherited]
    if missing:
        raise RuntimeError('0.7.31 updater missing: ' + ', '.join(missing))
    stale = ['0.7.29', r'0\.7\.29']
    found = [item for item in stale if item in inherited]
    if found:
        raise RuntimeError('0.7.31 updater contains stale source markers: ' + ', '.join(found))
    output = GENERATED / 'nkt-gpt-connector-upgrader-0.7.31.php'
    output.write_text(inherited, encoding='utf-8')
    return output


def build_release() -> tuple[Path, Path]:
    source, openapi, updater = build_source(), build_openapi(), build_updater()
    out = ROOT / 'artifacts'
    out.mkdir(parents=True, exist_ok=True)
    zip_path = out / f'nkt-gpt-connector-{VERSION}-{SLUG}-upgrader.zip'
    sha_path = out / f'nkt-gpt-connector-{VERSION}-{SLUG}-upgrader.sha256.txt'
    files = [
        (updater, 'nkt-gpt-connector-upgrader.php'),
        (source, 'protected-lifecycle-0.7.31.php'),
        (openapi, 'openapi-0.7.31.json'),
        (ROOT / 'CHANGELOG.md', 'CHANGELOG.txt'),
        (ROOT / 'CHANGELOG-0.7.31.md', 'CHANGELOG-0.7.31.txt'),
        (ROOT / 'README.md', 'README.txt'),
        (ROOT / 'VALIDATION-0.7.31.md', 'VALIDATION.txt'),
    ]
    with zipfile.ZipFile(zip_path, 'w', compression=zipfile.ZIP_DEFLATED, compresslevel=9) as archive:
        for source_path, target in files:
            info = zipfile.ZipInfo('nkt-gpt-connector-upgrader-0.7.31/' + target, date_time=(2026, 8, 2, 0, 0, 0))
            info.compress_type = zipfile.ZIP_DEFLATED
            info.external_attr = (0o100644 & 0xFFFF) << 16
            archive.writestr(info, source_path.read_bytes())
    digest = hashlib.sha256(zip_path.read_bytes()).hexdigest()
    sha_path.write_text(f'{digest}  {zip_path.name}\n', encoding='utf-8')
    return zip_path, sha_path


if __name__ == '__main__':
    release, checksum = build_release()
    print(release)
    print(checksum)
