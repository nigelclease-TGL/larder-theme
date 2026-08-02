#!/usr/bin/env python3
from pathlib import Path
import json
import subprocess
import sys

ROOT = Path(__file__).resolve().parents[1]
subprocess.run([sys.executable, str(ROOT / 'build_source.py')], check=True, capture_output=True, text=True)
subprocess.run([sys.executable, str(ROOT / 'build_openapi.py')], check=True, capture_output=True, text=True)
subprocess.run([sys.executable, str(ROOT / 'build_updater.py')], check=True, capture_output=True, text=True)

SRC = ROOT / 'artifacts' / 'generated' / 'protected-lifecycle-0.7.29.php'
SCHEMA = ROOT / 'artifacts' / 'generated' / 'openapi-0.7.29.json'
UPDATER = ROOT / 'artifacts' / 'generated' / 'nkt-gpt-connector-upgrader-0.7.29.php'
text = SRC.read_text(encoding='utf-8')
schema_text = SCHEMA.read_text(encoding='utf-8')
schema = json.loads(schema_text)
updater = UPDATER.read_text(encoding='utf-8')
checks = []


def check(name, condition):
    checks.append((name, bool(condition)))
    if not condition:
        print('FAIL:', name)


check('connector source targets 0.7.29', "const NKT_GPT_PAR_0723_VERSION         = '0.7.29';" in text)
check('runtime guard targets 0.7.29', "'0.7.29' !== NKT_GPT_CONNECTOR_VERSION" in text)
check('stored baseline compatibility through 0.7.29', "'0.7.28', '0.7.29'" in text)
check('0.7.28 cleanup inventory retained', 'function nkt_gpt_par_0728_inventory_connector_managed_drafts' in text)
check('0.7.28 native Trash retained', 'function nkt_gpt_par_0728_trash_connector_managed_drafts' in text)
check('legacy evidence snapshot exists', 'function nkt_gpt_par_0729_legacy_signal_snapshot' in text)
check('legacy evidence requires three signals', 'insufficient_independent_connector_signals' in text and 'count( $signals ) < 3' in text)
check('legacy evidence requires protected structure', 'protected_structural_evidence_missing' in text)
check('source mismatch guards exist', 'baseline_source_live_post_mismatch' in text and 'lifecycle_source_live_post_mismatch' in text)
check('lifecycle draft mismatch guard exists', 'lifecycle_draft_id_mismatch' in text)
check('ordinary draft state is rejected', 'draft_post_state_not_exact' in text)
check('source must be a published article', 'source_live_post_not_published_article' in text)
check('only manual and required workflow reasons are reconcilable', "return array( 'preserve_manual_or_unknown', 'preserve_required_workflow' );" in text)
check('ledger references cannot be reconciled', "'not_referenced' === (string) ( $class['programme_ledger_reference_state']" in text)
check('active drafts cannot be reconciled', "empty( $class['protected_draft_active'] )" in text)
check('clone and failure links cannot be reconciled', "empty( $class['linked_recipe_clone_ids'] )" in text and "empty( $class['linked_failure_evidence_ids'] )" in text)
check('hard preservation IDs remain blocked', 'nkt_gpt_par_0728_required_preservation_ids()[ $draft_id ]' in text)
check('read-only reconciliation inventory exists', 'function nkt_gpt_par_0729_inventory_legacy_reconciliation' in text)
check('reconciliation inventory is read only', "'read_only'         => true" in text and "'writes_attempted'  => false" in text)
check('reconciliation inventory is paginated', "'total_candidates'" in text and "'X-WP-TotalPages'" in text)
check('reconciliation evidence hash exists', "'reconciliation_evidence_hash'" in text)
check('eligible successors require proven ownership', 'superseding_draft_ownership_not_proven' in text)
check('eligible successors must be retained', 'nkt_gpt_par_0729_retained_successor_reasons' in text)
check('exact pair write callback exists', 'function nkt_gpt_par_0729_reconcile_legacy_supersessions' in text)
check('both explicit confirmations required', 'confirm_connector_ownership_reconciliation' in text and 'confirm_supersession_and_obsolescence' in text)
check('dry run returns no writes', "'dry_run'           => true" in text and "'writes_completed'  => false" in text)
check('whole batch preflight fails closed', 'reconciliation_preflight_failed' in text and 'writes_attempted' in text)
check('cross-pair chains are refused', 'cross_pair_chain_refused' in text)
check('duplicate obsolete IDs are refused', 'duplicate_or_invalid_obsolete_draft_id' in text)
check('successor ID must be newer', 'superseding_draft_id_not_newer' in text)
check('successor timestamp must be newer', 'superseding_draft_timestamp_not_newer' in text)
check('exact source equality enforced', 'superseding_draft_source_mismatch' in text)
check('all evidence hashes are compared', 'expected_reconciliation_evidence_hash' in text and 'expected_obsolete_classification_hash' in text and 'expected_superseding_classification_hash' in text)
check('all ownership hashes are compared', 'expected_obsolete_ownership_hash' in text and 'expected_superseding_ownership_hash' in text)
check('all lifecycle and ledger guards are compared', 'expected_obsolete_programme_ledger_state' in text and 'expected_superseding_programme_ledger_state' in text)
check('clone and failure arrays are compared', 'expected_superseding_linked_failure_evidence_ids' in text)
check('metadata rollback snapshots exist', 'function nkt_gpt_par_0729_meta_snapshot' in text and 'function nkt_gpt_par_0729_restore_meta_snapshot' in text)
check('reconciliation writes metadata only', "update_post_meta( $obsolete_id, '_nkt_connector_ownership'" in text)
check('obsolete disposition is explicit', "'_nkt_connector_cleanup_disposition', 'obsolete'" in text)
check('superseding draft ID is explicit', "'_nkt_connector_superseded_by_draft_id', $successor_id" in text)
check('post-write classifier must return safe', "'eligible_obsolete_connector_draft' ===" in text and "! empty( $after['safe_to_trash'] )" in text)
check('failed verification restores exact metadata', 'post_write_verification_failed' in text and 'restore_meta_snapshot' in text)
check('response declares protected objects unchanged', "'article_content_changed' => false" in text and "'live_posts_changed'=> false" in text and "'recipe_objects_changed' => false" in text)
check('status advertises no similarity inference', "'title_author_slug_content_similarity_used'    => false" in text)
check('status advertises no Trash in reconciliation', "'trash_allowed_in_reconciliation_action'       => false" in text)

