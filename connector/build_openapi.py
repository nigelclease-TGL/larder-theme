#!/usr/bin/env python3
from pathlib import Path
import json

ROOT = Path(__file__).resolve().parent
PARTS = sorted((ROOT / 'openapi' / 'parts').glob('schema-*'))
OUTPUT = ROOT / 'artifacts' / 'generated' / 'openapi-0.7.23.json'

def assemble() -> str:
    return ''.join(path.read_text(encoding='utf-8') for path in PARTS)

def write() -> Path:
    text = assemble()
    json.loads(text)
    OUTPUT.parent.mkdir(parents=True, exist_ok=True)
    OUTPUT.write_text(text, encoding='utf-8')
    return OUTPUT

if __name__ == '__main__':
    print(write())
