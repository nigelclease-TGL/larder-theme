#!/usr/bin/env python3
from pathlib import Path
import json
import subprocess
import sys

ROOT = Path(__file__).resolve().parents[1]
subprocess.run([sys.executable, str(ROOT / 'build_source.py')], check=True, capture_output=True, text=True)
subprocess.run([sys.executable, str(ROOT / 'build_openapi.py')], check=True, capture_output=True, text=True)
subprocess.run([sys.executable, str(ROOT / 'build_updater.py')], check=True, capture_output=True, text=True)
SRC = ROOT / 'artifacts' / 'generated' / 'protected-lifecycle-0.7.26.php'
SCHEMA = ROOT / 'artifacts' / 'generated' / 'openapi-0.7.26.json'
UPDATER = ROOT / 'artifacts' / 'generated' / 'nkt-gpt-connector-upgrader-0.7.26.php'
text = SRC.read_text(encoding='utf-8')
schema_text = SCHEMA.read_text(encoding='utf-8')
schema = json.loads(schema_text)
updater = UPDATER.read_text(encoding='utf-8')
checks = []

def check(name, condition):
    checks.append((name, bool(condition)))
    if not condition:
        print('FAIL:', name)

check('connector source targets 0.7.26', "const NKT_GPT_PAR_0723_VERSION         = '0.7.26';" in text)
check('structured evidence helper exists', 'nkt_gpt_par_0723_structured_nutrient_evidence' in text)
check('boundary preserving normalization exists', 'nkt_gpt_par_0723_boundary_preserving_text' in text)
check('table rows parsed', "preg_match_all( '/<tr\\b" in text and "preg_match_all( '/<(?:td|th)" in text)
check('table label exactness enforced', 'nkt_gpt_par_0723_canonical_nutrient_label' in text)
check('table value must be numeric', "preg_match( '/' . $number . '/u', $value_text )" in text)
check('adjacent records supported', "'adjacent_record'" in text)
check('same records supported', "'same_record'" in text)
check('colon text retained', "'colon_text'" in text)
check('distinct labels returned', "'labels' => $labels" in text and 'array_keys( $matched )' in text)
check('old colon-only counter removed from generated lifecycle', "preg_quote( $label, '/' ) . '\\s*:/iu', $segment_text" not in text)
check('zero-section signature uses structured evidence', "nkt_gpt_par_0723_structured_nutrient_evidence( $tail )" in text)
check('minimum four labels retained', '4 > $nutrient_count' in text)
check('single section fallback retained', 'unique_article_serving_h3_with_single_nutrition_section' in text)
check('zero section fallback retained', 'unique_article_serving_h3_with_unique_nutrition_presentation' in text)
check('ambiguous candidates rejected', 'multiple_visible_serving_h3_candidates' in text)
check('duplicate presentation rejected', 'nutrition_per_serving_marker_not_unique' in text)
check('daily value evidence retained', 'daily_value_marker_not_unique' in text)
check('calorie basis evidence retained', 'calorie_basis_marker_not_unique' in text)
check('serving mismatch guard retained', 'expected_serving_label_after' in text)
check('content hash retained', 'expected_live_content_hash' in text)
check('nutrition hash retained', 'expected_nutrition_section_hash' in text)
check('recipe hashes retained', 'expected_recipe_object_hashes' in text and 'expected_wprm_nutrition_hashes' in text)
check('media guards retained', 'expected_media_reference_hash' in text)
check('amazon guards retained', 'expected_amazon_destinations' in text)
check('affiliate guards retained', 'expected_affiliate_identifiers' in text)
check('replacement count retained', 'replacement_count_mismatch' in text)
check('draft restoration retained', "nkt_gpt_par_0723_direct_article_write( $draft_id, $before )" in text and 'draft_rolled_back' in text)

comparison = text[text.index('function nkt_gpt_par_0723_baseline_comparison_state'):text.index('function nkt_gpt_par_0723_compatible_baseline')]
for field in [
    'serving_label', 'serving_label_hash', 'serving_label_source', 'serving_label_block_path',
    'parsed_nutrition_section_count', 'matching_visible_serving_h3_count',
    'serving_fallback_accepted', 'serving_fallback_rejection_reason', 'serving_fallback_gate',
    'serving_fallback_raw_serving_h3_count', 'serving_fallback_segment_nutrition_marker_count',
    'serving_fallback_article_nutrition_marker_count', 'serving_fallback_nutrient_label_count',
    'serving_fallback_daily_value_marker_count', 'serving_fallback_calorie_basis_count',
    'serving_fallback_signature_accepted', 'serving_fallback_signature_rejection_reason',
    'serving_heading_candidates'
]:
    check('baseline ignores parser field ' + field, "$state['" + field + "']" in comparison)

check('status accepts stored baselines through 0.7.26', "'0.7.23', '0.7.24', '0.7.25', '0.7.26'" in text)
ops = []
for methods in schema['paths'].values():
    for op in methods.values():
        if isinstance(op, dict) and op.get('operationId'):
            ops.append(op['operationId'])
check('schema exposes 23 actions', len(ops) == 23)
check('schema public version is 0.7.26', schema['info']['version'].startswith('0.7.26'))
check('schema contains no earlier public version', not any(v in schema_text for v in ['0.7.23','0.7.24','0.7.25']))
check('schema has no combinators', not any(x in schema_text for x in ['"allOf"','"oneOf"','"anyOf"']))
check('schema remains compact', len(schema_text.splitlines()) < 300)

check('updater source version exact', "NKT_GPT_UPGRADER_0726_SOURCE_VERSION = '0.7.25'" in updater)
check('updater target version exact', "NKT_GPT_UPGRADER_0726_TARGET_VERSION = '0.7.26'" in updater)
check('updater uses generated lifecycle', 'protected-lifecycle-0.7.26.php' in updater)
check('updater patches exact old lifecycle', 'protected article lifecycle 0.7.25' in updater and 'protected article lifecycle 0.7.26' in updater)
check('updater verifies semantic source constant', '$source_constant_pattern' in updater and '$target_constant_pattern' in updater)
check('updater verifies semantic loader', '$source_loader_pattern' in updater and '$target_loader_pattern' in updater)
check('updater verifies installed old lifecycle version', '$old_lifecycle_version_guard' in updater and '$old_lifecycle_connector_guard' in updater)
check('updater does not require exact comment marker', 'exact expected 0.7.25 source markers' not in updater)
check('updater replaces source constant by regex once', 'preg_replace( $source_constant_pattern' in updater)
check('updater post-write verifies source removed', '0 === preg_match_all( $source_loader_pattern, $verification )' in updater)
check('updater backs up primary connector', 'secure_backup( $main_file, $main_backup )' in updater)
check('updater backs up old lifecycle', 'secure_backup( $old_lifecycle, $lifecycle_backup )' in updater)
check('updater verifies lifecycle SHA', "hash_file( 'sha256', $new_lifecycle )" in updater)
check('updater invalidates opcode cache', 'opcache_invalidate' in updater)
check('updater flushes WordPress cache', 'wp_cache_flush' in updater)
check('updater restores on failure', 'restore_all' in updater)
check('updater self deactivates', 'deactivate_plugins( plugin_basename( __FILE__ ), true )' in updater)
check('updater names previous updater exactly', 'NKT GPT Connector 0.7.25 Zero-Section Serving Evidence Upgrader' in updater)

runtime = subprocess.run(['php', str(ROOT / 'tests' / 'runtime_harness_0726.php')], capture_output=True, text=True)
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