reconcile_start = text.index('function nkt_gpt_par_0729_reconcile_legacy_supersessions')
reconcile_end = text.index('/** Register guarded reconciliation routes.')
reconcile_slice = text[reconcile_start:reconcile_end]
check('reconciliation action never calls native Trash', 'wp_trash_post' not in reconcile_slice)
check('reconciliation action never deletes', 'wp_delete_post' not in reconcile_slice and 'wp_delete_attachment' not in reconcile_slice)
check('reconciliation action never archives', 'cleanup_mode' not in reconcile_slice and 'archiveRevisionPairs' not in reconcile_slice)
check('reconciliation action never writes article content', 'wp_update_post' not in reconcile_slice and 'post_content' not in reconcile_slice)

for object_id in [41045, 40706, 40707, 34505, 34548, 41019, 41037, 41039, 30752, 30780, 30800, 41044, 40700, 40701, 36098, 39272]:
    check(f'required object {object_id} remains hard preserved', str(object_id) in text)

ops = []
for methods in schema['paths'].values():
    for op in methods.values():
        if isinstance(op, dict) and op.get('operationId'):
            ops.append(op['operationId'])
check('schema exposes 27 actions', len(ops) == 27)
check('cleanup inventory action retained', 'inventoryConnectorManagedDraftCleanup' in ops)
check('Trash action retained', 'trashConnectorManagedArticleDrafts' in ops)
check('legacy reconciliation inventory exposed', 'inventoryLegacyConnectorDraftReconciliation' in ops)
check('legacy reconciliation write exposed', 'reconcileLegacyConnectorDraftSupersession' in ops)
check('schema public version is 0.7.29', schema['info']['version'].startswith('0.7.29'))
check('schema contains no earlier public version', not any(v in schema_text for v in ['0.7.23', '0.7.24', '0.7.25', '0.7.26', '0.7.27', '0.7.28']))
check('schema has no combinators', not any(x in schema_text for x in ['"allOf"', '"oneOf"', '"anyOf"']))
reconcile_schema = schema['paths']['/protected-article-revisions/legacy-reconciliation/record']['post']['requestBody']['content']['application/json']['schema']
for field in ['connector_version', 'dry_run', 'confirm_connector_ownership_reconciliation', 'confirm_supersession_and_obsolescence', 'reconciliations']:
    check('reconciliation schema requires ' + field, field in reconcile_schema['required'])
item_schema = reconcile_schema['properties']['reconciliations']['items']
for field in [
    'obsolete_draft_id', 'superseding_draft_id', 'supersession_reason', 'expected_reconciliation_evidence_hash',
    'expected_source_live_post_id', 'expected_obsolete_classification_hash', 'expected_obsolete_ownership_hash',
    'expected_obsolete_wordpress_status', 'expected_obsolete_review_status', 'expected_obsolete_application_status',
    'expected_obsolete_programme_ledger_state', 'expected_obsolete_linked_recipe_clone_ids',
    'expected_obsolete_linked_failure_evidence_ids', 'expected_superseding_classification_hash',
    'expected_superseding_ownership_hash', 'expected_superseding_wordpress_status', 'expected_superseding_review_status',
    'expected_superseding_application_status', 'expected_superseding_programme_ledger_state',
    'expected_superseding_linked_recipe_clone_ids', 'expected_superseding_linked_failure_evidence_ids'
]:
    check('reconciliation item requires ' + field, field in item_schema['required'])

check('updater source version exact', "NKT_GPT_UPGRADER_0729_SOURCE_VERSION = '0.7.28'" in updater)
check('updater target version exact', "NKT_GPT_UPGRADER_0729_TARGET_VERSION = '0.7.29'" in updater)
check('updater uses generated lifecycle', 'protected-lifecycle-0.7.29.php' in updater)
check('updater identifies source lifecycle', 'protected-lifecycle-0.7.28.php' in updater)
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
