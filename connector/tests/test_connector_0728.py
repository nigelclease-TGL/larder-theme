#!/usr/bin/env python3
from pathlib import Path
import json
import subprocess
import sys

ROOT = Path(__file__).resolve().parents[1]
subprocess.run([sys.executable, str(ROOT / 'build_source.py')], check=True, capture_output=True, text=True)
subprocess.run([sys.executable, str(ROOT / 'build_openapi.py')], check=True, capture_output=True, text=True)
subprocess.run([sys.executable, str(ROOT / 'build_updater.py')], check=True, capture_output=True, text=True)

SRC = ROOT / 'artifacts' / 'generated' / 'protected-lifecycle-0.7.28.php'
SCHEMA = ROOT / 'artifacts' / 'generated' / 'openapi-0.7.28.json'
UPDATER = ROOT / 'artifacts' / 'generated' / 'nkt-gpt-connector-upgrader-0.7.28.php'
text = SRC.read_text(encoding='utf-8')
schema_text = SCHEMA.read_text(encoding='utf-8')
schema = json.loads(schema_text)
updater = UPDATER.read_text(encoding='utf-8')
checks = []


def check(name, condition):
    checks.append((name, bool(condition)))
    if not condition:
        print('FAIL:', name)


check('connector source targets 0.7.28', "const NKT_GPT_PAR_0723_VERSION         = '0.7.28';" in text)
check('runtime guard targets 0.7.28', "'0.7.28' !== NKT_GPT_CONNECTOR_VERSION" in text)
check('stored baseline compatibility through 0.7.28', "'0.7.27', '0.7.28'" in text)
check('explicit ownership bundle exists', "'_nkt_connector_ownership'" in text)
check('explicit connector managed marker exists', "'_nkt_connector_managed'" in text)
check('connector identity is exact', "'nigels_kitchen_table'" in text)
check('ownership refresh follows lifecycle metadata', "add_action( 'updated_post_meta', 'nkt_gpt_par_0728_ownership_meta_hook'" in text)
check('legacy ownership requires source workflow baseline lifecycle', 'conclusive_legacy_protected_lifecycle' in text)
check('ambiguous ownership stays preserved', 'preserve_manual_or_unknown' in text)
check('inventory callback exists', 'function nkt_gpt_par_0728_inventory_connector_managed_drafts' in text)
check('inventory route is readable', "'/protected-article-revisions/cleanup-inventory'" in text and 'WP_REST_Server::READABLE' in text)
check('inventory is explicitly read only', "'read_only'         => true" in text and "'writes_attempted'  => false" in text)
check('inventory pagination exists', "'total_pages'" in text and "'X-WP-TotalPages'" in text)
check('classification hash exists', "'safe_to_trash_classification_hash'" in text)
check('ownership hash exists', "'connector_ownership_hash'" in text)
check('native trash callback exists', 'function nkt_gpt_par_0728_trash_connector_managed_drafts' in text)
check('trash route requires exact ids', "'draft_post_ids' => array( 'type' => 'array', 'required' => true" in text)
check('native wp_trash_post used', 'wp_trash_post( $draft_id )' in text)
check('failed batches restore prior trash moves', 'wp_untrash_post( $trashed_id )' in text)
check('native trash confirmation required', 'confirm_native_wordpress_trash' in text)
check('batch fails when any guard fails', 'trash_preflight_failed' in text and 'at least one exact draft failed a required guard' in text)
check('safe_to_trash false is a hard guard', "'safe_to_trash_false'" in text)
check('ledger unavailable fails closed', "'unknown' === $ledger_state" in text)
check('protected active drafts preserved', "'preserve_active'" in text)
check('initialised drafts preserved', "'preserve_initialised'" in text)
check('audited drafts preserved', "'preserve_audited'" in text)
check('approved drafts preserved', "'preserve_approved'" in text)
check('not applied drafts preserved', "'preserve_not_applied'" in text)
check('applied records preserved', "'preserve_applied_record'" in text)
check('failure evidence preserved', "'preserve_failure_evidence'" in text)
check('clone evidence preserved', "'preserve_clone_evidence'" in text)
check('ledger references preserved', "'preserve_ledger_reference'" in text)
check('intentional exceptions preserved', "'preserve_intentional_exception'" in text)
for object_id in [41045, 40706, 40707, 34505, 34548, 41019, 41037, 41039, 30752, 30780, 30800, 41044, 40700, 40701, 36098, 39272]:
    check(f'required object {object_id} is hard preserved', str(object_id) in text)

