#!/usr/bin/env python3
from pathlib import Path

ROOT = Path(__file__).resolve().parent
TEMPLATE = ROOT / 'updater' / 'nkt-gpt-connector-upgrader.php'
OUTPUT = ROOT / 'artifacts' / 'generated' / 'nkt-gpt-connector-upgrader-0.7.29.php'


def assemble() -> str:
    text = TEMPLATE.read_text(encoding='utf-8')
    text = text.replace('0725', '0729')
    text = text.replace('0.7.25', '0.7.29')
    text = text.replace('0.7.24', '0.7.28')
    text = text.replace(r'0\.7\.25', r'0\.7\.29')
    text = text.replace(r'0\.7\.24', r'0\.7\.28')
    text = text.replace(
        'NKT GPT Connector 0.7.29 Zero-Section Serving Evidence Upgrader',
        'NKT GPT Connector 0.7.29 Guarded Legacy Draft Reconciliation Upgrader',
    )
    text = text.replace(
        'with corroborated zero-section Serving H3 extraction and protected-baseline compatibility.',
        'with read-only legacy connector evidence and exact-pair guarded ownership, supersession, and obsolescence metadata reconciliation.',
    )
    text = text.replace(
        "'NKT GPT Connector 0.7.28 Serving Heading Extraction Upgrader'",
        "'NKT GPT Connector 0.7.28 Connector Draft Ownership and Guarded Trash Upgrader'",
    )
    required = [
        "NKT_GPT_UPGRADER_0729_SOURCE_VERSION = '0.7.28'",
        "NKT_GPT_UPGRADER_0729_TARGET_VERSION = '0.7.29'",
        "protected-lifecycle-0.7.28.php",
        "protected-lifecycle-0.7.29.php",
        "openapi-0.7.29.json",
        "Version: 0.7.29",
        "NKT GPT Connector 0.7.29 Guarded Legacy Draft Reconciliation Upgrader",
        "NKT GPT Connector 0.7.28 Connector Draft Ownership and Guarded Trash Upgrader",
        "wp_cache_flush",
        "opcache_invalidate",
        "restore_all",
        "deactivate_plugins( plugin_basename( __FILE__ ), true )",
    ]
    missing = [value for value in required if value not in text]
    if missing:
        raise RuntimeError('Generated updater is missing: ' + ', '.join(missing))
    stale = ['0.7.24', '0.7.25', r'0\.7\.24', r'0\.7\.25', '0725']
    found = [value for value in stale if value in text]
    if found:
        raise RuntimeError('Generated updater contains stale source values: ' + ', '.join(found))
    return text


def write() -> Path:
    OUTPUT.parent.mkdir(parents=True, exist_ok=True)
    OUTPUT.write_text(assemble(), encoding='utf-8')
    return OUTPUT


if __name__ == '__main__':
    print(write())
