#!/usr/bin/env python3
from pathlib import Path
import json
import subprocess
import sys
import zipfile

ROOT = Path(__file__).resolve().parents[1]
subprocess.run([sys.executable, str(ROOT / 'build_0731.py')], check=True, capture_output=True, text=True)

SRC = ROOT / 'artifacts' / 'generated' / 'protected-lifecycle-0.7.31.php'
SCHEMA = ROOT / 'artifacts' / 'generated' / 'openapi-0.7.31.json'
UPDATER = ROOT / 'artifacts' / 'generated' / 'nkt-gpt-connector-upgrader-0.7.31.php'
ZIP = ROOT / 'artifacts' / 'nkt-gpt-connector-0.7.31-protected-recipe-name-only-revision-upgrader.zip'
FRAGMENT = ROOT / 'src' / 'parts' / '08g-recipe-name-only-revision.phpfrag'
text = SRC.read_text(encoding='utf-8')
schema_text = SCHEMA.read_text(encoding='utf-8')
schema = json.loads(schema_text)
updater = UPDATER.read_text(encoding='utf-8')
fragment = FRAGMENT.read_text(encoding='utf-8')
checks = []


def check(name, condition):
    checks.append((name, bool(condition)))
    if not condition:
        print('FAIL:', name)


check('connector source targets 0.7.31', "const NKT_GPT_PAR_0723_VERSION         = '0.7.31';" in text)
check('stored protected baselines retain 0.7.30 and 0.7.31', "'0.7.30', '0.7.31'" in text)
check('recipe_name_only exact scope exists', "const NKT_GPT_CRR_0731_SCOPE           = 'recipe_name_only';" in text)
check('nutrition_section_only compatibility scope retained', "const NKT_GPT_CRR_0731_COMPAT_SCOPE    = 'nutrition_section_only';" in text)
for function in ['start', 'update', 'audit', 'review', 'apply']:
    check(f'{function} wrapper exists', f'function nkt_gpt_crr_0731_{function}' in text)
check('five original complete recipe routes captured', all(path in text for path in [
    '/workflow/revisions/start', '/workflow/revisions/recipes/update', '/workflow/revisions/recipes/audit',
    '/workflow/revisions/review', '/workflow/revisions/apply',
]))
check('nutrition scope delegates unchanged', "NKT_GPT_CRR_0731_COMPAT_SCOPE === $scope" in fragment and "nkt_gpt_crr_0731_call_original( 'start', $request )" in fragment)
check('generic recipe edit disabled', "'generic_recipe_edit'    => array( 'available' => false )" in text)
check('explicit connector clone source guard exists', 'allow_current_live_connector_clone_source' in text and 'skip_live_connector_clones' in text)
check('fresh draft and clone evidence captured', all(item in text for item in ['before_drafts', 'before_clones', 'fresh_draft_created', 'fresh_clone_created']))
check('prior draft and clone evidence preserved', 'preserved_prior_draft_ids' in text and 'preserved_prior_clone_ids' in text)
check('update exact fields only', "array( 'draft_post_id', 'cloned_recipe_id', 'name' )" in text)
check('empty and unchanged target rejected', 'nkt_gpt_crr_0731_name_empty' in text and 'nkt_gpt_crr_0731_name_unchanged' in text)
check('name-only substantive comparison exists', 'manifest_ignore_name' in text and 'only_name_changed_on_clone' in text)
check('wprm nutrition protected', 'wprm_nutrition_unchanged' in text and 'clone_nutrition_unchanged' in text)
check('authorised and unexpected manifests returned', 'authorised_difference_manifest' in text and 'unexpected_difference_manifest' in text)
check('exact article recipe ID substitution required', 'article_only_recipe_id_substitution' in text and 'nkt_gpt_crr_0731_article_reference_only' in text)
check('fresh audit required for approval', 'nkt_gpt_crr_0731_audit_stale' in text and 'approved_audit_state_hash' in text)
check('current approved audit required for apply', 'nkt_gpt_crr_0731_apply_guard_failed' in text)
check('double apply prevented', 'nkt_gpt_crr_0731_already_applied' in text)
check('all protected snapshots restored on delegated write failure', 'nkt_gpt_crr_0731_restore_protected_states' in text and 'update_failed_restored' in text and 'apply_failed_restored' in text)
check('failed creation retained as failure evidence', 'nkt_gpt_crr_0731_mark_failed_pair' in text and '_nkt_crr_0731_initialisation_failed' in text)
check('source recipe direct edit not present', 'wp_update_post( $source_id' not in fragment and '$wpdb->update( $wpdb->posts, $source_id' not in fragment)
for forbidden in ['wp_trash_post', 'wp_delete_post', 'wp_delete_post', 'archiveRevisionPairs', 'cleanupRevisionObjects', 'repairProtectedRecipeStatusSideEffects', 'migration']:
    check('extension excludes ' + forbidden, forbidden not in fragment)