trash_slice = text[text.index('function nkt_gpt_par_0728_trash_connector_managed_drafts'):text.index('/** Register the read-only inventory')]
check('trash action does not permanently delete', 'wp_delete_post' not in trash_slice)
check('trash action does not empty trash', 'wp_delete_post' not in trash_slice and 'empty_trash' not in trash_slice)
check('trash action does not use archive mode', 'cleanup_mode' not in trash_slice and "'archive'" not in trash_slice)
check('status advertises no archive substitution', "'archive_substitution_allowed'            => false" in text)
check('status advertises no permanent delete', "'permanent_delete_allowed'                => false" in text)
check('status advertises no empty trash', "'empty_trash_allowed'                     => false" in text)

ops = []
for methods in schema['paths'].values():
    for op in methods.values():
        if isinstance(op, dict) and op.get('operationId'):
            ops.append(op['operationId'])
check('schema exposes 25 actions', len(ops) == 25)
check('inventory action exposed', 'inventoryConnectorManagedDraftCleanup' in ops)
check('trash action exposed', 'trashConnectorManagedArticleDrafts' in ops)
check('schema public version is 0.7.28', schema['info']['version'].startswith('0.7.28'))
check('schema contains no earlier public version', not any(v in schema_text for v in ['0.7.23', '0.7.24', '0.7.25', '0.7.26', '0.7.27']))
check('schema has no combinators', not any(x in schema_text for x in ['"allOf"', '"oneOf"', '"anyOf"']))
trash_schema = schema['paths']['/protected-article-revisions/trash']['post']['requestBody']['content']['application/json']['schema']
for field in [
    'draft_post_ids', 'expected_classification_hashes', 'expected_connector_ownership_hashes',
    'expected_wordpress_statuses', 'expected_source_live_post_ids', 'expected_review_statuses',
    'expected_application_statuses', 'expected_programme_ledger_states',
    'expected_protected_active_states', 'expected_linked_recipe_clone_ids',
    'expected_linked_failure_evidence_ids'
]:
    check('trash schema requires ' + field, field in trash_schema['required'])

check('updater source version exact', "NKT_GPT_UPGRADER_0728_SOURCE_VERSION = '0.7.27'" in updater)
check('updater target version exact', "NKT_GPT_UPGRADER_0728_TARGET_VERSION = '0.7.28'" in updater)
check('updater uses generated lifecycle', 'protected-lifecycle-0.7.28.php' in updater)
check('updater identifies source lifecycle', 'protected-lifecycle-0.7.27.php' in updater)
check('updater backs up primary connector', 'secure_backup( $main_file, $main_backup )' in updater)
check('updater backs up old lifecycle', 'secure_backup( $old_lifecycle, $lifecycle_backup )' in updater)
check('updater verifies lifecycle SHA', "hash_file( 'sha256', $new_lifecycle )" in updater)
check('updater invalidates opcode cache', 'opcache_invalidate' in updater)
check('updater flushes WordPress cache', 'wp_cache_flush' in updater)
check('updater restores on failure', 'restore_all' in updater)
check('updater self deactivates', 'deactivate_plugins( plugin_basename( __FILE__ ), true )' in updater)

for path in [SRC, UPDATER]:
    proc = subprocess.run(['php', '-l', str(path)], capture_output=True, text=True)
    check('PHP syntax ' + path.name, proc.returncode == 0)
    if proc.returncode:
        print(proc.stdout, proc.stderr)

failed = [name for name, passed in checks if not passed]
print(f'{len(checks) - len(failed)}/{len(checks)} checks passed')
if failed:
    print('Failed:', *failed, sep='\n- ')
    raise SystemExit(1)
