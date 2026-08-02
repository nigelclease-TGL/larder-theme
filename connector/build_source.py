#!/usr/bin/env python3
from pathlib import Path

ROOT = Path(__file__).resolve().parent
FRAGMENTS = [
    ROOT / 'src' / 'preamble.phpfrag',
    ROOT / 'src' / 'parts' / '01-core.phpfrag',
    ROOT / 'src' / 'parts' / '02-media-links.phpfrag',
    ROOT / 'src' / 'parts' / '03-nutrition-state.phpfrag',
    ROOT / 'src' / 'parts' / '04a-guard-maps.phpfrag',
    ROOT / 'src' / 'parts' / '04b-baseline-lifecycle.phpfrag',
    ROOT / 'src' / 'parts' / '04c-status.phpfrag',
    ROOT / 'src' / 'parts' / '05a-create.phpfrag',
    ROOT / 'src' / 'parts' / '05b-update.phpfrag',
    ROOT / 'src' / 'parts' / '06a-audit.phpfrag',
    ROOT / 'src' / 'parts' / '06b-review-backup.phpfrag',
    ROOT / 'src' / 'parts' / '07a-apply-rollback.phpfrag',
    ROOT / 'src' / 'parts' / '07b-repair.phpfrag',
    ROOT / 'src' / 'parts' / '08a-cleanup.phpfrag',
    ROOT / 'src' / 'parts' / '08b-routes.phpfrag',
]
OUTPUT = ROOT / 'artifacts' / 'generated' / 'protected-lifecycle-0.7.24.php'

def assemble() -> str:
    return ''.join(path.read_text(encoding='utf-8') for path in FRAGMENTS)

def write() -> Path:
    OUTPUT.parent.mkdir(parents=True, exist_ok=True)
    OUTPUT.write_text(assemble(), encoding='utf-8')
    return OUTPUT

if __name__ == '__main__':
    print(write())
