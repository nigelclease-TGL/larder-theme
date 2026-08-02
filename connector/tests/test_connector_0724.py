#!/usr/bin/env python3
from pathlib import Path
import json
import subprocess
import sys

ROOT = Path(__file__).resolve().parents[1]
subprocess.run([sys.executable, str(ROOT / 'build_source.py')], check=True, capture_output=True, text=True)
subprocess.run([sys.executable, str(ROOT / 'build_openapi.py')], check=True, capture_output=True, text=True)
SRC = ROOT / 'artifacts' / 'generated' / 'protected-lifecycle-0.7.24.php'
SCHEMA = ROOT / 'artifacts' / 'generated' / 'openapi-0.7.24.json'
UPDATER = ROOT / 'updater' / 'nkt-gpt-connector-upgrader.php'
text = SRC.read_text(encoding='utf-8')
schema_text = SCHEMA.read_text(encoding='utf-8')
schema = json.loads(schema_text)
updater = UPDATER.read_text(encoding='utf-8')
checks = []

def check(name, condition):
    checks.append((name, bool(condition)))
    if not condition:
        print('FAIL:', name)

check('connector source targets 0.7.24', "const NKT_GPT_PAR_0723_VERSION         = '0.7.24';" in text)
check('production fallback source exists', 'unique_article_serving_h3_with_single_nutrition_section' in text)
check('fallback requires one nutrition section', "1 !== (int) $nutrition_section_count" in text)
check('parsed article headings recursively collected', 'collect_serving_headings_in_blocks' in text)
check('raw H3 fallback exists', 'collect_raw_serving_h3_candidates' in text)
check('nested markup normalized', 'visible_heading_text' in text and 'wp_strip_all_tags' in text)
check('candidate expression is anchored', "^Serving\\s*:\\s*(.+)$" in text)
check('ambiguous candidates rejected', 'multiple_visible_serving_h3_candidates' in text)
check('zero candidates rejected', 'no_visible_serving_h3_candidate' in text)
check('status exposes section count', 'parsed_nutrition_section_count' in text and 'live_serving_evidence' in text)
check('status exposes candidate count', 'matching_visible_serving_h3_count' in text)
check('status exposes fallback decision', 'fallback_rejection_reason' in text)

comparison = text[text.index('function nkt_gpt_par_0723_baseline_comparison_state'):text.index('function nkt_gpt_par_0723_compatible_baseline')]
for field in [
    'serving_label', 'serving_label_hash', 'serving_label_source', 'serving_label_block_path',
    'parsed_nutrition_section_count', 'matching_visible_serving_h3_count',
    'serving_fallback_accepted', 'serving_fallback_rejection_reason', 'serving_heading_candidates'
]:
    check('baseline ignores parser field ' + field, "$state['" + field + "']" in comparison)

check('status accepts stored 0.7.23 baseline', "array( '0.7.23', '0.7.24' )" in text)
check('content hash remains guarded', 'expected_live_content_hash' in text)
check('nutrition hash remains guarded', 'expected_nutrition_section_hash' in text)
check('serving before remains guarded', 'expected_serving_label' in text)
check('serving after remains guarded', 'expected_serving_label_after' in text)
check('replacement counts remain guarded', 'replacement_count_mismatch' in text)
check('failed protected update restores draft', "nkt_gpt_par_0723_direct_article_write( $draft_id, $before )" in text and 'draft_rolled_back' in text)

ops = []
for methods in schema['paths'].values():
    for op in methods.values():
        if isinstance(op, dict) and op.get('operationId'):
            ops.append(op['operationId'])
check('schema exposes 23 actions', len(ops) == 23)
check('schema public version is 0.7.24', schema['info']['version'].startswith('0.7.24'))
check('schema contains no public 0.7.23 reference', '0.7.23' not in schema_text)
check('schema has no combinators', not any(x in schema_text for x in ['"allOf"', '"oneOf"', '"anyOf"']))
check('schema remains compact', len(schema_text.splitlines()) < 300)

check('updater source version exact', "NKT_GPT_UPGRADER_0724_SOURCE_VERSION = '0.7.23'" in updater)
check('updater target version exact', "NKT_GPT_UPGRADER_0724_TARGET_VERSION = '0.7.24'" in updater)
check('updater backs up primary connector', 'secure_backup( $main_file, $main_backup )' in updater)
check('updater backs up old lifecycle', 'secure_backup( $old_lifecycle, $lifecycle_backup )' in updater)
check('updater verifies lifecycle SHA', "hash_file( 'sha256', $new_lifecycle )" in updater)
check('updater invalidates opcode cache', 'opcache_invalidate' in updater)
check('updater flushes WordPress cache', 'wp_cache_flush' in updater)
check('updater self deactivates', 'deactivate_plugins( plugin_basename( __FILE__ ), true )' in updater)
check('updater patches loader instead of overriding functions', 'protected article lifecycle 0.7.23' in updater and 'protected article lifecycle 0.7.24' in updater)

runtime = subprocess.run(['php', str(ROOT / 'tests' / 'runtime_harness.php')], capture_output=True, text=True)
check('isolated runtime harness', runtime.returncode == 0)
print(runtime.stdout.strip())
if runtime.returncode:
    print(runtime.stderr)

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
