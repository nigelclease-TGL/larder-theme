#!/usr/bin/env python3
from pathlib import Path
import json
import subprocess
import sys

ROOT = Path(__file__).resolve().parents[1]
subprocess.run([sys.executable, str(ROOT / 'build_0730.py')], check=True, capture_output=True, text=True)

SRC = ROOT / 'artifacts' / 'generated' / 'protected-lifecycle-0.7.30.php'
SCHEMA = ROOT / 'artifacts' / 'generated' / 'openapi-0.7.30.json'
UPDATER = ROOT / 'artifacts' / 'generated' / 'nkt-gpt-connector-upgrader-0.7.30.php'
FRAGMENT = ROOT / 'src' / 'parts' / '08f-reusable-block-evidence.phpfrag'
text = SRC.read_text(encoding='utf-8')
schema_text = SCHEMA.read_text(encoding='utf-8')
schema = json.loads(schema_text)
updater = UPDATER.read_text(encoding='utf-8')
fragment_text = FRAGMENT.read_text(encoding='utf-8')
checks = []


def check(name, condition):
    checks.append((name, bool(condition)))
    if not condition:
        print('FAIL:', name)


check('connector source targets 0.7.30', "const NKT_GPT_PAR_0723_VERSION         = '0.7.30';" in text)
check('runtime guard targets 0.7.30', "'0.7.30' !== NKT_GPT_CONNECTOR_VERSION" in text)
check('stored baseline compatibility retains 0.7.29 and 0.7.30', "'0.7.29', '0.7.30'" in text)
check('read-only callback exists', 'function nkt_gpt_par_0730_inspect_reusable_block' in text)
check('testable evidence helper exists', 'function nkt_gpt_par_0730_reusable_block_evidence' in text)
check('canonical raw get_post lookup exists', "get_post( $block_id, OBJECT, 'raw' )" in text)
check('positive ID validation exists', 'nkt_gpt_reusable_block_id_invalid' in text)
check('published wp_block is accessible classification', "'exists_accessible'" in text)
check('literal WordPress status returned', "'literal_wordpress_status'" in text and "(string) $post->post_status" in text)
check('missing object classification exists', "'missing_or_deleted'" in text and 'nkt_gpt_reusable_block_missing' in text)
check('wrong post type classification exists', "'exists_wrong_post_type'" in text and 'nkt_gpt_reusable_block_wrong_post_type' in text)
check('access failure classification exists', "'exists_inaccessible'" in text and 'nkt_gpt_par_0730_reusable_block_evidence_access' in text)
check('new API-key evidence action does not require logged-in WordPress user', 'current_user_can(' not in fragment_text)
check('direct reference scan exists', 'nkt_gpt_par_0730_direct_block_references' in text and 'direct_core_block_references' in text)
check('raw content optional', "'include_raw_content'" in text and "response['raw_post_content']" in text)
check('raw content hash exists', "'raw_content_hash'" in text and "hash( 'sha256', $content )" in text)
check('object hash version returned', 'nkt-wp-block-object-v1' in text)
check('status independent hash exists', 'status_independent_object_hash' in text and 'nkt_gpt_par_0730_block_hash_payload( $post, false )' in text)
check('status inclusive hash exists', 'status_inclusive_object_hash' in text and 'nkt_gpt_par_0730_block_hash_payload( $post, true )' in text)
check('status only included conditionally', "if ( $include_status )" in text and "payload['post_status']" in text)
check('timestamps author parent and slug returned', all(field in text for field in ['created_local', 'created_gmt', 'modified_local', 'modified_gmt', 'author_id', 'parent_id', "'slug'"]))
check('response declares read only and no writes', all(item in text for item in ["'read_only'", "'changes_made'", "'writes_attempted'", "'writes_performed'"]))
check('status capability reporting exists', "'inspectReusableBlockEvidence' => true" in text and "'protected_baseline_changed' => false" in text)

helper_start = text.index('function nkt_gpt_par_0730_reusable_block_evidence')
helper_end = text.index('/** Inspect one arbitrary WordPress object')
helper = text[helper_start:helper_end]
for forbidden in ['wp_update_post', 'wp_insert_post', 'wp_delete_post', 'wp_trash_post', 'update_post_meta', 'delete_post_meta', 'add_post_meta']:
    check('evidence helper contains no mutation ' + forbidden, forbidden not in helper)

check('standalone GET route exists', "'/reusable-block-evidence'" in text and 'WP_REST_Server::READABLE' in text)
check('existing protected create retained', 'function nkt_gpt_par_0723_create' in text)
check('existing protected update retained', 'function nkt_gpt_par_0723_update' in text)
check('existing audit review apply rollback retained', all(name in text for name in ['nkt_gpt_par_0723_audit', 'nkt_gpt_par_0723_review', 'nkt_gpt_par_0723_apply', 'nkt_gpt_par_0723_rollback']))
check('cleanup and legacy reconciliation retained', all(name in text for name in ['nkt_gpt_par_0728_inventory_connector_managed_drafts', 'nkt_gpt_par_0729_inventory_legacy_reconciliation', 'nkt_gpt_par_0729_reconcile_legacy_supersessions']))

ops = [op['operationId'] for methods in schema['paths'].values() for op in methods.values() if isinstance(op, dict) and op.get('operationId')]
check('schema exposes 28 unique actions', len(ops) == 28 and len(set(ops)) == 28)
check('new action exposed once', ops.count('inspectReusableBlockEvidence') == 1)
check('schema version is 0.7.30', schema['info']['version'].startswith('0.7.30'))
check('schema has no old public connector version', '0.7.29' not in schema_text)
check('schema has no combinators', not any(token in schema_text for token in ['"allOf"', '"oneOf"', '"anyOf"']))
new_action = schema['paths']['/reusable-block-evidence']['get']
check('new action description below 300 chars', len(new_action['description']) <= 300)
params = {item['name']: item for item in new_action['parameters']}
for field in ['connector_version', 'reusable_block_id', 'include_raw_content', 'include_reference_scan', 'include_public_render_evidence']:
    check('schema exposes ' + field, field in params)
check('required request fields enforced', params['connector_version']['required'] and params['reusable_block_id']['required'])

check('updater source version exact', "NKT_GPT_UPGRADER_0730_SOURCE_VERSION = '0.7.29'" in updater)
check('updater target version exact', "NKT_GPT_UPGRADER_0730_TARGET_VERSION = '0.7.30'" in updater)
check('updater source lifecycle exact', 'protected-lifecycle-0.7.29.php' in updater)
check('updater target lifecycle exact', 'protected-lifecycle-0.7.30.php' in updater)
check('updater restores on failure', 'restore_all' in updater)
check('updater invalidates opcode cache', 'opcache_invalidate' in updater)
check('updater flushes WordPress cache', 'wp_cache_flush' in updater)
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
