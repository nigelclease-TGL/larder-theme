#!/usr/bin/env python3
from pathlib import Path

ROOT = Path(__file__).resolve().parent
FRAGMENTS = [
    ROOT / 'src' / 'preamble.phpfrag',
    ROOT / 'src' / 'parts' / '01-core.phpfrag',
    ROOT / 'src' / 'parts' / '02-media-links.phpfrag',
    ROOT / 'src' / 'parts' / '02c-structured-nutrient-evidence.phpfrag',
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
OUTPUT = ROOT / 'artifacts' / 'generated' / 'protected-lifecycle-0.7.26.php'

OLD_NUTRIENT_BLOCK = """\t$nutrient_count = 0;
\tforeach ( array( 'Calories', 'Total Fat', 'Carbohydrates', 'Carbs', 'Sugars', 'Protein', 'Sodium', 'Fiber', 'Fibre' ) as $label ) {
\t\tif ( preg_match( '/' . preg_quote( $label, '/' ) . '\\s*:/iu', $segment_text ) ) {
\t\t\t$nutrient_count++;
\t\t}
\t}
\t$evidence['serving_fallback_nutrient_label_count'] = $nutrient_count;
"""

NEW_NUTRIENT_BLOCK = """\t$nutrient_evidence = nkt_gpt_par_0723_structured_nutrient_evidence( $tail );
\t$nutrient_count = (int) $nutrient_evidence['count'];
\t$evidence['serving_fallback_nutrient_label_count'] = $nutrient_count;
"""


def assemble() -> str:
    text = ''.join(path.read_text(encoding='utf-8') for path in FRAGMENTS)
    if text.count(OLD_NUTRIENT_BLOCK) != 1:
        raise RuntimeError('Expected exactly one 0.7.25 nutrient-label counter block')
    text = text.replace(OLD_NUTRIENT_BLOCK, NEW_NUTRIENT_BLOCK, 1)
    text = text.replace(
        "array( '0.7.23', '0.7.24', '0.7.25' )",
        "array( '0.7.23', '0.7.24', '0.7.25', '0.7.26' )",
    )
    return text


def write() -> Path:
    OUTPUT.parent.mkdir(parents=True, exist_ok=True)
    OUTPUT.write_text(assemble(), encoding='utf-8')
    return OUTPUT


if __name__ == '__main__':
    print(write())
