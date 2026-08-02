#!/usr/bin/env python3
from pathlib import Path
import hashlib
import zipfile
from build_source import write as build_source
from build_openapi import write as build_openapi
from build_updater import write as build_updater

ROOT = Path(__file__).resolve().parent
generated_source = build_source()
generated_openapi = build_openapi()
generated_updater = build_updater()
OUT = ROOT / 'artifacts'
OUT.mkdir(parents=True, exist_ok=True)
zip_path = OUT / 'nkt-gpt-connector-0.7.29-guarded-legacy-draft-reconciliation-upgrader.zip'
sha_path = OUT / 'nkt-gpt-connector-0.7.29-guarded-legacy-draft-reconciliation-upgrader.sha256.txt'
prefix = 'nkt-gpt-connector-upgrader-0.7.29/'
files = [
    ('artifacts/generated/nkt-gpt-connector-upgrader-0.7.29.php', 'nkt-gpt-connector-upgrader.php'),
    ('artifacts/generated/protected-lifecycle-0.7.29.php', 'protected-lifecycle-0.7.29.php'),
    ('artifacts/generated/openapi-0.7.29.json', 'openapi-0.7.29.json'),
    ('CHANGELOG.md', 'CHANGELOG.txt'),
    ('README.md', 'README.txt'),
    ('VALIDATION.md', 'VALIDATION.txt'),
]
with zipfile.ZipFile(zip_path, 'w', compression=zipfile.ZIP_DEFLATED, compresslevel=9) as archive:
    for source, target in files:
        data = (ROOT / source).read_bytes()
        info = zipfile.ZipInfo(prefix + target, date_time=(2026, 8, 2, 0, 0, 0))
        info.compress_type = zipfile.ZIP_DEFLATED
        info.external_attr = (0o100644 & 0xFFFF) << 16
        archive.writestr(info, data)
digest = hashlib.sha256(zip_path.read_bytes()).hexdigest()
sha_path.write_text(f'{digest}  {zip_path.name}\n', encoding='utf-8')
print(zip_path)
print(digest)
