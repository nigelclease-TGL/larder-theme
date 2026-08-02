# Connector 0.7.26 implementation note

Production diagnosis proved that the five nutrient labels are literal first-cell text in a Gutenberg table and have no trailing colons. Connector 0.7.26 therefore uses structured, value-backed nutrient evidence rather than punctuation-dependent label matching.

The implementation is general and contains no post ID, draft ID, recipe ID, heading ID or serving-label exception.
