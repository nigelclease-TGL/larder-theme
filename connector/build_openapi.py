#!/usr/bin/env python3
from pathlib import Path
import json

ROOT = Path(__file__).resolve().parent
PARTS = sorted((ROOT / 'openapi' / 'parts').glob('schema-*'))
OUTPUT = ROOT / 'artifacts' / 'generated' / 'openapi-0.7.26.json'

def assemble() -> str:
    # The compact schema source is inherited from 0.7.23. Build-time replacement
    # updates every public version reference while preserving the 23-action shape.
    return ''.join(path.read_text(encoding='utf-8') for path in PARTS).replace('0.7.23', '0.7.26')

def write() -> Path:
    text = assemble()
    json.loads(text)
    OUTPUT.parent.mkdir(parents=True, exist_ok=True)
    OUTPUT.write_text(text, encoding='utf-8')
    return OUTPUT

if __name__ == '__main__':
    print(write())
