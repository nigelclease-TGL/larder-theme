#!/usr/bin/env python3
from pathlib import Path

ROOT = Path(__file__).resolve().parent
TEMPLATE = ROOT / 'updater' / 'nkt-gpt-connector-upgrader.php'
OUTPUT = ROOT / 'artifacts' / 'generated' / 'nkt-gpt-connector-upgrader-0.7.26.php'


def replace_once(text: str, old: str, new: str, label: str) -> str:
    count = text.count(old)
    if count != 1:
        raise RuntimeError(f'{label}: expected exactly one source occurrence, found {count}')
    return text.replace(old, new, 1)


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

    old_definitions = """\t$source_constant = \"define( 'NKT_GPT_CONNECTOR_VERSION', '0.7.25' );\";
\t$old_marker      = \"/* NKT protected article lifecycle 0.7.25 */\";
\t$new_marker      = \"/* NKT protected article lifecycle 0.7.26 */\";
"""
    new_definitions = """\t$source_constant_pattern = \"~define\\s*\\(\\s*['\\\"]NKT_GPT_CONNECTOR_VERSION['\\\"]\\s*,\\s*['\\\"]0\\.7\\.25['\\\"]\\s*\\)\\s*;~\";
\t$target_constant_pattern = \"~define\\s*\\(\\s*['\\\"]NKT_GPT_CONNECTOR_VERSION['\\\"]\\s*,\\s*['\\\"]0\\.7\\.26['\\\"]\\s*\\)\\s*;~\";
\t$source_loader_pattern   = \"~require_once\\s+__DIR__\\s*\\.\\s*['\\\"]/protected-lifecycle-0\\.7\\.25\\.php['\\\"]\\s*;~\";
\t$target_loader_pattern   = \"~require_once\\s+__DIR__\\s*\\.\\s*['\\\"]/protected-lifecycle-0\\.7\\.26\\.php['\\\"]\\s*;~\";
\t$old_lifecycle_source    = file_get_contents( $old_lifecycle );
\t$old_lifecycle_version_guard = \"const NKT_GPT_PAR_0723_VERSION         = '0.7.25';\";
\t$old_lifecycle_connector_guard = \"'0.7.25' !== NKT_GPT_CONNECTOR_VERSION\";
"""
    text = replace_once(text, old_definitions, new_definitions, 'semantic source definitions')

    old_preflight = """\tif ( 1 !== preg_match_all( '/^\\s*\\*\\s*Version:\\s*0\\.7\\.25\\s*$/m', $main_source )
\t\t|| 1 !== substr_count( $main_source, $source_constant )
\t\t|| false === strpos( $main_source, $old_marker )
\t\t|| false !== strpos( $main_source, $new_marker ) ) {
\t\twp_die( 'The active connector did not match the exact expected 0.7.25 source markers. No files were changed.', 'NKT Connector upgrade blocked', array( 'back_link' => true ) );
\t}
"""
    new_preflight = """\tif ( 1 !== preg_match_all( '/^\\s*\\*\\s*Version:\\s*0\\.7\\.25\\s*$/m', $main_source )
\t\t|| 1 !== preg_match_all( $source_constant_pattern, $main_source )
\t\t|| 0 !== preg_match_all( $target_constant_pattern, $main_source )
\t\t|| 1 !== preg_match_all( $source_loader_pattern, $main_source )
\t\t|| 0 !== preg_match_all( $target_loader_pattern, $main_source )
\t\t|| false === $old_lifecycle_source
\t\t|| false === strpos( $old_lifecycle_source, $old_lifecycle_version_guard )
\t\t|| false === strpos( $old_lifecycle_source, $old_lifecycle_connector_guard ) ) {
\t\twp_die( 'The active connector did not match the required semantic 0.7.25 version, loader and lifecycle guards. No files were changed.', 'NKT Connector upgrade blocked', array( 'back_link' => true ) );
\t}
"""
    text = replace_once(text, old_preflight, new_preflight, 'semantic preflight')

    old_constant_patch = "\t$patched = str_replace( $source_constant, \"define( 'NKT_GPT_CONNECTOR_VERSION', '0.7.26' );\", $patched, $constant_replacements );\n"
    new_constant_patch = "\t$patched = preg_replace( $source_constant_pattern, \"define( 'NKT_GPT_CONNECTOR_VERSION', '0.7.26' );\", $patched, 1, $constant_replacements );\n"
    text = replace_once(text, old_constant_patch, new_constant_patch, 'semantic constant replacement')

    old_loader_pattern = "\t$loader_pattern = \"~\\s*/\\* NKT protected article lifecycle 0\\.7\\.25 \\*/\\s*require_once __DIR__ \\. '/protected-lifecycle-0\\.7\\.25\\.php';\\s*~\";\n"
    new_loader_pattern = "\t$loader_pattern = \"~(?:\\s*/\\*\\s*NKT protected article lifecycle [^*]+\\*/\\s*)?require_once\\s+__DIR__\\s*\\.\\s*['\\\"]/protected-lifecycle-0\\.7\\.25\\.php['\\\"]\\s*;~\";\n"
    text = replace_once(text, old_loader_pattern, new_loader_pattern, 'semantic loader replacement')

    old_verification = """\t$main_ok = false !== $verification
\t\t&& false !== strpos( $verification, 'Version: 0.7.26' )
\t\t&& false !== strpos( $verification, \"define( 'NKT_GPT_CONNECTOR_VERSION', '0.7.26' );\" )
\t\t&& false !== strpos( $verification, $new_marker );
"""
    new_verification = """\t$main_ok = false !== $verification
\t\t&& 1 === preg_match_all( '/^\\s*\\*\\s*Version:\\s*0\\.7\\.26\\s*$/m', $verification )
\t\t&& 1 === preg_match_all( $target_constant_pattern, $verification )
\t\t&& 0 === preg_match_all( $source_constant_pattern, $verification )
\t\t&& 1 === preg_match_all( $target_loader_pattern, $verification )
\t\t&& 0 === preg_match_all( $source_loader_pattern, $verification );
"""
    text = replace_once(text, old_verification, new_verification, 'semantic post-write verification')

    required = [
        "NKT_GPT_UPGRADER_0726_SOURCE_VERSION = '0.7.25'",
        "NKT_GPT_UPGRADER_0726_TARGET_VERSION = '0.7.26'",
        "protected-lifecycle-0.7.25.php",
        "protected-lifecycle-0.7.26.php",
        "openapi-0.7.26.json",
        "Version: 0.7.26",
        'required semantic 0.7.25 version, loader and lifecycle guards',
        '$source_loader_pattern',
        '$target_loader_pattern',
        '$old_lifecycle_version_guard',
    ]
    missing = [value for value in required if value not in text]
    if missing:
        raise RuntimeError('Generated updater is missing: ' + ', '.join(missing))
    if 'exact expected 0.7.25 source markers' in text:
        raise RuntimeError('Generated updater still depends on the rejected exact-comment marker gate')
    return text


def write() -> Path:
    OUTPUT.parent.mkdir(parents=True, exist_ok=True)
    OUTPUT.write_text(assemble(), encoding='utf-8')
    return OUTPUT


if __name__ == '__main__':
    print(write())
