#!/usr/bin/env python3
from pathlib import Path

ROOT = Path(__file__).resolve().parent
TEMPLATE = ROOT / 'updater' / 'nkt-gpt-connector-upgrader.php'
OUTPUT = ROOT / 'artifacts' / 'generated' / 'nkt-gpt-connector-upgrader-0.7.26.php'


def assemble() -> str:
    text = TEMPLATE.read_text(encoding='utf-8')
    text = text.replace('0725', '0726')
    text = text.replace('0.7.25', '0.7.26')
    text = text.replace('0.7.24', '0.7.25')
    text = text.replace(
        'NKT GPT Connector 0.7.26 Zero-Section Serving Evidence Upgrader',
        'NKT GPT Connector 0.7.26 Structured Nutrient Evidence Upgrader',
    )
    text = text.replace(
        'with corroborated zero-section Serving H3 extraction and protected-baseline compatibility.',
        'with structured table/list/paragraph nutrient evidence and protected-baseline compatibility.',
    )
    text = text.replace(
        "'NKT GPT Connector 0.7.25 Serving Heading Extraction Upgrader'",
        "'NKT GPT Connector 0.7.25 Zero-Section Serving Evidence Upgrader'",
    )
    required = [
        "NKT_GPT_UPGRADER_0726_SOURCE_VERSION = '0.7.25'",
        "NKT_GPT_UPGRADER_0726_TARGET_VERSION = '0.7.26'",
        "protected-lifecycle-0.7.25.php",
        "protected-lifecycle-0.7.26.php",
        "openapi-0.7.26.json",
        "Version: 0.7.26",
    ]
    missing = [value for value in required if value not in text]
    if missing:
        raise RuntimeError('Generated updater is missing: ' + ', '.join(missing))
    return text


def write() -> Path:
    OUTPUT.parent.mkdir(parents=True, exist_ok=True)
    OUTPUT.write_text(assemble(), encoding='utf-8')
    return OUTPUT


if __name__ == '__main__':
    print(write())