check('0.7.30 reusable block evidence retained', 'function nkt_gpt_par_0730_inspect_reusable_block' in text and "'inspectReusableBlockEvidence' => true" in text)
check('existing protected article lifecycle retained', all(name in text for name in [
    'nkt_gpt_par_0723_create', 'nkt_gpt_par_0723_update', 'nkt_gpt_par_0723_audit',
    'nkt_gpt_par_0723_review', 'nkt_gpt_par_0723_apply', 'nkt_gpt_par_0723_rollback',
]))

ops = [op['operationId'] for methods in schema['paths'].values() for op in methods.values() if isinstance(op, dict) and op.get('operationId')]
check('schema exposes 28 unique actions', len(ops) == 28 and len(set(ops)) == 28)
check('all five complete recipe actions retained', all(item in ops for item in [
    'startCompleteRecipeRevision', 'updateClonedRecipeRevisions', 'auditClonedRecipeRevision',
    'reviewRecipeRevision', 'applyCompleteRecipeRevision',
]))
check('schema version is 0.7.31', schema['info']['version'].startswith('0.7.31'))
check('schema has no old public connector version', '0.7.30' not in schema_text)
check('schema has no combinators', not any(token in schema_text for token in ['"allOf"', '"oneOf"', '"anyOf"']))
check('all action descriptions at most 300 chars', all(
    len(op.get('description', '')) <= 300
    for methods in schema['paths'].values()
    for op in methods.values()
    if isinstance(op, dict) and op.get('operationId')
))
start = schema['paths']['/workflow/revisions/start']['post']
start_props = start['requestBody']['content']['application/json']['schema']['properties']
check('schema accepts exactly both protected scopes', start_props['correction_scope']['enum'] == ['nutrition_section_only', 'recipe_name_only'])
check('schema exposes explicit live connector clone authorisation', 'allow_current_live_connector_clone_source' in start_props)
check('schema does not expose generic recipe scope', 'generic_recipe_edit' not in schema_text)

check('updater source version exact', "NKT_GPT_UPGRADER_0731_SOURCE_VERSION = '0.7.30'" in updater)
check('updater target version exact', "NKT_GPT_UPGRADER_0731_TARGET_VERSION = '0.7.31'" in updater)
check('updater source lifecycle exact', 'protected-lifecycle-0.7.30.php' in updater)
check('updater target lifecycle exact', 'protected-lifecycle-0.7.31.php' in updater)
check('updater regex guards exact source', all(item in updater for item in [r'Version:\s*0\.7\.30\s*$', r'NKT protected article lifecycle 0\.7\.30', r'protected-lifecycle-0\.7\.30\.php']))
check('updater has no stale 0.7.29 source marker', '0.7.29' not in updater and r'0\.7\.29' not in updater)
check('updater previous name is exact', "'NKT GPT Connector 0.7.30 Reusable Block Object Evidence Upgrader'" in updater)
check('updater restores on failure', 'restore_all' in updater)
check('updater invalidates cache', 'opcache_invalidate' in updater and 'wp_cache_flush' in updater)
check('updater self deactivates', 'deactivate_plugins( plugin_basename( __FILE__ ), true )' in updater)

for path in [SRC, UPDATER]:
    proc = subprocess.run(['php', '-l', str(path)], capture_output=True, text=True)
    check('PHP syntax ' + path.name, proc.returncode == 0)
    if proc.returncode:
        print(proc.stdout, proc.stderr)
with zipfile.ZipFile(ZIP) as archive:
    check('release ZIP integrity', archive.testzip() is None)
    check('release ZIP contains lifecycle', any(name.endswith('/protected-lifecycle-0.7.31.php') for name in archive.namelist()))
    check('release ZIP contains OpenAPI', any(name.endswith('/openapi-0.7.31.json') for name in archive.namelist()))

failed = [name for name, passed in checks if not passed]
print(f'{len(checks) - len(failed)}/{len(checks)} connector 0.7.31 static and package checks passed')
if failed:
    print('Failed:', *failed, sep='\n- ')
    raise SystemExit(1)
