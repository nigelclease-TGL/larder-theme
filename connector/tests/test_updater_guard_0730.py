#!/usr/bin/env python3
from pathlib import Path
import subprocess
import sys

ROOT = Path(__file__).resolve().parents[1]
subprocess.run([sys.executable, str(ROOT / 'build_0730.py')], check=True, capture_output=True, text=True)

UPDATER = ROOT / 'artifacts' / 'generated' / 'nkt-gpt-connector-upgrader-0.7.30.php'
text = UPDATER.read_text(encoding='utf-8')
checks = []


def check(name, condition):
    checks.append((name, bool(condition)))
    if not condition:
        print('FAIL:', name)


check('hotfix updater package revision is 0.7.30.2', 'Version: 0.7.30.2' in text)
check('literal source version is exactly 0.7.29', "NKT_GPT_UPGRADER_0730_SOURCE_VERSION = '0.7.29'" in text)
check('literal target version is exactly 0.7.30', "NKT_GPT_UPGRADER_0730_TARGET_VERSION = '0.7.30'" in text)
check('header preflight regex matches 0.7.29', r"Version:\s*0\.7\.29\s*$" in text)
check('header replacement regex matches 0.7.29', r"Version:\s*)0\.7\.29(\s*$)" in text)
check('loader replacement regex matches 0.7.29 marker', r"NKT protected article lifecycle 0\.7\.29" in text)
check('loader replacement regex matches 0.7.29 file', r"protected-lifecycle-0\.7\.29\.php" in text)
check('source constant matches 0.7.29', "define( 'NKT_GPT_CONNECTOR_VERSION', '0.7.29' );" in text)
check('source lifecycle file is 0.7.29', "protected-lifecycle-0.7.29.php" in text)
check('previous 0.7.29 updater name is exact', "'NKT GPT Connector 0.7.29 Guarded Legacy Draft Reconciliation Upgrader'" in text)
check('no stale literal 0.7.28 marker remains', '0.7.28' not in text)
check('no stale escaped 0.7.28 marker remains', r'0\.7\.28' not in text)

fixture = """<?php
/**
 * Plugin Name: Nigel's Kitchen Table GPT Connector
 * Version: 0.7.29
 */
define( 'NKT_GPT_CONNECTOR_VERSION', '0.7.29' );
/* NKT protected article lifecycle 0.7.29 */
require_once __DIR__ . '/protected-lifecycle-0.7.29.php';
"""
check('synthetic installed source contains every guarded 0.7.29 marker', all(marker in fixture for marker in [
    '* Version: 0.7.29',
    "define( 'NKT_GPT_CONNECTOR_VERSION', '0.7.29' );",
    '/* NKT protected article lifecycle 0.7.29 */',
    "require_once __DIR__ . '/protected-lifecycle-0.7.29.php';",
]))

proc = subprocess.run(['php', '-l', str(UPDATER)], capture_output=True, text=True)
check('corrected updater PHP syntax passes', proc.returncode == 0)
if proc.returncode:
    print(proc.stdout, proc.stderr)

failed = [name for name, passed in checks if not passed]
print(f'{len(checks) - len(failed)}/{len(checks)} updater guard checks passed')
if failed:
    print('Failed:', *failed, sep='\n- ')
    raise SystemExit(1)
