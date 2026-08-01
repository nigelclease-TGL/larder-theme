#!/usr/bin/env python3
from pathlib import Path
import json, re, subprocess, tempfile, textwrap, sys

ROOT = Path(__file__).resolve().parents[1]
subprocess.run([sys.executable, str(ROOT / 'build_source.py')], check=True, capture_output=True, text=True)
subprocess.run([sys.executable, str(ROOT / 'build_openapi.py')], check=True, capture_output=True, text=True)
SRC = ROOT / 'artifacts' / 'generated' / 'protected-lifecycle-0.7.23.php'
SCHEMA = ROOT / 'artifacts' / 'generated' / 'openapi-0.7.23.json'
text = SRC.read_text(encoding='utf-8')
schema = json.loads(SCHEMA.read_text(encoding='utf-8'))
checks=[]
def check(name, cond):
    checks.append((name,bool(cond)))
    if not cond: print('FAIL:',name)

# 1-3 runtime/route/service acceptance.
check('runtime callback accepts request carrying both guard arguments', 'function nkt_gpt_par_0723_update( WP_REST_Request $request )' in text and "get_param( 'expected_recipe_statuses' )" in text and "get_param( 'expected_status_independent_recipe_hashes' )" in text)
route_block = text[text.index("'/protected-article-revisions/update'"):text.index("'/protected-article-revisions/audit'")]
operation_block = text[text.index('$operation_guards = array('):text.index('$operation_args = array_merge')]
check('REST route forwards expected_recipe_statuses', "'expected_recipe_statuses'" in operation_block and 'nkt_gpt_par_0723_update' in route_block)
check('REST route forwards expected_status_independent_recipe_hashes', "'expected_status_independent_recipe_hashes'" in operation_block and 'nkt_gpt_par_0723_update' in route_block)
check('internal preflight receives both maps', 'nkt_gpt_par_0723_validate_recipe_guard_maps( $request )' in text and 'nkt_gpt_par_0723_update_recipe_guard_evidence' in text)

# 4-8 map and drift guards are explicit and before direct write.
update = text[text.index('function nkt_gpt_par_0723_update( WP_REST_Request'):text.index('/** Build a complete audit record')]
check('map validation occurs before direct draft write', update.index('validate_recipe_guard_maps') < update.index('direct_article_write'))
for phrase in ['missing_recipe_ids','extra_recipe_ids','64 lowercase hexadecimal','Invalid literal WordPress post status']:
    check('map validation evidence: '+phrase, phrase in text)
check('recipe status drift rejects before write', 'status_matches' in text and 'update_recipe_preflight_failed' in update)
check('status independent hash drift rejects before write', 'status_independent_hash_matches' in text and 'update_recipe_preflight_failed' in update)
check('post-write drift restores draft and recipes', 'restore_recipe_snapshots' in update and 'draft_restoration_performed' in update and 'recipe_drift' in update)
check('0.7.22 protected draft baseline can be migrated after successful guarded update', 'compatible_baseline' in update and 'baseline_migrated_from' in update and "'_nkt_par_0722_baseline'" in text)

# 9-10 source-level serving extraction and same-value hash.
check('serving extractor is top-level NUTRITION scoped', 'extract_nutrition_serving_heading' in text and "'NUTRITION' === strtoupper" in text and 'nutrition_heading' in text)
check('unrelated served prose is not selected', "Serving\\s*:\\s*" in text and "served" not in text[text.index('function nkt_gpt_par_0723_extract_nutrition_serving_heading'):text.index('/** Extract a position-independent Nutrition manifest')])

# 11-13 cleanup protections.
cleanup = text[text.index('function nkt_gpt_par_0723_cleanup_revision_objects'):text.index('/** Override connector status')]
check('cleanup dry-run defaults to no write', "! $request->has_param( 'dry_run' )" in cleanup and "'writes_attempted' => false" in cleanup)
check('cleanup refuses live references', 'live_reference_exists' in cleanup and 'cleanup_preflight_failed' in cleanup)
for pid in ['41019','41037','30780','30800']:
    check('protected cleanup ID '+pid, pid in text[text.index('function nkt_gpt_par_0723_cleanup_protected_id'):text.index('/** Return a stored protected lifecycle record')])
check('permanent delete requires separate authorisation and prior archive', 'explicit_permanent_delete_authorisation' in cleanup and 'object_not_archived_first_in_this_batch' in cleanup)

# OpenAPI tests.
ops=[]
for p, methods in schema['paths'].items():
    for m, op in methods.items():
        if isinstance(op,dict) and op.get('operationId'): ops.append(op['operationId'])
check('schema exposes 23 actions', len(ops)==23)
check('cleanup action exposed separately with documented reason', 'cleanupRevisionObjects' in ops and '23 actions' in schema['info']['description'])
check('schema has no combinators', not any(x in SCHEMA.read_text() for x in ['"allOf"','"oneOf"','"anyOf"']))
check('schema below 300 lines', len(SCHEMA.read_text().splitlines()) < 300)
for comp in ['CreateProtectedArticleRevisionRequest','ProtectedRevisionOperationRequest','UpdateProtectedArticleRevisionRequest','ReviewProtectedArticleRevisionRequest']:
    props=schema['components']['schemas'][comp]['properties']
    check(comp+' named status map', props['expected_recipe_statuses'].get('$ref')=='#/components/schemas/RecipeStatusMap')
    check(comp+' named object hash map', props['expected_recipe_object_hashes'].get('$ref')=='#/components/schemas/HashMap')
    check(comp+' named independent hash map', props['expected_status_independent_recipe_hashes'].get('$ref')=='#/components/schemas/HashMap')

# Runtime helper/route tests with isolated WordPress mocks.
runtime=subprocess.run(['php',str(ROOT/'tests'/'runtime_harness.php')],capture_output=True,text=True)
check('isolated runtime harness',runtime.returncode==0)
print(runtime.stdout.strip())
if runtime.returncode: print(runtime.stderr)

# PHP syntax.
proc=subprocess.run(['php','-l',str(SRC)],capture_output=True,text=True)
check('PHP syntax',proc.returncode==0)
if proc.returncode: print(proc.stdout,proc.stderr)

failed=[n for n,p in checks if not p]
print(f'{len(checks)-len(failed)}/{len(checks)} checks passed')
if failed:
    print('Failed:',*failed,sep='\n- ')
    raise SystemExit(1)
